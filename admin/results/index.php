<?php
include_once __DIR__ . '/../../api/login/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';

$currentPage = 'results';
$pageTitle = "Student Results";
$breadcrumb = "Results";

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch stats
$totalResults = 0;
$passRate = 0;
$avgScore = 0;
$pendingReviews = 0;

// Total results
$stmt = $conn->query("SELECT COUNT(*) as count FROM results");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $totalResults = $row['count'];
}

// Pass rate
$stmt = $conn->query("SELECT 
    ROUND((COUNT(CASE WHEN score_percentage >= 50 THEN 1 END) / COUNT(*)) * 100, 1) as pass_rate 
    FROM results");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $passRate = $row['pass_rate'] ?? 0;
}

// Average score
$stmt = $conn->query("SELECT AVG(score_percentage) as avg_score FROM results");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $avgScore = round($row['avg_score'] ?? 0, 1);
}

// Pending reviews (results with less than 50% score)
$stmt = $conn->query("SELECT COUNT(*) as count FROM results WHERE score_percentage < 50");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pendingReviews = $row['count'];
}

// Fetch departments, programs, courses for filters
$departments = [];
$stmt = $conn->query("SELECT department_id, name FROM departments ORDER BY name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $departments[] = $row;
}

$programs = [];
$stmt = $conn->query("SELECT program_id, name FROM programs ORDER BY name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $programs[] = $row;
}

