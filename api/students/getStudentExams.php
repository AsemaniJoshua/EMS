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
    $type = isset($input['type']) ? $input['type'] : 'all'; // all, upcoming, ongoing, past
    $student_id = $_SESSION['student_id'];
    
    // Get student information for filtering
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
    
    $baseQuery = "
        SELECT 
            e.exam_id,
            e.title,
            e.exam_code,
            e.description,
            e.start_datetime,
            e.end_datetime,
            e.duration_minutes,
            e.pass_mark,
            e.total_marks,
            c.title as course_title,
            c.code as course_code,
            d.name as department_name,
            p.name as program_name,
            er.registration_id,
            er.registered_at,
            r.result_id,
            r.score_percentage,
            r.completed_at,
            CASE 
                WHEN r.result_id IS NOT NULL THEN 'Completed'
                WHEN NOW() > e.end_datetime THEN 'Expired'
                WHEN NOW() BETWEEN e.start_datetime AND e.end_datetime THEN 'Active'
                WHEN NOW() < e.start_datetime THEN 'Upcoming'
                ELSE 'Unknown'
            END as status,
            CASE 
                WHEN r.score_percentage >= e.pass_mark THEN 'Passed'
                WHEN r.score_percentage < e.pass_mark THEN 'Failed'
                ELSE NULL
            END as result_status
        FROM exams e
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON c.department_id = d.department_id
        JOIN programs p ON c.program_id = p.program_id
        LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id AND er.student_id = :student_id
        LEFT JOIN results r ON er.registration_id = r.registration_id
        WHERE e.status = 'Approved'
        AND (c.program_id = :program_id OR c.department_id = :department_id OR c.level_id = :level_id)
    ";
    
    // Add type-specific filters
    switch ($type) {
        case 'upcoming':
            $baseQuery .= " AND NOW() < e.start_datetime AND er.registration_id IS NOT NULL";
            break;
        case 'ongoing':
            $baseQuery .= " AND NOW() BETWEEN e.start_datetime AND e.end_datetime AND er.registration_id IS NOT NULL AND r.result_id IS NULL";
            break;
        case 'past':
            $baseQuery .= " AND r.result_id IS NOT NULL";
            break;
        case 'registered':
            $baseQuery .= " AND er.registration_id IS NOT NULL";
            break;
        case 'available':
            $baseQuery .= " AND er.registration_id IS NULL AND NOW() < e.start_datetime";
            break;
    }
    
    $baseQuery .= " ORDER BY e.start_datetime DESC";
    
    $stmt = $conn->prepare($baseQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':program_id', $student['program_id']);
    $stmt->bindParam(':department_id', $student['department_id']);
    $stmt->bindParam(':level_id', $student['level_id']);
    $stmt->execute();
    
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the results
    $formattedExams = [];
    foreach ($exams as $exam) {
        $formattedExams[] = [
            'exam_id' => $exam['exam_id'],
            'title' => $exam['title'],
            'exam_code' => $exam['exam_code'],
            'description' => $exam['description'],
            'course_title' => $exam['course_title'],
            'course_code' => $exam['course_code'],
            'department_name' => $exam['department_name'],
            'program_name' => $exam['program_name'],
            'start_datetime' => $exam['start_datetime'],
            'end_datetime' => $exam['end_datetime'],
            'duration_minutes' => $exam['duration_minutes'],
            'pass_mark' => $exam['pass_mark'],
            'total_marks' => $exam['total_marks'],
            'status' => $exam['status'],
            'is_registered' => $exam['registration_id'] !== null,
            'registration_id' => $exam['registration_id'],
            'registered_at' => $exam['registered_at'],
            'is_completed' => $exam['result_id'] !== null,
            'score_percentage' => $exam['score_percentage'],
            'result_status' => $exam['result_status'],
            'completed_at' => $exam['completed_at'],
            'formatted_start' => date('M j, Y g:i A', strtotime($exam['start_datetime'])),
            'formatted_end' => date('M j, Y g:i A', strtotime($exam['end_datetime'])),
            'time_remaining' => $exam['status'] === 'Active' ? 
                max(0, strtotime($exam['end_datetime']) - time()) : null
        ];
    }
    
    echo json_encode([
        'success' => true,
        'exams' => $formattedExams,
        'total_count' => count($formattedExams)
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
