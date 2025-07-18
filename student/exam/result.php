<?php
$pageTitle = "Exam Result";
$breadcrumb = "Exam Result";

// Start session and check authentication
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header('Location: /student/login/');
    exit;
}

$result_id = isset($_GET['result_id']) ? intval($_GET['result_id']) : 0;
if ($result_id <= 0) {
    header('Location: /student/results/');
    exit;
}

require_once '../../api/config/database.php';

$student_id = $_SESSION['student_id'];
$db = new Database();
$conn = $db->getConnection();

// Get result details
$resultQuery = "
    SELECT r.result_id, r.score_percentage, r.total_questions, r.correct_answers, 
           r.incorrect_answers, r.completed_at,
           e.exam_id, e.title as exam_title, e.exam_code, e.pass_mark, e.show_results,
           e.total_marks, e.duration_minutes,
           c.title as course_title, c.code as course_code,
           d.name as department_name,
           CASE WHEN r.score_percentage >= e.pass_mark THEN 'Passed' ELSE 'Failed' END as status
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    JOIN courses c ON e.course_id = c.course_id
    JOIN departments d ON e.department_id = d.department_id
    WHERE r.result_id = :result_id AND er.student_id = :student_id
";

$stmt = $conn->prepare($resultQuery);
$stmt->bindParam(':result_id', $result_id);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    header('Location: /student/results/?error=not_found');
    exit;
}

// Get detailed answers if results are shown
$detailedAnswers = [];
if ($result['show_results']) {
    $answersQuery = "
        SELECT q.question_id, q.question_text, q.sequence_number,
               c_selected.choice_id as selected_choice_id, c_selected.choice_text as selected_choice,
               c_correct.choice_id as correct_choice_id, c_correct.choice_text as correct_choice,
               c_selected.is_correct as is_correct
        FROM questions q
        LEFT JOIN student_answers sa ON q.question_id = sa.question_id 
            AND sa.registration_id = (SELECT registration_id FROM results WHERE result_id = :result_id)
        LEFT JOIN choices c_selected ON sa.choice_id = c_selected.choice_id
        LEFT JOIN choices c_correct ON q.question_id = c_correct.question_id AND c_correct.is_correct = 1
        WHERE q.exam_id = :exam_id
        ORDER BY q.sequence_number ASC, q.question_id ASC
    ";
    
    $stmt = $conn->prepare($answersQuery);
    $stmt->bindParam(':result_id', $result_id);
    $stmt->bindParam(':exam_id', $result['exam_id']);
    $stmt->execute();
    $detailedAnswers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <p class="mt-1 text-sm text-gray-500"><?php echo htmlspecialchars($result['exam_title']); ?></p>
                </div>
                <div class="flex space-x-3">
                                        <a href="/student/results/" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Results
                    </a>
                    <?php if ($result['show_results']): ?>
                    <a href="/api/results/printResult.php?result_id=<?php echo $result_id; ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Score -->
                        <div class="text-center">
                            <div class="text-3xl font-bold <?php echo $result['status'] === 'Passed' ? 'text-green-600' : 'text-red-600'; ?> mb-2">
                                <?php echo round($result['score_percentage'], 1); ?>%
                            </div>
                            <div class="text-sm text-gray-500">Final Score</div>
                        </div>
                        
                        <!-- Status -->
                        <div class="text-center">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?php echo $result['status'] === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> mb-2">
                                <i class="fas fa-<?php echo $result['status'] === 'Passed' ? 'check-circle' : 'times-circle'; ?> mr-1"></i>
                                <?php echo $result['status']; ?>
                            </div>
                            <div class="text-sm text-gray-500">Result Status</div>
                        </div>
                        
                        <!-- Correct Answers -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 mb-2">
                                <?php echo $result['correct_answers']; ?>/<?php echo $result['total_questions']; ?>
                            </div>
                            <div class="text-sm text-gray-500">Correct Answers</div>
                        </div>
                        
                        <!-- Pass Mark -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-600 mb-2">
                                <?php echo $result['pass_mark']; ?>%
                            </div>
                            <div class="text-sm text-gray-500">Pass Mark</div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Your Score</span>
                            <span><?php echo round($result['score_percentage'], 1); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="<?php echo $result['status'] === 'Passed' ? 'bg-green-500' : 'bg-red-500'; ?> h-3 rounded-full transition-all duration-300" 
                                 style="width: <?php echo min($result['score_percentage'], 100); ?>%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>0%</span>
                            <span class="text-yellow-600">Pass: <?php echo $result['pass_mark']; ?>%</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Exam Details</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Exam Title</h3>
                            <p class="text-gray-900"><?php echo htmlspecialchars($result['exam_title']); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Exam Code</h3>
                            <p class="text-gray-900"><?php echo htmlspecialchars($result['exam_code']); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Course</h3>
                            <p class="text-gray-900"><?php echo htmlspecialchars($result['course_code'] . ' - ' . $result['course_title']); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Department</h3>
                            <p class="text-gray-900"><?php echo htmlspecialchars($result['department_name']); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Completed At</h3>
                            <p class="text-gray-900"><?php echo date('M j, Y g:i A', strtotime($result['completed_at'])); ?></p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Duration</h3>
                            <p class="text-gray-900"><?php echo $result['duration_minutes']; ?> minutes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Performance Breakdown</h2>
                </div>
                <div class="p-6">
                    <div class="h-64">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Answers -->
            <?php if ($result['show_results'] && !empty($detailedAnswers)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Detailed Review</h2>
                    <p class="text-sm text-gray-600 mt-1">Review your answers and see the correct solutions</p>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($detailedAnswers as $index => $answer): ?>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Question <?php echo $index + 1; ?></h3>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold <?php echo $answer['is_correct'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <i class="fas fa-<?php echo $answer['is_correct'] ? 'check' : 'times'; ?> mr-1"></i>
                                <?php echo $answer['is_correct'] ? 'Correct' : 'Incorrect'; ?>
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-gray-900"><?php echo htmlspecialchars($answer['question_text']); ?></p>
                        </div>
                        
                        <div class="space-y-2">
                            <?php if ($answer['selected_choice_id']): ?>
                            <div class="p-3 rounded-lg <?php echo $answer['is_correct'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'; ?>">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2 text-gray-500"></i>
                                    <span class="font-medium text-gray-700">Your Answer:</span>
                                </div>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($answer['selected_choice']); ?></p>
                            </div>
                            <?php else: ?>
                            <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2 text-gray-500"></i>
                                    <span class="font-medium text-gray-700">Your Answer:</span>
                                </div>
                                <p class="mt-1 text-gray-500 italic">No answer provided</p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!$answer['is_correct'] || !$answer['selected_choice_id']): ?>
                            <div class="p-3 rounded-lg bg-green-50 border border-green-200">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-2 text-green-600"></i>
                                    <span class="font-medium text-green-700">Correct Answer:</span>
                                </div>
                                <p class="mt-1 text-gray-900"><?php echo htmlspecialchars($answer['correct_choice']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Performance Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Correct', 'Incorrect', 'Unanswered'],
                datasets: [{
                    data: [
                        <?php echo $result['correct_answers']; ?>,
                        <?php echo $result['incorrect_answers']; ?>,
                        <?php echo $result['total_questions'] - $result['correct_answers'] - $result['incorrect_answers']; ?>
                    ],
                    backgroundColor: [
                        '#10B981',
                        '#EF4444',
                        '#9CA3AF'
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
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = <?php echo $result['total_questions']; ?>;
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

