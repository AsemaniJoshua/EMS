<?php
// API endpoint to delete an exam (teacher only)
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
$exam_id = isset($input['exam_id']) ? intval($input['exam_id']) : 0;
$teacher_id = $_SESSION['teacher_id'];

if (!$exam_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing exam_id']);
    exit;
}

// Check if the exam belongs to this teacher
$stmt = $conn->prepare('SELECT exam_id FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id');
$stmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);
if (!$stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Exam not found or not owned by you']);
    exit;
}

// Delete the exam
$stmt = $conn->prepare('DELETE FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id');
if ($stmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id])) {
    echo json_encode(['status' => 'success', 'message' => 'Exam deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete exam']);
} 