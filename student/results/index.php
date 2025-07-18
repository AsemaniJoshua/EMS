<?php
$pageTitle = "My Results";
$breadcrumb = "Results";

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

require_once '../../api/config/database.php';

$student_id = $_SESSION['student_id'];
$db = new Database();
$conn = $db->getConnection();

// Get student information
$studentQuery = "
    SELECT s.*, p.name as program_name, d.name as department_name, l.name as level_name
    FROM students s
    JOIN programs p ON s.program_id = p.program_id
    JOIN departments d ON s.department_id = d.department_id
    JOIN levels l ON s.level_id = l.level_id
    WHERE s.student_id = :student_id
";
$stmt = $conn->prepare($studentQuery);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Get statistics
$statsQuery = "
    SELECT 
        COUNT(r.result_id) as total_exams,
        AVG(r.score_percentage) as average_score,
        MAX(r.score_percentage) as highest_score,
        MIN(r.score_percentage) as lowest_score,
        SUM(CASE WHEN r.score_percentage >= e.pass_mark THEN 1 ELSE 0 END) as passed_exams,
        SUM(CASE WHEN r.score_percentage < e.pass_mark THEN 1 ELSE 0 END) as failed_exams
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    WHERE er.student_id = :student_id
";
$stmt = $conn->prepare($statsQuery);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Default values if no results
$stats['total_exams'] = $stats['total_exams'] ?: 0;
$stats['average_score'] = $stats['average_score'] ? round($stats['average_score'], 1) : 0;
$stats['highest_score'] = $stats['highest_score'] ?: 0;
$stats['lowest_score'] = $stats['lowest_score'] ?: 0;
$stats['passed_exams'] = $stats['passed_exams'] ?: 0;
$stats['failed_exams'] = $stats['failed_exams'] ?: 0;

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$course_filter = isset($_GET['course']) ? $_GET['course'] : '';

// Build query conditions
$conditions = ['er.student_id = :student_id'];
$params = [':student_id' => $student_id];

