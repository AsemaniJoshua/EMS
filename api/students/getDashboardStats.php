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
    
    $student_id = $_SESSION['student_id'];
    
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
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'registered_exams' => $registeredExams,
            'this_month_exams' => $thisMonthExams,
            'average_score' => $averageScore,
            'score_improvement' => $scoreImprovement,
            'pending_exams' => $pendingExams,
            'completed_exams' => $completedExams,
            'this_week_completed' => $thisWeekCompleted
        ]
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
