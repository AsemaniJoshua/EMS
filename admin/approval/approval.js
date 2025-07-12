// Admin Exam Approval JS

document.addEventListener('DOMContentLoaded', function () {
    fetchExams();
    // Stats are now loaded via PHP, no need for fetchApprovalStats()
});

function fetchExams(status = 'Pending') {
    // Fetch exams from the API
    fetch('../../api/exams/getApprovalList.php?status=' + status)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                populateExams(data.exams || []);
                // Stats are now updated via PHP
            } else {
                showNotification(data.message || 'Failed to load exams', 'error');
            }
        })
        .catch(error => {
            showNotification('Failed to load exams: ' + error.message, 'error');
        });
}

// This function is no longer needed as stats are now loaded directly via PHP
function fetchApprovalStats() {
    // Left empty as stats are now handled server-side
}

function populateExams(exams) {
    const table = document.getElementById('approvalTable');
    table.innerHTML = '';

    // Update count display
    const approvalCount = document.getElementById('approvalCount');
    if (approvalCount) {
        approvalCount.textContent = exams.length + ' exams found';
    }

    if (exams.length === 0) {
        table.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No exams pending approval.</td></tr>';
        return;
    }

    exams.forEach(exam => {
        // Format dates nicely
        const startDate = exam.start_datetime ? new Date(exam.start_datetime).toLocaleString() : 'Not set';

        // Create status badge with appropriate color
        let statusBadge = '';
        if (exam.status === 'Pending') {
            statusBadge = '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>';
        } else if (exam.status === 'Approved') {
            statusBadge = '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Approved</span>';
        } else if (exam.status === 'Rejected') {
            statusBadge = '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>';
        } else if (exam.status === 'Draft') {
            statusBadge = '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Draft</span>';
        }

        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">${exam.title}</div>
                <div class="text-sm text-gray-500">Code: ${exam.exam_code}</div>
                <div class="text-xs text-gray-500 mt-1">Questions: ${exam.question_count || 0}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${exam.teacher_name}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${exam.course_code} - ${exam.course_title}</div>
                <div class="text-xs text-gray-500">${exam.department_name}</div>
                <div class="text-xs text-gray-500">${exam.program_name}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${exam.duration_minutes} min</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${startDate}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${statusBadge}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                ${exam.status === 'Pending' || exam.status === 'Draft' ? `
                <button onclick="approveExam(${exam.exam_id})" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium mr-2 transition-colors">
                    <i class="fas fa-check mr-1"></i> Approve
                </button>
                <button onclick="rejectExam(${exam.exam_id})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                    <i class="fas fa-times mr-1"></i> Reject
                </button>
                ` : `
                <div class="text-xs text-gray-500">
                    ${exam.status === 'Approved' ? 'Approved on ' : 'Rejected on '}
                    ${exam.approved_at ? new Date(exam.approved_at).toLocaleDateString() : 'N/A'}
                </div>
                `}
            </td>
        `;
        table.appendChild(row);
    });
}

function updateStatCards(stats) {
    // This function is no longer needed for stat cards as they are now updated directly via PHP
    // We only need to update the approval count in the table
}

function approveExam(id) {
    // Confirm before approving
    Swal.fire({
        title: 'Approve Exam?',
        text: "This will make the exam available to students based on its scheduled date.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            processApproval(id, 'approve');
        }
    });
}

function rejectExam(id) {
    // Get rejection reason
    Swal.fire({
        title: 'Reject Exam',
        input: 'textarea',
        inputLabel: 'Reason for rejection',
        inputPlaceholder: 'Enter the reason why this exam is being rejected...',
        inputAttributes: {
            'aria-label': 'Reason for rejection'
        },
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Reject',
        cancelButtonText: 'Cancel',
        preConfirm: (comment) => {
            if (!comment) {
                Swal.showValidationMessage('Please provide a reason for rejection');
            }
            return comment;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            processApproval(id, 'reject', result.value);
        }
    });
}

function processApproval(id, action, comment = '') {
    // Process approval or rejection via API
    fetch(`../../api/exams/processApproval.php?exam_id=${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            comment: comment
        })
    })
        .then(response => {
            if (!response.ok) throw new Error('Request failed');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Refresh the exam list and stats
                fetchExams();
                fetchApprovalStats();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Operation failed: ' + error.message, 'error');
        });
}

function setExamDate(id, startDatetime, endDatetime) {
    // This would be implemented when date editing is needed
    // Currently not used as we're focusing on approval/rejection
    showNotification('This feature is not yet implemented', 'info');
}

function filterApprovals() {
    const filterStatus = document.getElementById('filterStatus').value;
    fetchExams(filterStatus);
}

function bulkApprove() {
    // Show warning about bulk approval
    Swal.fire({
        title: 'Bulk Approve?',
        text: "This will approve all pending exams. Are you sure?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, approve all!'
    }).then((result) => {
        if (result.isConfirmed) {
            showNotification('Bulk approval feature is not fully implemented yet', 'info');
            // This would call a bulk approve API endpoint in a complete implementation
        }
    });
}

function exportReport() {
    showNotification('Generating report...', 'info');
    // This would generate a report in a complete implementation
    setTimeout(() => {
        showNotification('Report generation is not fully implemented yet', 'info');
    }, 1500);
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