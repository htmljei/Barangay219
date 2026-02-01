<?php
define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/../includes/auth-check.php';

requireLogin();
requireAnyRole([ROLE_BARANGAY_CAPTAIN, ROLE_SECRETARY, ROLE_KAGAWA]);

$page_title = 'Blotters';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-journal-text"></i> Blotter Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#blotterModal" onclick="resetBlotterForm()"><i class="bi bi-plus-circle"></i> Add Blotter</button>
        </div>
        <div class="data-table mt-4">
            <table class="table table-hover">
                <thead>
                    <tr><th>ID</th><th>Case Title</th><th>Complainant</th><th>Respondent</th><th>Incident Date</th><th>Hearing Date</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody id="blotterTableBody"><tr><td colspan="8" class="text-center">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="blotterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="blotterModalTitle">Add Blotter</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="blotterForm">
                <div class="modal-body">
                    <input type="hidden" id="blotterId" name="id">
                    <div class="row mb-2">
                        <div class="col-md-6"><label class="form-label">Case Title <span class="text-danger">*</span></label><input type="text" class="form-control" name="case_title" required></div>
                        <div class="col-md-3"><label class="form-label">Incident Date</label><input type="date" class="form-control" name="incident_date"></div>
                        <div class="col-md-3"><label class="form-label">Hearing Date</label><input type="date" class="form-control" name="hearing_date"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6"><label class="form-label">Complainant <span class="text-danger">*</span></label><input type="text" class="form-control" name="complainant_name" required></div>
                        <div class="col-md-6"><label class="form-label">Respondent</label><input type="text" class="form-control" name="respondent_name"></div>
                    </div>
                    <div class="mb-2"><label class="form-label">Incident Location</label><input type="text" class="form-control" name="incident_location"></div>
                    <div class="mb-2"><label class="form-label">Description <span class="text-danger">*</span></label><textarea class="form-control" name="description" rows="3" required></textarea></div>
                    <div class="mb-2"><label class="form-label">Hearing Notes</label><textarea class="form-control" name="hearing_notes" rows="2"></textarea></div>
                    <div class="row mb-2">
                        <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status"><option value="pending">Pending</option><option value="under_investigation">Under Investigation</option><option value="resolved">Resolved</option><option value="settled">Settled</option><option value="referred">Referred</option></select></div>
                        <div class="col-md-4"><label class="form-label">Settlement Date</label><input type="date" class="form-control" name="settlement_date"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script>window.API_URL = window.API_URL || '<?php echo addslashes(API_URL); ?>';</script>
<script src="<?php echo ASSETS_URL; ?>css/js/blotter.js"></script>
