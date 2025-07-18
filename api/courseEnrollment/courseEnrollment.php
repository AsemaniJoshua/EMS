<?php
// API endpoint for student course enrollment using enrollment key
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
    
    // Get enrollment key from request
    $input = json_decode(file_get_contents('php://input'), true);
    $enrollmentKey = isset($input['enrollment_key']) ? trim($input['enrollment_key']) : '';
    
    if (empty($enrollmentKey)) {
        echo json_encode(['success' => false, 'message' => 'Enrollment key is required']);
        exit;
    }
    
    // Get student information to validate program/department compatibility
    $studentQuery = "
        SELECT s.student_id, s.program_id, s.department_id, s.level_id,
               p.name as program_name, d.name as department_name
        FROM students s
        JOIN programs p ON s.program_id = p.program_id
        JOIN departments d ON s.department_id = d.department_id
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
    
    // Find course/exam by enrollment key
    // Assuming enrollment key could be exam_code or a custom enrollment field
    $courseQuery = "
        SELECT c.course_id, c.code, c.title, c.program_id, c.department_id, c.level_id,
               p.name as program_name, d.name as department_name,
               COUNT(e.exam_id) as available_exams
        FROM courses c
        JOIN programs p ON c.program_id = p.program_id
        JOIN departments d ON c.department_id = d.department_id
        LEFT JOIN exams e ON c.course_id = e.course_id AND e.status = 'Approved'
        WHERE c.code = :enrollment_key
        GROUP BY c.course_id, c.code, c.title, c.program_id, c.department_id, c.level_id, p.name, d.name
    ";
    
    $stmt = $conn->prepare($courseQuery);
    $stmt->bindParam(':enrollment_key', $enrollmentKey);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If not found by course code, try to find by exam code
    if (!$course) {
        $examQuery = "
            SELECT e.exam_id, e.exam_code, e.title, e.course_id, e.status,
                   c.code as course_code, c.title as course_title,
                   c.program_id, c.department_id, c.level_id,
                   p.name as program_name, d.name as department_name
            FROM exams e
            JOIN courses c ON e.course_id = c.course_id
            JOIN programs p ON c.program_id = p.program_id
            JOIN departments d ON c.department_id = d.department_id
            WHERE e.exam_code = :enrollment_key AND e.status = 'Approved'
        ";
        
        $stmt = $conn->prepare($examQuery);
        $stmt->bindParam(':enrollment_key', $enrollmentKey);
        $stmt->execute();
        $exam = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($exam) {
            // Check if student is already registered for this exam
            $registrationCheck = "
                SELECT registration_id FROM exam_registrations 
                WHERE exam_id = :exam_id AND student_id = :student_id
            ";
            $stmt = $conn->prepare($registrationCheck);
            $stmt->bindParam(':exam_id', $exam['exam_id']);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'You are already registered for this exam'
                ]);
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
            
            // Register student for the exam
            $conn->beginTransaction();
            
            $registerQuery = "
                INSERT INTO exam_registrations (exam_id, student_id, registered_at)
                VALUES (:exam_id, :student_id, NOW())
            ";
            $stmt = $conn->prepare($registerQuery);
            $stmt->bindParam(':exam_id', $exam['exam_id']);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Successfully registered for exam: ' . $exam['title'],
                'data' => [
                    'type' => 'exam',
                    'exam_id' => $exam['exam_id'],
                    'exam_title' => $exam['title'],
                    'course_title' => $exam['course_title']
                ]
            ]);
            exit;
        }
    }
    
    if (!$course) {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid enrollment key. No course or exam found.'
        ]);
        exit;
    }
    
    // Validate academic compatibility for course
    $compatible = false;
    if ($course['program_id'] == $student['program_id'] || 
        $course['department_id'] == $student['department_id'] ||
        $course['level_id'] == $student['level_id']) {
        $compatible = true;
    }
    
    if (!$compatible) {
        echo json_encode([
            'success' => false,
            'message' => 'This course is not available for your program: ' . $student['program_name']
        ]);
        exit;
    }
    
    // Check if there are available exams for this course
    if ($course['available_exams'] == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'No available exams found for this course'
        ]);
        exit;
    }
    
    // Get available exams for this course
    $availableExamsQuery = "
        SELECT e.exam_id, e.title, e.exam_code, e.start_datetime, e.end_datetime,
               CASE 
                   WHEN er.registration_id IS NOT NULL THEN 'Already Registered'
                   WHEN e.start_datetime > NOW() THEN 'Available'
                   WHEN NOW() BETWEEN e.start_datetime AND e.end_datetime THEN 'Active'
                   ELSE 'Expired'
               END as status
        FROM exams e
        LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id AND er.student_id = :student_id
        WHERE e.course_id = :course_id AND e.status = 'Approved'
        ORDER BY e.start_datetime ASC
    ";
    
    $stmt = $conn->prepare($availableExamsQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':course_id', $course['course_id']);
    $stmt->execute();
    $availableExams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Course found: ' . $course['title'],
        'data' => [
            'type' => 'course',
            'course' => $course,
            'available_exams' => $availableExams
        ]
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
