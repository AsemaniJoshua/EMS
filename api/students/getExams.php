<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Start session and check authentication
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $input = json_decode(file_get_contents('php://input'), true);
    $type = isset($input['type']) ? $input['type'] : 'upcoming';
    $student_id = $_SESSION['student_id'];
    
    // Get student information
    $studentQuery = "
        SELECT program_id, department_id, level_id 
        FROM students 
        WHERE student_id = :student_id
    ";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }
    
    $exams = [];
    
    if ($type === 'upcoming') {
        // Get upcoming exams
        $query = "
            SELECT e.exam_id, e.title, e.exam_code, e.description, e.duration_minutes, 
                   e.start_datetime, e.end_datetime, e.total_marks, e.pass_mark, e.max_attempts,
                   c.title as course_title,
                   CASE 
                       WHEN er.registration_id IS NOT NULL THEN 'Registered'
                       ELSE 'Available'
                   END as registration_status
            FROM exams e
            JOIN courses c ON e.course_id = c.course_id
            LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id AND er.student_id = :student_id
            WHERE e.status = 'Approved'
            AND e.start_datetime > NOW()
            AND (c.program_id = :program_id OR c.department_id = :department_id OR c.level_id = :level_id)
            ORDER BY e.start_datetime ASC
        ";
    } elseif ($type === 'ongoing') {
        // Get ongoing exams (registered and currently active)
        $query = "
            SELECT e.exam_id, e.title, e.exam_code, e.description, e.duration_minutes,
                   e.start_datetime, e.end_datetime, e.total_marks, e.pass_mark, e.max_attempts,
                   c.title as course_title,
                   'Registered' as registration_status,
                                      CASE 
                       WHEN r.result_id IS NOT NULL THEN 100
                       ELSE COALESCE(
                           (SELECT COUNT(*) * 100.0 / (SELECT COUNT(*) FROM questions WHERE exam_id = e.exam_id)
                            FROM student_answers sa 
                            JOIN questions q ON sa.question_id = q.question_id 
                            WHERE sa.registration_id = er.registration_id), 0
                       )
                   END as progress
            FROM exams e
            JOIN courses c ON e.course_id = c.course_id
            JOIN exam_registrations er ON e.exam_id = er.exam_id AND er.student_id = :student_id
            LEFT JOIN results r ON er.registration_id = r.registration_id
            WHERE e.status = 'Approved'
            AND NOW() BETWEEN e.start_datetime AND e.end_datetime
            AND r.result_id IS NULL
            ORDER BY e.end_datetime ASC
        ";
    } elseif ($type === 'past') {
        // Get completed exams
        $query = "
            SELECT e.exam_id, e.title, e.exam_code, e.description, e.duration_minutes,
                   e.start_datetime, e.end_datetime, e.total_marks, e.pass_mark, e.max_attempts,
                   c.title as course_title,
                   r.score_percentage, r.correct_answers, r.total_questions, r.completed_at,
                   'Completed' as registration_status
            FROM exams e
            JOIN courses c ON e.course_id = c.course_id
            JOIN exam_registrations er ON e.exam_id = er.exam_id AND er.student_id = :student_id
            JOIN results r ON er.registration_id = r.registration_id
            WHERE e.status = 'Approved'
            ORDER BY r.completed_at DESC
        ";
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':program_id', $student['program_id']);
    $stmt->bindParam(':department_id', $student['department_id']);
    $stmt->bindParam(':level_id', $student['level_id']);
    $stmt->execute();
    
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        $type => $exams
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>

