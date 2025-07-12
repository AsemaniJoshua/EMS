<?php
// API endpoint to get details for a specific result
header('Content-Type: application/json');
require_once '../config/database.php';

// Validate result ID
$resultId = isset($_GET['result_id']) ? intval($_GET['result_id']) : 0;
if ($resultId <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Valid result ID is required.']);
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
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Result not found.']);
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

    // Add questions to result
    $result['questions'] = $questions;

    // Return the data
    echo json_encode([
        'success' => true,
        'result' => $result
    ]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch result details: ' . $e->getMessage()
    ]);
}
