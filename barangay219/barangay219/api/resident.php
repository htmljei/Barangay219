<?php
/**
 * E-Barangay Information Management System
 * Resident Information API
 */

header('Content-Type: application/json');
define('ACCESS_ALLOWED', true);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';

requireLogin();
requireAnyRole([ROLE_BARANGAY_CAPTAIN, ROLE_SECRETARY]);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listResidents();
        break;
    case 'list_pending':
        listPendingRegistrations();
        break;
    case 'approve':
        approveResident();
        break;
    case 'reject':
        rejectResident();
        break;
    case 'get':
        getResident();
        break;
    case 'create':
        createResident();
        break;
    case 'update':
        updateResident();
        break;
    case 'delete':
        deleteResident();
        break;
    case 'search':
        searchResidents();
        break;
    default:
        sendResponse(false, 'Invalid action', null, 400);
        break;
}

/**
 * List all residents with pagination (approved by default; officials can pass approval_status=all|pending)
 */
function listResidents() {
    try {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? ITEMS_PER_PAGE);
        $offset = ($page - 1) * $limit;
        $approvalFilter = $_GET['approval_status'] ?? 'approved';
        $db = Database::getInstance();
        $where = "1=1";
        $paramsCount = [];
        if ($approvalFilter === 'pending') {
            $where .= " AND r.approval_status = 'pending'";
        } elseif ($approvalFilter === 'approved') {
            $where .= " AND (r.approval_status = 'approved' OR r.approval_status IS NULL)";
        }
        
        $countSql = "SELECT COUNT(*) as total FROM residents r WHERE $where";
        $total = $db->fetchOne($countSql, $paramsCount)['total'];
        
        $sql = "SELECT r.*, h.address as household_address, h.total_members
                FROM residents r
                LEFT JOIN households h ON r.household_id = h.id
                WHERE $where
                ORDER BY r.id ASC
                LIMIT ? OFFSET ?";
        $paramsList = array_merge($paramsCount, [$limit, $offset]);
        $residents = $db->fetchAll($sql, $paramsList);
        
        sendResponse(true, 'Residents retrieved successfully', [
            'residents' => $residents,
            'total' => (int)$total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ]);
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'approval_status') !== false) {
            try {
                $db = Database::getInstance();
                $total = $db->fetchOne("SELECT COUNT(*) as total FROM residents")['total'];
                $residents = $db->fetchAll("SELECT r.*, h.address as household_address, h.total_members FROM residents r LEFT JOIN households h ON r.household_id = h.id ORDER BY r.id ASC LIMIT ? OFFSET ?", [$limit, $offset]);
                sendResponse(true, 'Residents retrieved successfully', ['residents' => $residents, 'total' => (int)$total, 'page' => $page, 'limit' => $limit, 'total_pages' => ceil($total / $limit)]);
                return;
            } catch (Exception $e2) {
                sendResponse(false, 'Error retrieving residents', null, 500);
                return;
            }
        }
        error_log("List residents error: " . $e->getMessage());
        sendResponse(false, 'Error retrieving residents', null, 500);
    }
}

/**
 * List pending resident registrations (requires can_approve_registration)
 */
function listPendingRegistrations() {
    if (!canApproveRegistration()) {
        sendResponse(false, 'Access denied', null, 403);
        return;
    }
    try {
        $db = Database::getInstance();
        $sql = "SELECT r.*, h.address as household_address
                FROM residents r
                LEFT JOIN households h ON r.household_id = h.id
                WHERE r.approval_status = 'pending'
                ORDER BY r.created_at DESC";
        $residents = $db->fetchAll($sql);
        sendResponse(true, 'Pending registrations', $residents);
    } catch (Exception $e) {
        error_log("List pending error: " . $e->getMessage());
        sendResponse(false, 'Error retrieving pending', null, 500);
    }
}

/**
 * Approve a pending resident registration
 */
