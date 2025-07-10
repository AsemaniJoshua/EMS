<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['exam_id'], $data['student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Exam ID and Student ID are required.']);
    exit;
}
$exam_id = intval($data['exam_id']);
$student_id = intval($data['student_id']);

$db = new Database();
$conn = $db->getConnection();

try {
    // Find registration_id
    $stmt = $conn->prepare("SELECT registration_id FROM exam_registrations WHERE exam_id = :exam_id AND student_id = :student_id");
    $stmt->execute([':exam_id' => $exam_id, ':student_id' => $student_id]);
    $reg = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$reg) {
        echo json_encode(['status' => 'error', 'message' => 'Registration not found.']);
        exit;
    }
    $registration_id = $reg['registration_id'];

    $conn->beginTransaction();
    // Delete student answers and results (FK constraints)
    $conn->prepare("DELETE FROM student_answers WHERE registration_id = :registration_id")->execute([':registration_id' => $registration_id]);
    $conn->prepare("DELETE FROM results WHERE registration_id = :registration_id")->execute([':registration_id' => $registration_id]);
    // Delete registration
    $conn->prepare("DELETE FROM exam_registrations WHERE registration_id = :registration_id")->execute([':registration_id' => $registration_id]);
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Student removed from exam.']);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}