<?php
$pageTitle = "Exam Result";
$breadcrumb = "Exam Result";

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

$result_id = isset($_GET['result_id']) ? intval($_GET['result_id']) : 0;
$registration_id = isset($_GET['registration_id']) ? intval($_GET['registration_id']) : 0;

if ($result_id <= 0 && $registration_id <= 0) {
    header('Location: index.php');
    exit;
}

require_once '../../api/config/database.php';

$student_id = $_SESSION['student_id'];
$db = new Database();
$conn = $db->getConnection();

// Get result details
if ($result_id > 0) {
    $resultQuery = "
        SELECT r.*, er.registration_id, e.exam_id, e.title as exam_title, e.exam_code,
               e.pass_mark, e.show_results, e.start_datetime, e.end_datetime,
               c.title as course_title, c.code as course_code,
               d.name as department_name, p.name as program_name
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN exams e ON er.exam_id = e.exam_id
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON e.department_id = d.department_id
        JOIN programs p ON e.program_id = p.program_id
        WHERE r.result_id = :result_id AND er.student_id = :student_id
    ";
    $stmt = $conn->prepare($resultQuery);
    $stmt->bindParam(':result_id', $result_id);
    $stmt->bindParam(':student_id', $student_id);
} else {
    $resultQuery = "
        SELECT r.*, er.registration_id, e.exam_id, e.title as exam_title, e.exam_code,
               e.pass_mark, e.show_results, e.start_datetime, e.end_datetime,
               c.title as course_title, c.code as course_code,
               d.name as department_name, p.name as program_name
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN exams e ON er.exam_id = e.exam_id
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON e.department_id = d.department_id
        JOIN programs p ON e.program_id = p.program_id
        WHERE er.registration_id = :registration_id AND er.student_id = :student_id
    ";
    $stmt = $conn->prepare($resultQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->bindParam(':student_id', $student_id);
}

$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    header('Location: index.php?error=not_found');
    exit;
}

// Check if results should be shown
if (!$result['show_results']) {
    $hideDetails = true;
} else {
    $hideDetails = false;
}

