/**
 * E-Barangay Information Management System
 * Residents Management JavaScript
 */

let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadResidents();
    if (window.CAN_APPROVE_REGISTRATION && document.getElementById('pendingTableBody')) {
        loadPendingRegistrations();
    }
    
    document.getElementById('residentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveResident();
    });
    
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchResidents();
        }
    });
});

function loadPendingRegistrations() {
    const apiUrl = window.API_URL;
    if (!apiUrl) return;
    const tbody = document.getElementById('pendingTableBody');
    if (!tbody) return;
    fetch(apiUrl + 'resident.php?action=list_pending')
        .then(r => r.json())
        .then(d => {
            if (d.success && d.data && d.data.length) {
                tbody.innerHTML = d.data.map(r => {
                    const name = [r.first_name, r.middle_name, r.last_name].filter(Boolean).join(' ');
                    return `<tr>
                        <td>${r.id}</td>
                        <td>${escapeHtml(name)}</td>
                        <td>${escapeHtml((r.address || '').substring(0,40))}${(r.address || '').length > 40 ? '…' : ''}</td>
                        <td>${escapeHtml(r.contact_number || r.email || '-')}</td>
                        <td>
                            <button class="btn btn-sm btn-success" onclick="approveResidentReq(${r.id})"><i class="bi bi-check-lg"></i> Approve</button>
                            <button class="btn btn-sm btn-danger" onclick="rejectResidentReq(${r.id})"><i class="bi bi-x-lg"></i> Reject</button>
                        </td>
                    </tr>`;
                }).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No pending registrations</td></tr>';
            }
        })
        .catch(() => { if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="text-center">Error loading</td></tr>'; });
}

function approveResidentReq(id) {
    if (!confirm('Approve this registration?')) return;
    const fd = new FormData();
    fd.append('action', 'approve');
    fd.append('id', id);
    fetch((window.API_URL || '') + 'resident.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (d.success) { showAlert('success', d.message); loadPendingRegistrations(); loadResidents(currentPage); }
            else showAlert('error', d.message || 'Failed');
        })
        .catch(() => showAlert('error', 'Request failed'));
}

function rejectResidentReq(id) {
    if (!confirm('Reject this registration?')) return;
    const fd = new FormData();
    fd.append('action', 'reject');
    fd.append('id', id);
    fetch((window.API_URL || '') + 'resident.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if (d.success) { showAlert('success', d.message); loadPendingRegistrations(); }
            else showAlert('error', d.message || 'Failed');
        })
        .catch(() => showAlert('error', 'Request failed'));
}

/**
 * Load all residents
 */
function loadResidents(page = 1) {
    currentPage = page;
    const apiUrl = window.API_URL;
    if (!apiUrl) {
        console.error('API_URL is not defined. Please check your configuration.');
        showAlert('error', 'Configuration error. Please refresh the page.');
        return;
    }
    const itemsPerPage = window.ITEMS_PER_PAGE || 20;
    fetch(`${apiUrl}resident.php?action=list&page=${page}&limit=${itemsPerPage}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResidents(data.data.residents);
                displayPagination(data.data);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error loading residents');
        });
}

/**
 * Display residents in table
 */
function displayResidents(residents) {
    const tbody = document.getElementById('residentsTableBody');
    
    if (residents.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No residents found</td></tr>';
        return;
    }
    
    tbody.innerHTML = residents.map(resident => {
        const fullName = `${escapeHtml(resident.first_name)} ${escapeHtml(resident.middle_name || '')} ${escapeHtml(resident.last_name)} ${escapeHtml(resident.suffix || '')}`.trim();
        const age = calculateAge(resident.birth_date);
        
        return `
            <tr>
                <td>${resident.id}</td>
                <td>${fullName}</td>
                <td>${formatDate(resident.birth_date)} (${age} yrs)</td>
                <td>${formatGender(resident.gender)}</td>
                <td>${escapeHtml(resident.address)}</td>
                <td>${escapeHtml(resident.contact_number || '-')}</td>
                <td><span class="badge ${getStatusClass(resident.status)}">${formatStatus(resident.status)}</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editResident(${resident.id})" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-info" onclick="viewResident(${resident.id})" title="View">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteResident(${resident.id})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * Display pagination
 */
function displayPagination(data) {
    const pagination = document.getElementById('pagination');
    const totalPages = data.total_pages;
    
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let html = '';
    
    // Previous button
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadResidents(${currentPage - 1}); return false;">Previous</a>
    </li>`;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadResidents(${i}); return false;">${i}</a>
            </li>`;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    // Next button
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadResidents(${currentPage + 1}); return false;">Next</a>
    </li>`;
    
    pagination.innerHTML = html;
}

/**
 * Search residents
 */
function searchResidents() {
    const query = document.getElementById('searchInput').value.trim();
    
    if (!query) {
        loadResidents();
        return;
    }
    
    const apiUrl = window.API_URL;
    if (!apiUrl) {
        console.error('API_URL is not defined. Please check your configuration.');
        showAlert('error', 'Configuration error. Please refresh the page.');
        return;
    }
    fetch(`${apiUrl}resident.php?action=search&q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResidents(data.data);
                document.getElementById('pagination').innerHTML = '';
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error searching residents');
        });
}

