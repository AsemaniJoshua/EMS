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

            <!-- Results Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">All Results</h3>
                    <span id="resultCount" class="text-sm text-gray-500">Loading results...</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct/Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Loading indicator -->
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center">
                                        <i class="fas fa-spinner fa-spin mr-2 text-emerald-500"></i>
                                        <span class="text-gray-500">Loading results...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center">
                    <div class="text-sm text-gray-500" id="paginationInfo">
                        Showing <span id="firstResult">0</span> to <span id="lastResult">0</span> of <span id="totalResults">0</span> results
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

            <!-- Result Details Modal (hidden by default) -->
            <div id="resultModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Result Details</h3>
                        <button id="closeModal" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-6" id="modalContent">
                        <!-- Modal content will be populated dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables for pagination
            let currentPage = 1;
            const resultsPerPage = 20;
            let totalResults = 0;

            // Load results on page load
            fetchResults();

            // Set up event listeners
            document.getElementById('filterButton').addEventListener('click', function() {
                currentPage = 1; // Reset to first page when applying new filters
                fetchResults();
            });

            document.getElementById('exportResultsBtn').addEventListener('click', exportResults);
            document.getElementById('generateReportBtn').addEventListener('click', generateReport);
            document.getElementById('prevPage').addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    fetchResults();
                }
            });
            document.getElementById('nextPage').addEventListener('click', function() {
                currentPage++;
                fetchResults();
            });

            // Close modal when clicking the close button
            document.getElementById('closeModal').addEventListener('click', function() {
                document.getElementById('resultModal').classList.add('hidden');
            });

            // Also close modal when clicking outside of it
            document.getElementById('resultModal').addEventListener('click', function(event) {
                if (event.target === this) {
                    this.classList.add('hidden');
                }
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
             * Fetches results from the server based on current filters and pagination
             */
            function fetchResults() {
                const resultsTable = document.getElementById('resultsTable');
                resultsTable.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center">
                            <i class="fas fa-spinner fa-spin mr-2 text-emerald-500"></i>
                            <span class="text-gray-500">Loading results...</span>
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
                            populateResults(data.results);
                            updatePagination(data.pagination);
                        } else {
                            showNotification(data.message || 'Failed to load results', 'error');
                            resultsTable.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-red-500">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Error: ${data.message || 'Failed to load results'}
                            </td>
                        </tr>
                    `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching results:', error);
                        showNotification('Failed to load results. Please try again.', 'error');
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
             * Populates the results table with data
             */
            function populateResults(results) {
                const resultsTable = document.getElementById('resultsTable');
                resultsTable.innerHTML = '';

                if (results.length === 0) {
                    resultsTable.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No results found matching the filter criteria
                        </td>
                    </tr>
                `;
                    return;
                }

                results.forEach(result => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 transition-colors';

                    const passStatus = result.score_percentage >= 50;

                    row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${result.student_name}</div>
                        <div class="text-sm text-gray-500">${result.index_number}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${result.exam_title}</div>
                        <div class="text-sm text-gray-500">${result.exam_code}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${result.course_code}</div>
                        <div class="text-sm text-gray-500">${result.course_title}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${result.completed_at}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold ${passStatus ? 'text-emerald-600' : 'text-red-600'}">
                        ${result.score_percentage.toFixed(1)}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${result.correct_answers}/${result.total_questions}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${passStatus ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${passStatus ? 'Passed' : 'Failed'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900" onclick="viewResultDetails(${result.result_id})">
                            <i class="fas fa-eye mr-1"></i> View
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
                document.getElementById('resultCount').textContent = `${pagination.total_results} results found`;
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
        });

        /**
         * Views the details of a specific result
         */
        function viewResultDetails(resultId) {
            const modal = document.getElementById('resultModal');
            const modalContent = document.getElementById('modalContent');

            // Show loading indicator
            modalContent.innerHTML = `
            <div class="flex justify-center items-center py-8">
                <i class="fas fa-spinner fa-spin text-emerald-500 text-2xl"></i>
            </div>
        `;
            modal.classList.remove('hidden');

            // Fetch result details
            fetch(`../../api/results/getResultDetails.php?result_id=${resultId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        renderResultDetails(data.result, data.questions);
                    } else {
                        modalContent.innerHTML = `
                        <div class="text-center py-8 text-red-500">
                            <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                            <p>${data.message || 'Failed to load result details'}</p>
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching result details:', error);
                    modalContent.innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                        <p>Could not load result details. Please try again later.</p>
                    </div>
                `;
                });
        }

        /**
         * Renders the details of a specific result in the modal
         */
        function renderResultDetails(result, questions) {
            const modalContent = document.getElementById('modalContent');

            // Format the content
            let content = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-900">Exam Information</h4>
                    <div class="space-y-2">
                        <div><span class="font-medium">Exam:</span> ${result.exam_title}</div>
                        <div><span class="font-medium">Code:</span> ${result.exam_code}</div>
                        <div><span class="font-medium">Course:</span> ${result.course_code} - ${result.course_title}</div>
                        <div><span class="font-medium">Department:</span> ${result.department_name}</div>
                        <div><span class="font-medium">Date Completed:</span> ${result.completed_at}</div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-900">Student Information</h4>
                    <div class="space-y-2">
                        <div><span class="font-medium">Name:</span> ${result.student_name}</div>
                        <div><span class="font-medium">ID:</span> ${result.index_number}</div>
                        <div><span class="font-medium">Program:</span> ${result.program_name}</div>
                        <div><span class="font-medium">Score:</span> <span class="font-semibold ${result.score_percentage >= 50 ? 'text-emerald-600' : 'text-red-600'}">${result.score_percentage.toFixed(1)}%</span></div>
                        <div><span class="font-medium">Status:</span> <span class="px-2 py-1 text-xs font-semibold rounded-full ${result.score_percentage >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${result.score_percentage >= 50 ? 'Passed' : 'Failed'}</span></div>
                    </div>
                </div>
            </div>
        `;

            // Add questions and answers if available
            if (questions && questions.length > 0) {
                content += `
                <h4 class="text-lg font-semibold mb-4 text-gray-900">Questions & Answers</h4>
                <div class="space-y-6">
            `;

                questions.forEach((question, index) => {
                    const isCorrect = question.is_correct;
                    content += `
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="font-medium text-gray-900 mb-2">Question ${index + 1}: ${question.question_text}</div>
                        <div class="ml-4">
                            <div class="font-medium mt-2">Student's Answer:</div>
                            <div class="flex items-center ml-2 mt-1">
                                <span class="mr-2 ${isCorrect ? 'text-emerald-500' : 'text-red-500'}">
                                    <i class="fas fa-${isCorrect ? 'check-circle' : 'times-circle'}"></i>
                                </span>
                                <span>${question.student_answer}</span>
                            </div>
                            
                            ${!isCorrect ? `
                                <div class="font-medium mt-2 text-emerald-600">Correct Answer:</div>
                                <div class="ml-2 mt-1 text-emerald-600">${question.correct_answer}</div>
                            ` : ''}
                        </div>
                    </div>
                `;
                });

                content += `</div>`;
            }

            // Add actions buttons
            content += `
            <div class="mt-8 flex justify-end space-x-4">
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300" onclick="document.getElementById('resultModal').classList.add('hidden')">
                    Close
                </button>
                <button class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700" onclick="printResultDetails(${result.result_id})">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        `;

            modalContent.innerHTML = content;
        }

        /**
         * Prints the result details
         */
        function printResultDetails(resultId) {
            // Open a new window with print-friendly version
            window.open(`../../api/results/printResult.php?result_id=${resultId}`, '_blank');
        }

        /**
         * Exports the filtered results to CSV
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
         * Generates a comprehensive report
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
    </script>
</body>

</html>