// Get detailed answers if results are shown
$detailedAnswers = [];
if (!$hideDetails) {
    $answersQuery = "
        SELECT q.question_id, q.question_text, q.sequence_number,
               sa.choice_id as selected_choice_id,
               c_selected.choice_text as selected_choice_text,
               c_selected.is_correct as selected_is_correct,
               c_correct.choice_id as correct_choice_id,
               c_correct.choice_text as correct_choice_text
        FROM questions q
        LEFT JOIN student_answers sa ON q.question_id = sa.question_id AND sa.registration_id = :registration_id
        LEFT JOIN choices c_selected ON sa.choice_id = c_selected.choice_id
        LEFT JOIN choices c_correct ON q.question_id = c_correct.question_id AND c_correct.is_correct = 1
        WHERE q.exam_id = :exam_id
        ORDER BY q.sequence_number ASC, q.question_id ASC
    ";
    
    $stmt = $conn->prepare($answersQuery);
    $stmt->bindParam(':registration_id', $result['registration_id']);
    $stmt->bindParam(':exam_id', $result['exam_id']);
    $stmt->execute();
    $detailedAnswers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$passed = $result['score_percentage'] >= $result['pass_mark'];
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
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Exam Result</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        <?php echo htmlspecialchars($result['exam_title']); ?>
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="index.php" class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg border border-gray-300 font-semibold transition-colors duration-200 mr-3">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Results
                    </a>
                    <?php if (!$hideDetails): ?>
                                        <a href="/api/results/printResult.php?result_id=<?php echo $result['result_id']; ?>" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                        <i class="fas fa-print mr-2"></i>Print Result
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Result Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Result Summary</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Exam Information -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Exam Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Exam Code:</dt>
                                    <dd class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['exam_code']); ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Course:</dt>
                                    <dd class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['course_code'] . ' - ' . $result['course_title']); ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Department:</dt>
                                    <dd class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['department_name']); ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Program:</dt>
                                    <dd class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['program_name']); ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Exam Date:</dt>
                                    <dd class="text-sm font-medium text-gray-900"><?php echo date('M j, Y', strtotime($result['start_datetime'])); ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Completed:</dt>
                                    <dd class="text-sm font-medium text-gray-900"><?php echo date('M j, Y g:i A', strtotime($result['completed_at'])); ?></dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Score Information -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Score Information</h3>
                            
                            <?php if ($hideDetails): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-eye-slash text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-2">Detailed results are not available</p>
                                <p class="text-sm text-gray-500">The instructor has chosen not to show detailed results for this exam.</p>
                            </div>
                            <?php else: ?>
                            <dl class="space-y-2 mb-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Total Questions:</dt>
                                    <dd class="text-sm font-medium text-gray-900"><?php echo $result['total_questions']; ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Correct Answers:</dt>
                                    <dd class="text-sm font-medium text-green-600"><?php echo $result['correct_answers']; ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Incorrect Answers:</dt>
                                    <dd class="text-sm font-medium text-red-600"><?php echo $result['incorrect_answers']; ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Pass Mark:</dt>
                                    <dd class="text-sm font-medium text-gray-900"><?php echo $result['pass_mark']; ?>%</dd>
                                </div>
                            </dl>

                            <!-- Score Display -->
                            <div class="text-center p-6 bg-gray-50 rounded-lg">
                                <div class="text-4xl font-bold mb-2 <?php echo $passed ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo round($result['score_percentage'], 1); ?>%
                                </div>
                                <div class="text-lg font-semibold mb-2 <?php echo $passed ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $passed ? 'PASSED' : 'FAILED'; ?>
                                </div>
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-<?php echo $passed ? 'check-circle' : 'times-circle'; ?> text-2xl <?php echo $passed ? 'text-green-600' : 'text-red-600'; ?> mr-2"></i>
                                    <span class="text-gray-600">
                                        <?php echo $passed ? 'Congratulations!' : 'Better luck next time'; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$hideDetails): ?>
            <!-- Performance Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Performance Breakdown</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Chart -->
                        <div>
                            <div class="relative h-64">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Statistics -->
                        <div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                                        <span class="text-sm font-medium text-gray-700">Correct Answers</span>
                                    </div>
                                    <span class="text-lg font-bold text-green-600"><?php echo $result['correct_answers']; ?></span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                                        <span class="text-sm font-medium text-gray-700">Incorrect Answers</span>
                                    </div>
                                    <span class="text-lg font-bold text-red-600"><?php echo $result['incorrect_answers']; ?></span>
                                </div>
                                
                                <?php 
                                $unanswered = $result['total_questions'] - $result['correct_answers'] - $result['incorrect_answers'];
                                if ($unanswered > 0): 
                                ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-gray-400 rounded-full mr-3"></div>
                                        <span class="text-sm font-medium text-gray-700">Unanswered</span>
                                    </div>
                                    <span class="text-lg font-bold text-gray-600"><?php echo $unanswered; ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Accuracy Rate</span>
                                        <span class="text-lg font-bold text-gray-900">
                                            <?php echo $result['total_questions'] > 0 ? round(($result['correct_answers'] / $result['total_questions']) * 100, 1) : 0; ?>%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Answers -->
            <?php if (!empty($detailedAnswers)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Detailed Review</h2>
                    <p class="text-sm text-gray-600 mt-1">Review your answers and see the correct solutions</p>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($detailedAnswers as $index => $answer): ?>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                Question <?php echo $index + 1; ?>
                            </h3>
                            <?php if ($answer['selected_choice_id']): ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $answer['selected_is_correct'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $answer['selected_is_correct'] ? 'Correct' : 'Incorrect'; ?>
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    Not Answered
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-gray-900"><?php echo htmlspecialchars($answer['question_text']); ?></p>
                        </div>
                        
                        <div class="space-y-2">
                            <?php if ($answer['selected_choice_id']): ?>
                            <div class="p-3 rounded-lg <?php echo $answer['selected_is_correct'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'; ?>">
                                <div class="flex items-center">
                                    <i class="fas fa-<?php echo $answer['selected_is_correct'] ? 'check' : 'times'; ?> <?php echo $answer['selected_is_correct'] ? 'text-green-600' : 'text-red-600'; ?> mr-2"></i>
                                    <span class="text-sm font-medium <?php echo $answer['selected_is_correct'] ? 'text-green-800' : 'text-red-800'; ?>">Your Answer:</span>
                                </div>
                                <p class="mt-1 text-gray-900 ml-6"><?php echo htmlspecialchars($answer['selected_choice_text']); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!$answer['selected_is_correct'] && $answer['correct_choice_text']): ?>
                            <div class="p-3 rounded-lg bg-green-50 border border-green-200">
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-600 mr-2"></i>
                                    <span class="text-sm font-medium text-green-800">Correct Answer:</span>
                                </div>
                                <p class="mt-1 text-gray-900 ml-6"><?php echo htmlspecialchars($answer['correct_choice_text']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php if (!$hideDetails): ?>
    <script>
        // Performance Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Correct', 'Incorrect'<?php echo ($result['total_questions'] - $result['correct_answers'] - $result['incorrect_answers']) > 0 ? ", 'Unanswered'" : ''; ?>],
                datasets: [{
                    data: [
                        <?php echo $result['correct_answers']; ?>,
                        <?php echo $result['incorrect_answers']; ?>
                        <?php echo ($result['total_questions'] - $result['correct_answers'] - $result['incorrect_answers']) > 0 ? ', ' . ($result['total_questions'] - $result['correct_answers'] - $result['incorrect_answers']) : ''; ?>
                    ],
                    backgroundColor: [
                        '#10B981',
                        '#EF4444'
                        <?php echo ($result['total_questions'] - $result['correct_answers'] - $result['incorrect_answers']) > 0 ? ", '#9CA3AF'" : ''; ?>
                    ],
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

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query
$whereConditions = ['er.student_id = :student_id'];
$params = [':student_id' => $student_id];

if (!empty($search)) {
    $whereConditions[] = '(e.title LIKE :search OR e.exam_code LIKE :search OR c.title LIKE :search OR c.code LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

if ($status_filter === 'passed') {
    $whereConditions[] = 'r.score_percentage >= e.pass_mark';
} elseif ($status_filter === 'failed') {
    $whereConditions[] = 'r.score_percentage < e.pass_mark';
}

if (!empty($date_from)) {
    $whereConditions[] = 'DATE(r.completed_at) >= :date_from';
    $params[':date_from'] = $date_from;
}

if (!empty($date_to)) {
    $whereConditions[] = 'DATE(r.completed_at) <= :date_to';
    $params[':date_to'] = $date_to;
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

// Get total count
$countQuery = "
    SELECT COUNT(*)
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
$totalResults = $stmt->fetchColumn();
$totalPages = ceil($totalResults / $limit);

// Get results
$resultsQuery = "
    SELECT r.result_id, r.score_percentage, r.total_questions, r.correct_answers, 
           r.incorrect_answers, r.completed_at,
           e.exam_id, e.title as exam_title, e.exam_code, e.pass_mark, e.show_results,
           c.title as course_title, c.code as course_code,
           d.name as department_name,
           CASE WHEN r.score_percentage >= e.pass_mark THEN 'Passed' ELSE 'Failed' END as status
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    JOIN courses c ON e.course_id = c.course_id
    JOIN departments d ON e.department_id = d.department_id
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

// Get statistics
$statsQuery = "
    SELECT 
        COUNT(*) as total_exams,
        AVG(r.score_percentage) as average_score,
        SUM(CASE WHEN r.score_percentage >= e.pass_mark THEN 1 ELSE 0 END) as passed_count,
        SUM(CASE WHEN r.score_percentage < e.pass_mark THEN 1 ELSE 0 END) as failed_count,
        MAX(r.score_percentage) as highest_score,
        MIN(r.score_percentage) as lowest_score
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    WHERE er.student_id = :student_id
";

$stmt = $conn->prepare($statsQuery);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
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
                    <p class="mt-1 text-sm text-gray-500">View your exam results and performance statistics</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Exams</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900"><?php echo $stats['total_exams'] ?: 0; ?></div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                            <i class="fas fa-chart-line text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900"><?php echo $stats['average_score'] ? round($stats['average_score'], 1) : 0; ?>%</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Passed</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900"><?php echo $stats['passed_count'] ?: 0; ?></div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-red-50 rounded-lg p-3">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Failed</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900"><?php echo $stats['failed_count'] ?: 0; ?></div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Filter Results</h2>
                </div>
                <div class="p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                            <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                            <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>Filter
                            </button>
                            <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Exam Results</h2>
                                        <div class="text-sm text-gray-500">
                        Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $limit, $totalResults); ?> of <?php echo $totalResults; ?> results
                    </div>
                </div>
                
                <?php if (empty($results)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Results Found</h3>
                    <p class="text-gray-600">
                        <?php if (!empty($search) || !empty($status_filter) || !empty($date_from) || !empty($date_to)): ?>
                            No results match your current filters. Try adjusting your search criteria.
                        <?php else: ?>
                            You haven't completed any exams yet. Take some exams to see your results here.
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
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['exam_title']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($result['exam_code']); ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['course_title']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($result['course_code']); ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900"><?php echo round($result['score_percentage'], 1); ?>%</div>
                                        <?php if ($result['show_results']): ?>
                                        <div class="ml-2 text-xs text-gray-500">
                                            (<?php echo $result['correct_answers']; ?>/<?php echo $result['total_questions']; ?>)
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $result['status'] === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $result['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo date('M j, Y g:i A', strtotime($result['completed_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="view.php?result_id=<?php echo $result['result_id']; ?>" 
                                           class="text-emerald-600 hover:text-emerald-900 transition-colors duration-200">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($result['show_results']): ?>
                                        <a href="/api/results/printResult.php?result_id=<?php echo $result['result_id']; ?>" 
                                           target="_blank"
                                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalResults); ?> of <?php echo $totalResults; ?> results
                        </div>
                        <div class="flex space-x-2">
                            <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query(array_filter(['search' => $search, 'status' => $status_filter, 'date_from' => $date_from, 'date_to' => $date_to])); ?>" 
                               class="px-3 py-1 text-sm border rounded-md text-gray-500 bg-white border-gray-300 hover:bg-gray-50">
                                Previous
                            </a>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++): 
                                if ($i == $page): ?>
                                    <span class="px-3 py-1 text-sm border rounded-md text-white bg-emerald-600 border-emerald-600">
                                        <?php echo $i; ?>
                                    </span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_filter(['search' => $search, 'status' => $status_filter, 'date_from' => $date_from, 'date_to' => $date_to])); ?>" 
                                       class="px-3 py-1 text-sm border rounded-md text-gray-500 bg-white border-gray-300 hover:bg-gray-50">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif;
                            endfor; ?>

                            <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query(array_filter(['search' => $search, 'status' => $status_filter, 'date_from' => $date_from, 'date_to' => $date_to])); ?>" 
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

            <!-- Performance Chart -->
            <?php if (!empty($results) && count($results) >= 3): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Performance Trend</h2>
                    <p class="text-sm text-gray-600 mt-1">Your exam scores over time</p>
                </div>
                <div class="p-6">
                    <div class="h-64">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php if (!empty($results) && count($results) >= 3): ?>
    <script>
        // Performance Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        const chartData = {
            labels: [
                <?php 
                $chartResults = array_reverse(array_slice($results, 0, 10)); // Last 10 results
                foreach ($chartResults as $index => $result): 
                ?>
                '<?php echo date('M j', strtotime($result['completed_at'])); ?>'<?php echo $index < count($chartResults) - 1 ? ',' : ''; ?>
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Score (%)',
                data: [
                    <?php foreach ($chartResults as $index => $result): ?>
                    <?php echo round($result['score_percentage'], 1); ?><?php echo $index < count($chartResults) - 1 ? ',' : ''; ?>
                    <?php endforeach; ?>
                ],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Pass Mark',
                data: [
                    <?php foreach ($chartResults as $index => $result): ?>
                    <?php echo $result['pass_mark']; ?><?php echo $index < count($chartResults) - 1 ? ',' : ''; ?>
                    <?php endforeach; ?>
                ],
                borderColor: '#EF4444',
                backgroundColor: 'transparent',
                borderWidth: 1,
                borderDash: [5, 5],
                fill: false,
                pointRadius: 0
            }]
        };

        const chart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return `Score: ${context.parsed.y}%`;
                                } else {
                                    return `Pass Mark: ${context.parsed.y}%`;
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Exam Date'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Score (%)'
                        },
                        min: 0,
                        max: 100
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    </script>    
    <?php endif; ?>
<?php endif; ?>
</body>
</html> 