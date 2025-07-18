// Dashboard JS for Student

document.addEventListener('DOMContentLoaded', function () {
    // Initialize dashboard
    initializeDashboard();
    fetchDashboardData();
    initializeEnrollmentForm();
});

function initializeDashboard() {
    // Check authentication
    checkAuthentication();
    
    // Initialize tooltips and interactive elements
    initializeInteractiveElements();
    
    // Set up periodic data refresh
    setInterval(fetchDashboardData, 300000); // Refresh every 5 minutes
}

function checkAuthentication() {
    // This would typically be handled by PHP session check
    // But we can add client-side checks for better UX
    const currentPath = window.location.pathname;
    
    // If on login page and already logged in, redirect to dashboard
    if (currentPath.includes('/login/') && sessionStorage.getItem('student_logged_in')) {
        window.location.href = '/student/dashboard/';
    }
}

function initializeInteractiveElements() {
    // Add hover effects and click handlers for cards
    const statCards = document.querySelectorAll('.bg-white.overflow-hidden.shadow-sm');
    
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('transform', 'scale-105', 'shadow-lg');
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('transform', 'scale-105', 'shadow-lg');
        });
    });
    
    // Add click handlers for action buttons
    const actionButtons = document.querySelectorAll('[onclick*="registerForExam"]');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const examId = this.getAttribute('onclick').match(/\d+/)[0];
            registerForExam(parseInt(examId));
        });
    });
}

function fetchDashboardData() {
    // Fetch real dashboard statistics
    fetch('/api/student/getDashboardStats.php')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateDashboardStats(data.data);
            }
        })
        .catch(error => {
            console.error('Failed to fetch dashboard data:', error);
            // Don't show error notification for background refresh
        });
}

function updateDashboardStats(stats) {
    // Update statistics cards
    if (stats.registered_exams !== undefined) {
        updateStatCard('registered-exams', stats.registered_exams, stats.this_month_exams);
    }
    
    if (stats.average_score !== undefined) {
        updateStatCard('average-score', stats.average_score + '%', stats.score_improvement);
    }
    
    if (stats.pending_exams !== undefined) {
        updateStatCard('pending-exams', stats.pending_exams, 'Available now');
    }
    
    if (stats.completed_exams !== undefined) {
        updateStatCard('completed-exams', stats.completed_exams, stats.this_week_completed);
    }
    
    // Update upcoming exams table
    if (stats.upcoming_exams) {
        updateUpcomingExamsTable(stats.upcoming_exams);
    }
    
    // Update recent results table
    if (stats.recent_results) {
        updateRecentResultsTable(stats.recent_results);
    }
    
    // Update activity timeline
    if (stats.recent_activity) {
        updateActivityTimeline(stats.recent_activity);
    }
}

function updateStatCard(cardType, mainValue, subValue) {
    const card = document.querySelector(`[data-stat="${cardType}"]`);
    if (!card) return;
    
    const mainValueElement = card.querySelector('.text-xl.font-semibold');
    const subValueElement = card.querySelector('.text-emerald-600, .text-purple-600, .text-yellow-600');
    
    if (mainValueElement) {
        // Animate number change
        animateNumber(mainValueElement, mainValue);
    }
    
    if (subValueElement && subValue !== undefined) {
        subValueElement.textContent = '+' + subValue;
    }
}

function animateNumber(element, targetValue) {
    const currentValue = parseInt(element.textContent) || 0;
    const target = parseInt(targetValue) || 0;
    const duration = 1000; // 1 second
    const steps = 20;
    const increment = (target - currentValue) / steps;
    
    let current = currentValue;
    let step = 0;
    
    const timer = setInterval(() => {
        step++;
        current += increment;
        
        if (step >= steps) {
            element.textContent = targetValue;
            clearInterval(timer);
        } else {
            element.textContent = Math.round(current);
        }
    }, duration / steps);
}

