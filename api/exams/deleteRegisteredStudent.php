<?php

/**
 * API Endpoint: Delete Registered Student
 * This endpoint allows teachers to remove a student from an exam registration list
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
    !isset($data['student_id']) || empty($data['student_id'])
) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Exam ID and Student ID are required.'
    ]);
    exit;
}

// Connect to the database
$db = new Database();
$conn = $db->getConnection();

try {
    // Begin a transaction
    $conn->beginTransaction();

    // Check if the exam exists and is not completed
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

    if ($exam['status'] === 'Completed') {
        throw new Exception('Cannot modify registrations for a completed exam.');
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
        if ($teacherId != $exam['teacher_id']) {
            throw new Exception('You do not have permission to modify this exam.');
        }
    } else if (!$isAdmin) {
        throw new Exception('Unauthorized access.');
    }

    // Check if the student is registered for the exam
    $stmt = $conn->prepare("
        SELECT registration_id 
        FROM exam_registrations 
        WHERE exam_id = :exam_id AND student_id = :student_id
    ");
    $stmt->execute([
        'exam_id' => $data['exam_id'],
        'student_id' => $data['student_id']
    ]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$registration) {
        throw new Exception('Student is not registered for this exam.');
    }

    // Check if the student has already submitted answers
    $stmt = $conn->prepare("
        SELECT COUNT(*) as answer_count
        FROM student_answers
        WHERE registration_id = :registration_id
    ");
    $stmt->execute(['registration_id' => $registration['registration_id']]);
    $answerCount = $stmt->fetch(PDO::FETCH_ASSOC)['answer_count'];

    if ($answerCount > 0) {
        throw new Exception('Cannot remove this student as they have already submitted answers for this exam.');
    }

    // Delete the registration
    $stmt = $conn->prepare("
        DELETE FROM exam_registrations
        WHERE registration_id = :registration_id
    ");
    $stmt->execute(['registration_id' => $registration['registration_id']]);

    // Commit the transaction
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Student successfully removed from the exam registration.'
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