/**
 * Edit resident
 */
function editResident(id) {
    const apiUrl = window.API_URL;
    if (!apiUrl) {
        console.error('API_URL is not defined. Please check your configuration.');
        showAlert('error', 'Configuration error. Please refresh the page.');
        return;
    }
    fetch(`${apiUrl}resident.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const resident = data.data;
                document.getElementById('residentId').value = resident.id;
                document.getElementById('first_name').value = resident.first_name;
                document.getElementById('middle_name').value = resident.middle_name || '';
                document.getElementById('last_name').value = resident.last_name;
                document.getElementById('suffix').value = resident.suffix || '';
                document.getElementById('birth_date').value = resident.birth_date;
                document.getElementById('gender').value = resident.gender;
                document.getElementById('civil_status').value = resident.civil_status || '';
                document.getElementById('occupation').value = resident.occupation || '';
                document.getElementById('citizenship').value = resident.citizenship || 'Filipino';
                document.getElementById('address').value = resident.address;
                document.getElementById('contact_number').value = resident.contact_number || '';
                document.getElementById('household_id').value = resident.household_id || '';
                document.getElementById('relationship_to_head').value = resident.relationship_to_head || '';
                document.getElementById('status').value = resident.status;
                setIfExists('place_of_birth', resident.place_of_birth);
                setIfExists('length_of_stay_years', resident.length_of_stay_years);
                setIfExists('date_of_residency', resident.date_of_residency);
                setIfExists('email', resident.email);
                setIfExists('monthly_income', resident.monthly_income);
                setIfExists('employment_type', resident.employment_type);
                setIfExists('is_pwd', resident.is_pwd, 'checkbox');
                setIfExists('is_senior', resident.is_senior, 'checkbox');
                setIfExists('sss_number', resident.sss_number);
                setIfExists('philhealth_number', resident.philhealth_number);
                setIfExists('blood_type', resident.blood_type);
                setIfExists('allergies', resident.allergies);
                setIfExists('medical_conditions', resident.medical_conditions);
                setIfExists('disability', resident.disability);
                document.getElementById('residentModalTitle').textContent = 'Edit Resident';
                
                const modal = new bootstrap.Modal(document.getElementById('residentModal'));
                modal.show();
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error loading resident');
        });
}

/**
 * View resident details
 */
function viewResident(id) {
    // Redirect to profile page or show in modal
    const baseUrl = window.BASE_URL;
    if (!baseUrl) {
        console.error('BASE_URL is not defined. Please check your configuration.');
        showAlert('error', 'Configuration error. Please refresh the page.');
        return;
    }
    window.location.href = `${baseUrl}profile.php?resident_id=${id}`;
}

/**
 * Save resident (create or update)
 */
function saveResident() {
    const form = document.getElementById('residentForm');
    const formData = new FormData(form);
    const residentId = document.getElementById('residentId').value;
    
    formData.append('action', residentId ? 'update' : 'create');
    if (residentId) {
        formData.append('id', residentId);
    }
    
    const apiUrl = window.API_URL;
    if (!apiUrl) {
        console.error('API_URL is not defined. Please check your configuration.');
        showAlert('error', 'Configuration error. Please refresh the page.');
        return;
    }
    fetch(`${apiUrl}resident.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('residentModal')).hide();
            resetForm();
            loadResidents(currentPage);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error saving resident');
    });
}

/**
 * Delete resident
 */
function deleteResident(id) {
    if (confirm('Are you sure you want to delete this resident?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        const apiUrl = window.API_URL;
        if (!apiUrl) {
            console.error('API_URL is not defined. Please check your configuration.');
            showAlert('error', 'Configuration error. Please refresh the page.');
            return;
        }
        fetch(`${apiUrl}resident.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                loadResidents(currentPage);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error deleting resident');
        });
    }
}

/**
 * Reset form
 */
function setIfExists(id, val, type) {
    const el = document.getElementById(id);
    if (!el) return;
    if (type === 'checkbox') el.checked = !!val && val != '0';
    else el.value = val != null && val !== '' ? val : '';
}

function resetForm() {
    document.getElementById('residentForm').reset();
    document.getElementById('residentId').value = '';
    const cit = document.getElementById('citizenship'); if (cit) cit.value = 'Filipino';
    const st = document.getElementById('status'); if (st) st.value = 'active';
    document.getElementById('residentModalTitle').textContent = 'Add New Resident';
}

/**
 * Helper functions
 */
function calculateAge(birthDate) {
    if (!birthDate) return '-';
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

function formatGender(gender) {
    return gender ? gender.charAt(0).toUpperCase() + gender.slice(1) : '-';
}

function formatStatus(status) {
    return status ? status.charAt(0).toUpperCase() + status.slice(1) : '-';
}

function getStatusClass(status) {
    const classes = {
        'active': 'bg-success',
        'inactive': 'bg-secondary',
        'deceased': 'bg-dark',
        'transferred': 'bg-info'
    };
    return classes[status] || 'bg-secondary';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
