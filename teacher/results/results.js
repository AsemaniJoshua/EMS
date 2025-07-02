// Teacher Results JS

document.addEventListener('DOMContentLoaded', function () {
    fetchResults();
    document.getElementById('filterForm').onsubmit = function(e) {
        e.preventDefault();
        fetchResults();
    };
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