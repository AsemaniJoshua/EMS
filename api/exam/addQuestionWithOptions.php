<?php
// API endpoint to add a question with options to an exam (teacher only)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}
ini_set('session.cookie_path', '/');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$input = json_decode(file_get_contents('php://input'), true);
$exam_id = intval($input['exam_id'] ?? 0);
$question_text = trim($input['question_text'] ?? '');
$choice_text = $input['choice_text'] ?? [];
$is_correct = isset($input['is_correct']) ? intval($input['is_correct']) : null;
$teacher_id = $_SESSION['teacher_id'];

if (!$exam_id || !$question_text || !is_array($choice_text) || count($choice_text) < 2 || $is_correct === null) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required and at least 2 options.']);
    exit;
}

// Check exam ownership
$stmt = $conn->prepare('SELECT * FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id');
$stmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);
if (!$stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Exam not found or not owned by you.']);
    exit;
}

// Insert question
$stmt = $conn->prepare('INSERT INTO questions (exam_id, question_text, sequence_number) VALUES (:exam_id, :question_text, :sequence_number)');
$sequence_number = 1;
$stmt2 = $conn->prepare('SELECT MAX(sequence_number) as max_seq FROM questions WHERE exam_id = :exam_id');
$stmt2->execute(['exam_id' => $exam_id]);
if ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $sequence_number = intval($row['max_seq']) + 1;
}
$success = $stmt->execute([
    'exam_id' => $exam_id,
    'question_text' => $question_text,
    'sequence_number' => $sequence_number
]);
if (!$success) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add question.']);
    exit;
}
$question_id = $conn->lastInsertId();

// Insert options
$stmt = $conn->prepare('INSERT INTO choices (question_id, choice_text, is_correct) VALUES (:question_id, :choice_text, :is_correct)');
foreach ($choice_text as $i => $text) {
    $stmt->execute([
        'question_id' => $question_id,
        'choice_text' => trim($text),
        'is_correct' => ($i == $is_correct ? 1 : 0)
    ]);
}
echo json_encode(['status' => 'success', 'message' => 'Question added successfully']); 