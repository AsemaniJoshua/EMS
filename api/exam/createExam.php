<?php
// API endpoint to create a new exam (teacher only)
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
$title = trim($input['title'] ?? '');
$exam_code = trim($input['exam_code'] ?? '');
$course_id = intval($input['course_id'] ?? 0);
$duration_minutes = intval($input['duration_minutes'] ?? 0);
$start_datetime = $input['start_datetime'] ?? '';
$end_datetime = $input['end_datetime'] ?? '';
$description = trim($input['description'] ?? '');
$teacher_id = $_SESSION['teacher_id'];

// Validate required fields
if (!$title || !$exam_code || !$course_id || !$duration_minutes || !$start_datetime || !$end_datetime || !$description) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

// Check for duplicate exam code
$stmt = $conn->prepare('SELECT exam_id FROM exams WHERE exam_code = :exam_code');
$stmt->execute(['exam_code' => $exam_code]);
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

// Insert exam
$stmt = $conn->prepare('INSERT INTO exams (exam_code, title, description, department_id, program_id, semester_id, course_id, teacher_id, status, duration_minutes, total_marks, start_datetime, end_datetime, created_at) VALUES (:exam_code, :title, :description, :department_id, :program_id, :semester_id, :course_id, :teacher_id, :status, :duration_minutes, :total_marks, :start_datetime, :end_datetime, NOW())');
$success = $stmt->execute([
    'exam_code' => $exam_code,
    'title' => $title,
    'description' => $description,
    'department_id' => $course['department_id'],
    'program_id' => $course['program_id'],
    'semester_id' => $course['semester_id'],
    'course_id' => $course_id,
    'teacher_id' => $teacher_id,
    'status' => 'Draft',
    'duration_minutes' => $duration_minutes,
    'total_marks' => 100, // Default, can be updated later
    'start_datetime' => $start_datetime,
    'end_datetime' => $end_datetime
]);

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Exam created successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create exam.']);
} 