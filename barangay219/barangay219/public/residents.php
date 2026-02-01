<?php
/**
 * E-Barangay Information Management System
 * Residents Management Page
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/../includes/auth-check.php';

requireLogin();
requireAnyRole([ROLE_BARANGAY_CAPTAIN, ROLE_SECRETARY]);

$page_title = 'Residents Management';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people"></i> Residents Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#residentModal" onclick="resetForm()">
                <i class="bi bi-plus-circle"></i> Add New Resident
            </button>
        </div>

        <?php if (canApproveRegistration()): ?>
        <div class="card mb-4" id="pendingRegistrationsCard">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-hourglass-split"></i> Pending Registrations (resident self-registered; needs approval)</span>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadPendingRegistrations()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
            </div>
            <div class="card-body">
                <div id="pendingRegistrationsList">
                    <table class="table table-sm">
                        <thead><tr><th>ID</th><th>Name</th><th>Address</th><th>Contact</th><th>Actions</th></tr></thead>
                        <tbody id="pendingTableBody"><tr><td colspan="5" class="text-center">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="search-bar">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name or address...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="searchResidents()">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100" onclick="loadResidents()">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Residents Table -->
        <div class="data-table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Birth Date</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="residentsTableBody">
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center" id="pagination">
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Resident Modal -->
<div class="modal fade" id="residentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="residentModalTitle">Add New Resident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="residentForm">
                <div class="modal-body">
                    <input type="hidden" id="residentId" name="id">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="col-md-1 mb-3">
                            <label for="suffix" class="form-label">Suffix</label>
                            <input type="text" class="form-control" id="suffix" name="suffix" placeholder="Jr.">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="birth_date" class="form-label">Birth Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="civil_status" class="form-label">Civil Status</label>
                            <select class="form-select" id="civil_status" name="civil_status">
                                <option value="">Select Status</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="divorced">Divorced</option>
                                <option value="separated">Separated</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="deceased">Deceased</option>
                                <option value="transferred">Transferred</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" class="form-control" id="occupation" name="occupation">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="citizenship" class="form-label">Citizenship</label>
                            <input type="text" class="form-control" id="citizenship" name="citizenship" value="Filipino">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="household_id" class="form-label">Household ID</label>
                            <input type="number" class="form-control" id="household_id" name="household_id" min="0">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="relationship_to_head" class="form-label">Relationship to head</label>
                            <select class="form-select" id="relationship_to_head" name="relationship_to_head">
                                <option value="">—</option><option value="self">Self</option><option value="spouse">Spouse</option><option value="child">Child</option><option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2"><button type="button" class="btn btn-link p-0" data-bs-toggle="collapse" data-bs-target="#extendedFields">+ Extended fields (residency, socio-economic, ID &amp; health)</button></div>
                    <div class="collapse" id="extendedFields">
                        <div class="row">
                            <div class="col-md-3 mb-2"><label class="form-label">Place of birth</label><input type="text" class="form-control" id="place_of_birth" name="place_of_birth"></div>
                            <div class="col-md-2 mb-2"><label class="form-label">Length of stay (yrs)</label><input type="number" class="form-control" id="length_of_stay_years" name="length_of_stay_years" min="0"></div>
                            <div class="col-md-2 mb-2"><label class="form-label">Date of residency</label><input type="date" class="form-control" id="date_of_residency" name="date_of_residency"></div>
                            <div class="col-md-3 mb-2"><label class="form-label">Email</label><input type="email" class="form-control" id="email" name="email"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mb-2"><label class="form-label">Monthly income</label><input type="number" class="form-control" id="monthly_income" name="monthly_income" step="0.01" min="0"></div>
                            <div class="col-md-2 mb-2"><label class="form-label">Employment type</label><input type="text" class="form-control" id="employment_type" name="employment_type"></div>
                            <div class="col-md-2 mb-2"><label class="form-label">PWD</label><br><input type="checkbox" id="is_pwd" name="is_pwd" value="1"></div>
                            <div class="col-md-2 mb-2"><label class="form-label">Senior</label><br><input type="checkbox" id="is_senior" name="is_senior" value="1"></div>
                            <div class="col-md-2 mb-2"><label class="form-label">SSS No.</label><input type="text" class="form-control" id="sss_number" name="sss_number"></div>
                            <div class="col-md-2 mb-2"><label class="form-label">PhilHealth No.</label><input type="text" class="form-control" id="philhealth_number" name="philhealth_number"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mb-2"><label class="form-label">Blood type</label><select class="form-select" id="blood_type" name="blood_type"><option value="">—</option><option value="A+">A+</option><option value="A-">A-</option><option value="B+">B+</option><option value="B-">B-</option><option value="AB+">AB+</option><option value="AB-">AB-</option><option value="O+">O+</option><option value="O-">O-</option></select></div>
                            <div class="col-md-3 mb-2"><label class="form-label">Allergies</label><input type="text" class="form-control" id="allergies" name="allergies"></div>
                            <div class="col-md-3 mb-2"><label class="form-label">Medical conditions</label><input type="text" class="form-control" id="medical_conditions" name="medical_conditions"></div>
                            <div class="col-md-2 mb-2"><label class="form-label">Disability</label><input type="text" class="form-control" id="disability" name="disability"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Resident</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Define API URL and constants for JavaScript -->
<script>
    if (typeof window.API_URL === 'undefined') {
        window.API_URL = '<?php echo API_URL; ?>';
    }
    window.ITEMS_PER_PAGE = <?php echo ITEMS_PER_PAGE; ?>;
    window.BASE_URL = '<?php echo BASE_URL; ?>';
    window.CAN_APPROVE_REGISTRATION = <?php echo canApproveRegistration() ? 'true' : 'false'; ?>;
</script>
<script src="<?php echo ASSETS_URL; ?>css/js/residents.js?v=<?php echo time(); ?>"></script>
