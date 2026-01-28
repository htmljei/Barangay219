/**
 * Public Resident Registration form submit
 */
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    var fd = new FormData(form);
    fetch((window.API_URL || '').replace(/\/$/, '') + '/register.php', {
        method: 'POST',
        body: fd
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            showAlert('success', d.message || 'Registration submitted. An official will review your record.');
            form.reset();
            setTimeout(function() {
                window.location.href = (window.BASE_URL || '') + 'index.php';
            }, 2000);
        } else {
            showAlert('error', d.message || 'Registration failed.');
            btn.disabled = false;
        }
    })
    .catch(function() {
        showAlert('error', 'Network error. Please try again.');
        btn.disabled = false;
    });
});

function showAlert(type, msg) {
    var c = document.getElementById('alertContainer');
    c.innerHTML = '<div class="alert alert-' + (type === 'error' ? 'danger' : 'success') + ' alert-dismissible">' + msg + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}
