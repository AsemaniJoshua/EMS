// Dashboard JS for Student

document.addEventListener('DOMContentLoaded', function () {
    // Initialize dashboard functionality
    initializeDashboard();
    
    // Set up auto-refresh
    setInterval(refreshDashboardData, 5 * 60 * 1000); // Refresh every 5 minutes
});

function initializeDashboard() {
    // Initialize enrollment form
    const enrollForm = document.getElementById('enrollForm');
    if (enrollForm) {
        enrollForm.addEventListener('submit', handleEnrollmentSubmission);
    }
    
    // Add click handlers to stat cards
    const statCards = document.querySelectorAll('[data-stat]');
    statCards.forEach(card => {
        card.addEventListener('click', function() {
            const statType = this.dataset.stat;
            handleStatCardClick(statType);
        });
        
        // Add hover effects
        card.classList.add('cursor-pointer', 'hover:shadow-md', 'transition-shadow', 'duration-200');
    });
    
    // Initialize tooltips
    initializeTooltips();
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
                // Refresh the dashboard after successful registration
                setTimeout(() => {
                    refreshDashboardData();
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
            const statusColor = exam.status === 'Available' ? 'green' : 
                              exam.status === 'Active' ? 'blue' : 
                              exam.status === 'Already Registered' ? 'yellow' : 'gray';
            
            examsList += `
                <div class="border rounded-lg p-4 flex justify-between items-center hover:bg-gray-50 transition-colors">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">${escapeHtml(exam.title)}</h4>
                        <p class="text-sm text-gray-600">Code: ${escapeHtml(exam.exam_code)}</p>
                        <p class="text-sm text-gray-600">Start: ${formatDateTime(exam.start_datetime)}</p>
                    </div>
                    <div class="text-right ml-4">
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-${statusColor}-100 text-${statusColor}-800">
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
    
    // Show modal using SweetAlert2
    Swal.fire({
        title: `Course: ${escapeHtml(course.title)}`,
        html: `
            <div class="text-left">
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <p class="mb-2"><strong>Code:</strong> ${escapeHtml(course.code)}</p>
                    <p class="mb-2"><strong>Department:</strong> ${escapeHtml(course.department_name)}</p>
                    <p class="mb-2"><strong>Program:</strong> ${escapeHtml(course.program_name)}</p>
                </div>
                <h3 class="font-semibold mb-3 text-lg">Available Exams:</h3>
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
}

function registerForExam(examId) {
    Swal.fire({
        title: 'Confirm Registration',
        text: 'Do you want to register for this exam?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Register',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Registering...',
                text: 'Please wait while we register you for the exam.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
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
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        // Refresh the dashboard to show updated data
                        refreshDashboardData();
                    });
                } else {
                    Swal.fire({
                        title: 'Registration Failed',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while registering for the exam',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            });
        }
    });
}

function handleStatCardClick(statType) {
    switch(statType) {
        case 'registered-exams':
            window.location.href = '../exam/index.php';
            break;
        case 'average-score':
            window.location.href = '../results/index.php';
            break;
        case 'pending-exams':
            window.location.href = '../exam/index.php?tab=upcoming';
            break;
        case 'completed-exams':
            window.location.href = '../exam/index.php?tab=past';
            break;
        default:
            console.log('Clicked on stat:', statType);
    }
}

function refreshDashboardData() {
    fetch('/api/students/getDashboardStats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDashboardStats(data.data);
                showNotification('Dashboard updated', 'success');
            }
        })
        .catch(error => {
            console.error('Failed to refresh dashboard data:', error);
        });
}

function updateDashboardStats(data) {
    // Update stat cards
    updateStatCard('registered-exams', data.registered_exams, `+${data.this_month_exams} this month`);
    updateStatCard('average-score', `${data.average_score}%`, 'from last term');
    updateStatCard('pending-exams', data.pending_exams, 'available now');
    updateStatCard('completed-exams', data.completed_exams, 'this week');
    
    // Update tables
    updateUpcomingExamsTable(data.upcoming_exams);
    updateRecentResultsTable(data.recent_results);
}

function updateStatCard(statType, mainValue, subText) {
    const card = document.querySelector(`[data-stat="${statType}"]`);
    if (!card) return;
    
    const mainValueElement = card.querySelector('.text-xl');
    const subTextElement = card.querySelector('.text-sm .ml-1');
    
    if (mainValueElement) {
        mainValueElement.textContent = mainValue;
        // Add animation
        mainValueElement.classList.add('animate-pulse');
        setTimeout(() => {
            mainValueElement.classList.remove('animate-pulse');
        }, 1000);
    }
    
    if (subTextElement) {
        subTextElement.textContent = subText;
    }
}

function updateUpcomingExamsTable(exams) {
    const tbody = document.getElementById('upcomingExamsTable');
    if (!tbody) return;
    
    if (exams.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                    No upcoming exams available
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = exams.map(exam => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">${escapeHtml(exam.title)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${formatDateTime(exam.start_datetime)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 rounded ${exam.registration_status === 'Registered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'} text-xs font-semibold">
                    ${exam.registration_status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${exam.registration_status === 'Registered' ? 
                    `<button onclick="window.location.href='../exam/take.php?id=${exam.exam_id}'" 
                            class="text-emerald-600 hover:text-emerald-700 transition-colors duration-200" 
                            title="Take Exam">
                        <i class="fas fa-play text-lg"></i>
                    </button>` :
                    `<button onclick="registerForExam(${exam.exam_id})" 
                            class="text-blue-600 hover:text-blue-700 transition-colors duration-200" 
                            title="Register for Exam">
                        <i class="fas fa-plus text-lg"></i>
                    </button>`
                }
            </td>
        </tr>
    `).join('');
}

function updateRecentResultsTable(results) {
    const tbody = document.getElementById('recentResultsTable');
    if (!tbody) return;
    
    if (results.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                    No exam results available
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = results.map(result => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">${escapeHtml(result.title)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${Math.round(result.score_percentage * 10) / 10}%</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 rounded ${result.status === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-xs font-semibold">
                    ${result.status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${formatDate(result.completed_at)}
            </td>
        </tr>
    `).join('');
}

function initializeTooltips() {
    // Add tooltips to action buttons
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(event) {
    const element = event.target.closest('[title]');
    if (!element) return;
    
    const tooltip = document.createElement('div');
    tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg';
    tooltip.textContent = element.getAttribute('title');
    tooltip.id = 'tooltip';
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
}

function hideTooltip() {
    const tooltip = document.getElementById('tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

// Notification function
function showNotification(message, type = 'info') {
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
            <span>${escapeHtml(message)}</span>
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
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Utility functions
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDateTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

// Export functions for global access
window.registerForExam = registerForExam;
window.showNotification = showNotification;

