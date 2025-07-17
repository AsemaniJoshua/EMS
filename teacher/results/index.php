<?php
require_once __DIR__ . '/../../api/login/teacher/teacherSessionCheck.php';
require_once __DIR__ . '/../../api/config/database.php';
require_once __DIR__ . '/../components/teacherSidebar.php';
require_once __DIR__ . '/../components/teacherHeader.php';

$currentPage = 'results';
$pageTitle = "Exam Results";

// Check teacher session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header('Location: /teacher/login/');
    exit;
}

// Database connection
$db = new Database();
$conn = $db->getConnection();
$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher's exam statistics
$totalExams = 0;
$totalStudents = 0;
$avgScore = 0;
$pendingGrading = 0;

// Total exams conducted by this teacher
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM exams WHERE teacher_id = :teacher_id");
$stmt->execute(['teacher_id' => $teacher_id]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $totalExams = $row['count'];
}

// Total students who took this teacher's exams
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT er.student_id) as count 
    FROM results r 
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id 
    WHERE e.teacher_id = :teacher_id
");
$stmt->execute(['teacher_id' => $teacher_id]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $totalStudents = $row['count'];
}

// Average score across all teacher's exams
$stmt = $conn->prepare("
    SELECT AVG(r.score_percentage) as avg_score 
    FROM results r 
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id 
    WHERE e.teacher_id = :teacher_id
");
$stmt->execute(['teacher_id' => $teacher_id]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $avgScore = round($row['avg_score'] ?? 0, 1);
}

// Exams with pending grading (completed but not yet graded)
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM exams e 
    WHERE e.teacher_id = :teacher_id 
    AND e.status = 'Completed' 
    AND NOT EXISTS (
        SELECT 1 FROM exam_registrations er 
        JOIN results r ON er.registration_id = r.registration_id 
        WHERE er.exam_id = e.exam_id
    )
");
$stmt->execute(['teacher_id' => $teacher_id]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pendingGrading = $row['count'];
}

// Fetch teacher's courses for filter dropdown
$courses = [];
$stmt = $conn->prepare("
    SELECT DISTINCT c.course_id, c.code, c.title 
    FROM courses c 
    JOIN exams e ON c.course_id = e.course_id 
    WHERE e.teacher_id = :teacher_id 
    ORDER BY c.code
");
$stmt->execute(['teacher_id' => $teacher_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Teacher</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderTeacherSidebar($currentPage); ?>
    <?php renderTeacherHeader(); ?>

    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo $pageTitle; ?></h1>
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    View and analyze results for exams you have conducted. Track student performance and export data.
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Exams -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-clipboard-list text-emerald-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Exams Conducted</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $totalExams; ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Students Examined</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $totalStudents; ?></div>
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
                            <div class="flex-shrink-0 bg-yellow-50 rounded-lg p-3">
                                <i class="fas fa-star text-yellow-500 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg. Score</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $avgScore; ?>%</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Grading -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-hourglass-half text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Grading</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $pendingGrading; ?></div>
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
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-filter mr-2 text-blue-600"></i>
                        Filter Results
                    </h3>
                </div>
                <div class="p-6">
                    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                            <select name="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">All Courses</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['course_id']; ?>">
                                        <?php echo htmlspecialchars($course['code'] . ' - ' . $course['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">All Statuses</option>
                                <option value="Completed">Completed</option>
                                <option value="Approved">Approved</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                            <select name="date_range" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">All Time</option>
                                <option value="last_week">Last Week</option>
                                <option value="last_month">Last Month</option>
                                <option value="last_3_months">Last 3 Months</option>
                                <option value="last_6_months">Last 6 Months</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="button" id="filterButton" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Action Buttons -->
            <!-- <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-tools mr-2 text-emerald-600"></i>
                        Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-4">
                        <button id="exportResultsBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Export Results
                        </button>
                        <button id="generateReportBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Generate Report
                        </button>
                    </div>
                </div>
            </div> -->

            <!-- Exams Results Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-table mr-2 text-purple-600"></i>
                        Your Exam Results
                    </h3>
                    <span id="resultCount" class="text-sm text-gray-500">Loading...</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pass Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Results will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button id="prevPageMobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </button>
                        <button id="nextPageMobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span id="firstResult" class="font-medium">0</span> to <span id="lastResult" class="font-medium">0</span> of
                                <span id="totalResults" class="font-medium">0</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px" aria-label="Pagination">
                                <button id="prevPage" class="relative inline-flex items-center px-2 py-2 rounded-l-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="nextPage" class="relative inline-flex items-center px-2 py-2 rounded-r-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables for pagination
            let currentPage = 1;
            const resultsPerPage = 10;
            let totalResults = 0;

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

            // Mobile pagination
            document.getElementById('prevPageMobile').addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    fetchExamResults();
                }
            });

            document.getElementById('nextPageMobile').addEventListener('click', function() {
                currentPage++;
                fetchExamResults();
            });

            /**
             * Fetches exam results from the server based on current filters and pagination
             */
            function fetchExamResults() {
                const resultsTable = document.getElementById('resultsTable');
                resultsTable.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Loading exam results...
                        </td>
                    </tr>
                `;

                const form = document.getElementById('filterForm');
                const formData = new FormData(form);
                formData.append('page', currentPage);
                formData.append('resultsPerPage', resultsPerPage);

                // Send fetch request to teacher-specific endpoint
                fetch('/api/results/getTeacherResults.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            populateExamResults(data.exams);
                            updatePagination(data.pagination);
                        } else {
                            resultsTable.innerHTML = `
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-red-500">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Error loading results: ${data.message}
                                </td>
                            </tr>
                        `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resultsTable.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-red-500">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Error loading results. Please try again.
                            </td>
                        </tr>
                    `;
                    });
            }

            /**
             * Populates the exam results table with data
             */
            function populateExamResults(exams) {
                const resultsTable = document.getElementById('resultsTable');
                resultsTable.innerHTML = '';

                if (exams.length === 0) {
                    resultsTable.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-500 mb-2">No exam results found</p>
                                    <p class="text-sm text-gray-400">Try adjusting your filters or create your first exam.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                exams.forEach(exam => {
                    const statusColor = getStatusColor(exam.status);
                    const passRate = exam.total_students > 0 ? Math.round((exam.passed_students / exam.total_students) * 100) : 0;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${escapeHtml(exam.title)}</div>
                            <div class="text-sm text-gray-500">${escapeHtml(exam.exam_code)}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${escapeHtml(exam.course_code)}</div>
                            <div class="text-sm text-gray-500">${escapeHtml(exam.course_title)}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${formatDate(exam.start_datetime)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${exam.total_students || 0}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${exam.avg_score ? exam.avg_score + '%' : 'N/A'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${passRate}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColor}">
                                ${exam.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button onclick="viewExamResults(${exam.exam_id})" class="text-blue-600 hover:text-blue-900" title="View Results">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="exportExamResults(${exam.exam_id})" class="text-emerald-600 hover:text-emerald-900" title="Export">
                                <i class="fas fa-download"></i>
                            </button>
                            <button onclick="generateExamReport(${exam.exam_id})" class="text-purple-600 hover:text-purple-900" title="Generate Report">
                                <i class="fas fa-chart-bar"></i>
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
                const prevButtons = [document.getElementById('prevPage'), document.getElementById('prevPageMobile')];
                const nextButtons = [document.getElementById('nextPage'), document.getElementById('nextPageMobile')];

                prevButtons.forEach(btn => {
                    btn.disabled = pagination.current_page <= 1;
                    btn.classList.toggle('opacity-50', pagination.current_page <= 1);
                });

                nextButtons.forEach(btn => {
                    btn.disabled = pagination.current_page >= pagination.total_pages;
                    btn.classList.toggle('opacity-50', pagination.current_page >= pagination.total_pages);
                });

                // Update current page reference
                currentPage = pagination.current_page;
                totalResults = pagination.total_results;
            }

            /**
             * Get status color classes
             */
            function getStatusColor(status) {
                switch (status) {
                    case 'Completed':
                        return 'bg-emerald-100 text-emerald-800';
                    case 'Approved':
                        return 'bg-blue-100 text-blue-800';
                    case 'Draft':
                        return 'bg-gray-100 text-gray-800';
                    case 'Pending':
                        return 'bg-yellow-100 text-yellow-800';
                    default:
                        return 'bg-gray-100 text-gray-800';
                }
            }

            /**
             * Format date to a more readable format
             */
            function formatDate(dateString) {
                const date = new Date(dateString);
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                };
                return date.toLocaleDateString('en-US', options);
            }

            /**
             * Escape HTML to prevent XSS
             */
            function escapeHtml(text) {
                if (!text) return '';
                return text
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
         * Exports results for a specific exam
         */
        function exportExamResults(examId) {
            let queryParams = new URLSearchParams();
            queryParams.append('exam_id', examId);

            // Open the export endpoint in a new tab (will trigger download)
            window.open(`/api/results/exportTeacherResults.php?${queryParams.toString()}`, '_blank');

            showNotification('Exporting exam results...', 'info');
        }

        /**
         * Generates a comprehensive report for a specific exam
         */
        function generateExamReport(examId) {
            let queryParams = new URLSearchParams();
            queryParams.append('exam_id', examId);

            // Open the report generator in a new tab
            window.open(`/api/results/generateTeacherReport.php?${queryParams.toString()}`, '_blank');

            showNotification('Generating exam report...', 'info');
        }

        /**
         * Exports the filtered exam results summary to CSV
         */
        function exportResults() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            formData.append('export_type', 'teacher_exams_summary');

            // Build the query string
            const queryString = new URLSearchParams(formData).toString();

            // Open the export endpoint in a new tab (will trigger download)
            window.open(`/api/results/exportTeacherResults.php?${queryString}`, '_blank');

            showNotification('Exporting exam results summary...', 'info');
        }

        /**
         * Generates a comprehensive report for the exams summary
         */
        function generateReport() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            formData.append('report_type', 'teacher_exams_summary');

            // Build the query string
            const queryString = new URLSearchParams(formData).toString();

            // Open the report generator in a new tab
            window.open(`/api/results/generateTeacherReport.php?${queryString}`, '_blank');

            showNotification('Generating exam results summary report...', 'info');
        }

        /**
         * Shows a toast notification
         */
        function showNotification(message, type = 'info') {
            const icons = {
                success: 'success',
                error: 'error',
                info: 'info',
                warning: 'warning'
            };

            Swal.fire({
                title: '',
                text: message,
                icon: icons[type] || 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    </script>
</body>

</html>