<?php
// auto_finalize_results.php
// Cron job to auto-insert results after exam end time, send notifications, and email

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/cron_errors.log');

require_once 'config/db.php'; // Database connection

function sendEmail($to, $subject, $body)
{
    $headers = "From: no-reply@examportal.com\r\n" .
        "Reply-To: no-reply@examportal.com\r\n" .
        "Content-Type: text/plain; charset=UTF-8\r\n";
    return mail($to, $subject, $body, $headers);
}

// 1. Get all expired exams
$examQuery = "SELECT exam_id FROM exams WHERE end_datetime <= NOW()";
$examStmt = $pdo->query($examQuery);
$expiredExams = $examStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($expiredExams as $exam) {
    $examId = $exam['exam_id'];

    // 2. Get students with no result yet
    $regQuery = "
        SELECT r.registration_id, r.student_id, u.email, u.first_name
        FROM exam_registrations r
        JOIN users u ON r.student_id = u.user_id
        LEFT JOIN results res ON res.registration_id = r.registration_id
        WHERE r.exam_id = :exam_id AND res.registration_id IS NULL
    ";
    $regStmt = $pdo->prepare($regQuery);
    $regStmt->execute(['exam_id' => $examId]);
    $pendingRegs = $regStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pendingRegs as $reg) {
        $registrationId = $reg['registration_id'];
        $studentId = $reg['student_id'];
        $email = $reg['email'];
        $firstName = $reg['first_name'];

        // 3. Calculate result
        $scoreQuery = "
            SELECT
                COUNT(sa.question_id) AS total_questions,
                SUM(CASE WHEN c.is_correct THEN 1 ELSE 0 END) AS correct_answers,
                SUM(CASE WHEN NOT c.is_correct THEN 1 ELSE 0 END) AS incorrect_answers,
                ROUND((SUM(CASE WHEN c.is_correct THEN 1 ELSE 0 END) / COUNT(sa.question_id)) * 100, 2) AS score_percentage
            FROM student_answers sa
            JOIN choices c ON sa.choice_id = c.choice_id
            WHERE sa.registration_id = :registration_id
        ";
        $scoreStmt = $pdo->prepare($scoreQuery);
        $scoreStmt->execute(['registration_id' => $registrationId]);
        $score = $scoreStmt->fetch(PDO::FETCH_ASSOC);

        if ($score && $score['total_questions'] > 0) {
            // 4. Store in results
            $insertQuery = "
                INSERT INTO results (registration_id, total_questions, correct_answers, incorrect_answers, score_percentage)
                VALUES (:registration_id, :total, :correct, :incorrect, :score)
            ";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([
                'registration_id' => $registrationId,
                'total' => $score['total_questions'],
                'correct' => $score['correct_answers'],
                'incorrect' => $score['incorrect_answers'],
                'score' => $score['score_percentage']
            ]);

            // 5. Insert into notifications table
            $msg = "Your exam was automatically submitted and graded. You scored " . $score['score_percentage'] . "%.";
            $notifyQuery = "INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)";
            $notifyStmt = $pdo->prepare($notifyQuery);
            $notifyStmt->execute([
                'user_id' => $studentId,
                'message' => $msg
            ]);

            // 6. Send email to student
            $emailSubject = "Exam Auto-Submission & Result";
            $emailBody = "Hello $firstName,\n\nYour exam has been automatically submitted and scored.\nYou scored: " . $score['score_percentage'] . "%.\n\nYou can view the full result by logging into your portal.\n\n--\nOnline Exam System";
            sendEmail($email, $emailSubject, $emailBody);
        }
    }
}

// Log execution
file_put_contents('cron_log.txt', date('Y-m-d H:i:s') . " - Auto-finalized results + notifications executed.\n", FILE_APPEND);
