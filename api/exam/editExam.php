<?php
// API endpoint to edit an existing exam (teacher only)
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
$title = trim($input['title'] ?? '');
$exam_code = trim($input['exam_code'] ?? '');
$course_id = intval($input['course_id'] ?? 0);
$duration_minutes = intval($input['duration_minutes'] ?? 0);
$start_datetime = $input['start_datetime'] ?? '';
$end_datetime = $input['end_datetime'] ?? '';
$description = trim($input['description'] ?? '');
$teacher_id = $_SESSION['teacher_id'];

// Validate required fields
if (!$exam_id || !$title || !$exam_code || !$course_id || !$duration_minutes || !$start_datetime || !$end_datetime || !$description) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

// Check exam ownership
$stmt = $conn->prepare('SELECT * FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id');
$stmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);
$exam = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$exam) {
    echo json_encode(['status' => 'error', 'message' => 'Exam not found or not owned by you.']);
    exit;
}

// Check for duplicate exam code (exclude current exam)
$stmt = $conn->prepare('SELECT exam_id FROM exams WHERE exam_code = :exam_code AND exam_id != :exam_id');
$stmt->execute(['exam_code' => $exam_code, 'exam_id' => $exam_id]);
if ($stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Exam code already exists.']);
    exit;
}

// Get course info for department, program, semester
$stmt = $conn->prepare('SELECT department_id, program_id, semester_id FROM courses WHERE course_id = :course_id');
$stmt->execute(['course_id' => $course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$course) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid course.']);
    exit;
}

// Update exam
$stmt = $conn->prepare('UPDATE exams SET exam_code = :exam_code, title = :title, description = :description, department_id = :department_id, program_id = :program_id, semester_id = :semester_id, course_id = :course_id, duration_minutes = :duration_minutes, start_datetime = :start_datetime, end_datetime = :end_datetime WHERE exam_id = :exam_id AND teacher_id = :teacher_id');
$success = $stmt->execute([
    'exam_code' => $exam_code,
    'title' => $title,
    'description' => $description,
    'department_id' => $course['department_id'],
    'program_id' => $course['program_id'],
    'semester_id' => $course['semester_id'],
    'course_id' => $course_id,
    'duration_minutes' => $duration_minutes,
    'start_datetime' => $start_datetime,
    'end_datetime' => $end_datetime,
    'exam_id' => $exam_id,
    'teacher_id' => $teacher_id
]);

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Exam updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update exam.']);
} 