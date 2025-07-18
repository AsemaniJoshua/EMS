<?php
// API endpoint to get student profile information
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
    
    $student_id = $_SESSION['student_id'];
    
    // Get student profile with related information
    $profileQuery = "
        SELECT 
            s.student_id,
            s.index_number,
            s.username,
            s.first_name,
            s.last_name,
            s.email,
            s.phone_number,
            s.date_of_birth,
            s.gender,
            s.status,
            s.created_at,
            s.updated_at,
            p.name as program_name,
            p.program_id,
            d.name as department_name,
            d.department_id,
            l.name as level_name,
            l.level_id
        FROM students s
        JOIN programs p ON s.program_id = p.program_id
        JOIN departments d ON s.department_id = d.department_id
        JOIN levels l ON s.level_id = l.level_id
        WHERE s.student_id = :student_id
    ";
    
    $stmt = $conn->prepare($profileQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$profile) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Profile not found']);
        exit;
    }
    
    // Get enrolled courses
    $coursesQuery = "
        SELECT DISTINCT
            c.course_id,
            c.code,
            c.title,
            c.credits,
            d.name as department_name,
            COUNT(DISTINCT e.exam_id) as total_exams,
            COUNT(DISTINCT er.registration_id) as registered_exams,
            COUNT(DISTINCT r.result_id) as completed_exams
        FROM courses c
        JOIN departments d ON c.department_id = d.department_id
        LEFT JOIN exams e ON c.course_id = e.course_id AND e.status = 'Approved'
        LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id AND er.student_id = :student_id
        LEFT JOIN results r ON er.registration_id = r.registration_id
        WHERE (c.program_id = :program_id OR c.department_id = :department_id)
        AND c.level_id = :level_id
        GROUP BY c.course_id, c.code, c.title, c.credits, d.name
        ORDER BY c.code
    ";
    
    $stmt = $conn->prepare($coursesQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':program_id', $profile['program_id']);
    $stmt->bindParam(':department_id', $profile['department_id']);
    $stmt->bindParam(':level_id', $profile['level_id']);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get academic statistics
    $statsQuery = "
        SELECT 
            COUNT(DISTINCT er.exam_id) as total_registered_exams,
            COUNT(DISTINCT r.result_id) as total_completed_exams,
            AVG(r.score_percentage) as average_score,
            MAX(r.score_percentage) as highest_score,
            MIN(r.score_percentage) as lowest_score,
            SUM(CASE WHEN r.score_percentage >= e.pass_mark THEN 1 ELSE 0 END) as passed_exams,
            SUM(CASE WHEN r.score_percentage < e.pass_mark THEN 1 ELSE 0 END) as failed_exams
        FROM exam_registrations er
        LEFT JOIN results r ON er.registration_id = r.registration_id
        LEFT JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.student_id = :student_id
    ";
    
    $stmt = $conn->prepare($statsQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get recent activity
    $activityQuery = "
        SELECT 
            'exam_completed' as activity_type,
            e.title as activity_title,
            r.completed_at as activity_date,
            CONCAT('Scored ', ROUND(r.score_percentage, 1), '%') as activity_description,
            CASE WHEN r.score_percentage >= e.pass_mark THEN 'passed' ELSE 'failed' END as activity_status
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.student_id = :student_id
        
        UNION ALL
        
        SELECT 
            'exam_registered' as activity_type,
            e.title as activity_title,
            er.registered_at as activity_date,
            'Registered for exam' as activity_description,
            'registered' as activity_status
        FROM exam_registrations er
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.student_id = :student_id
        AND er.registered_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        
        ORDER BY activity_date DESC
        LIMIT 10
    ";
    
    $stmt = $conn->prepare($activityQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response
    $response = [
        'success' => true,
        'data' => [
            'profile' => [
                'student_id' => $profile['student_id'],
                'index_number' => $profile['index_number'],
                'username' => $profile['username'],
                'first_name' => $profile['first_name'],
                'last_name' => $profile['last_name'],
                'full_name' => trim($profile['first_name'] . ' ' . $profile['last_name']),
                'email' => $profile['email'],
                'phone_number' => $profile['phone_number'],
                'date_of_birth' => $profile['date_of_birth'],
                'gender' => $profile['gender'],
                'status' => $profile['status'],
                'program_name' => $profile['program_name'],
                'department_name' => $profile['department_name'],
                'level_name' => $profile['level_name'],
                'joined_date' => date('M Y', strtotime($profile['created_at'])),
                'last_updated' => $profile['updated_at']
            ],
            'courses' => $courses,
            'statistics' => [
                'total_registered_exams' => (int)$stats['total_registered_exams'],
                'total_completed_exams' => (int)$stats['total_completed_exams'],
                'average_score' => $stats['average_score'] ? round($stats['average_score'], 1) : 0,
                'highest_score' => $stats['highest_score'] ? round($stats['highest_score'], 1) : 0,
                'lowest_score' => $stats['lowest_score'] ? round($stats['lowest_score'], 1) : 0,
                'passed_exams' => (int)$stats['passed_exams'],
                'failed_exams' => (int)$stats['failed_exams'],
                'pass_rate' => $stats['total_completed_exams'] > 0 ? 
                    round(($stats['passed_exams'] / $stats['total_completed_exams']) * 100, 1) : 0
            ],
            'recent_activity' => $recentActivity
        ]
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
