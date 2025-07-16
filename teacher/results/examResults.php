<?php
require_once '../components/teacherSidebar.php';
require_once '../components/teacherHeader.php';
require_once '../../api/login/teacher/teacherSessionCheck.php';
require_once '../../api/config/database.php';

// Check if exam_id is provided
if (!isset($_GET['exam_id']) || empty($_GET['exam_id'])) {
    header('Location: index.php');
    exit;
}

$exam_id = intval($_GET['exam_id']);
$teacher_id = $_SESSION['teacher_id'];

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Verify exam belongs to teacher and get exam details
$examStmt = $conn->prepare("
    SELECT e.*, c.code as course_code, c.title as course_title,
           d.name as department_name, p.name as program_name,
           t.first_name as teacher_first_name, t.last_name as teacher_last_name
    FROM exams e
    LEFT JOIN courses c ON e.course_id = c.course_id
    LEFT JOIN programs p ON c.program_id = p.program_id
    LEFT JOIN departments d ON p.department_id = d.department_id
    LEFT JOIN teachers t ON e.teacher_id = t.teacher_id
    WHERE e.exam_id = :exam_id AND e.teacher_id = :teacher_id
");
$examStmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);
$exam = $examStmt->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
    header('Location: index.php?error=exam_not_found');
    exit;
}

// Get exam statistics
$statsStmt = $conn->prepare("
    SELECT 
        COUNT(r.result_id) as total_students,
        COUNT(CASE WHEN r.score_percentage >= e.pass_mark THEN 1 END) as passed_students,
        ROUND(AVG(r.score_percentage), 1) as avg_score,
        MIN(r.score_percentage) as min_score,
        MAX(r.score_percentage) as max_score,
        AVG(r.total_questions) as avg_questions_attempted,
        COUNT(CASE WHEN r.completed_at IS NOT NULL THEN 1 END) as completed_count
    FROM results r 
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    WHERE er.exam_id = :exam_id
");
$statsStmt->execute(['exam_id' => $exam_id]);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Calculate pass rate
$passRate = $stats['total_students'] > 0 ? round(($stats['passed_students'] / $stats['total_students']) * 100, 1) : 0;

$currentPage = 'results';
$pageTitle = "Exam Results - " . $exam['title'];
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
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <a href="index.php" class="text-emerald-600 hover:text-emerald-700">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo htmlspecialchars($exam['title']); ?></h1>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="exportExamResults(<?php echo $exam_id; ?>)" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Export Results
                        </button>
                        <button onclick="generateExamReport(<?php echo $exam_id; ?>)" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Exam Information -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                        Exam Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Exam Code</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($exam['exam_code']); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Course</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($exam['course_code'] . ' - ' . $exam['course_title']); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php
                                    switch ($exam['status']) {
                                        case 'Completed':
                                            echo 'bg-emerald-100 text-emerald-800';
                                            break;
                                        case 'Approved':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'Draft':
                                            echo 'bg-gray-100 text-gray-800';
                                            break;
                                        case 'Pending':
                                            echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo htmlspecialchars($exam['status']); ?>
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Start Date & Time</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo $exam['start_datetime'] ? date('M j, Y g:i A', strtotime($exam['start_datetime'])) : 'Not set'; ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">End Date & Time</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo $exam['end_datetime'] ? date('M j, Y g:i A', strtotime($exam['end_datetime'])) : 'Not set'; ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Duration</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo $exam['duration_minutes']; ?> minutes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $stats['total_students']; ?></div>
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
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-chart-line text-emerald-600 text-xl"></i>
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
                            <div class="flex-shrink-0 bg-yellow-50 rounded-lg p-3">
                                <i class="fas fa-star text-yellow-500 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $stats['avg_score'] ?? 'N/A'; ?>%</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Score Range -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-arrows-alt-h text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Score Range</dt>
                                    <dd>
                                        <div class="text-lg font-semibold text-gray-900"><?php echo ($stats['min_score'] ?? 'N/A') . ' - ' . ($stats['max_score'] ?? 'N/A'); ?>%</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Results Filter -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-filter mr-2 text-emerald-600"></i>
                        Filter Student Results
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Student</label>
                            <input type="text" id="studentSearch" placeholder="Name or student number..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Score</label>
                            <input type="number" id="scoreMin" placeholder="0" min="0" max="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Score</label>
                            <input type="number" id="scoreMax" placeholder="100" min="0" max="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        <div class="flex items-end">
                            <button onclick="filterResults()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Results Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-table mr-2 text-purple-600"></i>
                        Student Results
                    </h3>
                    <span id="resultCount" class="text-sm text-gray-500">Loading...</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Results will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load results on page load
            loadStudentResults();
        });

        function loadStudentResults() {
            const resultsTable = document.getElementById('resultsTable');
            resultsTable.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Loading student results...
                    </td>
                </tr>
            `;

            // Fetch student results for this exam
            fetch('/api/results/getExamStudentResults.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'exam_id=<?php echo $exam_id; ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        populateStudentResults(data.results);
                        document.getElementById('resultCount').textContent = `${data.results.length} students`;
                    } else {
                        resultsTable.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-red-500">
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

        function populateStudentResults(results) {
            const resultsTable = document.getElementById('resultsTable');
            resultsTable.innerHTML = '';

            if (results.length === 0) {
                resultsTable.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center py-8">
                                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium text-gray-500 mb-2">No student results found</p>
                                <p class="text-sm text-gray-400">Students haven't taken this exam yet.</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            results.forEach(result => {
                const isPassed = result.score_percentage >= <?php echo $exam['pass_mark']; ?>;
                const completedDate = result.completed_at ? formatDate(result.completed_at) : 'N/A';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${escapeHtml(result.first_name + ' ' + result.last_name)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${escapeHtml(result.index_number)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${result.correct_answers}/${result.total_questions}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${result.score_percentage}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isPassed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'}">
                            ${isPassed ? 'Pass' : 'Fail'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${completedDate}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button onclick="viewResultDetails(${result.result_id})" class="text-blue-600 hover:text-blue-900" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                `;
                resultsTable.appendChild(row);
            });
        }

        function filterResults() {
            const search = document.getElementById('studentSearch').value.toLowerCase();
            const minScore = parseFloat(document.getElementById('scoreMin').value) || 0;
            const maxScore = parseFloat(document.getElementById('scoreMax').value) || 100;

            const rows = document.querySelectorAll('#resultsTable tr');
            let visibleCount = 0;

            rows.forEach(row => {
                if (row.cells.length < 8) return; // Skip header or empty rows

                const studentName = row.cells[0].textContent.toLowerCase();
                const studentNumber = row.cells[1].textContent.toLowerCase();
                const percentage = parseFloat(row.cells[3].textContent);

                const matchesSearch = search === '' ||
                    studentName.includes(search) ||
                    studentNumber.includes(search);
                const matchesScore = percentage >= minScore && percentage <= maxScore;

                if (matchesSearch && matchesScore) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('resultCount').textContent = `${visibleCount} students (filtered)`;
        }

        function viewResultDetails(resultId) {
            window.location.href = `viewResult.php?id=${resultId}`;
        }

        function exportExamResults(examId) {
            window.open(`/api/results/exportTeacherResults.php?exam_id=${examId}`, '_blank');
            showNotification('Exporting exam results...', 'info');
        }

        function generateExamReport(examId) {
            window.open(`/api/results/generateTeacherReport.php?exam_id=${examId}`, '_blank');
            showNotification('Generating exam report...', 'info');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

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