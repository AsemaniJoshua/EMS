<?php
// API endpoint to get student dashboard statistics
header('Content-Type: application/json');

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
    
    // Get student information
    $studentQuery = "
        SELECT s.*, p.name as program_name, d.name as department_name, l.name as level_name
        FROM students s
        JOIN programs p ON s.program_id = p.program_id
        JOIN departments d ON s.department_id = d.department_id
        JOIN levels l ON s.level_id = l.level_id
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
    
    // 1. Total Registered Exams
    $stmt = $conn->prepare("SELECT COUNT(*) FROM exam_registrations WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $registeredExams = $stmt->fetchColumn();
    
    // 2. This month's registered exams
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM exam_registrations 
        WHERE student_id = :student_id 
        AND MONTH(registered_at) = MONTH(CURRENT_DATE())
        AND YEAR(registered_at) = YEAR(CURRENT_DATE())
    ");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $thisMonthExams = $stmt->fetchColumn();
    
    // 3. Average Score
    $stmt = $conn->prepare("
        SELECT AVG(r.score_percentage) 
        FROM results r 
        JOIN exam_registrations er ON r.registration_id = er.registration_id 
        WHERE er.student_id = :student_id
    ");

        $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $averageScore = round($stmt->fetchColumn() ?: 0, 1);
    
    // 4. Last term's average for comparison
    $stmt = $conn->prepare("
        SELECT AVG(r.score_percentage) 
        FROM results r 
        JOIN exam_registrations er ON r.registration_id = er.registration_id 
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.student_id = :student_id 
        AND e.created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH)
    ");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $lastTermAverage = round($stmt->fetchColumn() ?: 0, 1);
    $scoreImprovement = $averageScore - $lastTermAverage;
    
    // 5. Pending Exams (Registered but not yet taken)
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM exam_registrations er
        JOIN exams e ON er.exam_id = e.exam_id
        LEFT JOIN results r ON er.registration_id = r.registration_id
        WHERE er.student_id = :student_id 
        AND r.result_id IS NULL
        AND e.status = 'Approved'
        AND NOW() BETWEEN e.start_datetime AND e.end_datetime
    ");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $pendingExams = $stmt->fetchColumn();
    
    // 6. Completed Exams
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM results r 
        JOIN exam_registrations er ON r.registration_id = er.registration_id 
        WHERE er.student_id = :student_id
    ");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $completedExams = $stmt->fetchColumn();
    
    // 7. This week's completed exams
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM results r 
        JOIN exam_registrations er ON r.registration_id = er.registration_id 
        WHERE er.student_id = :student_id 
        AND WEEK(r.completed_at) = WEEK(CURRENT_DATE())
        AND YEAR(r.completed_at) = YEAR(CURRENT_DATE())
    ");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $thisWeekCompleted = $stmt->fetchColumn();
    
    // Fetch upcoming exams
    $upcomingExamsQuery = "
        SELECT e.exam_id, e.title, e.start_datetime, e.end_datetime, e.status,
               CASE 
                   WHEN er.registration_id IS NOT NULL THEN 'Registered'
                   ELSE 'Available'
               END as registration_status
        FROM exams e
        JOIN courses c ON e.course_id = c.course_id
        LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id AND er.student_id = :student_id
        WHERE e.status = 'Approved'
        AND e.start_datetime > NOW()
        AND (c.program_id = :program_id OR c.department_id = :department_id)
        ORDER BY e.start_datetime ASC
        LIMIT 5
    ";
    $stmt = $conn->prepare($upcomingExamsQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':program_id', $student['program_id']);
    $stmt->bindParam(':department_id', $student['department_id']);
    $stmt->execute();
    $upcomingExams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch recent results
    $recentResultsQuery = "
        SELECT r.score_percentage, r.completed_at, e.title, e.pass_mark,
               CASE WHEN r.score_percentage >= e.pass_mark THEN 'Passed' ELSE 'Failed' END as status
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.student_id = :student_id
        ORDER BY r.completed_at DESC
        LIMIT 5
    ";
    $stmt = $conn->prepare($recentResultsQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $recentResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch recent activity
    $recentActivityQuery = "
        SELECT 
            'exam_completed' as activity_type,
            e.title as activity_title,
            r.completed_at as activity_date,
            CONCAT('Scored ', ROUND(r.score_percentage, 1), '%') as activity_description
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.student_id = :student_id
        
        UNION ALL
        
        SELECT 
            'exam_registered' as activity_type,
            e.title as activity_title,
            er.registered_at as activity_date,
            'Registered for exam' as activity_description
        FROM exam_registrations er
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.student_id = :student_id
        AND er.registered_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        
        ORDER BY activity_date DESC
        LIMIT 6
    ";
    $stmt = $conn->prepare($recentActivityQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'student_info' => $student,
            'registered_exams' => $registeredExams,
            'this_month_exams' => $thisMonthExams,
            'average_score' => $averageScore,
            'score_improvement' => $scoreImprovement,
            'pending_exams' => $pendingExams,
            'completed_exams' => $completedExams,
            'this_week_completed' => $thisWeekCompleted,
            'upcoming_exams' => $upcomingExams,
            'recent_results' => $recentResults,
            'recent_activity' => $recentActivity
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Get dashboard stats error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to load dashboard statistics'
    ]);
} catch (Exception $e) {
    error_log("Get dashboard stats error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error occurred'
    ]);
}
?>


    
