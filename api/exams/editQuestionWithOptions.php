<?php

/**
 * API Endpoint: Edit Question With Options
 * This endpoint allows teachers to edit an existing question and its multiple options
 */

header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/database.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (
    !isset($data['question_id']) || empty($data['question_id']) ||
    !isset($data['question_text']) || empty($data['question_text']) ||
    !isset($data['choices']) || !is_array($data['choices']) || count($data['choices']) < 2
) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input data. Required fields: question_id, question_text, and at least two choices.'
    ]);
    exit;
}

// Check if at least one choice is marked as correct
$hasCorrectChoice = false;
foreach ($data['choices'] as $choice) {
    if (isset($choice['is_correct']) && $choice['is_correct']) {
        $hasCorrectChoice = true;
        break;
    }
}

if (!$hasCorrectChoice) {
    echo json_encode([
        'status' => 'error',
        'message' => 'At least one choice must be marked as correct.'
    ]);
    exit;
}

// Connect to the database
$db = new Database();
$conn = $db->getConnection();

try {
    // Begin a transaction
    $conn->beginTransaction();

    // Get question information and check if it exists
    $stmt = $conn->prepare("
        SELECT q.question_id, e.exam_id, e.status, e.teacher_id
        FROM questions q
        JOIN exams e ON q.exam_id = e.exam_id
        WHERE q.question_id = :question_id
    ");
    $stmt->execute(['question_id' => $data['question_id']]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$question) {
        throw new Exception('Question not found.');
    }

    if ($question['status'] === 'Completed' || $question['status'] === 'Approved') {
        throw new Exception('Cannot edit questions for an exam that is already approved or completed.');
    }

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Check if user has permission (either admin or the teacher who created this exam)
    $isTeacher = isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true;
    $isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

    if ($isTeacher) {
        $teacherId = $_SESSION['teacher_id'];
        if ($teacherId != $question['teacher_id']) {
            throw new Exception('You do not have permission to modify this exam.');
        }
    } else if (!$isAdmin) {
        throw new Exception('Unauthorized access.');
    }

    // Update the question
    $stmt = $conn->prepare("
        UPDATE questions
        SET question_text = :question_text
        WHERE question_id = :question_id
    ");
    $stmt->execute([
        'question_text' => $data['question_text'],
        'question_id' => $data['question_id']
    ]);

    // Delete all existing choices
    $stmt = $conn->prepare("
        DELETE FROM choices
        WHERE question_id = :question_id
    ");
    $stmt->execute(['question_id' => $data['question_id']]);

    // Insert the updated choices
    $stmt = $conn->prepare("
        INSERT INTO choices (question_id, choice_text, is_correct)
        VALUES (:question_id, :choice_text, :is_correct)
    ");

    foreach ($data['choices'] as $choice) {
        $stmt->execute([
            'question_id' => $data['question_id'],
            'choice_text' => $choice['choice_text'],
            'is_correct' => $choice['is_correct'] ? 1 : 0
        ]);
    }

    // Commit the transaction
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Question updated successfully.'
    ]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
