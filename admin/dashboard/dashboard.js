// Admin Dashboard JS

document.addEventListener('DOMContentLoaded', function () {
    fetchAdminDashboard();
});

function fetchAdminDashboard() {
    // Placeholder for backend API call
    fetch('/api/admin/dashboard')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            showNotification('Admin dashboard loaded (placeholder)', 'success');
        })
        .catch(error => {
            showNotification('Failed to load admin dashboard (placeholder)', 'error');
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