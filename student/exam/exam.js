// Student Exam JS - Real backend integration

let currentTab = 'upcoming';
let examData = {
    upcoming: [],
    ongoing: [],
    past: []
};

document.addEventListener('DOMContentLoaded', function () {
    initializeExamPage();
    setupEventListeners();
    loadExamData('upcoming');
});

function initializeExamPage() {
    // Set up enrollment form
    const enrollForm = document.getElementById('enrollForm');
    if (enrollForm) {
        enrollForm.addEventListener('submit', handleEnrollment);
    }
}

function setupEventListeners() {
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.getAttribute('data-tab');
            switchTab(tab);
        });
    });
    
    // Search and filter
    const searchInput = document.getElementById('searchInput');
    const subjectFilter = document.getElementById('subjectFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    [searchInput, subjectFilter, statusFilter].forEach(el => {
        el.addEventListener('input', () => {
            filterAndRenderExams();
        });
    });
}

function switchTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('text-emerald-600', 'bg-emerald-50', 'border-b-2', 'border-emerald-600', 'rounded-t');
        btn.classList.add('text-gray-600');
    });
    
    const activeBtn = document.querySelector(`[data-tab="${tab}"]`);
    activeBtn.classList.add('text-emerald-600', 'bg-emerald-50', 'border-b-2', 'border-emerald-600', 'rounded-t');
    activeBtn.classList.remove('text-gray-600');
    
    // Show/hide tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    document.getElementById(`tab-${tab}`).classList.remove('hidden');
    
    currentTab = tab;
    
    // Load data if not already loaded
    if (!examData[tab] || examData[tab].length === 0) {
        loadExamData(tab);
    } else {
        renderExams(tab, examData[tab]);
    }
}

function loadExamData(type) {
    showLoading(true);
    
    fetch('/api/students/getExams.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            type: type,
            student_id: studentData.student_id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            examData[type] = data[type] || [];
            renderExams(type, examData[type]);
        } else {
            showNotification('Failed to load exams: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error loading exams:', error);
        showNotification('Error loading exams', 'error');
    })
    .finally(() => {
        showLoading(false);
    });
}

function renderExams(type, exams) {
    if (type === 'upcoming') {
        renderUpcomingExams(exams);
    } else if (type === 'ongoing') {
        renderOngoingExams(exams);
    } else if (type === 'past') {
        renderPastExams(exams);
    }
}

function renderUpcomingExams(exams) {
    const container = document.getElementById('upcomingExamsContainer');
    
    if (exams.length === 0) {
        container.innerHTML = `
            <div class="col-span-2 text-center py-8 flex flex-col justify-center items-center">
                <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">No upcoming exams found</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = '';
    exams.forEach(exam => {
        const startDate = new Date(exam.start_datetime);
        const now = new Date();
        const timeDiff = startDate - now;
        const daysLeft = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
        
        const examCard = document.createElement('div');
        examCard.className = 'bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 flex flex-col gap-3 relative';
        
        examCard.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-blue-700">${exam.course_title}</span>
                <span class="px-2 py-1 rounded ${exam.registration_status === 'Registered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'} text-xs font-semibold">
                    ${exam.registration_status === 'Registered' ? 'Registered' : 'Available'}
                </span>
            </div>
            <h2 class="text-xl font-bold text-gray-900">${exam.title}</h2>
            <div class="text-gray-600 text-sm mb-2">
                <i class="fas fa-calendar mr-2"></i>
                ${startDate.toLocaleDateString()} at ${startDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
            </div>
            <div class="text-gray-600 text-sm mb-2">
                <i class="fas fa-clock mr-2"></i>
                Duration: ${exam.duration_minutes} minutes
            </div>
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-hourglass-half text-blue-500"></i>
                <span class="text-gray-700 text-sm">
                    ${daysLeft > 0 ? `Starts in ${daysLeft} day${daysLeft > 1 ? 's' : ''}` : 'Starting soon'}
                </span>
            </div>
            <div class="mt-auto flex gap-2">
                <button onclick="openExamModal(${exam.exam_id})" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    View Details
                </button>
                ${exam.registration_status === 'Registered' ? 
                    `<button onclick="startExam(${exam.exam_id})" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                        Take Exam
                    </button>` :
                    `<button onclick="registerForExam(${exam.exam_id})" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                        Register
                    </button>`
                }
            </div>
        `;
        
        container.appendChild(examCard);
    });
}

