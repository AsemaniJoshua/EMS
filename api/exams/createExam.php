<?php

/**
 * API Endpoint: Create Exam
 * Allows teachers to create new exams
 */

header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/database.php';


// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get input data - handle both FormData and JSON
$data = $_POST;
if (empty($_POST)) {
    $data = json_decode(file_get_contents('php://input'), true);
}

// Validate required fields
$requiredFields = [
    'title',
    'exam_code',
    'department_id',
    'program_id',
    'semester_id',
    'course_id',
    'start_date',
    'start_time',
    'end_date',
    'end_time',
    'duration_minutes',
    'total_marks',
    'passing_score'
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
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is admin or teacher and set the appropriate user ID
    $is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    $is_teacher = isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true;

    if (!$is_admin && !$is_teacher) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized access'
        ]);
        exit;
    }

    $teacher_id = $is_teacher ? $_SESSION['teacher_id'] : null;
    $admin_id = $is_admin ? $_SESSION['admin_id'] : null;

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
    if (isset($data['start_datetime']) && isset($data['end_datetime'])) {
        // Format from teacher's form (datetime-local)
        $startDateTime = DateTime::createFromFormat('Y-m-d\TH:i', $data['start_datetime']);
        $endDateTime = DateTime::createFromFormat('Y-m-d\TH:i', $data['end_datetime']);
    } else {
        // Format from admin's form (separate date and time fields)
        $startDateTime = DateTime::createFromFormat('Y-m-d H:i', $data['start_date'] . ' ' . $data['start_time']);
        $endDateTime = DateTime::createFromFormat('Y-m-d H:i', $data['end_date'] . ' ' . $data['end_time']);

        // Set the datetime fields for consistency
        $data['start_datetime'] = $data['start_date'] . 'T' . $data['start_time'];
        $data['end_datetime'] = $data['end_date'] . 'T' . $data['end_time'];
    }

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
        'level_id' => $data['level_id'] ?? null,
        'course_id' => $data['course_id'],
        'teacher_id' => $data['teacher_id'],
        'status' => $data['status'] ?? 'Draft',
        'duration_minutes' => $data['duration_minutes'] ?? $data['duration'],
        'pass_mark' => $data['pass_mark'] ?? $data['passing_score'],
        'total_marks' => $data['total_marks'],
        'start_datetime' => $startDateTime->format('Y-m-d H:i:s'),
        'end_datetime' => $endDateTime->format('Y-m-d H:i:s'),
        'max_attempts' => $data['max_attempts'] ?? 1,
        'randomize' => isset($data['randomize']) ? (int)$data['randomize'] : 0,
        'show_results' => isset($data['show_results']) ? (int)$data['show_results'] : 1,
        'anti_cheating' => isset($data['anti_cheating']) ? (int)$data['anti_cheating'] : 0,
        // 'created_by' => $is_admin ? $admin_id : $teacher_id,
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Insert the exam
    $insertStmt = $conn->prepare("
        INSERT INTO exams (
            exam_code, title, description, department_id, program_id, semester_id, level_id,
            course_id, teacher_id, status, duration_minutes, pass_mark, total_marks, 
            start_datetime, end_datetime, max_attempts, randomize, show_results, anti_cheating,
             created_at
        ) VALUES (
            :exam_code, :title, :description, :department_id, :program_id, :semester_id, :level_id,
            :course_id, :teacher_id, :status, :duration_minutes, :pass_mark, :total_marks,
            :start_datetime, :end_datetime, :max_attempts, :randomize, :show_results, :anti_cheating,
            :created_at
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
