<?php

/**
 * API Endpoint: Submit Exam For Approval
 * Updates an exam's status from Draft to Pending for admin approval
 */

header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Check request method
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
if (!isset($data['exam_id']) || empty($data['exam_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Exam ID is required'
    ]);
    exit;
}

$examId = intval($data['exam_id']);

// Connect to the database
$db = new Database();
$conn = $db->getConnection();

try {
   
    $teacher_id = $_SESSION['teacher_id'];

    // Check if the exam belongs to this teacher and is in Draft status
    $stmt = $conn->prepare("
        SELECT exam_id, status 
        FROM exams 
        WHERE exam_id = :exam_id AND teacher_id = :teacher_id
    ");
    $stmt->execute([
        'exam_id' => $examId,
        'teacher_id' => $teacher_id
    ]);
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$exam) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Exam not found or you do not have permission to update it'
        ]);
        exit;
    }

    if ($exam['status'] !== 'Draft') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Only exams in Draft status can be submitted for approval'
        ]);
        exit;
    }

    // Check if the exam has any questions
    $stmt = $conn->prepare("
        SELECT COUNT(*) as question_count 
        FROM questions 
        WHERE exam_id = :exam_id
    ");
    $stmt->execute(['exam_id' => $examId]);
    $questionCount = $stmt->fetch(PDO::FETCH_ASSOC)['question_count'];

    if ($questionCount === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Cannot submit an exam without questions'
        ]);
        exit;
    }

    // Update exam status to Pending
    $stmt = $conn->prepare("
        UPDATE exams 
        SET status = 'Pending' 
        WHERE exam_id = :exam_id AND teacher_id = :teacher_id
    ");

    $result = $stmt->execute([
        'exam_id' => $examId,
        'teacher_id' => $teacher_id
    ]);

    if ($result) {
        // Create a notification for admins (if you have a notifications system)
        // This would typically be done with a separate function or table insert

        echo json_encode([
            'status' => 'success',
            'message' => 'Exam submitted for approval successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to submit exam for approval'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
