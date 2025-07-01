// Dashboard JS for Student

document.addEventListener('DOMContentLoaded', function () {
    // Example: Fetch dashboard data (AJAX placeholder)
    fetchDashboardData();
});

function fetchDashboardData() {
    // Placeholder for backend API call
    fetch('/api/student/dashboard')
        .then(response => {
            // Simulate API response for now
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // TODO: Update dashboard with real data
            showNotification('Dashboard data loaded (placeholder)', 'success');
        })
        .catch(error => {
            showNotification('Failed to load dashboard data (placeholder)', 'error');
        });
}

// Notification utility (toast)
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