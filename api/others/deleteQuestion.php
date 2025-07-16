<?php
// API endpoint to delete a question (and its options) for an exam (teacher only)
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
$question_id = intval($input['question_id'] ?? 0);
$exam_id = intval($input['exam_id'] ?? 0);
$teacher_id = $_SESSION['teacher_id'];

if (!$question_id || !$exam_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing question_id or exam_id.']);
    exit;
}

// Check exam ownership
$stmt = $conn->prepare('SELECT * FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id');
$stmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);
if (!$stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Exam not found or not owned by you.']);
    exit;
}

// Delete options
$stmt = $conn->prepare('DELETE FROM choices WHERE question_id = :question_id');
$stmt->execute(['question_id' => $question_id]);
// Delete question
$stmt = $conn->prepare('DELETE FROM questions WHERE question_id = :question_id AND exam_id = :exam_id');
$success = $stmt->execute(['question_id' => $question_id, 'exam_id' => $exam_id]);
if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Question deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete question.']);
} 