document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('teacher-login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const usernameEmail = document.getElementById('username-email').value;
            const password = document.getElementById('password').value;
            // Simulate API call
            // Replace with real API call to /api/teacher/login
            const isFirstLogin = (usernameEmail === 'newteacher' && password === 'default123');
            if (isFirstLogin) {
                showNotification('Welcome! This appears to be your first login. Please reset your password.', 'info');
                setTimeout(() => {
                    window.location.href = 'reset-password/index.php';
                }, 1500);
            } else {
                showNotification('Login successful! Redirecting to dashboard.', 'success');
                setTimeout(() => {
                    window.location.href = '../index.php?page=dashboard';
                }, 1500);
            }
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
