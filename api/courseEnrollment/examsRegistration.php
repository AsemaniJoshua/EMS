<?php
// API endpoint for direct exam registration
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Start session and check authentication
// if (session_status() == PHP_SESSION_NONE) {
//     ini_set('session.cookie_path', '/');
//     session_start();
// }

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $student_id = $_SESSION['student_id'];
    
    // Get exam ID from request
    $input = json_decode(file_get_contents('php://input'), true);
    $exam_id = isset($input['exam_id']) ? intval($input['exam_id']) : 0;
    
    if ($exam_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Valid exam ID is required']);
        exit;
    }
    
    // Get student information
    $studentQuery = "
        SELECT s.student_id, s.program_id, s.department_id, s.level_id
        FROM students s
        WHERE s.student_id = :student_id
    ";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }
    
    // Get exam information and validate
    $examQuery = "
        SELECT e.exam_id, e.title, e.status, e.start_datetime, e.end_datetime,
               c.program_id, c.department_id, c.level_id
        FROM exams e
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.exam_id = :exam_id
    ";
    $stmt = $conn->prepare($examQuery);
    $stmt->bindParam(':exam_id', $exam_id);
    $stmt->execute();
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$exam) {
        echo json_encode(['success' => false, 'message' => 'Exam not found']);
        exit;
    }
    
    // Check if exam is approved
    if ($exam['status'] !== 'Approved') {
        echo json_encode(['success' => false, 'message' => 'Exam is not available for registration']);
        exit;
    }
    
    // Check if exam is still available for registration
    if (strtotime($exam['start_datetime']) <= time()) {
        echo json_encode(['success' => false, 'message' => 'Registration period for this exam has ended']);
        exit;
    }
    
    // Validate academic compatibility
    $compatible = false;
    if ($exam['program_id'] == $student['program_id'] || 
        $exam['department_id'] == $student['department_id'] ||
        $exam['level_id'] == $student['level_id']) {
        $compatible = true;
    }
    
    if (!$compatible) {
        echo json_encode([
            'success' => false,
            'message' => 'This exam is not available for your program/department/level'
        ]);
        exit;
    }
    
    // Check if student is already registered
    $registrationCheck = "
        SELECT registration_id FROM exam_registrations 
        WHERE exam_id = :exam_id AND student_id = :student_id
    ";
    $stmt = $conn->prepare($registrationCheck);
    $stmt->bindParam(':exam_id', $exam_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false, 
            'message' => 'You are already registered for this exam'
        ]);
        exit;
    }
    
    // Register student for the exam
    $conn->beginTransaction();
    
    $registerQuery = "
        INSERT INTO exam_registrations (exam_id, student_id, registered_at)
        VALUES (:exam_id, :student_id, NOW())
    ";
    $stmt = $conn->prepare($registerQuery);
    $stmt->bindParam(':exam_id', $exam_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully registered for exam: ' . $exam['title']
    ]);
    
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
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