function renderOngoingExams(exams) {
    const container = document.getElementById('ongoingExamsContainer');
    
    if (exams.length === 0) {
        container.innerHTML = `
            <div class="col-span-2 text-center py-8 flex flex-col justify-center items-center">
                <i class="fas fa-clock text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">No ongoing exams</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = '';
    exams.forEach(exam => {
        const endDate = new Date(exam.end_datetime);
        const now = new Date();
        const timeLeft = endDate - now;
        const hoursLeft = Math.floor(timeLeft / (1000 * 60 * 60));
        const minutesLeft = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        
        const examCard = document.createElement('div');
        examCard.className = 'bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 flex flex-col gap-3 relative';
        
        examCard.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-emerald-700">${exam.course_title}</span>
                <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-semibold animate-pulse">
                    Ongoing
                </span>
            </div>
            <h2 class="text-xl font-bold text-gray-900">${exam.title}</h2>
            <div class="text-gray-600 text-sm mb-2">
                <i class="fas fa-calendar mr-2"></i>
                Started: ${new Date(exam.start_datetime).toLocaleString()}
            </div>
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-clock text-emerald-500"></i>
                <span class="text-gray-700 text-sm">
                    Time left: <span class="font-semibold text-red-600">${hoursLeft}h ${minutesLeft}m</span>
                </span>
            </div>
            ${exam.progress ? `
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: ${exam.progress}%"></div>
                </div>
                <div class="text-sm text-gray-600 mb-2">Progress: ${exam.progress}%</div>
            ` : ''}
            <div class="mt-auto flex gap-2">
                <button onclick="openExamModal(${exam.exam_id})" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    View Details
                </button>
                <button onclick="continueExam(${exam.exam_id})" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    Continue Exam
                </button>
            </div>
        `;
        
        container.appendChild(examCard);
    });
}

