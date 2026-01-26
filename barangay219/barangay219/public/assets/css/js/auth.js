// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) {
        console.error('Login form not found');
        return;
    }
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'login');
        
        // Get API URL from global variable (defined in HTML)
        const apiUrl = window.API_URL;
        if (!apiUrl) {
            console.error('API_URL is not defined. Please check your configuration.');
            showAlert('danger', 'Configuration error. Please refresh the page.');
            return;
        }
        
        // Debug logging
        console.log('API URL:', apiUrl);
        console.log('Full URL:', apiUrl + 'auth.php');
        
        if (!apiUrl || apiUrl.includes('<?php') || apiUrl.includes('%3C')) {
            console.error('Invalid API URL detected:', apiUrl);
            showAlert('danger', 'Configuration error. Please refresh the page.');
            return;
        }
        
        fetch(apiUrl + 'auth.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                window.location.href = d.data.redirect;
            } else {
                showAlert('danger', d.message);
            }
        })
        .catch(e => {
            console.error('Login error:', e);
            showAlert('danger', 'Login error. Please try again.');
        });
    });
});

function showAlert(type, message) {
    const container = document.getElementById('alertContainer');
    container.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
}
