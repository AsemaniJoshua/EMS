<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['question_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Question ID is required.']);
    exit;
}
$question_id = intval($data['question_id']);

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->beginTransaction();
    // Delete choices first (FK constraint)
    $stmt = $conn->prepare("DELETE FROM choices WHERE question_id = :question_id");
    $stmt->execute([':question_id' => $question_id]);
    // Delete the question
    $stmt = $conn->prepare("DELETE FROM questions WHERE question_id = :question_id");
    $stmt->execute([':question_id' => $question_id]);
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Question deleted successfully.']);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}