<?php
/**
 * E-Barangay Information Management System
 * Public Resident Registration (no login)
 * Submissions require official approval.
 */
define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/../config/constants.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Registration - <?php echo APP_NAME; ?></title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="text-center mb-4">
            <i class="bi bi-building" style="font-size: 2.5rem; color: #0d6efd;"></i>
            <h3><?php echo APP_NAME; ?></h3>
            <p class="text-muted"><?php echo BARANGAY_NAME; ?></p>
            <h4>Resident Registration</h4>
            <p class="text-muted small">Your submission will be reviewed by barangay officials.</p>
        </div>

        <div id="alertContainer"></div>

        <form id="registerForm">
            <input type="hidden" name="action" value="register">
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-personal">Personal</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-address">Address & Residency</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-household">Household & Contact</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-socio">Socio-economic</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-id">ID & Gov't</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-health">Health</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-personal">
                    <div class="row">
                        <div class="col-md-3 mb-2"><label class="form-label">First Name <span class="text-danger">*</span></label><input type="text" class="form-control" name="first_name" required></div>
                        <div class="col-md-3 mb-2"><label class="form-label">Middle Name</label><input type="text" class="form-control" name="middle_name"></div>
                        <div class="col-md-3 mb-2"><label class="form-label">Last Name <span class="text-danger">*</span></label><input type="text" class="form-control" name="last_name" required></div>
                        <div class="col-md-1 mb-2"><label class="form-label">Suffix</label><input type="text" class="form-control" name="suffix" placeholder="Jr."></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-2"><label class="form-label">Birth Date <span class="text-danger">*</span></label><input type="date" class="form-control" name="birth_date" required></div>
                        <div class="col-md-2 mb-2"><label class="form-label">Gender <span class="text-danger">*</span></label><select class="form-select" name="gender" required><option value="">--</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                        <div class="col-md-2 mb-2"><label class="form-label">Civil Status</label><select class="form-select" name="civil_status"><option value="">--</option><option value="single">Single</option><option value="married">Married</option><option value="widowed">Widowed</option><option value="divorced">Divorced</option><option value="separated">Separated</option></select></div>
                        <div class="col-md-2 mb-2"><label class="form-label">Citizenship</label><input type="text" class="form-control" name="citizenship" value="Filipino"></div>
                        <div class="col-md-2 mb-2"><label class="form-label">Place of Birth</label><input type="text" class="form-control" name="place_of_birth"></div>
                        <div class="col-md-2 mb-2"><label class="form-label">Occupation</label><input type="text" class="form-control" name="occupation"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-address">
                    <div class="mb-2"><label class="form-label">Address <span class="text-danger">*</span></label><textarea class="form-control" name="address" rows="2" required></textarea></div>
                    <div class="row">
                        <div class="col-md-4 mb-2"><label class="form-label">Length of Stay (years)</label><input type="number" class="form-control" name="length_of_stay_years" min="0"></div>
                        <div class="col-md-4 mb-2"><label class="form-label">Date of Residency</label><input type="date" class="form-control" name="date_of_residency"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-household">
                    <div class="row">
                        <div class="col-md-4 mb-2"><label class="form-label">Household ID (if applicable)</label><input type="number" class="form-control" name="household_id" min="0"></div>
                        <div class="col-md-4 mb-2"><label class="form-label">Relationship to Head</label><select class="form-select" name="relationship_to_head"><option value="">--</option><option value="self">Self</option><option value="spouse">Spouse</option><option value="child">Child</option><option value="other">Other</option></select></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2"><label class="form-label">Contact Number</label><input type="text" class="form-control" name="contact_number"></div>
                        <div class="col-md-4 mb-2"><label class="form-label">Email</label><input type="email" class="form-control" name="email"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-socio">
                    <div class="row">
                        <div class="col-md-3 mb-2"><label class="form-label">Monthly Income (PHP)</label><input type="number" class="form-control" name="monthly_income" step="0.01" min="0"></div>
                        <div class="col-md-3 mb-2"><label class="form-label">Employment Type</label><input type="text" class="form-control" name="employment_type"></div>
                        <div class="col-md-3 mb-2"><label class="form-label">Income Source</label><input type="text" class="form-control" name="income_source"></div>
                        <div class="col-md-2 mb-2"><label class="form-label">PWD</label><br><input type="checkbox" name="is_pwd" value="1"></div>
                        <div class="col-md-2 mb-2"><label class="form-label">Senior</label><br><input type="checkbox" name="is_senior" value="1"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-id">
                    <div class="row">
                        <div class="col-md-4 mb-2"><label class="form-label">SSS No.</label><input type="text" class="form-control" name="sss_number"></div>
                        <div class="col-md-4 mb-2"><label class="form-label">PhilHealth No.</label><input type="text" class="form-control" name="philhealth_number"></div>
                        <div class="col-md-4 mb-2"><label class="form-label">GSIS No.</label><input type="text" class="form-control" name="gsis_number"></div>
                        <div class="col-md-4 mb-2"><label class="form-label">TIN</label><input type="text" class="form-control" name="tin_number"></div>
                        <div class="col-md-4 mb-2"><label class="form-label">Voter's ID</label><input type="text" class="form-control" name="voter_id"></div>
                        <div class="col-md-4 mb-2"><label class="form-label">Precinct No.</label><input type="text" class="form-control" name="precinct_number"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-health">
                    <div class="row">
                        <div class="col-md-3 mb-2"><label class="form-label">Blood Type</label><select class="form-select" name="blood_type"><option value="">--</option><option value="A+">A+</option><option value="A-">A-</option><option value="B+">B+</option><option value="B-">B-</option><option value="AB+">AB+</option><option value="AB-">AB-</option><option value="O+">O+</option><option value="O-">O-</option></select></div>
                        <div class="col-md-3 mb-2"><label class="form-label">Allergies</label><input type="text" class="form-control" name="allergies"></div>
                        <div class="col-md-3 mb-2"><label class="form-label">Medical Conditions</label><input type="text" class="form-control" name="medical_conditions"></div>
                        <div class="col-md-3 mb-2"><label class="form-label">Disability</label><input type="text" class="form-control" name="disability"></div>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Submit Registration</button>
                <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary">Back to Login</a>
            </div>
        </form>
    </div>

    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
    <script>
        window.API_URL = '<?php echo addslashes(API_URL); ?>';
        window.BASE_URL = '<?php echo addslashes(BASE_URL); ?>';
    </script>
    <script src="<?php echo ASSETS_URL; ?>css/js/register.js"></script>
</body>
</html>
