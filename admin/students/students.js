// Admin Manage Students JS

document.addEventListener('DOMContentLoaded', function () {
    fetchStudents();
    document.getElementById('addStudentBtn').onclick = addStudent;
});

function fetchStudents() {
    // Placeholder for backend API call
    fetch('/api/admin/students')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            populateStudents(data.students || []);
        })
        .catch(error => {
            showNotification('Failed to load students (placeholder)', 'error');
        });
}

function populateStudents(students) {
    const table = document.getElementById('studentsTable');
    table.innerHTML = '';
    if (students.length === 0) {
        table.innerHTML = '<tr><td colspan="4" class="text-center py-4">No students found.</td></tr>';
        return;
    }
    students.forEach(student => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="py-2 px-4 border-b">${student.name}</td>
            <td class="py-2 px-4 border-b">${student.email}</td>
            <td class="py-2 px-4 border-b">${student.blocked ? 'Blocked' : 'Active'}</td>
            <td class="py-2 px-4 border-b">
                <button onclick="blockStudent('${student.id}', ${!student.blocked})" class="text-${student.blocked ? 'green' : 'red'}-500 hover:underline">${student.blocked ? 'Unblock' : 'Block'}</button>
            </td>
        `;
        table.appendChild(row);
    });
}

function addStudent() {
    // Placeholder for add student logic
    showNotification('Add student feature coming soon!', 'info');
}

function blockStudent(id, block) {
    // Placeholder for backend API call
    fetch(`/api/admin/students/${id}/${block ? 'block' : 'unblock'}`, { method: 'POST' })
        .then(response => {
            if (!response.ok) throw new Error('Action failed');
            return response.json();
        })
        .then(data => {
            showNotification(`Student ${block ? 'blocked' : 'unblocked'}! (placeholder)`, 'success');
            fetchStudents();
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