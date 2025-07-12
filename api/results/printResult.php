<?php
// API endpoint to print a detailed result
header('Content-Type: text/html');
require_once '../config/database.php';

// Validate result ID
$resultId = isset($_GET['result_id']) ? intval($_GET['result_id']) : 0;
if ($resultId <= 0) {
    echo '<div style="color:red; text-align:center; padding: 20px;">';
    echo '<h1>Error</h1>';
    echo '<p>Valid result ID is required.</p>';
    echo '</div>';
    exit();
}

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->getConnection();

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
            s.first_name as student_first_name,
            s.last_name as student_last_name,
            CONCAT(s.first_name, ' ', s.last_name) as student_name,
            s.index_number,
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
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo '<div style="color:red; text-align:center; padding: 20px;">';
        echo '<h1>Error</h1>';
        echo '<p>Result not found.</p>';
        echo '</div>';
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

    // Determine if pass or fail
    $isPassed = $result['score_percentage'] >= 50;
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Result Details - <?php echo htmlspecialchars($result['student_name']); ?></title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 900px;
                margin: 0 auto;
                padding: 20px;
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .school-name {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .report-title {
                font-size: 18px;
                margin-bottom: 5px;
                text-transform: uppercase;
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 20px;
                border: 1px solid #ddd;
                padding: 15px;
                border-radius: 5px;
                background-color: #f9f9f9;
            }

            .info-section h3 {
                margin-top: 0;
                margin-bottom: 10px;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5px;
            }

            .info-item {
                margin-bottom: 8px;
            }

            .label {
                font-weight: bold;
                display: inline-block;
                width: 140px;
            }

            .score-section {
                text-align: center;
                margin: 30px 0;
                padding: 15px;
                border-radius: 5px;
                background-color: <?php echo $isPassed ? '#ecfdf5' : '#fef2f2'; ?>;
                border: 2px solid <?php echo $isPassed ? '#10b981' : '#ef4444'; ?>;
            }

            .score {
                font-size: 36px;
                font-weight: bold;
                color: <?php echo $isPassed ? '#047857' : '#b91c1c'; ?>;
                margin: 0;
            }

            .score-details {
                font-size: 16px;
                color: <?php echo $isPassed ? '#047857' : '#b91c1c'; ?>;
            }

            .status {
                display: inline-block;
                padding: 5px 15px;
                border-radius: 20px;
                font-weight: bold;
                color: white;
                background-color: <?php echo $isPassed ? '#10b981' : '#ef4444'; ?>;
                margin-top: 10px;
            }

            .questions-section {
                margin-top: 30px;
            }

            .question {
                margin-bottom: 20px;
                padding: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            .question-text {
                font-weight: bold;
                margin-bottom: 10px;
            }

            .answer {
                margin-left: 20px;
                padding: 5px;
                border-radius: 3px;
            }

            .student-answer {
                background-color: <?php echo $isPassed ? '#ecfdf5' : '#fef2f2'; ?>;
                border-left: 4px solid <?php echo $isPassed ? '#10b981' : '#ef4444'; ?>;
            }

            .correct-answer {
                background-color: #ecfdf5;
                border-left: 4px solid #10b981;
            }

            .footer {
                margin-top: 40px;
                text-align: center;
                font-size: 12px;
                color: #777;
                border-top: 1px solid #ddd;
                padding-top: 20px;
            }

            .print-btn {
                background-color: #2563eb;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                margin-top: 20px;
            }

            @media print {
                .print-btn {
                    display: none;
                }

                .container {
                    padding: 0;
                }

                body {
                    margin: 0;
                }
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="header">
                <div class="school-name">Examination Management System</div>
                <div class="report-title">Exam Result Report</div>
                <div>Generated on: <?php echo date('F j, Y \a\t g:i A'); ?></div>
            </div>

            <div class="info-grid">
                <div class="info-section">
                    <h3>Student Information</h3>
                    <div class="info-item">
                        <span class="label">Name:</span>
                        <?php echo htmlspecialchars($result['student_name']); ?>
                    </div>
                    <div class="info-item">
                        <span class="label">ID:</span>
                        <?php echo htmlspecialchars($result['index_number']); ?>
                    </div>
                    <div class="info-item">
                        <span class="label">Department:</span>
                        <?php echo htmlspecialchars($result['department_name']); ?>
                    </div>
                    <div class="info-item">
                        <span class="label">Program:</span>
                        <?php echo htmlspecialchars($result['program_name']); ?>
                    </div>
                </div>

                <div class="info-section">
                    <h3>Exam Information</h3>
                    <div class="info-item">
                        <span class="label">Exam:</span>
                        <?php echo htmlspecialchars($result['exam_title']); ?>
                    </div>
                    <div class="info-item">
                        <span class="label">Code:</span>
                        <?php echo htmlspecialchars($result['exam_code']); ?>
                    </div>
                    <div class="info-item">
                        <span class="label">Course:</span>
                        <?php echo htmlspecialchars($result['course_code'] . ' - ' . $result['course_title']); ?>
                    </div>
                    <div class="info-item">
                        <span class="label">Date Completed:</span>
                        <?php echo htmlspecialchars($result['completed_at']); ?>
                    </div>
                </div>
            </div>

            <div class="score-section">
                <p class="score"><?php echo number_format($result['score_percentage'], 1); ?>%</p>
                <p class="score-details"><?php echo $result['correct_answers']; ?> correct out of <?php echo $result['total_questions']; ?> questions</p>
                <div class="status"><?php echo $isPassed ? 'PASSED' : 'FAILED'; ?></div>
            </div>

            <?php if (!empty($questions)): ?>
                <div class="questions-section">
                    <h3>Question Analysis</h3>

                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question">
                            <div class="question-text">Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['question_text']); ?></div>

                            <div class="answer student-answer">
                                <strong>Your Answer:</strong> <?php echo htmlspecialchars($question['student_answer']); ?>
                                <?php if ($question['is_correct']): ?>
                                    <span style="color:#047857;"> ✓ Correct</span>
                                <?php else: ?>
                                    <span style="color:#b91c1c;"> ✗ Incorrect</span>
                                <?php endif; ?>
                            </div>

                            <?php if (!$question['is_correct']): ?>
                                <div class="answer correct-answer">
                                    <strong>Correct Answer:</strong> <?php echo htmlspecialchars($question['correct_answer']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <button class="print-btn" onclick="window.print()">Print Report</button>

            <div class="footer">
                <p>This is an official exam result document from the Examination Management System.</p>
                <p>Document ID: <?php echo md5($resultId . $result['student_id'] . $result['exam_id']); ?></p>
                <p>Generated on: <?php echo date('Y-m-d H:i:s'); ?></p>
            </div>
        </div>
    </body>

    </html>
<?php
} catch (Exception $e) {
    echo '<div style="color:red; text-align:center; padding: 20px;">';
    echo '<h1>Error</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
