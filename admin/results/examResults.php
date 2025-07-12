<?php
include_once __DIR__ . '/../../api/login/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';

$currentPage = 'results';
$pageTitle = "Exam Results Analytics";
$breadcrumb = "Results > Exam Analytics";

// Check if exam_id is provided
$examId = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

if ($examId <= 0) {
    header("Location: index.php");
    exit;
}

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch exam details
$stmt = $conn->prepare("
    SELECT 
        e.exam_id, e.title, e.exam_code, e.description, 
        e.status, e.duration_minutes, e.pass_mark, e.total_marks, 
        DATE_FORMAT(e.start_datetime, '%M %d, %Y %H:%i') as start_datetime,
        DATE_FORMAT(e.end_datetime, '%M %d, %Y %H:%i') as end_datetime,
        c.course_id, c.code as course_code, c.title as course_title,
        d.department_id, d.name as department_name,
        p.program_id, p.name as program_name,
        t.teacher_id, CONCAT(t.first_name, ' ', t.last_name) as teacher_name,
        (SELECT COUNT(*) FROM exam_registrations er WHERE er.exam_id = e.exam_id) AS registered_students,
        (SELECT COUNT(*) FROM results r JOIN exam_registrations er ON r.registration_id = er.registration_id 
            WHERE er.exam_id = e.exam_id) AS submitted_results,
        (SELECT COUNT(*) FROM questions q WHERE q.exam_id = e.exam_id) AS total_questions
    FROM exams e
    JOIN courses c ON e.course_id = c.course_id
    JOIN departments d ON e.department_id = d.department_id
    JOIN programs p ON e.program_id = p.program_id
    JOIN teachers t ON e.teacher_id = t.teacher_id
    WHERE e.exam_id = :exam_id
");
$stmt->execute([':exam_id' => $examId]);
$exam = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
    header("Location: index.php");
    exit;
}