if (!empty($search)) {
    $conditions[] = '(e.title LIKE :search OR e.exam_code LIKE :search OR c.title LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

if ($status_filter === 'passed') {
    $conditions[] = 'r.score_percentage >= e.pass_mark';
} elseif ($status_filter === 'failed') {
    $conditions[] = 'r.score_percentage < e.pass_mark';
}

if (!empty($course_filter)) {
    $conditions[] = 'c.course_id = :course_filter';
    $params[':course_filter'] = $course_filter;
}

$whereClause = 'WHERE ' . implode(' AND ', $conditions);

// Get total count for pagination
$countQuery = "
    SELECT COUNT(r.result_id)
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    JOIN courses c ON e.course_id = c.course_id
    $whereClause
";
$stmt = $conn->prepare($countQuery);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$total_results = $stmt->fetchColumn();
$total_pages = ceil($total_results / $limit);

// Get results
$resultsQuery = "
    SELECT 
        r.result_id,
        r.score_percentage,
        r.total_questions,
        r.correct_answers,
        r.incorrect_answers,
        r.completed_at,
        e.exam_id,
        e.title as exam_title,
        e.exam_code,
        e.pass_mark,
        e.total_marks,
        e.show_results,
        c.title as course_title,
        c.code as course_code,
        CASE WHEN r.score_percentage >= e.pass_mark THEN 'Passed' ELSE 'Failed' END as status
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    JOIN courses c ON e.course_id = c.course_id
    $whereClause
    ORDER BY r.completed_at DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($resultsQuery);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get courses for filter dropdown
$coursesQuery = "
    SELECT DISTINCT c.course_id, c.title, c.code
    FROM courses c
    JOIN exams e ON c.course_id = e.course_id
    JOIN exam_registrations er ON e.exam_id = er.exam_id
    JOIN results r ON er.registration_id = r.registration_id
    WHERE er.student_id = :student_id
    ORDER BY c.code
";
$stmt = $conn->prepare($coursesQuery);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Student</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    
    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">My Results</h1>
                    <p class="mt-1 text-sm text-gray-500">View your exam results and performance analytics</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Exams</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $stats['total_exams']; ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                                <i class="fas fa-chart-line text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                                                        <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $stats['average_score']; ?>%</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Passed Exams</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $stats['passed_exams']; ?></div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">
                                                <?php echo $stats['total_exams'] > 0 ? round(($stats['passed_exams'] / $stats['total_exams']) * 100, 1) : 0; ?>%
                                            </span>
                                            <span class="ml-1 text-gray-500">pass rate</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-trophy text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Highest Score</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $stats['highest_score']; ?>%</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Chart -->
            <?php if ($stats['total_exams'] > 0): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Performance Overview</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Pass/Fail Distribution</h4>
                            <div class="relative h-64">
                                <canvas id="passFailChart"></canvas>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-4">Score Distribution</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Average Score</span>
                                    <span class="text-sm font-semibold text-gray-900"><?php echo $stats['average_score']; ?>%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Highest Score</span>
                                    <span class="text-sm font-semibold text-green-600"><?php echo $stats['highest_score']; ?>%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Lowest Score</span>
                                    <span class="text-sm font-semibold text-red-600"><?php echo $stats['lowest_score']; ?>%</span>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm text-gray-600">Pass Rate</span>
                                        <span class="text-sm font-semibold text-emerald-600">
                                            <?php echo $stats['total_exams'] > 0 ? round(($stats['passed_exams'] / $stats['total_exams']) * 100, 1) : 0; ?>%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-emerald-500 h-2 rounded-full" 
                                             style="width: <?php echo $stats['total_exams'] > 0 ? round(($stats['passed_exams'] / $stats['total_exams']) * 100, 1) : 0; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Search and Filter -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search exams..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">All Results</option>
                                <option value="passed" <?php echo $status_filter === 'passed' ? 'selected' : ''; ?>>Passed</option>
                                <option value="failed" <?php echo $status_filter === 'failed' ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                            <select name="course" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">All Courses</option>
                                <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['course_id']; ?>" <?php echo $course_filter == $course['course_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['code'] . ' - ' . $course['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Exam Results</h3>
                    <span class="text-sm text-gray-500"><?php echo $total_results; ?> results found</span>
                </div>
                
                <?php if (empty($results)): ?>
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Results Found</h3>
                    <p class="text-gray-600">
                        <?php if (!empty($search) || !empty($status_filter) || !empty($course_filter)): ?>
                            Try adjusting your search criteria or filters.
                        <?php else: ?>
                            You haven't completed any exams yet.
                        <?php endif; ?>
                    </p>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($results as $result): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($result['exam_title']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($result['exam_code']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo htmlspecialchars($result['course_title']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($result['course_code']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        <?php echo round($result['score_percentage'], 1); ?>%
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo $result['correct_answers']; ?>/<?php echo $result['total_questions']; ?> correct
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-semibold <?php echo $result['status'] === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $result['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M j, Y', strtotime($result['completed_at'])); ?>
                                    <div class="text-xs text-gray-400">
                                        <?php echo date('g:i A', strtotime($result['completed_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="view.php?result_id=<?php echo $result['result_id']; ?>" 
                                       class="text-emerald-600 hover:text-emerald-700 mr-3">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php if ($result['show_results']): ?>
                                    <a href="/api/results/printResult.php?result_id=<?php echo $result['result_id']; ?>" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-print"></i> Print
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $limit, $total_results); ?> of <?php echo $total_results; ?> results
                        </div>
                        <div class="flex space-x-2">
                            <?php if ($page > 1): ?>
                                                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&course=<?php echo urlencode($course_filter); ?>" 
                               class="px-3 py-1 text-sm border rounded-md text-gray-500 bg-white border-gray-300 hover:bg-gray-50">
                                Previous
                            </a>
                            <?php endif; ?>

                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            for ($i = $start_page; $i <= $end_page; $i++):
                                if ($i == $page): ?>
                                    <span class="px-3 py-1 text-sm border rounded-md text-white bg-emerald-600 border-emerald-600">
                                        <?php echo $i; ?>
                                    </span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&course=<?php echo urlencode($course_filter); ?>" 
                                       class="px-3 py-1 text-sm border rounded-md text-gray-500 bg-white border-gray-300 hover:bg-gray-50">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif;
                            endfor; ?>

                            <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&course=<?php echo urlencode($course_filter); ?>" 
                               class="px-3 py-1 text-sm border rounded-md text-gray-500 bg-white border-gray-300 hover:bg-gray-50">
                                Next
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        <?php if ($stats['total_exams'] > 0): ?>
        // Pass/Fail Chart
        const passFailCtx = document.getElementById('passFailChart').getContext('2d');
        const passFailChart = new Chart(passFailCtx, {
            type: 'doughnut',
            data: {
                labels: ['Passed', 'Failed'],
                datasets: [{
                    data: [<?php echo $stats['passed_exams']; ?>, <?php echo $stats['failed_exams']; ?>],
                    backgroundColor: ['#10B981', '#EF4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = <?php echo $stats['total_exams']; ?>;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // Add center text to pass/fail chart
        Chart.register({
            id: 'centerTextPassFail',
            beforeDraw: function(chart) {
                if (chart.canvas.id === 'passFailChart') {
                    const ctx = chart.ctx;
                    const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                    
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    
                    // Pass rate
                    const passRate = <?php echo $stats['total_exams'] > 0 ? round(($stats['passed_exams'] / $stats['total_exams']) * 100, 1) : 0; ?>;
                    ctx.font = 'bold 20px Arial';
                    ctx.fillStyle = passRate >= 50 ? '#10B981' : '#EF4444';
                    ctx.fillText(passRate + '%', centerX, centerY - 8);
                    
                    // Label
                    ctx.font = '12px Arial';
                    ctx.fillStyle = '#6B7280';
                    ctx.fillText('Pass Rate', centerX, centerY + 12);
                    
                    ctx.restore();
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>