$courses = [];
$stmt = $conn->query("SELECT course_id, code, title FROM courses ORDER BY code");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $courses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <button onclick="window.location.href='../dashboard/index.php'" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo $pageTitle; ?></h1>
                        <p class="mt-1 text-sm text-gray-500">View, analyze, and manage student exam results</p>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Results -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Results</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($totalResults); ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pass Rate -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pass Rate</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $passRate; ?>%</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average Score -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $avgScore; ?>%</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Reviews -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-50 rounded-lg p-3">
                                <i class="fas fa-flag text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Failed Results</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $pendingReviews; ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Filter Results</h3>
                </div>
                <div class="p-6">
                    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="searchStudent" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                            <input type="text" id="searchStudent" name="student" placeholder="Student Name or ID" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="searchExam" class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
                            <input type="text" id="searchExam" name="exam" placeholder="Exam Title or Code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="filterDepartment" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <select id="filterDepartment" name="department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['department_id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="filterProgram" class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                            <select id="filterProgram" name="program_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">All Programs</option>
                                <?php foreach ($programs as $prog): ?>
                                    <option value="<?php echo $prog['program_id']; ?>"><?php echo htmlspecialchars($prog['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="filterCourse" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                            <select id="filterCourse" name="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">All Courses</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['code'] . ' - ' . $course['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="filterStatus" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="filterStatus" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">All Results</option>
                                <option value="pass">Passed (≥50%)</option>
                                <option value="fail">Failed (<50%)< /option>
                            </select>
                        </div>

                        <div>
                            <label for="filterDate" class="block text-sm font-medium text-gray-700 mb-1">Date (From)</label>
                            <input type="date" id="filterDateFrom" name="date_from" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="filterDate" class="block text-sm font-medium text-gray-700 mb-1">Date (To)</label>
                            <input type="date" id="filterDateTo" name="date_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>

                        <div class="flex items-end">
                            <button type="button" id="filterButton" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 w-full flex items-center justify-center">
                                <i class="fas fa-filter mr-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Results Management</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <p class="text-gray-600">Export results or generate comprehensive reports</p>
                        <div class="flex gap-3">
                            <button id="exportResultsBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center">
                                <i class="fas fa-file-export mr-2"></i>
                                Export to CSV
                            </button>
                            <button id="generateReportBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exams Results Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Exam Results</h3>
                    <span id="resultCount" class="text-sm text-gray-500">Loading exams...</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Loading indicator -->
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center">
                                        <i class="fas fa-spinner fa-spin mr-2 text-emerald-500"></i>
                                        <span class="text-gray-500">Loading exam results...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center">
                    <div class="text-sm text-gray-500" id="paginationInfo">
                        Showing <span id="firstResult">0</span> to <span id="lastResult">0</span> of <span id="totalResults">0</span> exams
                    </div>
                    <div class="flex space-x-2">
                        <button id="prevPage" class="px-3 py-1 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-left mr-1"></i> Previous
                        </button>
                        <button id="nextPage" class="px-3 py-1 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            Next <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- No longer need the exam results modal since we use a dedicated page -->

            <!-- Result modals removed in favor of dedicated pages -->
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables for pagination
            let currentPage = 1;
            const resultsPerPage = 50;
            let totalResults = 0;
            let currentExamId = null;

            // Load results on page load
            fetchExamResults();

            // Set up event listeners
            document.getElementById('filterButton').addEventListener('click', function() {
                currentPage = 1; // Reset to first page when applying new filters
                fetchExamResults();
            });

            document.getElementById('exportResultsBtn').addEventListener('click', exportResults);
            document.getElementById('generateReportBtn').addEventListener('click', generateReport);
            document.getElementById('prevPage').addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    fetchExamResults();
                }
            });
            document.getElementById('nextPage').addEventListener('click', function() {
                currentPage++;
                fetchExamResults();
            });

            // Set up cascading dropdown filters (department -> program -> course)
            document.getElementById('filterDepartment').addEventListener('change', function() {
                const departmentId = this.value;
                const programSelect = document.getElementById('filterProgram');
                const courseSelect = document.getElementById('filterCourse');

                // Reset program and course dropdowns
                programSelect.innerHTML = '<option value="">All Programs</option>';
                courseSelect.innerHTML = '<option value="">All Courses</option>';

                if (departmentId) {
                    // Fetch programs for the selected department
                    programSelect.disabled = true;

                    axios.get('../../api/exams/getProgramsByDepartment.php', {
                            params: {
                                departmentId: departmentId
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                const programs = response.data.programs;
                                programs.forEach(program => {
                                    const option = document.createElement('option');
                                    option.value = program.program_id;
                                    option.textContent = program.name;
                                    programSelect.appendChild(option);
                                });
                            }
                            programSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching programs:', error);
                            showNotification('Failed to load programs', 'error');
                            programSelect.disabled = false;
                        });
                }
            });

            document.getElementById('filterProgram').addEventListener('change', function() {
                const programId = this.value;
                const departmentId = document.getElementById('filterDepartment').value;
                const courseSelect = document.getElementById('filterCourse');

                // Reset course dropdown
                courseSelect.innerHTML = '<option value="">All Courses</option>';

                if (programId) {
                    // Fetch courses for the selected program
                    courseSelect.disabled = true;

                    axios.get('../../api/exams/getCoursesByProgram.php', {
                            params: {
                                programId: programId,
                                departmentId: departmentId
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                const courses = response.data.courses;
                                courses.forEach(course => {
                                    const option = document.createElement('option');
                                    option.value = course.course_id;
                                    option.textContent = `${course.code} - ${course.title}`;
                                    courseSelect.appendChild(option);
                                });
                            }
                            courseSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching courses:', error);
                            showNotification('Failed to load courses', 'error');
                            courseSelect.disabled = false;
                        });
                }
            });

            /**
             * Fetches exam results from the server based on current filters and pagination
             */
            function fetchExamResults() {
                const resultsTable = document.getElementById('resultsTable');
                resultsTable.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center">
                            <i class="fas fa-spinner fa-spin mr-2 text-emerald-500"></i>
                            <span class="text-gray-500">Loading exam results...</span>
                        </div>
                    </td>
                </tr>
            `;

                const form = document.getElementById('filterForm');
                const formData = new FormData(form);
                formData.append('page', currentPage);
                formData.append('resultsPerPage', resultsPerPage);

                // Send fetch request
                fetch('../../api/results/getResults.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            populateExamResults(data.results);
                            updatePagination(data.pagination);
                        } else {
                            showNotification(data.message || 'Failed to load exam results', 'error');
                            resultsTable.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-red-500">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Error: ${data.message || 'Failed to load exam results'}
                            </td>
                        </tr>
                    `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching exam results:', error);
                        showNotification('Failed to load exam results. Please try again.', 'error');
                        resultsTable.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-red-500">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Error: Could not connect to server
                        </td>
                    </tr>
                `;
                    });
            }

            /**
             * Populates the exams results table with data
             */
            function populateExamResults(results) {
                const resultsTable = document.getElementById('resultsTable');
                resultsTable.innerHTML = '';

                if (results.length === 0) {
                    resultsTable.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No exam results found matching the filter criteria
                        </td>
                    </tr>
                `;
                    return;
                }

                results.forEach(exam => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 transition-colors';

                    const passRate = exam.pass_count > 0 ?
                        Math.round((exam.pass_count / exam.total_students) * 100) : 0;

                    const passRateClass = passRate >= 70 ? 'text-emerald-600' :
                        passRate >= 50 ? 'text-yellow-600' : 'text-red-600';

                    row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${escapeHtml(exam.exam_title)}</div>
                        <div class="text-sm text-gray-500">Code: ${escapeHtml(exam.exam_code)}</div>
                        <div class="text-xs text-gray-400">${escapeHtml(exam.department_name)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${escapeHtml(exam.course_code)}</div>
                        <div class="text-sm text-gray-500">${escapeHtml(exam.course_title)}</div>
                        <div class="text-xs text-gray-400">${escapeHtml(exam.program_name)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        <div class="text-sm font-medium">${exam.total_students}</div>
                        <div class="text-xs text-gray-400">Taken: ${exam.submitted_results || 0}</div>
                        <div class="text-xs text-gray-400">Last: ${exam.last_completed ? formatDate(exam.last_completed) : 'N/A'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="mr-4">
                                <div class="text-sm font-medium">Avg: ${parseFloat(exam.avg_score).toFixed(1)}%</div>
                                <div class="text-xs text-gray-500">Range: ${parseFloat(exam.min_score).toFixed(1)}% - ${parseFloat(exam.max_score).toFixed(1)}%</div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold ${passRateClass}">Pass: ${passRate}%</div>
                                <div class="text-xs text-gray-500">${exam.pass_count} pass, ${exam.fail_count} fail</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="examResults.php?exam_id=${exam.exam_id}" class="text-blue-600 hover:text-blue-900 block mb-2">
                            <i class="fas fa-chart-bar mr-1"></i> Analytics
                        </a>
                        <button class="text-green-600 hover:text-green-900 block" onclick="exportExamResults(${exam.exam_id})">
                            <i class="fas fa-file-export mr-1"></i> Export
                        </button>
                    </td>
                `;

                    resultsTable.appendChild(row);
                });
            }

            /**
             * Updates the pagination controls and info
             */
            function updatePagination(pagination) {
                document.getElementById('resultCount').textContent = `${pagination.total_results} exams found`;
                document.getElementById('firstResult').textContent = pagination.first_result;
                document.getElementById('lastResult').textContent = pagination.last_result;
                document.getElementById('totalResults').textContent = pagination.total_results;

                // Update button states
                document.getElementById('prevPage').disabled = pagination.current_page <= 1;
                document.getElementById('nextPage').disabled = pagination.current_page >= pagination.total_pages;

                // Update current page reference
                currentPage = pagination.current_page;
                totalResults = pagination.total_results;
            }

            /**
             * Format date to a more readable format
             */
            function formatDate(dateString) {
                const date = new Date(dateString);
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                return date.toLocaleDateString('en-US', options);
            }

            /**
             * Escape HTML to prevent XSS
             */
            function escapeHtml(text) {
                if (!text) return '';
                return text
                    .toString()
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        });
        /**
         * Redirects to the detailed exam results page
         */
        function viewExamResults(examId) {
            window.location.href = `examResults.php?exam_id=${examId}`;
        }

        /**
         * Renders the detailed exam results in the modal
         */
        function renderExamResults(data) {
            const modalContent = document.getElementById('examResultsContent');
            const modalTitle = document.getElementById('examResultsTitle');
            const exam = data.exam;
            const stats = data.statistics;
            const results = data.results;
            const distribution = data.distribution;

            // Update modal title
            modalTitle.textContent = `Results: ${exam.title} (${exam.exam_code})`;

            // Format the content - start with exam info and statistics
            let content = `
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Exam Information -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900">Exam Information</h3>
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="mb-2"><span class="font-medium">Title:</span> ${escapeHtml(exam.title)}</div>
                                    <div class="mb-2"><span class="font-medium">Code:</span> ${escapeHtml(exam.exam_code)}</div>
                                    <div class="mb-2"><span class="font-medium">Course:</span> ${escapeHtml(exam.course_code)} - ${escapeHtml(exam.course_title)}</div>
                                    <div class="mb-2"><span class="font-medium">Department:</span> ${escapeHtml(exam.department_name)}</div>
                                </div>
                                <div>
                                    <div class="mb-2"><span class="font-medium">Program:</span> ${escapeHtml(exam.program_name)}</div>
                                    <div class="mb-2"><span class="font-medium">Date:</span> ${exam.date || 'N/A'}</div>
                                    <div class="mb-2"><span class="font-medium">Duration:</span> ${exam.duration || 'N/A'} minutes</div>
                                    <div class="mb-2"><span class="font-medium">Questions:</span> ${exam.total_questions || 'N/A'}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Key Statistics -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-900">Key Statistics</h3>
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <div class="mb-2">
                                <span class="font-medium">Registered Students:</span> 
                                <span class="text-blue-600 font-semibold">${exam.registered_students}</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-medium">Completed Exams:</span> 
                                <span class="text-blue-600 font-semibold">${exam.submitted_results}</span> 
                                (${exam.registered_students > 0 ? Math.round((exam.submitted_results / exam.registered_students) * 100) : 0}%)
                            </div>
                            <div class="mb-2">
                                <span class="font-medium">Average Score:</span> 
                                <span class="font-semibold">${parseFloat(stats.avg_score).toFixed(1)}%</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-medium">Score Range:</span> 
                                <span>${parseFloat(stats.min_score).toFixed(1)}% - ${parseFloat(stats.max_score).toFixed(1)}%</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-medium">Pass Rate:</span> 
                                <span class="font-semibold ${stats.pass_rate >= 70 ? 'text-emerald-600' : stats.pass_rate >= 50 ? 'text-yellow-600' : 'text-red-600'}">${parseFloat(stats.pass_rate).toFixed(1)}%</span>
                                <span>(${stats.pass_count} passed, ${stats.fail_count} failed)</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Score Distribution -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Score Distribution</h3>
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex flex-wrap">
                            ${renderScoreDistribution(distribution)}
                        </div>
                    </div>
                </div>
                
                <!-- Student Results Search and Filter -->
                <div class="mb-6 bg-white rounded-lg border border-gray-200 p-4">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Filter Student Results</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                            <input type="text" id="studentSearch" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Name or ID">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Score Range</label>
                            <div class="flex items-center">
                                <input type="number" id="scoreMin" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0" max="100" value="0">
                                <span class="mx-2">-</span>
                                <input type="number" id="scoreMax" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0" max="100" value="100">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">All Results</option>
                                <option value="pass">Passed</option>
                                <option value="fail">Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button id="applyFilters" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors duration-200" onclick="filterStudentResults()">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
                
                <!-- Student Results Table -->
                <h3 class="text-lg font-semibold mb-4 text-gray-900">Student Results (${results.length})</h3>
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct/Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Completed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentResultsTable">
                                ${renderStudentResultsRows(results)}
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors duration-200" onclick="document.getElementById('examResultsModal').classList.add('hidden')">
                        Close
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200" onclick="exportExamResults(${exam.exam_id})">
                        <i class="fas fa-file-export mr-2"></i>Export Results
                    </button>
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors duration-200" onclick="generateExamReport(${exam.exam_id})">
                        <i class="fas fa-chart-bar mr-2"></i>Generate Report
                    </button>
                </div>
            `;

            // Set the content and initialize filters
            modalContent.innerHTML = content;

            // Store results for filtering
            modalContent.dataset.results = JSON.stringify(results);

            // Add filter event listeners
            document.getElementById('applyFilters').addEventListener('click', filterStudentResults);
            document.getElementById('studentSearch').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    filterStudentResults();
                }
            });
        }

        /**
         * Renders the score distribution chart
         */
        function renderScoreDistribution(distribution) {
            if (!distribution || distribution.length === 0) {
                return '<div class="w-full text-center py-4 text-gray-500">No data available</div>';
            }

            // Find the max count for scaling
            const maxCount = Math.max(...distribution.map(d => d.count));

            let html = '<div class="w-full flex flex-col items-center">';
            html += '<div class="w-full flex justify-between">';

            // Sort ranges for display
            const sortedDist = [...distribution].sort((a, b) => {
                // Extract the lower number from the range and sort in ascending order
                const aNum = parseInt(a.range_label.split('-')[0]);
                const bNum = parseInt(b.range_label.split('-')[0]);
                return bNum - aNum; // Sort in descending order
            });

            sortedDist.forEach(range => {
                const heightPercentage = (range.count / maxCount) * 100;
                const colorClass = range.range_label === '90-100' || range.range_label === '80-89' || range.range_label === '70-79' ?
                    'bg-emerald-500' :
                    range.range_label === '60-69' || range.range_label === '50-59' ?
                    'bg-blue-500' :
                    'bg-red-500';

                html += `
                    <div class="mx-1 flex flex-col items-center" style="min-width: 40px;">
                        <div class="text-xs mb-1 text-gray-600">${range.count}</div>
                        <div class="${colorClass} rounded-t w-8" style="height: ${Math.max(heightPercentage, 5)}%; min-height: 10px;"></div>
                        <div class="text-xs mt-1">${range.range_label}%</div>
                    </div>
                `;
            });

            html += '</div></div>';
            return html;
        }

        /**
         * Renders the student results table rows
         */
        function renderStudentResultsRows(results) {
            if (!results || results.length === 0) {
                return `
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No student results found
                        </td>
                    </tr>
                `;
            }

            let html = '';
            results.forEach(result => {
                const isPassed = result.score_percentage >= 50;
                const statusClass = isPassed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';

                html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${escapeHtml(result.student_name)}</div>
                            <div class="text-xs text-gray-500">${escapeHtml(result.index_number)}</div>
                            ${result.email ? `<div class="text-xs text-gray-500">${escapeHtml(result.email)}</div>` : ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${escapeHtml(result.program_name)}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold ${isPassed ? 'text-emerald-600' : 'text-red-600'}">
                            ${parseFloat(result.score_percentage).toFixed(1)}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${result.correct_answers}/${result.total_questions}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${isPassed ? 'Passed' : 'Failed'}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${result.completed_at}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="viewResult.php?id=${result.result_id}" class="text-blue-600 hover:text-blue-900 mr-2">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                            <button class="text-green-600 hover:text-green-900" onclick="printResultDetails(${result.result_id})">
                                <i class="fas fa-print mr-1"></i> Print
                            </button>
                        </td>
                    </tr>
                `;
            });

            return html;
        }

        /**
         * Filters the student results based on user input
         */
        function filterStudentResults() {
            const modalContent = document.getElementById('examResultsContent');
            const studentSearch = document.getElementById('studentSearch').value.toLowerCase();
            const scoreMin = parseFloat(document.getElementById('scoreMin').value) || 0;
            const scoreMax = parseFloat(document.getElementById('scoreMax').value) || 100;
            const status = document.getElementById('statusFilter').value;

            // Get the stored results
            const results = JSON.parse(modalContent.dataset.results || '[]');

            // Apply filters
            const filteredResults = results.filter(result => {
                // Student name/ID filter
                const studentMatches = !studentSearch ||
                    result.student_name.toLowerCase().includes(studentSearch) ||
                    result.index_number.toLowerCase().includes(studentSearch);

                // Score range filter
                const scoreMatches = result.score_percentage >= scoreMin &&
                    result.score_percentage <= scoreMax;

                // Status filter
                const statusMatches = !status ||
                    (status === 'pass' && result.score_percentage >= 50) ||
                    (status === 'fail' && result.score_percentage < 50);

                return studentMatches && scoreMatches && statusMatches;
            });

            // Update the table
            document.getElementById('studentResultsTable').innerHTML = renderStudentResultsRows(filteredResults);
        }

        /**
         * Views the details of a specific student result
         */
        /**
         * Redirects to the result details page
         */
        function viewResultDetails(resultId) {
            // Redirect to the result details page
            window.location.href = `viewResult.php?id=${resultId}`;
        }

        /**
         * Prints the result details
         */
        function printResultDetails(resultId) {
            // Open a new window with print-friendly version
            window.open(`../../api/results/printResult.php?result_id=${resultId}`, '_blank');
        }

        /**
         * Exports results for a specific exam
         */
        function exportExamResults(examId) {
            // Get filter values if in exam results modal
            let queryParams = new URLSearchParams();
            queryParams.append('exam_id', examId);

            if (document.getElementById('examResultsContent').contains(document.getElementById('studentSearch'))) {
                const studentSearch = document.getElementById('studentSearch').value;
                const scoreMin = document.getElementById('scoreMin').value;
                const scoreMax = document.getElementById('scoreMax').value;
                const status = document.getElementById('statusFilter').value;

                if (studentSearch) queryParams.append('student', studentSearch);
                if (scoreMin) queryParams.append('score_min', scoreMin);
                if (scoreMax) queryParams.append('score_max', scoreMax);
                if (status) queryParams.append('status', status);
            }

            // Open the export endpoint in a new tab (will trigger download)
            window.open(`../../api/results/exportResults.php?${queryParams.toString()}`, '_blank');

            showNotification('Exporting exam results...', 'info');
        }

        /**
         * Generates a comprehensive report for a specific exam
         */
        function generateExamReport(examId) {
            // Get filter values if in exam results modal
            let queryParams = new URLSearchParams();
            queryParams.append('exam_id', examId);

            if (document.getElementById('examResultsContent').contains(document.getElementById('studentSearch'))) {
                const studentSearch = document.getElementById('studentSearch').value;
                const scoreMin = document.getElementById('scoreMin').value;
                const scoreMax = document.getElementById('scoreMax').value;
                const status = document.getElementById('statusFilter').value;

                if (studentSearch) queryParams.append('student', studentSearch);
                if (scoreMin) queryParams.append('score_min', scoreMin);
                if (scoreMax) queryParams.append('score_max', scoreMax);
                if (status) queryParams.append('status', status);
            }

            // Open the report generator in a new tab
            window.open(`../../api/results/generateReport.php?${queryParams.toString()}`, '_blank');

            showNotification('Generating exam report...', 'info');
        }

        /**
         * Exports the filtered results to CSV (all exams)
         */
        function exportResults() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);

            // Build the query string
            const queryString = new URLSearchParams(formData).toString();

            // Open the export endpoint in a new tab (will trigger download)
            window.open(`../../api/results/exportResults.php?${queryString}`, '_blank');

            showNotification('Exporting results...', 'info');
        }

        /**
         * Generates a comprehensive report (all exams)
         */
        function generateReport() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);

            // Build the query string
            const queryString = new URLSearchParams(formData).toString();

            // Open the report generator in a new tab
            window.open(`../../api/results/generateReport.php?${queryString}`, '_blank');

            showNotification('Generating report...', 'info');
        }

        /**
         * Shows a toast notification
         */
        function showNotification(message, type = 'info') {
            Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }

        /**
         * Utility function to escape HTML special characters
         */
        function escapeHtml(unsafe) {
            if (typeof unsafe !== 'string') return unsafe;
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>

</html>