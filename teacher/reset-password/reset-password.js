// Teacher Reset Password JS

document.addEventListener('DOMContentLoaded', function () {
    const resetForm = document.getElementById('reset-password-form');
    if (resetForm) {
        resetForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            // Replace with real API call to /api/teacher/reset-password
            fetch('/api/teacher/reset-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            })
                .then(response => response.json())
                .then(data => {
                    showNotification('If an account with that email exists, a password reset link has been sent to your inbox.', 'success');
                })
                .catch(error => {
                    showNotification('Failed to send reset link.', 'error');
                });
        });
    }
    function showNotification(message, type = 'info') {
        let notification = document.getElementById('notification-toast');
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'notification-toast';
            document.body.appendChild(notification);
        }
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
        };
        notification.className = `fixed top-5 right-5 px-6 py-3 rounded shadow-lg text-white text-base font-semibold z-50 ${colors[type] || colors.info}`;
        notification.textContent = message;
        notification.style.display = 'block';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }
}); 