<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['exam_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Exam ID is required.']);
    exit;
}
$exam_id = intval($data['exam_id']);

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
    echo json_encode(['status' => 'success', 'message' => 'Exam deleted successfully.']);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}