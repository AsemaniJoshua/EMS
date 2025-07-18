<?php
// API endpoint to get student academic performance data
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
    
    // Get performance over time (last 12 months)
    $performanceQuery = "
        SELECT 
            DATE_FORMAT(r.completed_at, '%Y-%m') as month,
            AVG(r.score_percentage) as average_score,
            COUNT(r.result_id) as exam_count
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        WHERE er.student_id = :student_id
        AND r.completed_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(r.completed_at, '%Y-%m')
        ORDER BY month ASC
    ";
    
    $stmt = $conn->prepare($performanceQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $performanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get subject-wise performance
    $subjectQuery = "
        SELECT 
            d.name as department_name,
            c.title as course_title,
            c.code as course_code,
            AVG(r.score_percentage) as average_score,
            COUNT(r.result_id) as exam_count,
            MAX(r.score_percentage) as highest_score,
            MIN(r.score_percentage) as lowest_score
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN exams e ON er.exam_id = e.exam_id
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON c.department_id = d.department_id
        WHERE er.student_id = :student_id
        GROUP BY d.department_id, c.course_id
        ORDER BY average_score DESC
    ";
    
    $stmt = $conn->prepare($subjectQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $subjectPerformance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent exam results
    $recentResultsQuery = "
        SELECT 
            e.title as exam_title,
            c.code as course_code,
            c.title as course_title,
            r.score_percentage,
            r.completed_at,
            e.pass_mark,
            CASE WHEN r.score_percentage >= e.pass_mark THEN 'Passed' ELSE 'Failed' END as status
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN exams e ON er.exam_id = e.exam_id
        JOIN courses c ON e.course_id = c.course_id
        WHERE er.student_id = :student_id
        ORDER BY r.completed_at DESC
        LIMIT 10
    ";
    
    $stmt = $conn->prepare($recentResultsQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $recentResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get grade distribution
    $gradeDistributionQuery = "
        SELECT 
            CASE 
                WHEN r.score_percentage >= 80 THEN 'A'
                WHEN r.score_percentage >= 70 THEN 'B'
                WHEN r.score_percentage >= 60 THEN 'C'
                WHEN r.score_percentage >= 50 THEN 'D'
                ELSE 'F'
            END as grade,
            COUNT(*) as count
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        WHERE er.student_id = :student_id
        GROUP BY 
            CASE 
                WHEN r.score_percentage >= 80 THEN 'A'
                WHEN r.score_percentage >= 70 THEN 'B'
                WHEN r.score_percentage >= 60 THEN 'C'
                WHEN r.score_percentage >= 50 THEN 'D'
                ELSE 'F'
            END
        ORDER BY grade
    ";
    
    $stmt = $conn->prepare($gradeDistributionQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $gradeDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'performance_over_time' => $performanceData,
            'subject_performance' => $subjectPerformance,
            'recent_results' => $recentResults,
            'grade_distribution' => $gradeDistribution
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
