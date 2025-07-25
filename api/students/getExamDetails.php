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
    $exam_id = isset($input['exam_id']) ? intval($input['exam_id']) : 0;
    $student_id = $_SESSION['student_id'];
    
    if ($exam_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Valid exam ID is required']);
        exit;
    }
    
    // Get exam details with registration status
    $query = "
        SELECT e.exam_id, e.title, e.exam_code, e.description, e.duration_minutes,
               e.start_datetime, e.end_datetime, e.total_marks, e.pass_mark, e.max_attempts,
               e.randomize, e.show_results, e.anti_cheating,
               c.title as course_title, c.code as course_code,
               d.name as department_name, p.name as program_name,
               t.first_name as teacher_first_name, t.last_name as teacher_last_name,
               CASE 
                   WHEN er.registration_id IS NOT NULL THEN 'Registered'
                   ELSE 'Not Registered'
               END as registration_status,
               COUNT(q.question_id) as total_questions
        FROM exams e
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON c.department_id = d.department_id
        JOIN programs p ON c.program_id = p.program_id
        JOIN teachers t ON e.teacher_id = t.teacher_id
        LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id AND er.student_id = :student_id
        LEFT JOIN questions q ON e.exam_id = q.exam_id
        WHERE e.exam_id = :exam_id AND e.status = 'Approved'
        GROUP BY e.exam_id
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':exam_id', $exam_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$exam) {
        echo json_encode(['success' => false, 'message' => 'Exam not found or not available']);
        exit;
    }
    
    // Get student's attempt history for this exam
    $attemptsQuery = "
        SELECT COUNT(r.result_id) as attempts_used
        FROM exam_registrations er
        LEFT JOIN results r ON er.registration_id = r.registration_id
        WHERE er.exam_id = :exam_id AND er.student_id = :student_id
    ";
    
    $stmt = $conn->prepare($attemptsQuery);
    $stmt->bindParam(':exam_id', $exam_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    $attempts = $stmt->fetch(PDO::FETCH_ASSOC);
    $exam['attempts_used'] = $attempts['attempts_used'] ?? 0;
    $exam['attempts_remaining'] = $exam['max_attempts'] - $exam['attempts_used'];
    
    echo json_encode([
        'success' => true,
        'exam' => $exam
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
