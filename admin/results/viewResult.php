<?php
include_once __DIR__ . '/../../api/login/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';

$currentPage = 'results';
$pageTitle = "Result Details";
$breadcrumb = "Results > Result Details";

// Validate result ID
$resultId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($resultId <= 0) {
    header('Location: index.php');
    exit();
}

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch result details
$resultData = null;
$questions = [];

try {
    // Fetch result details
    $query = "
        SELECT 
            r.result_id,
            r.student_id,
            r.exam_id,
            r.total_questions,
            r.correct_answers,
            r.incorrect_answers,
            r.score_percentage,
            r.completed_at,
            s.first_name,
            s.last_name,
            CONCAT(s.first_name, ' ', s.last_name) as student_name,
            s.index_number,
            s.email,
            p.program_id,
            p.name as program_name,
            e.title as exam_title,
            e.exam_code,
            c.code as course_code,
            c.title as course_title,
            d.name as department_name
        FROM results r
        JOIN students s ON r.student_id = s.student_id
        JOIN programs p ON s.program_id = p.program_id
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN exams e ON er.exam_id = e.exam_id
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON c.department_id = d.department_id
        WHERE r.result_id = :result_id
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':result_id', $resultId, PDO::PARAM_INT);
    $stmt->execute();
    $resultData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resultData) {
        $_SESSION['error'] = "Result not found.";
        header('Location: index.php');
        exit();
    }

    // Fetch questions and student answers
    $query = "
        SELECT 
            q.question_id,
            q.question_text,
            q.sequence_number,
            sa.student_answer,
            (SELECT option_text FROM options WHERE question_id = q.question_id AND is_correct = 1 LIMIT 1) as correct_answer,
            (SELECT option_id FROM options WHERE question_id = q.question_id AND is_correct = 1 LIMIT 1) as correct_option_id,
            sa.option_id as selected_option_id,
            (SELECT is_correct FROM options WHERE option_id = sa.option_id) as is_correct
        FROM questions q
        JOIN student_answers sa ON q.question_id = sa.question_id
        WHERE sa.result_id = :result_id
        ORDER BY q.sequence_number
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':result_id', $resultId, PDO::PARAM_INT);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading result details: " . $e->getMessage();
    header('Location: index.php');
    exit();
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <?php include_once __DIR__ . '/../components/adminHeader.php'; ?>

    <main class="flex">
        <!-- Sidebar -->
        <?php include_once __DIR__ . '/../components/adminSidebar.php'; ?>

        <!-- Main content -->
        <div class="flex-grow p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Result Details</h1>
                    <p class="text-sm text-gray-600"><?php echo $breadcrumb; ?></p>
                </div>
                <div class="flex space-x-2">
                    <a href="index.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Results
                    </a>
                    <button onclick="printResultDetails(<?php echo $resultId; ?>)" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors duration-200">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>

            <!-- Result Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Exam Information -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-900">Exam Information</h3>
                        <div class="space-y-3">
                            <div><span class="font-medium">Exam:</span> <?php echo htmlspecialchars($resultData['exam_title']); ?></div>
                            <div><span class="font-medium">Code:</span> <?php echo htmlspecialchars($resultData['exam_code']); ?></div>
                            <div><span class="font-medium">Course:</span> <?php echo htmlspecialchars($resultData['course_code']); ?> - <?php echo htmlspecialchars($resultData['course_title']); ?></div>
                            <div><span class="font-medium">Department:</span> <?php echo htmlspecialchars($resultData['department_name']); ?></div>
                            <div><span class="font-medium">Date Completed:</span> <?php echo htmlspecialchars($resultData['completed_at']); ?></div>
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-900">Student Information</h3>
                        <div class="space-y-3">
                            <div><span class="font-medium">Name:</span> <?php echo htmlspecialchars($resultData['student_name']); ?></div>
                            <div><span class="font-medium">ID:</span> <?php echo htmlspecialchars($resultData['index_number']); ?></div>
                            <div><span class="font-medium">Program:</span> <?php echo htmlspecialchars($resultData['program_name']); ?></div>
                            <?php if (!empty($resultData['email'])): ?>
                                <div><span class="font-medium">Email:</span> <?php echo htmlspecialchars($resultData['email']); ?></div>
                            <?php endif; ?>
                            <div>
                                <span class="font-medium">Score:</span>
                                <span class="font-semibold <?php echo $resultData['score_percentage'] >= 50 ? 'text-emerald-600' : 'text-red-600'; ?>">
                                    <?php echo number_format($resultData['score_percentage'], 1); ?>%
                                </span>
                            </div>
                            <div>
                                <span class="font-medium">Status:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $resultData['score_percentage'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $resultData['score_percentage'] >= 50 ? 'Passed' : 'Failed'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Analytics -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">Result Analytics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Score Card -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="text-sm text-gray-500 mb-1">Score</div>
                        <div class="text-3xl font-bold <?php echo $resultData['score_percentage'] >= 50 ? 'text-emerald-600' : 'text-red-600'; ?>">
                            <?php echo number_format($resultData['score_percentage'], 1); ?>%
                        </div>
                        <div class="text-sm text-gray-500 mt-2">
                            <?php echo $resultData['correct_answers']; ?> of <?php echo $resultData['total_questions']; ?> questions correct
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="md:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <canvas id="resultChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Questions & Answers -->
            <?php if (!empty($questions)): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Questions & Answers</h3>
                    <div class="space-y-6">
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="font-medium text-gray-900 mb-3">Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['question_text']); ?></div>
                                <div class="ml-4">
                                    <div class="font-medium mt-2">Student's Answer:</div>
                                    <div class="flex items-center ml-2 mt-1">
                                        <span class="mr-2 <?php echo $question['is_correct'] ? 'text-emerald-500' : 'text-red-500'; ?>">
                                            <i class="fas fa-<?php echo $question['is_correct'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                        </span>
                                        <span><?php echo htmlspecialchars($question['student_answer']); ?></span>
                                    </div>

                                    <?php if (!$question['is_correct']): ?>
                                        <div class="font-medium mt-2 text-emerald-600">Correct Answer:</div>
                                        <div class="ml-2 mt-1 text-emerald-600"><?php echo htmlspecialchars($question['correct_answer']); ?></div>
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
        // Set up the chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('resultChart').getContext('2d');

            const correctAnswers = <?php echo $resultData['correct_answers']; ?>;
            const incorrectAnswers = <?php echo $resultData['incorrect_answers']; ?>;

            const chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Correct', 'Incorrect'],
                    datasets: [{
                        data: [correctAnswers, incorrectAnswers],
                        backgroundColor: [
                            '#10B981', // emerald-500
                            '#EF4444' // red-500
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Answer Distribution',
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            });
        });

        /**
         * Prints the result details
         */
        function printResultDetails(resultId) {
            // Open a new window with print-friendly version
            window.open(`../../api/results/printResult.php?result_id=${resultId}`, '_blank');
        }
    </script>
</body>

</html>