function approveResident() {
    if (!canApproveRegistration()) {
        sendResponse(false, 'Access denied', null, 403);
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method', null, 405);
        return;
    }
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        sendResponse(false, 'Resident ID is required', null, 400);
        return;
    }
    try {
        $db = Database::getInstance();
        $db->query(
            "UPDATE residents SET approval_status = 'approved', approved_by_user_id = ?, approved_at = NOW() WHERE id = ? AND approval_status = 'pending'",
            [getCurrentUserId(), $id]
        );
        $resident = $db->fetchOne("SELECT * FROM residents WHERE id = ?", [$id]);
        if ($resident && ($resident['approval_status'] ?? '') === 'approved') {
            sendResponse(true, 'Registration approved', $resident);
        } else {
            sendResponse(false, 'Resident not found or not pending', null, 400);
        }
    } catch (Exception $e) {
        error_log("Approve error: " . $e->getMessage());
        sendResponse(false, 'Error approving', null, 500);
    }
}

/**
 * Reject a pending resident registration
 */
function rejectResident() {
    if (!canApproveRegistration()) {
        sendResponse(false, 'Access denied', null, 403);
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method', null, 405);
        return;
    }
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        sendResponse(false, 'Resident ID is required', null, 400);
        return;
    }
    try {
        $db = Database::getInstance();
        $db->query(
            "UPDATE residents SET approval_status = 'rejected', approved_by_user_id = ?, approved_at = NOW() WHERE id = ? AND approval_status = 'pending'",
            [getCurrentUserId(), $id]
        );
        $resident = $db->fetchOne("SELECT * FROM residents WHERE id = ?", [$id]);
        sendResponse(true, 'Registration rejected', $resident);
    } catch (Exception $e) {
        error_log("Reject error: " . $e->getMessage());
        sendResponse(false, 'Error rejecting', null, 500);
    }
}

/**
 * Get single resident
 */
function getResident() {
    $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
    
    if (!$id) {
        sendResponse(false, 'Resident ID is required', null, 400);
        return;
    }
    
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT r.*, h.address as household_address, h.total_members, h.family_head_id
                FROM residents r
                LEFT JOIN households h ON r.household_id = h.id
                WHERE r.id = ?";
        
        $resident = $db->fetchOne($sql, [$id]);
        
        if (!$resident) {
            sendResponse(false, 'Resident not found', null, 404);
            return;
        }
        
        sendResponse(true, 'Resident retrieved successfully', $resident);
        
    } catch (Exception $e) {
        error_log("Get resident error: " . $e->getMessage());
        sendResponse(false, 'Error retrieving resident', null, 500);
    }
}

/**
 * Create new resident (official registration: approval_status=approved, registration_type=official)
 */
