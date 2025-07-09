// Student Login JS

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('login-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loginUser();
        });
    }
});

function loginUser() {
    fetch('/api/student/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            email: document.querySelector('[name=email]').value,
            password: document.querySelector('[name=password]').value
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Login failed');
        return response.json();
    })
    .then(data => {
        showNotification('Login successful!', 'success');
        setTimeout(() => {
            window.location.href = '/student/dashboard/index.php';
        }, 1200);
    })
    .catch(error => {
        showNotification('Login failed: ' + error.message, 'error');
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