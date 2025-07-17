<?php

/**
 * API Endpoint: Edit Exam (Teacher)
 * Updates an existing exam that belongs to the authenticated teacher
 */

header('Content-Type: application/json');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = [
    'examId',
    'title',
    'examCode',
    'departmentId',
    'programId',
    'semesterId',
    'courseId',
    'startDateTime',
    'endDateTime',
    'duration',
    'passMark',
    'totalMarks'
];

foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || trim($input[$field]) === '') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required field: ' . $field
        ]);
        exit;
    }
}

// Extract and sanitize input data
$examId = intval($input['examId']);
$title = trim($input['title']);
$examCode = trim($input['examCode']);
$departmentId = intval($input['departmentId']);
$programId = intval($input['programId']);
$semesterId = intval($input['semesterId']);
$courseId = intval($input['courseId']);
$startDateTime = $input['startDateTime'];
$endDateTime = $input['endDateTime'];
$duration = intval($input['duration']);
$passMark = floatval($input['passMark']);
$totalMarks = intval($input['totalMarks']);
$description = trim($input['description'] ?? '');
$maxAttempts = intval($input['maxAttempts'] ?? 1);

// Handle boolean settings
$randomize = isset($input['randomize']) && $input['randomize'] ? 1 : 0;
$showResults = isset($input['showResults']) && $input['showResults'] ? 1 : 0;
$antiCheating = isset($input['antiCheating']) && $input['antiCheating'] ? 1 : 0;

$teacher_id = $_SESSION['teacher_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if exam exists and belongs to this teacher
    $stmt = $conn->prepare('SELECT exam_id, status FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id');
    $stmt->execute(['exam_id' => $examId, 'teacher_id' => $teacher_id]);
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$exam) {
        echo json_encode(['status' => 'error', 'message' => 'Exam not found or you do not have permission to edit it']);
        exit;
    }

    // Check if exam can be edited (only Draft exams can be edited by teachers)
    if ($exam['status'] !== 'Draft') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Only exams in Draft status can be edited'
        ]);
        exit;
    }

    // Check for duplicate exam code (exclude current exam)
    $stmt = $conn->prepare('SELECT exam_id FROM exams WHERE exam_code = :exam_code AND exam_id != :exam_id');
    $stmt->execute(['exam_code' => $examCode, 'exam_id' => $examId]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Exam code already exists']);
        exit;
    }

    // Validate date/time
    $startDateTimeObj = new DateTime($startDateTime);
    $endDateTimeObj = new DateTime($endDateTime);

    if ($endDateTimeObj <= $startDateTimeObj) {
        echo json_encode(['status' => 'error', 'message' => 'End date and time must be after start date and time']);
        exit;
    }

    // Validate course belongs to the specified department and program
    $stmt = $conn->prepare('
        SELECT c.course_id 
        FROM courses c 
        WHERE c.course_id = :course_id 
        AND c.department_id = :department_id 
        AND c.program_id = :program_id
    ');
    $stmt->execute([
        'course_id' => $courseId,
        'department_id' => $departmentId,
        'program_id' => $programId
    ]);

    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid course selection for the specified department and program']);
        exit;
    }

    // Update exam
    $updateSql = '
        UPDATE exams SET 
            title = :title,
            exam_code = :exam_code,
            description = :description,
            department_id = :department_id,
            program_id = :program_id,
            semester_id = :semester_id,
            course_id = :course_id,
            duration_minutes = :duration_minutes,
            start_datetime = :start_datetime,
            end_datetime = :end_datetime,
            pass_mark = :pass_mark,
            total_marks = :total_marks,
            max_attempts = :max_attempts,
            randomize = :randomize,
            show_results = :show_results,
            anti_cheating = :anti_cheating
        WHERE exam_id = :exam_id AND teacher_id = :teacher_id
    ';

    $stmt = $conn->prepare($updateSql);
    $success = $stmt->execute([
        'title' => $title,
        'exam_code' => $examCode,
        'description' => $description,
        'department_id' => $departmentId,
        'program_id' => $programId,
        'semester_id' => $semesterId,
        'course_id' => $courseId,
        'duration_minutes' => $duration,
        'start_datetime' => $startDateTime,
        'end_datetime' => $endDateTime,
        'pass_mark' => $passMark,
        'total_marks' => $totalMarks,
        'max_attempts' => $maxAttempts,
        'randomize' => $randomize,
        'show_results' => $showResults,
        'anti_cheating' => $antiCheating,
        'exam_id' => $examId,
        'teacher_id' => $teacher_id
    ]);

    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Exam updated successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update exam'
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