function renderPastExams(exams) {
    const tbody = document.getElementById('pastExamsTable');
    
    if (exams.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500 flex flex-col justify-center items-center">
                    <i class="fas fa-history text-2xl mb-2 block"></i>
                    No past exams found
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = '';
    exams.forEach(exam => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        const statusClass = exam.score_percentage >= exam.pass_mark ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const statusText = exam.score_percentage >= exam.pass_mark ? 'Passed' : 'Failed';
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${exam.title}</div>
                <div class="text-sm text-gray-500">${exam.exam_code}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${exam.course_title}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${new Date(exam.completed_at).toLocaleDateString()}</div>
                <div class="text-sm text-gray-500">${new Date(exam.completed_at).toLocaleTimeString()}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${exam.score_percentage}%</div>
                <div class="text-sm text-gray-500">${exam.correct_answers}/${exam.total_questions}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 rounded ${statusClass} text-xs font-semibold">
                    ${statusText}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="viewResult(${exam.exam_id})" class="text-emerald-600 hover:text-emerald-900 mr-3">
                    <i class="fas fa-eye"></i> View
                </button>
                <button onclick="downloadResult(${exam.exam_id})" class="text-blue-600 hover:text-blue-900">
                    <i class="fas fa-download"></i> Download
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function filterAndRenderExams() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const subjectFilter = document.getElementById('subjectFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    let filteredExams = examData[currentTab] || [];
    
    // Apply filters
    if (searchTerm) {
        filteredExams = filteredExams.filter(exam => 
            exam.title.toLowerCase().includes(searchTerm) ||
            exam.course_title.toLowerCase().includes(searchTerm) ||
            exam.exam_code.toLowerCase().includes(searchTerm)
        );
    }
    
    if (subjectFilter) {
        filteredExams = filteredExams.filter(exam => 
            exam.course_title === subjectFilter
        );
    }
    
    if (statusFilter) {
        if (statusFilter === 'upcoming') {
            filteredExams = filteredExams.filter(exam => new Date(exam.start_datetime) > new Date());
        } else if (statusFilter === 'ongoing') {
            filteredExams = filteredExams.filter(exam => {
                const now = new Date();
                return now >= new Date(exam.start_datetime) && now <= new Date(exam.end_datetime);
            });
        } else if (statusFilter === 'completed') {
            filteredExams = filteredExams.filter(exam => exam.completed_at);
        }
    }
    
    renderExams(currentTab, filteredExams);
}

function handleEnrollment(e) {
    e.preventDefault();
    const key = document.getElementById('enrollKey').value.trim();
    const submitButton = e.target.querySelector('button[type="submit"]');
    
    if (!key) {
        showNotification('Please enter an enrollment key', 'error');
        return;
    }
    
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    fetch('/api/courseEnrollment/courseEnrollment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            enrollment_key: key
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Refresh current tab data
            examData[currentTab] = [];
            loadExamData(currentTab);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while processing your request', 'error');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Submit';
        document.getElementById('enrollKey').value = '';
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
        confirmButtonText: 'Yes, Register'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Registering...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/api/courseEnrollment/examsRegistration.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
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
                        // Refresh current tab data
                        examData[currentTab] = [];
                        loadExamData(currentTab);
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

function openExamModal(examId) {
    // Show loading modal
    document.getElementById('examModal').classList.remove('hidden');
    document.getElementById('modalContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-emerald-600 mb-4"></i>
            <p class="text-gray-600">Loading exam details...</p>
        </div>
    `;
    
    // Fetch exam details
    fetch('/api/student/getExamDetails.php', {
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
            displayExamModal(data.exam);
        } else {
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-600 mb-4"></i>
                    <p class="text-red-600">Failed to load exam details</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('modalContent').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600 mb-4"></i>
                <p class="text-red-600">Error loading exam details</p>
            </div>
        `;
    });
}

function displayExamModal(exam) {
    const startDate = new Date(exam.start_datetime);
    const endDate = new Date(exam.end_datetime);
    const now = new Date();
    
    let statusBadge = '';
    let actionButton = '';
    
    if (now < startDate) {
        statusBadge = '<span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-semibold">Upcoming</span>';
        if (exam.registration_status === 'Registered') {
            actionButton = '<button onclick="closeExamModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">Close</button>';
        } else {
            actionButton = `<button onclick="registerForExamFromModal(${exam.exam_id})" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">Register Now</button>`;
        }
    } else if (now >= startDate && now <= endDate) {
        statusBadge = '<span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-semibold animate-pulse">Active</span>';
        if (exam.registration_status === 'Registered') {
            actionButton = `<button onclick="startExamFromModal(${exam.exam_id})" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">Start Exam</button>`;
        } else {
            actionButton = '<button onclick="closeExamModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">Close</button>';
        }
    } else {
        statusBadge = '<span class="px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm font-semibold">Ended</span>';
        actionButton = '<button onclick="closeExamModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">Close</button>';
    }
    
    document.getElementById('modalContent').innerHTML = `
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-emerald-700">${exam.title}</h2>
                ${statusBadge}
            </div>
            <p class="text-gray-600 mb-4">${exam.description || 'No description available'}</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-3">Exam Information</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex justify-between">
                        <span class="text-gray-600">Course:</span>
                        <span class="font-medium">${exam.course_title}</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Code:</span>
                        <span class="font-medium">${exam.exam_code}</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-medium">${exam.duration_minutes} minutes</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Total Marks:</span>
                        <span class="font-medium">${exam.total_marks}</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Pass Mark:</span>
                        <span class="font-medium">${exam.pass_mark}%</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Max Attempts:</span>
                        <span class="font-medium">${exam.max_attempts}</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-3">Schedule</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex justify-between">
                        <span class="text-gray-600">Start Date:</span>
                        <span class="font-medium">${startDate.toLocaleDateString()}</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Start Time:</span>
                        <span class="font-medium">${startDate.toLocaleTimeString()}</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">End Date:</span>
                        <span class="font-medium">${endDate.toLocaleDateString()}</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">End Time:</span>
                        <span class="font-medium">${endDate.toLocaleTimeString()}</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-yellow-800 mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Important Instructions
            </h3>
            <ul class="text-sm text-yellow-700 space-y-1">
                <li>• Make sure you have a stable internet connection</li>
                <li>• Do not refresh the page during the exam</li>
                <li>• Your answers are automatically saved</li>
                <li>• You have ${exam.max_attempts} attempt(s) for this exam</li>
                ${exam.anti_cheating ? '<li>• Anti-cheating measures are enabled</li>' : ''}
                ${exam.randomize ? '<li>• Questions will be randomized</li>' : ''}
            </ul>
        </div>
        
        <div class="flex justify-end gap-3">
            <button onclick="closeExamModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                Cancel
            </button>
            ${actionButton}
        </div>
    `;
}

function closeExamModal() {
    document.getElementById('examModal').classList.add('hidden');
}

function registerForExamFromModal(examId) {
    closeExamModal();
    registerForExam(examId);
}

function startExam(examId) {
    window.location.href = `take.php?exam_id=${examId}`;
}

function startExamFromModal(examId) {
    closeExamModal();
    startExam(examId);
}

function continueExam(examId) {
    window.location.href = `take.php?exam_id=${examId}&continue=1`;
}

function viewResult(examId) {
    window.location.href = `../results/view.php?exam_id=${examId}`;
}

function downloadResult(examId) {
    window.open(`/api/results/printResult.php?exam_id=${examId}`, '_blank');
}

function showLoading(show) {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (show) {
        loadingIndicator.classList.remove('hidden');
    } else {
        loadingIndicator.classList.add('hidden');
    }
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
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Auto-refresh ongoing exams every 30 seconds
// setInterval(() => {
//     if (currentTab === 'ongoing' && examData.ongoing.length > 0) {
//         loadExamData('ongoing');
//     }
// }, 30000);


