/**
 * Complaints handling: full CRUD and status updates
 */
var COMPLAINTS_API = (typeof window !== 'undefined' && window.API_URL) ? window.API_URL : '';

document.addEventListener('DOMContentLoaded', function() {
    loadComplaints();
    var form = document.getElementById('complaintForm');
    if (form) form.addEventListener('submit', function(e) { e.preventDefault(); saveComplaint(); });
});

function loadComplaints() {
    fetch(COMPLAINTS_API + 'complaints.php?action=list')
        .then(r => r.json())
        .then(d => {
            if (d.success && d.data) {
                var tbody = document.getElementById('complaintsTableBody');
                if (!tbody) return;
                if (d.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center">No complaints</td></tr>';
                    return;
                }
                tbody.innerHTML = d.data.map(function(c) {
                    return '<tr><td>' + c.id + '</td><td>' + (c.complaint_title || '') + '</td><td>' + (c.complainant_name || '') + '</td><td>' + (c.respondent_name || '-') + '</td><td>' + formatDate(c.filing_date) + '</td><td><span class="badge bg-' + getStatusColor(c.status) + '">' + (c.status || 'pending') + '</span></td><td><button class="btn btn-sm btn-primary" onclick="viewComplaint(' + c.id + ')">View</button> <button class="btn btn-sm btn-outline-primary" onclick="editComplaint(' + c.id + ')">Edit</button></td></tr>';
                }).join('');
            }
        })
        .catch(function() {
            var tbody = document.getElementById('complaintsTableBody');
            if (tbody) tbody.innerHTML = '<tr><td colspan="7" class="text-center">Error loading</td></tr>';
        });
}

function viewComplaint(id) {
    fetch(COMPLAINTS_API + 'complaints.php?action=get&id=' + id)
        .then(r => r.json())
        .then(d => {
            if (d.success && d.data) {
                var c = d.data;
                var msg = 'Title: ' + (c.complaint_title || '') + '\nComplainant: ' + (c.complainant_name || '') + '\nRespondent: ' + (c.respondent_name || '') + '\nType: ' + (c.complaint_type || '') + '\nFiling: ' + formatDate(c.filing_date) + '\nStatus: ' + (c.status || '') + '\nNarrative: ' + (c.narrative || '') + (c.resolution_date ? '\nResolution: ' + formatDate(c.resolution_date) : '');
                alert(msg);
            }
        });
}

function editComplaint(id) {
    fetch(COMPLAINTS_API + 'complaints.php?action=get&id=' + id)
        .then(r => r.json())
        .then(d => {
            if (d.success && d.data) {
                var c = d.data;
                document.getElementById('complaintId').value = c.id;
                var form = document.getElementById('complaintForm');
                if (form) {
                    setVal(form, 'complaint_title', c.complaint_title);
                    setVal(form, 'complaint_type', c.complaint_type);
                    setVal(form, 'complainant_name', c.complainant_name);
                    setVal(form, 'respondent_name', c.respondent_name);
                    setVal(form, 'filing_date', c.filing_date);
                    setVal(form, 'narrative', c.narrative);
                    setVal(form, 'status', c.status);
                    setVal(form, 'resolution_date', c.resolution_date);
                }
                var tit = document.getElementById('complaintModalTitle');
                if (tit) tit.textContent = 'Edit Complaint';
                var modal = window.bootstrap && document.getElementById('complaintModal');
                if (modal) new window.bootstrap.Modal(modal).show();
            }
        });
}

function setVal(form, name, val) {
    var el = form && form.elements[name];
    if (el) el.value = (val != null && val !== undefined) ? val : '';
}

function saveComplaint() {
    var form = document.getElementById('complaintForm');
    if (!form) return;
    var fd = new FormData(form);
    fd.append('action', document.getElementById('complaintId').value ? 'update' : 'create');
    fetch(COMPLAINTS_API + 'complaints.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                if (window.bootstrap && document.getElementById('complaintModal')) {
                    var m = window.bootstrap.Modal.getInstance(document.getElementById('complaintModal'));
                    if (m) m.hide();
                }
                resetComplaintForm();
                loadComplaints();
            } else {
                alert(d.message || 'Error');
            }
        })
        .catch(function() { alert('Request failed'); });
}

function resetComplaintForm() {
    var form = document.getElementById('complaintForm');
    if (form) form.reset();
    var idEl = document.getElementById('complaintId');
    if (idEl) idEl.value = '';
    var tit = document.getElementById('complaintModalTitle');
    if (tit) tit.textContent = 'Add Complaint';
}

function getStatusColor(s) {
    var c = { 'pending': 'warning', 'under_review': 'info', 'resolved': 'success', 'dismissed': 'danger' };
    return c[s] || 'secondary';
}
function formatDate(d) { return d ? new Date(d).toLocaleDateString() : '-'; }
