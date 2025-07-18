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
        echo json_encode(['success' => false, 'message' => 'Invalid exam ID']);
        exit;
    }
    
    // Get exam data and verify student registration
    $examQuery = "
        SELECT e.exam_id, e.title, e.description, e.duration_minutes, e.pass_mark, 
               e.start_datetime, e.end_datetime, e.anti_cheating, e.show_results,
               er.registration_id, er.registered_at,
               c.title as course_title, c.code as course_code
        FROM exams e
        JOIN exam_registrations er ON e.exam_id = er.exam_id
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.exam_id = :exam_id 
        AND er.student_id = :student_id 
        AND e.status = 'Approved'
    ";
    
    $stmt = $conn->prepare($examQuery);
    $stmt->bindParam(':exam_id', $exam_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$exam) {
        echo json_encode(['success' => false, 'message' => 'Exam not found or not registered']);
        exit;
    }
    
    // Check if exam is currently active
    $now = time();
    $start_time = strtotime($exam['start_datetime']);
    $end_time = strtotime($exam['end_datetime']);
    
    if ($now < $start_time) {
        echo json_encode(['success' => false, 'message' => 'Exam has not started yet']);
        exit;
    }
    
    if ($now > $end_time) {
        echo json_encode(['success' => false, 'message' => 'Exam has expired']);
        exit;
    }
    
    // Check if already completed
    $resultCheck = "
        SELECT result_id FROM results 
        WHERE registration_id = :registration_id
    ";
    $stmt = $conn->prepare($resultCheck);
    $stmt->bindParam(':registration_id', $exam['registration_id']);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Exam already completed']);
        exit;
    }
    
    // Calculate time remaining
    $elapsed_time = $now - $start_time;
    $total_time = $exam['duration_minutes'] * 60;
    $time_remaining = max(0, min($total_time, $end_time - $now));
    
    // Get exam progress if exists
    $progressQuery = "
        SELECT time_remaining FROM exam_progress 
        WHERE registration_id = :registration_id
    ";
    $stmt = $conn->prepare($progressQuery);
    $stmt->bindParam(':registration_id', $exam['registration_id']);
    $stmt->execute();
    
    $progress = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($progress && $progress['time_remaining'] > 0) {
        $time_remaining = min($time_remaining, $progress['time_remaining']);
    }
    
    echo json_encode([
        'success' => true,
        'exam' => $exam,
        'registration_id' => $exam['registration_id'],
        'time_remaining' => $time_remaining,
        'server_time' => $now
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
