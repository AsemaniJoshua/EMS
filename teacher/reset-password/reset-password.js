// Teacher Reset Password JS

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('resetPasswordForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            resetPassword();
        });
    }
});

function resetPassword() {
    const password = document.querySelector('[name=password]').value;
    const confirmPassword = document.querySelector('[name=confirm_password]').value;
    if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'error');
        return;
    }
    // Placeholder for backend API call
    fetch('/api/teacher/reset-password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ password })
    })
    .then(response => {
        if (!response.ok) throw new Error('Reset failed');
        return response.json();
    })
    .then(data => {
        showNotification('Password reset successful! (placeholder)', 'success');
        // TODO: Redirect to login or dashboard
    })
    .catch(error => {
        showNotification('Password reset failed (placeholder)', 'error');
    });
}

function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
    };
    const toast = document.createElement('div');
    toast.className = `fixed top-5 right-5 px-4 py-2 rounded shadow text-white z-50 ${colors[type] || colors.info}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
} 