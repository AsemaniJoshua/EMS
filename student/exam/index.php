<?php
$pageTitle = "Exams";
$breadcrumb = "Exams";

// Start session and check authentication
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

// Check if student is logged in
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header('Location: /student/login/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Student</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    
    <!-- Main content area -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-4 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Exams</h1>
                    <p class="mt-1 text-sm text-gray-500">Your upcoming, ongoing, and past exams.</p>
                </div>
            </div>
            
            <!-- Enrollment Key Input -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-emerald-700 mb-1">Join a Course or Exam</h2>
                    <p class="text-gray-600 text-sm">Enter your enrollment key to access upcoming exams for your course.</p>
                </div>
                <form id="enrollForm" class="flex gap-2 w-full md:w-auto">
                    <input id="enrollKey" type="text" required placeholder="Enrollment Key" class="px-4 py-2 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent flex-1" />
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">Submit</button>
                </form>
            </div>
            
            <!-- Tabs -->
            <div class="mb-6">
                <div class="flex gap-2 border-b">
                    <button class="tab-btn px-4 py-2 font-semibold text-emerald-600 border-b-2 border-emerald-600 bg-emerald-50 rounded-t" data-tab="upcoming">Upcoming</button>
                    <button class="tab-btn px-4 py-2 font-semibold text-gray-600 hover:text-emerald-600" data-tab="ongoing">Ongoing</button>
                    <button class="tab-btn px-4 py-2 font-semibold text-gray-600 hover:text-emerald-600" data-tab="past">Past</button>
                    <button class="tab-btn px-4 py-2 font-semibold text-gray-600 hover:text-emerald-600" data-tab="available">Available</button>
                </div>
            </div>
            
            <!-- Search & Filter -->
            <div class="flex flex-col md:flex-row gap-4 mb-6 items-center">
                <input type="text" id="searchInput" placeholder="Search exams..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent flex-1" />
                <select id="subjectFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">All Subjects</option>
                </select>
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                                        <option value="expired">Expired</option>
                </select>
            </div>
            
            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="text-center py-8 hidden">
                <i class="fas fa-spinner fa-spin text-2xl text-emerald-600"></i>
                <p class="mt-2 text-gray-600">Loading exams...</p>
            </div>
            
            <!-- Tab Content: Upcoming Exams -->
            <div id="tab-upcoming" class="tab-content">
                <div id="upcoming-exams-container" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Exams will be loaded here -->
                </div>
            </div>
            
            <!-- Tab Content: Ongoing Exams -->
            <div id="tab-ongoing" class="tab-content hidden">
                <div id="ongoing-exams-container" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Exams will be loaded here -->
                </div>
            </div>
            
            <!-- Tab Content: Past Exams -->
            <div id="tab-past" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Past Exams</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody id="past-exams-table" class="bg-white divide-y divide-gray-200">
                                <!-- Past exams will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Tab Content: Available Exams -->
            <div id="tab-available" class="tab-content hidden">
                <div id="available-exams-container" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Available exams will be loaded here -->
                </div>
            </div>
            
            <!-- Exam Details Modal -->
            <div id="examModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden">
                <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 p-8 relative max-h-96 overflow-y-auto">
                    <button onclick="closeExamModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
                        <i class="fas fa-times w-6 h-6"></i>
                    </button>
                    <div id="modalContent">
                        <!-- Modal content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        let currentTab = 'upcoming';
        let allExams = [];
        let filteredExams = [];
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeTabs();
            initializeFilters();
            initializeEnrollmentForm();
            loadExams(currentTab);
        });
        
        function initializeTabs() {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tab = this.getAttribute('data-tab');
                    switchTab(tab);
                });
            });
        }
        
        function switchTab(tab) {
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('text-emerald-600', 'bg-emerald-50', 'border-b-2', 'border-emerald-600', 'rounded-t');
                b.classList.add('text-gray-600');
            });
            
            const activeBtn = document.querySelector(`[data-tab="${tab}"]`);
            activeBtn.classList.add('text-emerald-600', 'bg-emerald-50', 'border-b-2', 'border-emerald-600', 'rounded-t');
            activeBtn.classList.remove('text-gray-600');
            
            // Update tab content
            document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
            document.getElementById('tab-' + tab).classList.remove('hidden');
            
            currentTab = tab;
            loadExams(tab);
        }
        
        function initializeFilters() {
            const searchInput = document.getElementById('searchInput');
            const subjectFilter = document.getElementById('subjectFilter');
            const statusFilter = document.getElementById('statusFilter');
            
            [searchInput, subjectFilter, statusFilter].forEach(el => {
                el.addEventListener('input', () => {
                    applyFilters();
                });
            });
        }
        
        function initializeEnrollmentForm() {
            document.getElementById('enrollForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const key = document.getElementById('enrollKey').value.trim();
                const submitButton = this.querySelector('button[type="submit"]');
                
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
                            // Refresh the current tab
                            setTimeout(() => {
                                loadExams(currentTab);
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
            });
        }
        
        function loadExams(type) {
            showLoading(true);
            
            fetch('/api/students/getStudentExams.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allExams = data.exams;
                    filteredExams = [...allExams];
                    renderExams(type);
                    populateSubjectFilter();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to load exams', 'error');
            })
            .finally(() => {
                showLoading(false);
            });
        }
        
        function renderExams(type) {
            if (type === 'past') {
                renderPastExamsTable();
            } else {
                renderExamCards(type);
            }
        }
        
        function renderExamCards(type) {
            const container = document.getElementById(`${type}-exams-container`);
            container.innerHTML = '';
            
            if (filteredExams.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">No ${type} exams found</p>
                    </div>
                `;
                return;
            }
            
            filteredExams.forEach(exam => {
                const card = createExamCard(exam, type);
                container.appendChild(card);
            });
        }
        
        function createExamCard(exam, type) {
            const card = document.createElement('div');
            card.className = 'bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 flex flex-col gap-3';
            
            const statusColor = getStatusColor(exam.status);
            const actionButton = getActionButton(exam, type);
            
            card.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-blue-700">${exam.course_code}</span>
                    <span class="px-2 py-1 rounded ${statusColor} text-xs font-semibold">${exam.status}</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900">${exam.title}</h2>
                <p class="text-sm text-gray-600">${exam.course_title}</p>
                <div class="text-gray-600 text-sm">
                    <div>Start: ${exam.formatted_start}</div>
                    <div>Duration: ${exam.duration_minutes} minutes</div>
                    <div>Pass Mark: ${exam.pass_mark}%</div>
                </div>
                ${exam.time_remaining ? `
                    <div class="flex items-center gap-2 text-orange-600">
                        <i class="fas fa-clock"></i>
                        <span class="text-sm font-semibold">Time left: ${formatTimeRemaining(exam.time_remaining)}</span>
                    </div>
                ` : ''}
                <div class="mt-auto flex gap-2">
                    <button onclick="openExamModal(${exam.exam_id})" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                        View Details
                    </button>
                    ${actionButton}
                </div>
            `;
            
            return card;
        }
        
        function renderPastExamsTable() {
            const tbody = document.getElementById('past-exams-table');
            tbody.innerHTML = '';
            
            if (filteredExams.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No past exams found
                        </td>
                    </tr>
                `;
                return;
            }
            
            filteredExams.forEach(exam => {
                const row = document.createElement('tr');
                const statusColor = exam.result_status === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">${exam.title}</div>
                        <div class="text-sm text-gray-500">${exam.exam_code}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${exam.course_title}</div>
                        <div class="text-sm text-gray-500">${exam.course_code}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${new Date(exam.completed_at).toLocaleDateString()}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${exam.score_percentage ? Math.round(exam.score_percentage) + '%' : 'N/A'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 rounded ${statusColor} text-xs font-semibold">
                            ${exam.result_status || 'N/A'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="viewResult(${exam.result_id})" class="text-emerald-600 hover:text-emerald-900">
                            View Result
                        </button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
        }
        
        function getStatusColor(status) {
            switch (status.toLowerCase()) {
                case 'upcoming': return 'bg-blue-100 text-blue-800';
                case 'active': return 'bg-green-100 text-green-800';
                case 'completed': return 'bg-gray-100 text-gray-800';
                case 'expired': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }
        
                function getActionButton(exam, type) {
            if (type === 'upcoming' && exam.is_registered) {
                return `<button onclick="startExam(${exam.exam_id}, ${exam.registration_id})" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    Start Exam
                </button>`;
            } else if (type === 'ongoing' && exam.is_registered) {
                return `<button onclick="continueExam(${exam.exam_id}, ${exam.registration_id})" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    Continue Exam
                </button>`;
            } else if (type === 'available' && !exam.is_registered) {
                return `<button onclick="registerForExam(${exam.exam_id})" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    Register
                </button>`;
            }
            return '';
        }
        
        function formatTimeRemaining(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            if (hours > 0) {
                return `${hours}h ${minutes}m ${secs}s`;
            } else if (minutes > 0) {
                return `${minutes}m ${secs}s`;
            } else {
                return `${secs}s`;
            }
        }
        
        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const subjectFilter = document.getElementById('subjectFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            
            filteredExams = allExams.filter(exam => {
                const matchesSearch = !searchTerm || 
                    exam.title.toLowerCase().includes(searchTerm) ||
                    exam.course_title.toLowerCase().includes(searchTerm) ||
                    exam.exam_code.toLowerCase().includes(searchTerm);
                
                const matchesSubject = !subjectFilter || exam.course_code === subjectFilter;
                const matchesStatus = !statusFilter || exam.status.toLowerCase() === statusFilter;
                
                return matchesSearch && matchesSubject && matchesStatus;
            });
            
            renderExams(currentTab);
        }
        
        function populateSubjectFilter() {
            const subjectFilter = document.getElementById('subjectFilter');
            const subjects = [...new Set(allExams.map(exam => exam.course_code))];
            
            // Clear existing options except the first one
            subjectFilter.innerHTML = '<option value="">All Subjects</option>';
            
            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject;
                option.textContent = subject;
                subjectFilter.appendChild(option);
            });
        }
        
        function showLoading(show) {
            const indicator = document.getElementById('loadingIndicator');
            if (show) {
                indicator.classList.remove('hidden');
            } else {
                indicator.classList.add('hidden');
            }
        }
        
        function openExamModal(examId) {
            const exam = allExams.find(e => e.exam_id === examId);
            if (!exam) return;
            
            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = `
                <h2 class="text-2xl font-bold text-emerald-700 mb-4">${exam.title}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Exam Details</h3>
                        <ul class="space-y-1 text-sm text-gray-600">
                            <li><strong>Code:</strong> ${exam.exam_code}</li>
                            <li><strong>Course:</strong> ${exam.course_title} (${exam.course_code})</li>
                            <li><strong>Duration:</strong> ${exam.duration_minutes} minutes</li>
                            <li><strong>Pass Mark:</strong> ${exam.pass_mark}%</li>
                            <li><strong>Total Marks:</strong> ${exam.total_marks}</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Schedule</h3>
                        <ul class="space-y-1 text-sm text-gray-600">
                            <li><strong>Start:</strong> ${exam.formatted_start}</li>
                            <li><strong>End:</strong> ${exam.formatted_end}</li>
                            <li><strong>Status:</strong> <span class="px-2 py-1 rounded ${getStatusColor(exam.status)} text-xs font-semibold">${exam.status}</span></li>
                            ${exam.is_registered ? '<li><strong>Registration:</strong> <span class="text-green-600">Registered</span></li>' : ''}
                        </ul>
                    </div>
                </div>
                ${exam.description ? `
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-600">${exam.description}</p>
                    </div>
                ` : ''}
                <div class="flex gap-3">
                    <button onclick="closeExamModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                        Close
                    </button>
                    ${getModalActionButton(exam)}
                </div>
            `;
            
            document.getElementById('examModal').classList.remove('hidden');
        }
        
        function getModalActionButton(exam) {
            if (exam.status === 'Active' && exam.is_registered && !exam.is_completed) {
                return `<button onclick="startExam(${exam.exam_id}, ${exam.registration_id})" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    Start Exam
                </button>`;
            } else if (exam.status === 'Upcoming' && !exam.is_registered) {
                return `<button onclick="registerForExam(${exam.exam_id})" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    Register for Exam
                </button>`;
            } else if (exam.is_completed) {
                return `<button onclick="viewResult(${exam.result_id})" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    View Result
                </button>`;
            }
            return '';
        }
        
        function closeExamModal() {
            document.getElementById('examModal').classList.add('hidden');
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
                            closeExamModal();
                            // Refresh the current tab
                            setTimeout(() => {
                                loadExams(currentTab);
                            }, 1500);
                        } else {
                            showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while registering for the exam', 'error');
                    });
                }
            });
        }
        
        function startExam(examId, registrationId) {
            Swal.fire({
                title: 'Start Exam',
                text: 'Are you ready to start this exam? Make sure you have a stable internet connection.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Start Exam'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `take.php?registration_id=${registrationId}`;
                }
            });
        }
        
        function continueExam(examId, registrationId) {
            window.location.href = `take.php?registration_id=${registrationId}`;
        }
        
        function viewResult(resultId) {
            window.location.href = `../results/view.php?result_id=${resultId}`;
        }
        
        function showCourseExams(courseData) {
            const course = courseData.course;
            const exams = courseData.available_exams;
            
            let examsList = '';
            if (exams.length === 0) {
                examsList = '<p class="text-gray-500">No exams available for this course.</p>';
            } else {
                examsList = '<div class="space-y-3">';
                exams.forEach(exam => {
                    const statusColor = exam.status === 'Available' ? 'green' : 
                                      exam.status === 'Active' ? 'blue' : 
                                      exam.status === 'Already Registered' ? 'yellow' : 'gray';
                    
                    examsList += `
                        <div class="border rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <h4 class="font-semibold">${exam.title}</h4>
                                <p class="text-sm text-gray-600">Code: ${exam.exam_code}</p>
                                <p class="text-sm text-gray-600">Start: ${new Date(exam.start_datetime).toLocaleString()}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-${statusColor}-100 text-${statusColor}-800">
                                    ${exam.status}
                                </span>
                                ${exam.status === 'Available' ? 
                                    `<button onclick="registerForExam(${exam.exam_id})" class="block mt-2 bg-emerald-600 text-white px-3 py-1 rounded text-sm hover:bg-emerald-700">
                                        Register
                                    </button>` : ''}
                            </div>
                        </div>
                    `;
                });
                examsList += '</div>';
            }
            
            Swal.fire({
                title: `Course: ${course.title}`,
                html: `
                    <div class="text-left">
                        <p class="mb-4"><strong>Code:</strong> ${course.code}</p>
                        <p class="mb-4"><strong>Department:</strong> ${course.department_name}</p>
                        <p class="mb-4"><strong>Program:</strong> ${course.program_name}</p>
                        <h3 class="font-semibold mb-3">Available Exams:</h3>
                        ${examsList}
                    </div>
                `,
                width: '600px',
                showConfirmButton: false,
                showCloseButton: true
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
        
        // Make functions globally available
        window.openExamModal = openExamModal;
        window.closeExamModal = closeExamModal;
        window.registerForExam = registerForExam;
        window.startExam = startExam;
        window.continueExam = continueExam;
        window.viewResult = viewResult;
        window.showCourseExams = showCourseExams;
    </script>
</body>
</html>


