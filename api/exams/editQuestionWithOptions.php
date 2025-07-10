<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../api/login/sessionCheck.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['question_id'], $data['question_text'], $data['choices']) || !is_array($data['choices'])) {
    echo json_encode(['status' => 'error', 'message' => 'Required data missing.']);
    exit;
}

$question_id = intval($data['question_id']);
$question_text = trim($data['question_text']);
$choices = $data['choices'];

if (empty($question_text)) {
    echo json_encode(['status' => 'error', 'message' => 'Question text cannot be empty.']);
    exit;
}

if (count($choices) < 2) {
    echo json_encode(['status' => 'error', 'message' => 'At least two choices are required.']);
    exit;
}

// Check that exactly one choice is marked as correct
$correctCount = 0;
foreach ($choices as $choice) {
    if (!isset($choice['choice_text'], $choice['is_correct'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid choice data.']);
        exit;
    }
    if ($choice['is_correct']) {
        $correctCount++;
    }
}

if ($correctCount !== 1) {
    echo json_encode(['status' => 'error', 'message' => 'Exactly one choice must be marked as correct.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->beginTransaction();

    // Update question text
    $stmt = $conn->prepare("UPDATE questions SET question_text = :question_text WHERE question_id = :question_id");
    $stmt->execute([
        ':question_text' => $question_text,
        ':question_id' => $question_id
    ]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Question not found');
    }

    // Delete existing choices
    $stmt = $conn->prepare("DELETE FROM choices WHERE question_id = :question_id");
    $stmt->execute([':question_id' => $question_id]);

    // Insert new choices
    $choiceStmt = $conn->prepare("INSERT INTO choices (question_id, choice_text, is_correct) 
                                 VALUES (:question_id, :choice_text, :is_correct)");

    foreach ($choices as $choice) {
        $choiceStmt->execute([
            ':question_id' => $question_id,
            ':choice_text' => trim($choice['choice_text']),
            ':is_correct' => $choice['is_correct'] ? 1 : 0
        ]);
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Question and choices updated successfully.']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
