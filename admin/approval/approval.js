// Admin Exam Approval JS

document.addEventListener('DOMContentLoaded', function () {
    fetchExams();
});

function fetchExams() {
    // Placeholder for backend API call
    fetch('/api/admin/approval')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            populateExams(data.exams || []);
        })
        .catch(error => {
            showNotification('Failed to load exams (placeholder)', 'error');
        });
}

function populateExams(exams) {
    const table = document.getElementById('approvalTable');
    table.innerHTML = '';
    if (exams.length === 0) {
        table.innerHTML = '<tr><td colspan="7" class="text-center py-4">No exams pending approval.</td></tr>';
        return;
    }
    exams.forEach(exam => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="py-2 px-4 border-b">${exam.name}</td>
            <td class="py-2 px-4 border-b">${exam.teacher}</td>
            <td class="py-2 px-4 border-b">${exam.category}</td>
            <td class="py-2 px-4 border-b">${exam.duration} min</td>
            <td class="py-2 px-4 border-b"><input type="datetime-local" value="${exam.startTime || ''}" onchange="setStartTime('${exam.id}', this.value)" class="border rounded px-2 py-1"></td>
            <td class="py-2 px-4 border-b">${exam.status}</td>
            <td class="py-2 px-4 border-b">
                <button onclick="approveExam('${exam.id}')" class="bg-green-500 text-white px-2 py-1 rounded mr-2">Approve</button>
                <button onclick="disapproveExam('${exam.id}')" class="bg-red-500 text-white px-2 py-1 rounded">Disapprove</button>
            </td>
        `;
        table.appendChild(row);
    });
}

function approveExam(id) {
    // Placeholder for backend API call
    fetch(`/api/admin/approval/${id}/approve`, { method: 'POST' })
        .then(response => {
            if (!response.ok) throw new Error('Approval failed');
            return response.json();
        })
        .then(data => {
            showNotification('Exam approved! (placeholder)', 'success');
            fetchExams();
        })
        .catch(error => {
            showNotification('Approval failed (placeholder)', 'error');
        });
}

function disapproveExam(id) {
    // Placeholder for backend API call
    fetch(`/api/admin/approval/${id}/disapprove`, { method: 'POST' })
        .then(response => {
            if (!response.ok) throw new Error('Disapproval failed');
            return response.json();
        })
        .then(data => {
            showNotification('Exam disapproved! (placeholder)', 'success');
            fetchExams();
        })
        .catch(error => {
            showNotification('Disapproval failed (placeholder)', 'error');
        });
}

function setStartTime(id, value) {
    // Placeholder for backend API call
    fetch(`/api/admin/approval/${id}/set-start`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ startTime: value })
    })
    .then(response => {
        if (!response.ok) throw new Error('Set start time failed');
        return response.json();
    })
    .then(data => {
        showNotification('Start time set! (placeholder)', 'success');
    })
    .catch(error => {
        showNotification('Set start time failed (placeholder)', 'error');
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