/**
 * Blotter Management: case handling and scheduling (hearing date)
 */
var BLOTTER_API = (typeof window !== 'undefined' && window.API_URL) ? window.API_URL : '';

document.addEventListener('DOMContentLoaded', function() {
    loadBlotters();
    var form = document.getElementById('blotterForm');
    if (form) form.addEventListener('submit', function(e) { e.preventDefault(); saveBlotter(); });
});

function loadBlotters() {
    fetch(BLOTTER_API + 'blotter.php?action=list')
        .then(r => r.json())
        .then(d => {
            if (d.success && d.data) {
                var tbody = document.getElementById('blotterTableBody');
                if (!tbody) return;
                if (d.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center">No records</td></tr>';
                    return;
                }
                tbody.innerHTML = d.data.map(function(b) {
                    return '<tr><td>' + b.id + '</td><td>' + (b.case_title || '') + '</td><td>' + (b.complainant_name || '') + '</td><td>' + (b.respondent_name || '-') + '</td><td>' + formatDate(b.incident_date) + '</td><td>' + formatDate(b.hearing_date) + '</td><td><span class="badge bg-' + getStatusColor(b.status) + '">' + (b.status || 'pending') + '</span></td><td><button class="btn btn-sm btn-primary" onclick="viewBlotter(' + b.id + ')">View</button> <button class="btn btn-sm btn-outline-primary" onclick="editBlotter(' + b.id + ')">Edit</button></td></tr>';
                }).join('');
            }
        })
        .catch(function() {
            var tbody = document.getElementById('blotterTableBody');
            if (tbody) tbody.innerHTML = '<tr><td colspan="8" class="text-center">Error loading</td></tr>';
        });
}

function viewBlotter(id) {
    fetch(BLOTTER_API + 'blotter.php?action=get&id=' + id)
        .then(r => r.json())
        .then(d => {
            if (d.success && d.data) {
                var b = d.data;
                var msg = 'Case: ' + (b.case_title || '') + '\nComplainant: ' + (b.complainant_name || '') + '\nRespondent: ' + (b.respondent_name || '') + '\nIncident: ' + formatDate(b.incident_date) + '\nHearing: ' + formatDate(b.hearing_date) + '\nStatus: ' + (b.status || '') + '\nDescription: ' + (b.description || '') + (b.hearing_notes ? '\nHearing notes: ' + b.hearing_notes : '');
                alert(msg);
            }
        });
}

function editBlotter(id) {
    fetch(BLOTTER_API + 'blotter.php?action=get&id=' + id)
        .then(r => r.json())
        .then(d => {
            if (d.success && d.data) {
                var b = d.data;
                document.getElementById('blotterId').value = b.id;
                var form = document.getElementById('blotterForm');
                if (form) {
                    setVal(form, 'case_title', b.case_title);
                    setVal(form, 'complainant_name', b.complainant_name);
                    setVal(form, 'respondent_name', b.respondent_name);
                    setVal(form, 'incident_date', b.incident_date);
                    setVal(form, 'incident_location', b.incident_location);
                    setVal(form, 'description', b.description);
                    setVal(form, 'status', b.status);
                    setVal(form, 'settlement_date', b.settlement_date);
                    setVal(form, 'hearing_date', b.hearing_date);
                    setVal(form, 'hearing_notes', b.hearing_notes);
                }
                var tit = document.getElementById('blotterModalTitle');
                if (tit) tit.textContent = 'Edit Blotter';
                var modal = window.bootstrap && document.getElementById('blotterModal');
                if (modal) new window.bootstrap.Modal(modal).show();
            }
        });
}

function setVal(form, name, val) {
    var el = form && form.elements[name];
    if (el) el.value = (val != null && val !== undefined) ? val : '';
}

function saveBlotter() {
    var form = document.getElementById('blotterForm');
    if (!form) return;
    var fd = new FormData(form);
    fd.append('action', document.getElementById('blotterId').value ? 'update' : 'create');
    fetch(BLOTTER_API + 'blotter.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                if (window.bootstrap && document.getElementById('blotterModal')) {
                    var m = window.bootstrap.Modal.getInstance(document.getElementById('blotterModal'));
                    if (m) m.hide();
                }
                resetBlotterForm();
                loadBlotters();
            } else {
                alert(d.message || 'Error');
            }
        })
        .catch(function() { alert('Request failed'); });
}

function resetBlotterForm() {
    var form = document.getElementById('blotterForm');
    if (form) form.reset();
    var idEl = document.getElementById('blotterId');
    if (idEl) idEl.value = '';
    var tit = document.getElementById('blotterModalTitle');
    if (tit) tit.textContent = 'Add Blotter';
}

function getStatusColor(s) {
    var c = { 'pending': 'warning', 'under_investigation': 'info', 'resolved': 'success', 'settled': 'primary', 'referred': 'secondary' };
    return c[s] || 'secondary';
}
function formatDate(d) { return d ? new Date(d).toLocaleDateString() : '-'; }