function updateUpcomingExamsTable(exams) {
    const tbody = document.querySelector('#upcomingExamsTable');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
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
    
    exams.forEach(exam => {
        const row = document.createElement('tr');
        const statusColor = exam.registration_status === 'Registered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
        const actionButton = exam.registration_status === 'Registered' 
            ? `<button onclick="window.location.href='/student/exam/take.php?id=${exam.exam_id}'" class="text-emerald-600 hover:text-emerald-700 transition-colors duration-200">
                <i class="fas fa-play text-lg cursor-pointer"></i>
               </button>`
            : `<button onclick="registerForExam(${exam.exam_id})" class="text-blue-600 hover:text-blue-700 transition-colors duration-200">
                <i class="fas fa-plus text-lg cursor-pointer"></i>
               </button>`;
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                ${escapeHtml(exam.title)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${formatDateTime(exam.start_datetime)}
            </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 rounded ${statusColor} text-xs font-semibold">
                    ${exam.registration_status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${actionButton}
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function updateRecentResultsTable(results) {
    const tbody = document.querySelector('#recentResultsTable');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
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
    
    results.forEach(result => {
        const row = document.createElement('tr');
        const statusColor = result.status === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                ${escapeHtml(result.title)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${Math.round(result.score_percentage * 10) / 10}%
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 rounded ${statusColor} text-xs font-semibold">
                    ${result.status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${formatDate(result.completed_at)}
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function updateActivityTimeline(activities) {
    const timeline = document.querySelector('.relative.border-l-2.border-emerald-200');
    if (!timeline) return;
    
    timeline.innerHTML = '';
    
    if (activities.length === 0) {
        timeline.innerHTML = '<li class="text-gray-500">No recent activity</li>';
        return;
    }
    
    activities.forEach(activity => {
        const li = document.createElement('li');
        const colorClass = activity.activity_type === 'exam_completed' ? 'bg-green-500' : 'bg-emerald-500';
        const textColorClass = activity.activity_type === 'exam_completed' ? 'text-green-700' : 'text-emerald-700';
        
        li.innerHTML = `
            <span class="absolute -left-3 top-1 w-3 h-3 ${colorClass} rounded-full"></span>
            <span class="font-semibold ${textColorClass}">
                ${escapeHtml(activity.activity_title)}
            </span> - 
            <span class="text-gray-700">${escapeHtml(activity.activity_description)}</span>
            <span class="text-gray-500 text-sm block">
                ${formatDate(activity.activity_date)}
            </span>
        `;
        
        timeline.appendChild(li);
    });
}

function initializeEnrollmentForm() {
    const enrollForm = document.getElementById('enrollForm');
    if (!enrollForm) return;
    
    enrollForm.addEventListener('submit', function(e) {
        e.preventDefault();
        handleEnrollment();
    });
}

async function handleEnrollment() {
    const enrollKey = document.getElementById('enrollKey').value.trim();
    const submitButton = document.querySelector('#enrollForm button[type="submit"]');
    
    if (!enrollKey) {
        showNotification('Please enter an enrollment key', 'error');
        return;
    }
    
    // Disable button and show loading
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    try {
        const response = await fetch('/api/courseEnrollment/courseEnrollment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                enrollment_key: enrollKey
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (data.data.type === 'exam') {
                showNotification(data.message, 'success');
                // Refresh the upcoming exams table
                setTimeout(() => {
                    fetchDashboardData();
                }, 1500);
            } else if (data.data.type === 'course') {
                showCourseExams(data.data);
            }
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('An error occurred while processing your request', 'error');
    } finally {
        // Re-enable button
        submitButton.disabled = false;
        submitButton.innerHTML = 'Submit';
        // Clear input
        document.getElementById('enrollKey').value = '';
    }
}

function showCourseExams(courseData) {
    const course = courseData.course;
    const exams = courseData.available_exams;
    
    let examsList = '';
    if (exams.length === 0) {
        examsList = '<p class="text-gray-500 text-center py-4">No exams available for this course.</p>';
    } else {
        examsList = '<div class="space-y-3 max-h-96 overflow-y-auto">';
        exams.forEach(exam => {
            const statusColor = exam.status === 'Available' ? 'green' : 
                              exam.status === 'Active' ? 'blue' : 
                              exam.status === 'Already Registered' ? 'yellow' : 'gray';
            
            const registerButton = exam.status === 'Available' ? 
                `<button onclick="registerForExamFromModal(${exam.exam_id})" class="bg-emerald-600 text-white px-3 py-1 rounded text-sm hover:bg-emerald-700 transition-colors">
                    Register
                </button>` : '';
            
            examsList += `
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">${escapeHtml(exam.title)}</h4>
                            <p class="text-sm text-gray-600 mt-1">Code: ${escapeHtml(exam.exam_code)}</p>
                            <p class="text-sm text-gray-600">Start: ${formatDateTime(exam.start_datetime)}</p>
                            <p class="text-sm text-gray-600">End: ${formatDateTime(exam.end_datetime)}</p>
                        </div>
                        <div class="text-right ml-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-${statusColor}-100 text-${statusColor}-800 block mb-2">
                                ${exam.status}
                            </span>
                            ${registerButton}
                        </div>
                    </div>
                </div>
            `;
        });
        examsList += '</div>';
    }
    
    // Create and show modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b">
                <h2 class="text-xl font-bold text-gray-900">Course: ${escapeHtml(course.title)}</h2>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4 text-sm text-gray-600">
                    <p><strong>Code:</strong> ${escapeHtml(course.code)}</p>
                    <p><strong>Department:</strong> ${escapeHtml(course.department_name)}</p>
                    <p><strong>Program:</strong> ${escapeHtml(course.program_name)}</p>
                </div>
                <h3 class="font-semibold mb-3 text-gray-900">Available Exams:</h3>
                ${examsList}
            </div>
            <div class="flex justify-end p-6 border-t bg-gray-50">
                <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Close
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

async function registerForExam(examId) {
    const confirmation = await showConfirmDialog(
        'Confirm Registration',
        'Do you want to register for this exam?',
        'Yes, Register',
        'Cancel'
    );
    
    if (!confirmation) return;
    
    try {
        const response = await fetch('/api/courseEnrollment/examsRegistration.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                exam_id: examId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            // Refresh the dashboard data
            setTimeout(() => {
                fetchDashboardData();
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('An error occurred while registering for the exam', 'error');
    }
}

// Function for registering from modal
window.registerForExamFromModal = async function(examId) {
    await registerForExam(examId);
    // Close modal after registration
    const modal = document.querySelector('.fixed.inset-0.bg-black');
    if (modal) {
        modal.remove();
    }
};

function showConfirmDialog(title, message, confirmText, cancelText) {
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-question text-emerald-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">${title}</h3>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6">${message}</p>
                    <div class="flex justify-end space-x-3">
                        <button onclick="resolveConfirm(false)" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                            ${cancelText}
                        </button>
                        <button onclick="resolveConfirm(true)" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        window.resolveConfirm = function(result) {
            modal.remove();
            delete window.resolveConfirm;
            resolve(result);
        };
        
        // Close on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                window.resolveConfirm(false);
            }
        });
    });
}

function showNotification(message, type) {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 4000);
}

// Utility functions
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
    }

// Performance monitoring
function trackPerformance() {
    if ('performance' in window) {
        window.addEventListener('load', function() {
            setTimeout(function() {
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData) {
                    console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
                }
            }, 0);
        });
    }
}

// Initialize performance tracking
trackPerformance();

// Export functions for global access
window.registerForExam = registerForExam;
window.showCourseExams = showCourseExams;
window.showNotification = showNotification;


