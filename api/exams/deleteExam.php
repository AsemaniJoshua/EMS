<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get data from request (supports both form POST and JSON)
$inputData = file_get_contents('php://input');
if (!empty($inputData)) {
    // Try to decode as JSON
    $data = json_decode($inputData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = null;
    }
}

// If not JSON or JSON parsing failed, try POST data
if (empty($data)) {
    $data = $_POST;
}

// Check exam ID from either source
$exam_id = 0;
if (isset($data['exam_id'])) {
    $exam_id = intval($data['exam_id']);
} else if (isset($data['examId'])) {
    $exam_id = intval($data['examId']);
}

if ($exam_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid exam ID is required.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->beginTransaction();
    // Delete all related data (order matters due to FKs)
    // 1. Get all question IDs for this exam
    $stmt = $conn->prepare("SELECT question_id FROM questions WHERE exam_id = :exam_id");
    $stmt->execute([':exam_id' => $exam_id]);
    $question_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // 2. Delete choices for all questions
    if ($question_ids) {
        $in = implode(',', array_fill(0, count($question_ids), '?'));
        $conn->prepare("DELETE FROM choices WHERE question_id IN ($in)")->execute($question_ids);
    }

    // 3. Delete questions
    $conn->prepare("DELETE FROM questions WHERE exam_id = ?")->execute([$exam_id]);

    // 4. Get all registration IDs for this exam
    $stmt = $conn->prepare("SELECT registration_id FROM exam_registrations WHERE exam_id = :exam_id");
    $stmt->execute([':exam_id' => $exam_id]);
    $registration_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // 5. Delete student answers and results
    if ($registration_ids) {
        $in = implode(',', array_fill(0, count($registration_ids), '?'));
        $conn->prepare("DELETE FROM student_answers WHERE registration_id IN ($in)")->execute($registration_ids);
        $conn->prepare("DELETE FROM results WHERE registration_id IN ($in)")->execute($registration_ids);
    }

    // 6. Delete registrations
    $conn->prepare("DELETE FROM exam_registrations WHERE exam_id = ?")->execute([$exam_id]);

    // 7. Delete the exam
    $conn->prepare("DELETE FROM exams WHERE exam_id = ?")->execute([$exam_id]);

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Exam deleted successfully.']);
} catch (PDOException $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
