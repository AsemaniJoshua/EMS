<?php

/**
 * API Endpoint: Create Exam
 * Allows teachers to create new exams
 */

header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../login/teacher/teacherSessionCheck.php';

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = [
    'title',
    'exam_code',
    'department_id',
    'program_id',
    'semester_id',
    'course_id',
    'start_datetime',
    'end_datetime',
    'duration_minutes',
    'total_marks',
    'pass_mark'
];

foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode([
            'status' => 'error',
            'message' => "Field '{$field}' is required"
        ]);
        exit;
    }
}

// Connect to the database
$db = new Database();
$conn = $db->getConnection();

try {
    $teacher_id = $_SESSION['teacher_id'];

    // Check if exam code already exists
    $checkStmt = $conn->prepare("SELECT exam_id FROM exams WHERE exam_code = :exam_code");
    $checkStmt->execute(['exam_code' => $data['exam_code']]);

    if ($checkStmt->fetch()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Exam code already exists. Please use a different code.'
        ]);
        exit;
    }

    // Validate datetime values
    $startDateTime = DateTime::createFromFormat('Y-m-d\TH:i', $data['start_datetime']);
    $endDateTime = DateTime::createFromFormat('Y-m-d\TH:i', $data['end_datetime']);

    if (!$startDateTime || !$endDateTime) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid date/time format'
        ]);
        exit;
    }

    if ($endDateTime <= $startDateTime) {
        echo json_encode([
            'status' => 'error',
            'message' => 'End time must be after start time'
        ]);
        exit;
    }

    // Prepare data for insertion
    $insertData = [
        'exam_code' => $data['exam_code'],
        'title' => $data['title'],
        'description' => $data['description'] ?? null,
        'department_id' => $data['department_id'],
        'program_id' => $data['program_id'],
        'semester_id' => $data['semester_id'],
        'course_id' => $data['course_id'],
        'teacher_id' => $teacher_id,
        'status' => $data['status'] ?? 'Draft',
        'duration_minutes' => $data['duration_minutes'],
        'pass_mark' => $data['pass_mark'],
        'total_marks' => $data['total_marks'],
        'start_datetime' => $startDateTime->format('Y-m-d H:i:s'),
        'end_datetime' => $endDateTime->format('Y-m-d H:i:s'),
        'max_attempts' => $data['max_attempts'] ?? 1,
        'randomize' => isset($data['randomize']) ? (int)$data['randomize'] : 0,
        'show_results' => isset($data['show_results']) ? (int)$data['show_results'] : 1,
        'anti_cheating' => isset($data['anti_cheating']) ? (int)$data['anti_cheating'] : 1
    ];

    // Insert the exam
    $insertStmt = $conn->prepare("
        INSERT INTO exams (
            exam_code, title, description, department_id, program_id, semester_id, 
            course_id, teacher_id, status, duration_minutes, pass_mark, total_marks, 
            start_datetime, end_datetime, max_attempts, randomize, show_results, anti_cheating
        ) VALUES (
            :exam_code, :title, :description, :department_id, :program_id, :semester_id,
            :course_id, :teacher_id, :status, :duration_minutes, :pass_mark, :total_marks,
            :start_datetime, :end_datetime, :max_attempts, :randomize, :show_results, :anti_cheating
        )
    ");

    $result = $insertStmt->execute($insertData);

    if ($result) {
        $examId = $conn->lastInsertId();

        echo json_encode([
            'status' => 'success',
            'message' => 'Exam created successfully',
            'exam_id' => $examId
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create exam'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
