<?php

/**
 * API Endpoint: Delete Question
 * This endpoint allows teachers to delete a question and all its associated choices
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
if (!isset($data['question_id']) || empty($data['question_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Question ID is required.'
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
        SELECT q.question_id, q.sequence_number, e.exam_id, e.status, e.teacher_id
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
        throw new Exception('Cannot delete questions from an exam that is already approved or completed.');
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

    // Delete all choices for the question
    $stmt = $conn->prepare("
        DELETE FROM choices
        WHERE question_id = :question_id
    ");
    $stmt->execute(['question_id' => $data['question_id']]);

    // Delete the question
    $stmt = $conn->prepare("
        DELETE FROM questions
        WHERE question_id = :question_id
    ");
    $stmt->execute(['question_id' => $data['question_id']]);

    // Update sequence numbers for remaining questions
    $stmt = $conn->prepare("
        UPDATE questions
        SET sequence_number = sequence_number - 1
        WHERE exam_id = :exam_id AND sequence_number > :old_sequence
    ");
    $stmt->execute([
        'exam_id' => $question['exam_id'],
        'old_sequence' => $question['sequence_number']
    ]);

    // Commit the transaction
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Question deleted successfully.'
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
