<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['exam_id'], $data['question_text'])) {
    echo json_encode(['status' => 'error', 'message' => 'Exam ID and question text are required.']);
    exit;
}

$exam_id = intval($data['exam_id']);
$question_text = trim($data['question_text']);

if (empty($question_text)) {
    echo json_encode(['status' => 'error', 'message' => 'Question text cannot be empty.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    // Get next sequence number
    $stmt = $conn->prepare("SELECT COALESCE(MAX(sequence_number), 0) + 1 AS next_seq FROM questions WHERE exam_id = :exam_id");
    $stmt->execute([':exam_id' => $exam_id]);
    $next_sequence = $stmt->fetch(PDO::FETCH_ASSOC)['next_seq'];

    // Insert question
    $stmt = $conn->prepare("INSERT INTO questions (exam_id, question_text, sequence_number) VALUES (:exam_id, :question_text, :sequence_number)");
    $stmt->execute([
        ':exam_id' => $exam_id,
        ':question_text' => $question_text,
        ':sequence_number' => $next_sequence
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Question added successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}