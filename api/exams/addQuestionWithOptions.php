<?php

/**
 * API Endpoint: Add Question With Options
 * This endpoint allows teachers to add a new question with multiple options to an exam
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
    !isset($data['exam_id']) || empty($data['exam_id']) ||
    !isset($data['question_text']) || empty($data['question_text']) ||
    !isset($data['choices']) || !is_array($data['choices']) || count($data['choices']) < 2
) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input data. Required fields: exam_id, question_text, and at least two choices.'
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

    // Check if the exam exists and is not completed or already approved
    $stmt = $conn->prepare("
        SELECT status, teacher_id 
        FROM exams 
        WHERE exam_id = :exam_id
    ");
    $stmt->execute(['exam_id' => $data['exam_id']]);
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$exam) {
        throw new Exception('Exam not found.');
    }

    if ($exam['status'] === 'Completed' || $exam['status'] === 'Approved') {
        throw new Exception('Cannot add questions to an exam that is already approved or completed.');
    }

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // // Check if user has permission (either admin or the teacher who created this exam)
    $isTeacher = isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true;
    $isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

    if ($isTeacher) {
        $teacherId = $_SESSION['teacher_id'];
        if ($teacherId != $exam['teacher_id']) {
            throw new Exception('You do not have permission to modify this exam.');
        }
    } else if (!$isAdmin) {
        throw new Exception('Unauthorized access.');
    }

    // Get the next sequence number for the question
    $stmt = $conn->prepare("
        SELECT COALESCE(MAX(sequence_number), 0) + 1 as next_sequence 
        FROM questions 
        WHERE exam_id = :exam_id
    ");
    $stmt->execute(['exam_id' => $data['exam_id']]);
    $nextSequence = $stmt->fetch(PDO::FETCH_ASSOC)['next_sequence'];

    // Insert the question
    $stmt = $conn->prepare("
        INSERT INTO questions (exam_id, question_text, sequence_number)
        VALUES (:exam_id, :question_text, :sequence_number)
    ");
    $stmt->execute([
        'exam_id' => $data['exam_id'],
        'question_text' => $data['question_text'],
        'sequence_number' => $nextSequence
    ]);

    $questionId = $conn->lastInsertId();

    // Insert the choices
    $stmt = $conn->prepare("
        INSERT INTO choices (question_id, choice_text, is_correct)
        VALUES (:question_id, :choice_text, :is_correct)
    ");

    foreach ($data['choices'] as $choice) {
        $stmt->execute([
            'question_id' => $questionId,
            'choice_text' => $choice['choice_text'],
            'is_correct' => $choice['is_correct'] ? 1 : 0
        ]);
    }

    // Commit the transaction
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Question added successfully.',
        'question_id' => $questionId
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
