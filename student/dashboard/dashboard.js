// Dashboard JS for Student - Updated with API integration

document.addEventListener('DOMContentLoaded', function () {
    // Initialize dashboard
    initializeDashboard();
    
    // Setup enrollment form
    setupEnrollmentForm();
    
    // Load real dashboard data
    loadDashboardStats();
});

function initializeDashboard() {
    // Check if SweetAlert2 is loaded
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 not loaded, using basic alerts');
    }
}

function setupEnrollmentForm() {
    const enrollForm = document.getElementById('enrollForm');
    if (enrollForm) {
        enrollForm.addEventListener('submit', handleEnrollmentSubmission);
    }
}

function handleEnrollmentSubmission(e) {
    e.preventDefault();
    
    const key = document.getElementById('enrollKey').value.trim();
    const submitButton = e.target.querySelector('button[type="submit"]');
    
    if (!key) {
        showNotification('Please enter an enrollment key', 'error');
        return;
    }
    
    // Disable button and show loading
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    // Make API call
    fetch('/api/courseEnrollment/courseEnrollment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            enrollment_key: key
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.data.type === 'exam') {
                showNotification(data.message, 'success');
                // Refresh the page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else if (data.data.type === 'course') {
                showCourseExams(data.data);
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while processing your request', 'error');
    })
    .finally(() => {
        // Re-enable button
        submitButton.disabled = false;
        submitButton.innerHTML = 'Submit';
        // Clear input
        document.getElementById('enrollKey').value = '';
    });
}

function showCourseExams(courseData) {
    const course = courseData.course;
    const exams = courseData.available_exams;
    
    let examsList = '';
    if (exams.length === 0) {
        examsList = '<p class="text-gray-500 text-center py-4">No exams available for this course.</p>';
    } else {
        examsList = '<div class="space-y-3">';
        exams.forEach(exam => {
            const statusColor = getStatusColor(exam.status);
            
            examsList += `
                <div class="border rounded-lg p-4 flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-gray-800">${exam.title}</h4>
                        <p class="text-sm text-gray-600">Code: ${exam.exam_code}</p>
                        <p class="text-sm text-gray-600">Start: ${formatDateTime(exam.start_datetime)}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 rounded text-xs font-semibold ${statusColor.bg} ${statusColor.text}">
                            ${exam.status}
                        </span>
                        ${exam.status === 'Available' ? 
                            `<button onclick="registerForExam(${exam.exam_id})" class="block mt-2 bg-emerald-600 text-white px-3 py-1 rounded text-sm hover:bg-emerald-700 transition-colors">
                                Register
                            </button>` : ''}
                    </div>
                </div>
            `;
        });
        examsList += '</div>';
    }
    
    // Show modal using SweetAlert2 if available, otherwise use basic modal
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: `Course: ${course.title}`,
            html: `
                <div class="text-left">
                    <p class="mb-2"><strong>Code:</strong> ${course.code}</p>
                    <p class="mb-2"><strong>Department:</strong> ${course.department_name}</p>
                    <p class="mb-4"><strong>Program:</strong> ${course.program_name}</p>
                    <h3 class="font-semibold mb-3">Available Exams:</h3>
                    ${examsList}
                </div>
            `,
            width: '600px',
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                popup: 'text-left'
            }
        });
    } else {
        // Fallback to basic modal
        showBasicModal(`Course: ${course.title}`, examsList);
    }
}

function getStatusColor(status) {
    switch (status) {
        case 'Available':
            return { bg: 'bg-green-100', text: 'text-green-800' };
        case 'Active':
            return { bg: 'bg-blue-100', text: 'text-blue-800' };
        case 'Already Registered':
            return { bg: 'bg-yellow-100', text: 'text-yellow-800' };
        default:
            return { bg: 'bg-gray-100', text: 'text-gray-800' };
    }
}

function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleString();
}

function registerForExam(examId) {
    const confirmAction = () => {
        // Make API call to register for specific exam
        fetch('/api/courseEnrollment/examsRegistration.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                exam_id: examId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }
                // Refresh the page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while registering for the exam', 'error');
        });
    };
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Confirm Registration',
            text: 'Do you want to register for this exam?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Register'
        }).then((result) => {
            if (result.isConfirmed) {
                confirmAction();
            }
        });
    } else {
        if (confirm('Do you want to register for this exam?')) {
            confirmAction();
        }
    }
}

function loadDashboardStats() {
    // This would typically fetch real data from the backend
        fetch('/api/student/getDashboardStats.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDashboardStats(data.stats);
        }
    })
    .catch(error => {
        console.error('Error loading dashboard stats:', error);
    });
}

function updateDashboardStats(stats) {
    // Update registered exams count
    const registeredExamsElement = document.querySelector('[data-stat="registered-exams"]');
    if (registeredExamsElement && stats.registered_exams !== undefined) {
        registeredExamsElement.textContent = stats.registered_exams;
    }
    
    // Update average score
    const averageScoreElement = document.querySelector('[data-stat="average-score"]');
    if (averageScoreElement && stats.average_score !== undefined) {
        averageScoreElement.textContent = stats.average_score + '%';
    }
    
    // Update pending exams
    const pendingExamsElement = document.querySelector('[data-stat="pending-exams"]');
    if (pendingExamsElement && stats.pending_exams !== undefined) {
        pendingExamsElement.textContent = stats.pending_exams;
    }
    
    // Update completed exams
    const completedExamsElement = document.querySelector('[data-stat="completed-exams"]');
    if (completedExamsElement && stats.completed_exams !== undefined) {
        completedExamsElement.textContent = stats.completed_exams;
    }
}

function showBasicModal(title, content) {
    // Create basic modal if SweetAlert2 is not available
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">${title}</h2>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div>${content}</div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on background click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function showNotification(message, type) {
    const bgColor = type === 'success' ? 'bg-green-500' : 
                   type === 'error' ? 'bg-red-500' : 
                   type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
    
    const icon = type === 'success' ? 'check-circle' : 
                type === 'error' ? 'exclamation-circle' : 
                type === 'warning' ? 'exclamation-triangle' : 'info-circle';
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${icon} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Global function for exam registration (called from modal buttons)
window.registerForExam = registerForExam;

