// Teacher Results JS

document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/teacher/results')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('results-container');
            if (container) {
                container.innerHTML = '';
                data.forEach(result => {
                    const div = document.createElement('div');
                    div.className = 'bg-white rounded-lg shadow p-4 mb-4';
                    div.innerHTML = `<div class='font-semibold text-gray-800'>${result.student_name}</div><div class='text-gray-600'>Score: ${result.score}</div>`;
                    container.appendChild(div);
                });
            }
        })
        .catch(error => {
            showNotification('Failed to load results.', 'error');
        });
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

function fetchResults() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form)).toString();
    // Placeholder for backend API call
    fetch('/api/teacher/results?' + params)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            populateResults(data.results || []);
        })
        .catch(error => {
            showNotification('Failed to load results (placeholder)', 'error');
        });
}

function populateResults(results) {
    const table = document.getElementById('resultsTable');
    table.innerHTML = '';
    if (results.length === 0) {
        table.innerHTML = '<tr><td colspan="6" class="text-center py-4">No results found.</td></tr>';
        return;
    }
    results.forEach(result => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="py-2 px-4 border-b">${result.student}</td>
            <td class="py-2 px-4 border-b">${result.exam}</td>
            <td class="py-2 px-4 border-b">${result.category}</td>
            <td class="py-2 px-4 border-b">${result.date}</td>
            <td class="py-2 px-4 border-b">${result.score}</td>
            <td class="py-2 px-4 border-b">${result.passed ? 'Passed' : 'Failed'}</td>
        `;
        table.appendChild(row);
    });
} 