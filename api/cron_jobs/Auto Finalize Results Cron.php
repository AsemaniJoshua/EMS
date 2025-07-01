<?php
// auto_finalize_results.php
// Cron job to auto-insert results after exam end time
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/cron_errors.log');

require_once 'config/db.php';

// 1. Get all exams whose end time has passed
$examQuery = "SELECT exam_id FROM exams WHERE end_datetime <= NOW()";
$examStmt = $pdo->query($examQuery);
$expiredExams = $examStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($expiredExams as $exam) {
    $examId = $exam['exam_id'];

    // 2. Get all students for that exam who have NOT been finalized
    $regQuery = "
        SELECT r.registration_id
        FROM exam_registrations r
        LEFT JOIN results res ON res.registration_id = r.registration_id
        WHERE r.exam_id = :exam_id AND res.registration_id IS NULL
    ";
    $regStmt = $pdo->prepare($regQuery);
    $regStmt->execute(['exam_id' => $examId]);
    $pendingRegs = $regStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pendingRegs as $reg) {
        $registrationId = $reg['registration_id'];

        // 3. Calculate result for each pending registration
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

        // If the student answered at least one question, insert the result
        if ($score && $score['total_questions'] > 0) {
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
        }
    }
}

// Optional: log to file
file_put_contents('cron_log.txt', date('Y-m-d H:i:s') . " - Auto-finalized results executed.\n", FILE_APPEND);