function createResident() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method', null, 405);
        return;
    }
    
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $middle_name = sanitizeInput($_POST['middle_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    $suffix = sanitizeInput($_POST['suffix'] ?? '');
    $birth_date = $_POST['birth_date'] ?? '';
    $gender = sanitizeInput($_POST['gender'] ?? '');
    $civil_status = sanitizeInput($_POST['civil_status'] ?? '');
    $occupation = sanitizeInput($_POST['occupation'] ?? '');
    $citizenship = sanitizeInput($_POST['citizenship'] ?? 'Filipino');
    $place_of_birth = sanitizeInput($_POST['place_of_birth'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $length_of_stay_years = trim($_POST['length_of_stay_years'] ?? '') === '' ? null : intval($_POST['length_of_stay_years']);
    $date_of_residency = trim($_POST['date_of_residency'] ?? '') === '' ? null : $_POST['date_of_residency'];
    $contact_number = sanitizeInput($_POST['contact_number'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $household_id = intval($_POST['household_id'] ?? 0);
    $relationship_to_head = sanitizeInput($_POST['relationship_to_head'] ?? '');
    $relationship_to_head = in_array($relationship_to_head, ['self','spouse','child','other']) ? $relationship_to_head : null;
    $monthly_income = trim($_POST['monthly_income'] ?? '') === '' ? null : floatval($_POST['monthly_income']);
    $employment_type = sanitizeInput($_POST['employment_type'] ?? '');
    $income_source = sanitizeInput($_POST['income_source'] ?? '');
    $is_pwd = isset($_POST['is_pwd']) ? 1 : 0;
    $is_senior = isset($_POST['is_senior']) ? 1 : 0;
    $sss_number = sanitizeInput($_POST['sss_number'] ?? '');
    $philhealth_number = sanitizeInput($_POST['philhealth_number'] ?? '');
    $gsis_number = sanitizeInput($_POST['gsis_number'] ?? '');
    $tin_number = sanitizeInput($_POST['tin_number'] ?? '');
    $voter_id = sanitizeInput($_POST['voter_id'] ?? '');
    $precinct_number = sanitizeInput($_POST['precinct_number'] ?? '');
    $blood_type = sanitizeInput($_POST['blood_type'] ?? '');
    $allergies = sanitizeInput($_POST['allergies'] ?? '');
    $medical_conditions = sanitizeInput($_POST['medical_conditions'] ?? '');
    $disability = sanitizeInput($_POST['disability'] ?? '');
    $status = sanitizeInput($_POST['status'] ?? RESIDENT_ACTIVE);
    
    if (empty($first_name) || empty($last_name) || empty($birth_date) || empty($gender) || empty($address)) {
        sendResponse(false, 'First name, last name, birth date, gender, and address are required', null, 400);
        return;
    }
    $allowed_genders = ['male', 'female', 'other'];
    if (!in_array($gender, $allowed_genders)) {
        sendResponse(false, 'Invalid gender', null, 400);
        return;
    }
    
    try {
        $db = Database::getInstance();
        $sql = "INSERT INTO residents (
            first_name, middle_name, last_name, suffix, birth_date, gender, civil_status, occupation, citizenship,
            place_of_birth, address, length_of_stay_years, date_of_residency, contact_number, email,
            household_id, relationship_to_head, monthly_income, employment_type, income_source, is_pwd, is_senior,
            sss_number, philhealth_number, gsis_number, tin_number, voter_id, precinct_number,
            blood_type, allergies, medical_conditions, disability,
            status, approval_status, registration_type
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, 'approved','official')";
        $params = [
            $first_name, $middle_name ?: null, $last_name, $suffix ?: null, $birth_date, $gender,
            $civil_status ?: null, $occupation ?: null, $citizenship, $place_of_birth ?: null,
            $address, $length_of_stay_years, $date_of_residency, $contact_number ?: null, $email ?: null,
            $household_id ?: null, $relationship_to_head,
            $monthly_income, $employment_type ?: null, $income_source ?: null, $is_pwd, $is_senior,
            $sss_number ?: null, $philhealth_number ?: null, $gsis_number ?: null, $tin_number ?: null, $voter_id ?: null, $precinct_number ?: null,
            $blood_type ?: null, $allergies ?: null, $medical_conditions ?: null, $disability ?: null, $status
        ];
        $db->query($sql, $params);
        $residentId = $db->lastInsertId();
        $resident = $db->fetchOne("SELECT * FROM residents WHERE id = ?", [$residentId]);
        sendResponse(true, 'Resident created successfully', $resident);
    } catch (Exception $e) {
        error_log("Create resident error: " . $e->getMessage());
        sendResponse(false, 'Error creating resident', null, 500);
    }
}

/**
 * Update resident (includes extended fields)
 */
function updateResident() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method', null, 405);
        return;
    }
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        sendResponse(false, 'Resident ID is required', null, 400);
        return;
    }
    
    $fields = [
        'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
        'middle_name' => sanitizeInput($_POST['middle_name'] ?? ''),
        'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
        'suffix' => sanitizeInput($_POST['suffix'] ?? ''),
        'birth_date' => $_POST['birth_date'] ?? '',
        'gender' => sanitizeInput($_POST['gender'] ?? ''),
        'civil_status' => sanitizeInput($_POST['civil_status'] ?? ''),
        'occupation' => sanitizeInput($_POST['occupation'] ?? ''),
        'citizenship' => sanitizeInput($_POST['citizenship'] ?? ''),
        'place_of_birth' => sanitizeInput($_POST['place_of_birth'] ?? ''),
        'address' => sanitizeInput($_POST['address'] ?? ''),
        'length_of_stay_years' => trim($_POST['length_of_stay_years'] ?? '') === '' ? null : intval($_POST['length_of_stay_years']),
        'date_of_residency' => trim($_POST['date_of_residency'] ?? '') === '' ? null : $_POST['date_of_residency'],
        'contact_number' => sanitizeInput($_POST['contact_number'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'household_id' => intval($_POST['household_id'] ?? 0) ?: null,
        'relationship_to_head' => in_array(sanitizeInput($_POST['relationship_to_head'] ?? ''), ['self','spouse','child','other']) ? sanitizeInput($_POST['relationship_to_head']) : null,
        'monthly_income' => trim($_POST['monthly_income'] ?? '') === '' ? null : floatval($_POST['monthly_income']),
        'employment_type' => sanitizeInput($_POST['employment_type'] ?? ''),
        'income_source' => sanitizeInput($_POST['income_source'] ?? ''),
        'is_pwd' => isset($_POST['is_pwd']) ? 1 : 0,
        'is_senior' => isset($_POST['is_senior']) ? 1 : 0,
        'sss_number' => sanitizeInput($_POST['sss_number'] ?? ''),
        'philhealth_number' => sanitizeInput($_POST['philhealth_number'] ?? ''),
        'gsis_number' => sanitizeInput($_POST['gsis_number'] ?? ''),
        'tin_number' => sanitizeInput($_POST['tin_number'] ?? ''),
        'voter_id' => sanitizeInput($_POST['voter_id'] ?? ''),
        'precinct_number' => sanitizeInput($_POST['precinct_number'] ?? ''),
        'blood_type' => sanitizeInput($_POST['blood_type'] ?? ''),
        'allergies' => sanitizeInput($_POST['allergies'] ?? ''),
        'medical_conditions' => sanitizeInput($_POST['medical_conditions'] ?? ''),
        'disability' => sanitizeInput($_POST['disability'] ?? ''),
        'status' => sanitizeInput($_POST['status'] ?? ''),
    ];
    
    try {
        $db = Database::getInstance();
        $existing = $db->fetchOne("SELECT id FROM residents WHERE id = ?", [$id]);
        if (!$existing) {
            sendResponse(false, 'Resident not found', null, 404);
            return;
        }
        $sets = [];
        $params = [];
        $colMap = [
            'length_of_stay_years'=>'length_of_stay_years','date_of_residency'=>'date_of_residency','monthly_income'=>'monthly_income',
            'is_pwd'=>'is_pwd','is_senior'=>'is_senior','household_id'=>'household_id','relationship_to_head'=>'relationship_to_head'
        ];
        foreach ($fields as $k => $v) {
            $col = $colMap[$k] ?? $k;
            $sets[] = "`$col` = ?";
            $params[] = ($v === '' && !in_array($k, ['relationship_to_head','household_id','length_of_stay_years','date_of_residency','monthly_income'])) ? null : $v;
        }
        $params[] = $id;
        $sql = "UPDATE residents SET " . implode(', ', $sets) . " WHERE id = ?";
        $db->query($sql, $params);
        $resident = $db->fetchOne("SELECT * FROM residents WHERE id = ?", [$id]);
        sendResponse(true, 'Resident updated successfully', $resident);
    } catch (Exception $e) {
        error_log("Update resident error: " . $e->getMessage());
        sendResponse(false, 'Error updating resident', null, 500);
    }
}

/**
 * Delete resident (soft delete)
 */
function deleteResident() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method', null, 405);
        return;
    }
    
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        sendResponse(false, 'Resident ID is required', null, 400);
        return;
    }
    
    try {
        $db = Database::getInstance();
        
        // Soft delete by setting status to inactive
        $sql = "UPDATE residents SET status = 'inactive' WHERE id = ?";
        $db->query($sql, [$id]);
        
        sendResponse(true, 'Resident deleted successfully', null);
        
    } catch (Exception $e) {
        error_log("Delete resident error: " . $e->getMessage());
        sendResponse(false, 'Error deleting resident', null, 500);
    }
}

/**
 * Search residents
 */
function searchResidents() {
    $query = sanitizeInput($_GET['q'] ?? $_POST['q'] ?? '');
    
    if (empty($query)) {
        sendResponse(false, 'Search query is required', null, 400);
        return;
    }
    
    try {
        $db = Database::getInstance();
        
        $searchTerm = "%{$query}%";
        $sql = "SELECT r.*, h.address as household_address
                FROM residents r
                LEFT JOIN households h ON r.household_id = h.id
                WHERE r.first_name LIKE ? 
                   OR r.middle_name LIKE ? 
                   OR r.last_name LIKE ? 
                   OR r.address LIKE ?
                   OR CONCAT(r.first_name, ' ', r.last_name) LIKE ?
                ORDER BY r.id ASC
                LIMIT 50";
        
        $residents = $db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        
        sendResponse(true, 'Search completed', $residents);
        
    } catch (Exception $e) {
        error_log("Search residents error: " . $e->getMessage());
        sendResponse(false, 'Error searching residents', null, 500);
    }
}

/**
 * Send JSON response
 */
function sendResponse($success, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit();
}
