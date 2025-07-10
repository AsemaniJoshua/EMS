<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['question_id'], $data['question_text'])) {
    echo json_encode(['status' => 'error', 'message' => 'Question ID and question text are required.']);
    exit;
}

$question_id = intval($data['question_id']);
$question_text = trim($data['question_text']);

if (empty($question_text)) {
    echo json_encode(['status' => 'error', 'message' => 'Question text cannot be empty.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("UPDATE questions SET question_text = :question_text WHERE question_id = :question_id");
    $stmt->execute([
        ':question_text' => $question_text,
        ':question_id' => $question_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Question updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Question not found or no changes made.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}