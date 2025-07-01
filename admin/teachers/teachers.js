// Admin Manage Teachers JS

document.addEventListener('DOMContentLoaded', function () {
    fetchTeachers();
    document.getElementById('addTeacherBtn').onclick = addTeacher;
});

function fetchTeachers() {
    // Placeholder for backend API call
    fetch('/api/admin/teachers')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            populateTeachers(data.teachers || []);
        })
        .catch(error => {
            showNotification('Failed to load teachers (placeholder)', 'error');
        });
}

function populateTeachers(teachers) {
    const table = document.getElementById('teachersTable');
    table.innerHTML = '';
    if (teachers.length === 0) {
        table.innerHTML = '<tr><td colspan="4" class="text-center py-4">No teachers found.</td></tr>';
        return;
    }
    teachers.forEach(teacher => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="py-2 px-4 border-b">${teacher.name}</td>
            <td class="py-2 px-4 border-b">${teacher.email}</td>
            <td class="py-2 px-4 border-b">${teacher.blocked ? 'Blocked' : 'Active'}</td>
            <td class="py-2 px-4 border-b">
                <button onclick="blockTeacher('${teacher.id}', ${!teacher.blocked})" class="text-${teacher.blocked ? 'green' : 'red'}-500 hover:underline">${teacher.blocked ? 'Unblock' : 'Block'}</button>
            </td>
        `;
        table.appendChild(row);
    });
}

function addTeacher() {
    // Placeholder for add teacher logic
    showNotification('Add teacher feature coming soon!', 'info');
}

function blockTeacher(id, block) {
    // Placeholder for backend API call
    fetch(`/api/admin/teachers/${id}/${block ? 'block' : 'unblock'}`, { method: 'POST' })
        .then(response => {
            if (!response.ok) throw new Error('Action failed');
            return response.json();
        })
        .then(data => {
            showNotification(`Teacher ${block ? 'blocked' : 'unblocked'}! (placeholder)`, 'success');
            fetchTeachers();
        })
        .catch(error => {
            showNotification('Action failed (placeholder)', 'error');
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