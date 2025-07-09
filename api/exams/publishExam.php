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
    $stmt = $conn->prepare("UPDATE exams SET status = 'Approved' WHERE exam_id = :exam_id");
    $stmt->execute([':exam_id' => $exam_id]);
    echo json_encode(['status' => 'success', 'message' => 'Exam published successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}