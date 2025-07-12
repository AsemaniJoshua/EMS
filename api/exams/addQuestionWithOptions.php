<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../../api/login/admin/sessionCheck.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['exam_id'], $data['question_text'], $data['choices']) || !is_array($data['choices'])) {
    echo json_encode(['status' => 'error', 'message' => 'Required data missing.']);
    exit;
}

$exam_id = intval($data['exam_id']);
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

    // Get next sequence number
    $stmt = $conn->prepare("SELECT COALESCE(MAX(sequence_number), 0) + 1 AS next_seq FROM questions WHERE exam_id = :exam_id");
    $stmt->execute([':exam_id' => $exam_id]);
    $next_sequence = $stmt->fetch(PDO::FETCH_ASSOC)['next_seq'];

    // Insert question
    $stmt = $conn->prepare("INSERT INTO questions (exam_id, question_text, sequence_number) 
                            VALUES (:exam_id, :question_text, :sequence_number)");
    $stmt->execute([
        ':exam_id' => $exam_id,
        ':question_text' => $question_text,
        ':sequence_number' => $next_sequence
    ]);

    $question_id = $conn->lastInsertId();

    // Insert choices
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
    echo json_encode(['status' => 'success', 'message' => 'Question and choices added successfully.']);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
