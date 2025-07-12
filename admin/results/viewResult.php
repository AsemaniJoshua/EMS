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
            r.registration_id,
            r.total_questions,
            r.correct_answers,
            r.incorrect_answers,
            r.score_percentage,
            DATE_FORMAT(r.completed_at, '%M %d, %Y %H:%i') as completed_at,
            s.student_id,
            CONCAT(s.first_name, ' ', s.last_name) as student_name,
            s.index_number,
            s.email,
            e.exam_id,
            e.title as exam_title,
            e.exam_code,
            c.course_id,
            c.code as course_code,
            c.title as course_title,
            d.name as department_name,
            p.name as program_name
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN students s ON er.student_id = s.student_id
        JOIN exams e ON er.exam_id = e.exam_id
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON e.department_id = d.department_id
        JOIN programs p ON e.program_id = p.program_id
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
            sa.choice_id as student_choice_id,
            student_choice.choice_text as student_answer,
            student_choice.is_correct,
            correct_choice.choice_id as correct_choice_id,
            correct_choice.choice_text as correct_answer
        FROM exam_registrations er
        JOIN results r ON er.registration_id = r.registration_id
        JOIN questions q ON q.exam_id = er.exam_id
        JOIN student_answers sa ON sa.question_id = q.question_id AND sa.registration_id = er.registration_id
        JOIN choices student_choice ON student_choice.choice_id = sa.choice_id
        LEFT JOIN choices correct_choice ON correct_choice.question_id = q.question_id AND correct_choice.is_correct = TRUE
        WHERE r.result_id = :result_id
        ORDER BY q.sequence_number
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':result_id', $resultId, PDO::PARAM_INT);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all choices for each question
    $questionChoices = [];
    foreach ($questions as $question) {
        $choicesQuery = "
            SELECT 
                choice_id,
                choice_text,
                is_correct
            FROM choices
            WHERE question_id = :question_id
            ORDER BY choice_id
        ";

        $choicesStmt = $conn->prepare($choicesQuery);
        $choicesStmt->bindValue(':question_id', $question['question_id'], PDO::PARAM_INT);
        $choicesStmt->execute();
        $choices = $choicesStmt->fetchAll(PDO::FETCH_ASSOC);

        $questionChoices[$question['question_id']] = $choices;
    }

    // Add all choices to each question
    foreach ($questions as &$question) {
        $question['all_choices'] = $questionChoices[$question['question_id']];
    }
    unset($question); // Break the reference
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
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300 flex">
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
                                    <div class="font-medium mt-2 mb-2">Answer Options:</div>

                                    <?php foreach ($question['all_choices'] as $choice):
                                        $borderClass = '';
                                        $bgClass = '';
                                        $iconClass = '';
                                        $textClass = '';
                                        $icon = '';

                                        if ($choice['choice_id'] == $question['student_choice_id'] && $choice['is_correct']) {
                                            $borderClass = 'border-emerald-500';
                                            $bgClass = 'bg-emerald-50';
                                            $icon = 'check-circle';
                                            $iconClass = 'text-emerald-500';
                                            $textClass = 'text-emerald-700 font-medium';
                                        } else if ($choice['choice_id'] == $question['student_choice_id']) {
                                            $borderClass = 'border-red-500';
                                            $bgClass = 'bg-red-50';
                                            $icon = 'times-circle';
                                            $iconClass = 'text-red-500';
                                            $textClass = 'text-red-700 line-through';
                                        } else if ($choice['is_correct']) {
                                            $borderClass = 'border-emerald-500';
                                            $bgClass = 'bg-white';
                                            $icon = 'check';
                                            $iconClass = 'text-emerald-500';
                                            $textClass = 'text-emerald-700 font-medium';
                                        } else {
                                            $borderClass = 'border-gray-200';
                                            $bgClass = 'bg-white';
                                            $icon = 'circle';
                                            $iconClass = 'text-gray-400';
                                            $textClass = 'text-gray-700';
                                        }
                                    ?>
                                        <div class="flex items-center ml-2 mt-2 p-2 border-l-4 rounded <?php echo $borderClass; ?> <?php echo $bgClass; ?>">
                                            <span class="mr-2">
                                                <i class="<?php echo $choice['choice_id'] == $question['student_choice_id'] || $choice['is_correct'] ? 'fas' : 'far'; ?> fa-<?php echo $icon; ?> <?php echo $iconClass; ?>"></i>
                                            </span>

                                            <span class="<?php echo $textClass; ?>">
                                                <?php echo htmlspecialchars($choice['choice_text']); ?>

                                                <?php if ($choice['choice_id'] == $question['student_choice_id']): ?>
                                                    <span class="ml-2 text-sm text-gray-500">(Student's answer)</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
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