<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['question_id'], $data['choice_text'])) {
    echo json_encode(['status' => 'error', 'message' => 'Question ID and choice text are required.']);
    exit;
}

$question_id = intval($data['question_id']);
$choice_text = trim($data['choice_text']);
$is_correct = isset($data['is_correct']) ? (bool)$data['is_correct'] : false;

if (empty($choice_text)) {
    echo json_encode(['status' => 'error', 'message' => 'Choice text cannot be empty.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->beginTransaction();

    // If this is marked as correct, unmark all other choices for this question
    if ($is_correct) {
        $stmt = $conn->prepare("UPDATE choices SET is_correct = 0 WHERE question_id = :question_id");
        $stmt->execute([':question_id' => $question_id]);
    }

    // Insert new choice
    $stmt = $conn->prepare("INSERT INTO choices (question_id, choice_text, is_correct) VALUES (:question_id, :choice_text, :is_correct)");
    $stmt->execute([
        ':question_id' => $question_id,
        ':choice_text' => $choice_text,
        ':is_correct' => $is_correct ? 1 : 0
    ]);

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Option added successfully.']);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}