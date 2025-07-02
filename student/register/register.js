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
    // Placeholder for backend API call
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
        showNotification('Registration successful! (placeholder)', 'success');
        // TODO: Redirect to login or dashboard
    })
    .catch(error => {
        showNotification('Registration failed (placeholder)', 'error');
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