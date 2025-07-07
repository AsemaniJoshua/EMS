// Teacher Dashboard JS

document.addEventListener('DOMContentLoaded', function () {
    // API fetching is commented out to prevent 404 errors.
    fetchTeacherDashboard();
});

// API fetching is commented out to prevent 404 errors.
function fetchTeacherDashboard() {
    fetch('/api/teacher/dashboard')
        .then(response => response.json())
        .then(data => {
            // Handle dashboard data
            console.log('Dashboard data:', data);
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
        });
}

fetchTeacherDashboard();

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