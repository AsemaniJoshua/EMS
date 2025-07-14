<?php
// API endpoint to fetch a question and its options for editing (teacher only)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

$question_id = intval($_GET['id'] ?? 0);
$teacher_id = $_SESSION['teacher_id'];

if (!$question_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing question_id.']);
    exit;
}

// Fetch question and exam_id
$stmt = $conn->prepare('SELECT * FROM questions WHERE question_id = :question_id');
$stmt->execute(['question_id' => $question_id]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$question) {
    echo json_encode(['status' => 'error', 'message' => 'Question not found.']);
    exit;
}
$exam_id = $question['exam_id'];
// Check exam ownership
$stmt = $conn->prepare('SELECT * FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id');
$stmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);
if (!$stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Exam not found or not owned by you.']);
    exit;
}
// Fetch options
$stmt = $conn->prepare('SELECT * FROM choices WHERE question_id = :question_id');
$stmt->execute(['question_id' => $question_id]);
$options = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'question' => $question, 'options' => $options]); 