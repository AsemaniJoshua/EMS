// Student Registration JS

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            registerUser();
        });
    }
});

function registerUser() {
    const name = document.querySelector('[name=name]').value;
    const email = document.querySelector('[name=email]').value;
    const password = document.querySelector('[name=password]').value;
    const confirmPassword = document.querySelector('[name=confirm_password]').value;
    if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'error');
        return;
    }
    fetch('/api/student/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, password })
    })
    .then(response => {
        if (!response.ok) throw new Error('Registration failed');
        return response.json();
    })
    .then(data => {
        showNotification('Registration successful!', 'success');
        setTimeout(() => {
            window.location.href = '../login/index.php';
        }, 1200);
    })
    .catch(error => {
        showNotification('Registration failed: ' + error.message, 'error');
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