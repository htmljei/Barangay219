<?php
/**
 * E-Barangay Information Management System
 * Public Resident Registration API (no login required)
 * Submissions created with approval_status=pending; officials must approve.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-check.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action !== 'register') {
    sendJson(false, 'Invalid action', null, 400);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(false, 'Invalid request method', null, 405);
}

// Same validation and insert as resident create, but approval_status=pending, registration_type=resident
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

if (empty($first_name) || empty($last_name) || empty($birth_date) || empty($gender) || empty($address)) {
    sendJson(false, 'First name, last name, birth date, gender, and address are required', null, 400);
}

$allowed_genders = ['male', 'female', 'other'];
if (!in_array($gender, $allowed_genders)) {
    sendJson(false, 'Invalid gender', null, 400);
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
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, 'active','pending','resident')";

    $params = [
        $first_name, $middle_name ?: null, $last_name, $suffix ?: null, $birth_date, $gender,
        $civil_status ?: null, $occupation ?: null, $citizenship, $place_of_birth ?: null,
        $address, $length_of_stay_years, $date_of_residency, $contact_number ?: null, $email ?: null,
        $household_id ?: null, in_array($relationship_to_head, ['self','spouse','child','other']) ? $relationship_to_head : null,
        $monthly_income, $employment_type ?: null, $income_source ?: null, $is_pwd, $is_senior,
        $sss_number ?: null, $philhealth_number ?: null, $gsis_number ?: null, $tin_number ?: null, $voter_id ?: null, $precinct_number ?: null,
        $blood_type ?: null, $allergies ?: null, $medical_conditions ?: null, $disability ?: null
    ];

    $db->query($sql, $params);
    $id = $db->lastInsertId();
    sendJson(true, 'Registration submitted. An official will review and approve your record.', ['id' => (int)$id]);
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Unknown column') !== false) {
        sendJson(false, 'System update in progress. Please run database migration: database/migrations/001_thesis_requirements.sql', null, 500);
    } else {
        error_log('Register API error: ' . $e->getMessage());
        sendJson(false, 'Registration failed. Please try again.', null, 500);
    }
}

function sendJson($ok, $msg, $data, $code = 200) {
    http_response_code($code);
    echo json_encode(['success' => $ok, 'message' => $msg, 'data' => $data]);
    exit;
}