// Fetch exam statistics
$stmt = $conn->prepare("
    SELECT 
        COUNT(r.result_id) as total_results,
        MIN(r.score_percentage) as min_score,
        MAX(r.score_percentage) as max_score,
        AVG(r.score_percentage) as avg_score,
        SUM(CASE WHEN r.score_percentage >= 50 THEN 1 ELSE 0 END) as pass_count,
        SUM(CASE WHEN r.score_percentage < 50 THEN 1 ELSE 0 END) as fail_count,
        ROUND((SUM(CASE WHEN r.score_percentage >= 50 THEN 1 ELSE 0 END) / COUNT(r.result_id)) * 100, 1) as pass_rate
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    WHERE er.exam_id = :exam_id
");
$stmt->execute([':exam_id' => $examId]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch score distribution
$stmt = $conn->prepare("
    SELECT 
        CASE 
            WHEN r.score_percentage >= 90 THEN '90-100'
            WHEN r.score_percentage >= 80 THEN '80-89'
            WHEN r.score_percentage >= 70 THEN '70-79'
            WHEN r.score_percentage >= 60 THEN '60-69'
            WHEN r.score_percentage >= 50 THEN '50-59'
            WHEN r.score_percentage >= 40 THEN '40-49'
            WHEN r.score_percentage >= 30 THEN '30-39'
            WHEN r.score_percentage >= 20 THEN '20-29'
            WHEN r.score_percentage >= 10 THEN '10-19'
            ELSE '0-9'
        END as range_label,
        COUNT(*) as count
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    WHERE er.exam_id = :exam_id
    GROUP BY range_label
    ORDER BY range_label DESC
");
$stmt->execute([':exam_id' => $examId]);
$distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format distribution for chart
$distributionLabels = [];
$distributionCounts = [];
foreach ($distribution as $range) {
    $distributionLabels[] = $range['range_label'];
    $distributionCounts[] = intval($range['count']);
}

// Fetch recent results
$stmt = $conn->prepare("
    SELECT 
        r.result_id,
        r.total_questions,
        r.correct_answers,
        r.incorrect_answers,
        r.score_percentage,
        DATE_FORMAT(r.completed_at, '%M %d, %Y %H:%i') as completed_at,
        s.student_id,
        CONCAT(s.first_name, ' ', s.last_name) as student_name,
        s.index_number,
        s.email,
        p.name as program_name
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN students s ON er.student_id = s.student_id
    JOIN programs p ON s.program_id = p.program_id
    WHERE er.exam_id = :exam_id
    ORDER BY r.score_percentage DESC, r.completed_at DESC
    LIMIT 100
");
$stmt->execute([':exam_id' => $examId]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch question performance data
$stmt = $conn->prepare("
    SELECT 
        q.question_id,
        q.question_text,
        COUNT(DISTINCT sa.registration_id) as total_answers,
        SUM(CASE WHEN c.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
        ROUND((SUM(CASE WHEN c.is_correct = 1 THEN 1 ELSE 0 END) / COUNT(DISTINCT sa.registration_id)) * 100, 1) as correct_percentage
    FROM questions q
    JOIN student_answers sa ON q.question_id = sa.question_id
    JOIN choices c ON sa.choice_id = c.choice_id
    JOIN exam_registrations er ON sa.registration_id = er.registration_id
    WHERE q.exam_id = :exam_id
    GROUP BY q.question_id, q.question_text
    ORDER BY correct_percentage DESC
");
$stmt->execute([':exam_id' => $examId]);
$questionPerformance = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format question performance data for chart
$questionLabels = [];
$questionPerformanceData = [];
foreach ($questionPerformance as $index => $question) {
    // Truncate long question text for chart labels
    $questionLabels[] = "Q" . ($index + 1);
    $questionPerformanceData[] = floatval($question['correct_percentage']);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex text-sm text-gray-500">
                    <li><a href="../dashboard/" class="hover:text-gray-900">Dashboard</a></li>
                    <li class="mx-2">/</li>
                    <li><a href="index.php" class="hover:text-gray-900">Results</a></li>
                    <li class="mx-2">/</li>
                    <li class="text-gray-900 font-medium truncate">Analytics: <?php echo htmlspecialchars($exam['title']); ?></li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        <?php echo htmlspecialchars($exam['title']); ?>
                        <span class="text-lg font-normal text-gray-500 ml-2">(<?php echo htmlspecialchars($exam['exam_code']); ?>)</span>
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        <?php echo htmlspecialchars($exam['course_code'] . ' - ' . $exam['course_title']); ?> |
                        <?php echo htmlspecialchars($exam['department_name']); ?> |
                        <?php echo htmlspecialchars($exam['program_name']); ?>
                    </p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-3">
                    <a href="../../api/results/exportResults.php?exam_id=<?php echo $exam['exam_id']; ?>" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm flex items-center transition-colors duration-200">
                        <i class="fas fa-file-export mr-2"></i>
                        Export to CSV
                    </a>
                    <a href="../../api/results/generateReport.php?exam_id=<?php echo $exam['exam_id']; ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm flex items-center transition-colors duration-200">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Generate Report
                    </a>
                </div>
            </div>

            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Students Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Students</h3>
                        <span class="rounded-full bg-blue-100 text-blue-800 p-1">
                            <i class="fas fa-users"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-bold text-gray-900"><?php echo $exam['registered_students']; ?></div>
                        <div class="text-sm text-gray-500 mt-1">
                            <span class="font-semibold"><?php echo $exam['submitted_results']; ?></span> completed 
                            (<?php echo $exam['registered_students'] > 0 ? round(($exam['submitted_results'] / $exam['registered_students']) * 100) : 0; ?>%)
                        </div>
                    </div>
                </div>

                <!-- Average Score Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Average Score</h3>
                        <span class="rounded-full bg-emerald-100 text-emerald-800 p-1">
                            <i class="fas fa-chart-line"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-bold text-gray-900"><?php echo number_format($stats['avg_score'], 1); ?>%</div>
                        <div class="text-sm text-gray-500 mt-1">
                            Range: <?php echo number_format($stats['min_score'], 1); ?>% - <?php echo number_format($stats['max_score'], 1); ?>%
                        </div>
                    </div>
                </div>

                <!-- Pass Rate Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Pass Rate</h3>
                        <span class="rounded-full bg-<?php echo $stats['pass_rate'] >= 70 ? 'emerald' : ($stats['pass_rate'] >= 50 ? 'yellow' : 'red'); ?>-100 
                                   text-<?php echo $stats['pass_rate'] >= 70 ? 'emerald' : ($stats['pass_rate'] >= 50 ? 'yellow' : 'red'); ?>-800 p-1">
                            <i class="fas fa-check-circle"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-bold text-<?php echo $stats['pass_rate'] >= 70 ? 'emerald' : ($stats['pass_rate'] >= 50 ? 'yellow' : 'red'); ?>-600">
                            <?php echo number_format($stats['pass_rate'], 1); ?>%
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            <?php echo $stats['pass_count']; ?> passed, <?php echo $stats['fail_count']; ?> failed
                        </div>
                    </div>
                </div>

                <!-- Questions Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Questions</h3>
                        <span class="rounded-full bg-purple-100 text-purple-800 p-1">
                            <i class="fas fa-question-circle"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-bold text-gray-900"><?php echo $exam['total_questions']; ?></div>
                        <div class="text-sm text-gray-500 mt-1">
                            Avg. correct: <?php echo count($questionPerformance) > 0 ? number_format(array_sum($questionPerformanceData) / count($questionPerformanceData), 1) : 0; ?>%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Score Distribution Chart -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Score Distribution</h3>
                    <div class="h-64">
                        <canvas id="scoreDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Question Performance Chart -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Question Performance</h3>
                    <div class="h-64">
                        <canvas id="questionPerformanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Exam Details Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">General Information</h4>
                        <div class="space-y-2">
                            <div><span class="font-medium">Title:</span> <?php echo htmlspecialchars($exam['title']); ?></div>
                            <div><span class="font-medium">Code:</span> <?php echo htmlspecialchars($exam['exam_code']); ?></div>
                            <div><span class="font-medium">Status:</span> 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php 
                                    $statusClass = '';
                                    switch ($exam['status']) {
                                        case 'Published':
                                            $statusClass = 'bg-emerald-100 text-emerald-800';
                                            break;
                                        case 'Draft':
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            break;
                                        case 'Approved':
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'Completed':
                                            $statusClass = 'bg-purple-100 text-purple-800';
                                            break;
                                        default:
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                    }
                                    echo $statusClass;
                                    ?>">
                                    <?php echo htmlspecialchars($exam['status']); ?>
                                </span>
                            </div>
                            <div><span class="font-medium">Teacher:</span> <?php echo htmlspecialchars($exam['teacher_name']); ?></div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Time Information</h4>
                        <div class="space-y-2">
                            <div><span class="font-medium">Start:</span> <?php echo $exam['start_datetime']; ?></div>
                            <div><span class="font-medium">End:</span> <?php echo $exam['end_datetime']; ?></div>
                            <div><span class="font-medium">Duration:</span> <?php echo $exam['duration_minutes']; ?> minutes</div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Course Information</h4>
                        <div class="space-y-2">
                            <div><span class="font-medium">Course:</span> <?php echo htmlspecialchars($exam['course_code'] . ' - ' . $exam['course_title']); ?></div>
                            <div><span class="font-medium">Program:</span> <?php echo htmlspecialchars($exam['program_name']); ?></div>
                            <div><span class="font-medium">Department:</span> <?php echo htmlspecialchars($exam['department_name']); ?></div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($exam['description'])): ?>
                <div class="mt-6">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</h4>
                    <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($exam['description'])); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Student Results Filter -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Student Results</h3>
                <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="studentFilter" class="block text-sm font-medium text-gray-700 mb-1">Student Name/ID</label>
                        <input type="text" id="studentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" placeholder="Search by name or ID">
                    </div>
                    <div>
                        <label for="scoreRange" class="block text-sm font-medium text-gray-700 mb-1">Score Range</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" id="minScore" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" min="0" max="100" value="0">
                            <span class="text-gray-500">to</span>
                            <input type="number" id="maxScore" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" min="0" max="100" value="100">
                        </div>
                    </div>
                    <div>
                        <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Results</option>
                            <option value="pass">Passed</option>
                            <option value="fail">Failed</option>
                        </select>
                    </div>
                    <div class="md:col-span-3 flex justify-end">
                        <button type="button" id="applyFilterBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm flex items-center transition-colors duration-200">
                            <i class="fas fa-filter mr-2"></i>
                            Apply Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Student Results Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Student Results</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct/Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Completed</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTable" class="bg-white divide-y divide-gray-200">
                            <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No results found for this exam.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($results as $result): ?>
                                <tr class="result-row hover:bg-gray-50" 
                                    data-student="<?php echo strtolower(htmlspecialchars($result['student_name']) . ' ' . htmlspecialchars($result['index_number'])); ?>"
                                    data-score="<?php echo $result['score_percentage']; ?>"
                                    data-status="<?php echo $result['score_percentage'] >= 50 ? 'pass' : 'fail'; ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['student_name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($result['index_number']); ?></div>
                                        <?php if (!empty($result['email'])): ?>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($result['email']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($result['program_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium <?php echo $result['score_percentage'] >= 50 ? 'text-emerald-600' : 'text-red-600'; ?>">
                                            <?php echo number_format($result['score_percentage'], 1); ?>%
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $result['correct_answers']; ?>/<?php echo $result['total_questions']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $result['score_percentage'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $result['score_percentage'] >= 50 ? 'Passed' : 'Failed'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $result['completed_at']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="viewResultDetails(<?php echo $result['result_id']; ?>)">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </button>
                                        <button class="text-green-600 hover:text-green-900" onclick="printResultDetails(<?php echo $result['result_id']; ?>)">
                                            <i class="fas fa-print mr-1"></i> Print
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Question Performance Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Question Performance</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct %</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct/Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($questionPerformance)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No question performance data available.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($questionPerformance as $index => $question): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <span class="font-medium">Q<?php echo $index + 1; ?>:</span> 
                                            <?php echo htmlspecialchars(mb_strimwidth($question['question_text'], 0, 100, "...")); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="h-2.5 rounded-full <?php
                                                if ($question['correct_percentage'] >= 70) echo 'bg-emerald-500';
                                                else if ($question['correct_percentage'] >= 50) echo 'bg-yellow-500';
                                                else echo 'bg-red-500';
                                            ?>" style="width: <?php echo $question['correct_percentage']; ?>%"></div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900 mt-1">
                                            <?php echo number_format($question['correct_percentage'], 1); ?>%
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $question['correct_answers']; ?>/<?php echo $question['total_answers']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            if ($question['correct_percentage'] >= 80) echo 'bg-green-100 text-green-800';
                                            else if ($question['correct_percentage'] >= 60) echo 'bg-blue-100 text-blue-800';
                                            else if ($question['correct_percentage'] >= 40) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php 
                                            if ($question['correct_percentage'] >= 80) echo 'Easy';
                                            else if ($question['correct_percentage'] >= 60) echo 'Medium';
                                            else if ($question['correct_percentage'] >= 40) echo 'Hard';
                                            else echo 'Very Hard';
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Student Result Detail Modal (hidden by default) -->
    <div id="resultModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Student Result Details</h3>
                <button id="closeResultModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6" id="modalContent">
                <!-- Modal content will be populated dynamically -->
            </div>
        </div>
    </div>

    <script>
        // DOM elements
        const resultsTable = document.getElementById('resultsTable');
        const studentFilter = document.getElementById('studentFilter');
        const minScore = document.getElementById('minScore');
        const maxScore = document.getElementById('maxScore');
        const statusFilter = document.getElementById('statusFilter');
        const applyFilterBtn = document.getElementById('applyFilterBtn');
        const closeResultModal = document.getElementById('closeResultModal');
        const resultModal = document.getElementById('resultModal');

        // Chart configuration
        document.addEventListener('DOMContentLoaded', function() {
            // Configure score distribution chart
            const scoreDistributionCtx = document.getElementById('scoreDistributionChart').getContext('2d');
            const scoreDistributionChart = new Chart(scoreDistributionCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($distributionLabels); ?>,
                    datasets: [{
                        label: 'Number of Students',
                        data: <?php echo json_encode($distributionCounts); ?>,
                        backgroundColor: [
                            'rgba(52, 211, 153, 0.8)', // 90-100
                            'rgba(52, 211, 153, 0.7)', // 80-89
                            'rgba(52, 211, 153, 0.6)', // 70-79
                            'rgba(59, 130, 246, 0.7)', // 60-69
                            'rgba(59, 130, 246, 0.6)', // 50-59
                            'rgba(239, 68, 68, 0.5)', // 40-49
                            'rgba(239, 68, 68, 0.6)', // 30-39
                            'rgba(239, 68, 68, 0.7)', // 20-29
                            'rgba(239, 68, 68, 0.8)', // 10-19
                            'rgba(239, 68, 68, 0.9)'  // 0-9
                        ],
                        borderColor: [
                            'rgba(52, 211, 153, 1)',
                            'rgba(52, 211, 153, 1)',
                            'rgba(52, 211, 153, 1)',
                            'rgba(59, 130, 246, 1)',
                            'rgba(59, 130, 246, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Students'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Score Range (%)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label + '%';
                                },
                                label: function(context) {
                                    return context.raw + ' student' + (context.raw !== 1 ? 's' : '');
                                }
                            }
                        }
                    }
                }
            });

            // Configure question performance chart
            const questionPerformanceCtx = document.getElementById('questionPerformanceChart').getContext('2d');
            const questionPerformanceChart = new Chart(questionPerformanceCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($questionLabels); ?>,
                    datasets: [{
                        label: 'Correct Answers (%)',
                        data: <?php echo json_encode($questionPerformanceData); ?>,
                        backgroundColor: <?php echo json_encode($questionPerformanceData); ?>.map(value => {
                            if (value >= 70) return 'rgba(52, 211, 153, 0.7)';
                            else if (value >= 50) return 'rgba(251, 191, 36, 0.7)';
                            else return 'rgba(239, 68, 68, 0.7)';
                        }),
                        borderColor: <?php echo json_encode($questionPerformanceData); ?>.map(value => {
                            if (value >= 70) return 'rgba(52, 211, 153, 1)';
                            else if (value >= 50) return 'rgba(251, 191, 36, 1)';
                            else return 'rgba(239, 68, 68, 1)';
                        }),
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Correct Answers (%)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });

        // Filter student results
        applyFilterBtn.addEventListener('click', filterResults);
        
        function filterResults() {
            const studentValue = studentFilter.value.toLowerCase();
            const minScoreValue = parseFloat(minScore.value) || 0;
            const maxScoreValue = parseFloat(maxScore.value) || 100;
            const statusValue = statusFilter.value;
            
            const rows = document.querySelectorAll('.result-row');
            
            rows.forEach(row => {
                const studentText = row.dataset.student;
                const score = parseFloat(row.dataset.score);
                const status = row.dataset.status;
                
                const studentMatch = !studentValue || studentText.includes(studentValue);
                const scoreMatch = score >= minScoreValue && score <= maxScoreValue;
                const statusMatch = !statusValue || status === statusValue;
                
                if (studentMatch && scoreMatch && statusMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Close modal when clicking the close button
        closeResultModal.addEventListener('click', function() {
            resultModal.classList.add('hidden');
        });

        // Also close modal when clicking outside of it
        resultModal.addEventListener('click', function(event) {
            if (event.target === this) {
                this.classList.add('hidden');
            }
        });

        /**
         * Views the details of a specific result
         */
        function viewResultDetails(resultId) {
            const modalContent = document.getElementById('modalContent');

            // Show loading indicator
            modalContent.innerHTML = `
                <div class="flex justify-center items-center py-8">
                    <i class="fas fa-spinner fa-spin text-emerald-500 text-2xl"></i>
                </div>
            `;
            resultModal.classList.remove('hidden');

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
         * Prints the result details
         */
        function printResultDetails(resultId) {
            // Open a new window with print-friendly version
            window.open(`../../api/results/printResult.php?result_id=${resultId}`, '_blank');
        }
        
        /**
         * Utility function to escape HTML
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
