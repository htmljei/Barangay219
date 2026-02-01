<?php
define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/../includes/auth-check.php';

requireLogin();
requireAnyRole([ROLE_BARANGAY_CAPTAIN, ROLE_SECRETARY, ROLE_KAGAWA]);

$page_title = 'Complaints';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-exclamation-triangle"></i> Complaints Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#complaintModal" onclick="resetComplaintForm()"><i class="bi bi-plus-circle"></i> Add Complaint</button>
        </div>
        <div class="data-table mt-4">
            <table class="table table-hover">
                <thead>
                    <tr><th>ID</th><th>Title</th><th>Complainant</th><th>Respondent</th><th>Filing Date</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody id="complaintsTableBody"><tr><td colspan="7" class="text-center">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="complaintModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="complaintModalTitle">Add Complaint</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="complaintForm">
                <div class="modal-body">
                    <input type="hidden" id="complaintId" name="id">
                    <div class="row mb-2">
                        <div class="col-md-6"><label class="form-label">Complaint Title <span class="text-danger">*</span></label><input type="text" class="form-control" name="complaint_title" required></div>
                        <div class="col-md-6"><label class="form-label">Complaint Type</label><input type="text" class="form-control" name="complaint_type" placeholder="e.g. noise, boundary"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6"><label class="form-label">Complainant <span class="text-danger">*</span></label><input type="text" class="form-control" name="complainant_name" required></div>
                        <div class="col-md-6"><label class="form-label">Respondent</label><input type="text" class="form-control" name="respondent_name"></div>
                    </div>
                    <div class="mb-2"><label class="form-label">Filing Date</label><input type="date" class="form-control" name="filing_date"></div>
                    <div class="mb-2"><label class="form-label">Narrative <span class="text-danger">*</span></label><textarea class="form-control" name="narrative" rows="4" required></textarea></div>
                    <div class="row mb-2">
                        <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status"><option value="pending">Pending</option><option value="under_review">Under Review</option><option value="resolved">Resolved</option><option value="dismissed">Dismissed</option></select></div>
                        <div class="col-md-4"><label class="form-label">Resolution Date</label><input type="date" class="form-control" name="resolution_date"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script>window.API_URL = window.API_URL || '<?php echo addslashes(API_URL); ?>';</script>
<script src="<?php echo ASSETS_URL; ?>css/js/complaints.js"></script>
