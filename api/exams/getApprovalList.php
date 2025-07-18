<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get exams with status filter if provided
    $status = isset($_GET['status']) ? $_GET['status'] : 'Pending';
    $statusFilter = $status === 'all' ? '' : " AND e.status = :status";

    $query = "
        SELECT 
            e.exam_id, 
            e.title,
            e.exam_code,
            e.description,
            e.status,
            e.duration_minutes,
            e.start_datetime,
            e.end_datetime,
            e.created_at,
            e.approved_by,
            e.approved_at,
            c.code as course_code,
            c.title as course_title,
            d.name as department_name,
            p.name as program_name,
            CONCAT(t.first_name, ' ', t.last_name) as teacher_name,
            (SELECT COUNT(*) FROM questions q WHERE q.exam_id = e.exam_id) as question_count
        FROM exams e
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON e.department_id = d.department_id
        JOIN programs p ON e.program_id = p.program_id
        JOIN teachers t ON e.teacher_id = t.teacher_id
        WHERE 1=1" . $statusFilter . "
        ORDER BY e.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    if ($status !== 'all') {
        $stmt->bindParam(':status', $status);
    }
    $stmt->execute();
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get statistics for each status
    $statsQuery = "
        SELECT 
            status,
            COUNT(*) as count,
            DATE(NOW()) as today_date
        FROM exams
        WHERE status IN ('Pending', 'Approved', 'Rejected')
        GROUP BY status
    ";
    $statsStmt = $conn->query($statsQuery);
    $stats = [];
    while ($row = $statsStmt->fetch(PDO::FETCH_ASSOC)) {
        $stats[$row['status']] = $row['count'];
    }

    // Get today's stats
    $todayStatsQuery = "
        SELECT 
            status,
            COUNT(*) as count
        FROM exams
        WHERE status IN ('Approved', 'Rejected')
        AND DATE(approved_at) = DATE(NOW())
        GROUP BY status
    ";
    $todayStatsStmt = $conn->query($todayStatsQuery);
    $todayStats = [];
    while ($row = $todayStatsStmt->fetch(PDO::FETCH_ASSOC)) {
        $todayStats[$row['status']] = $row['count'];
    }

    // Get yesterday's stats for comparison
    $yesterdayStatsQuery = "
        SELECT 
            status,
            COUNT(*) as count
        FROM exams
        WHERE status IN ('Approved', 'Rejected')
        AND DATE(approved_at) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))
        GROUP BY status
    ";
    $yesterdayStatsStmt = $conn->query($yesterdayStatsQuery);
    $yesterdayStats = [];
    while ($row = $yesterdayStatsStmt->fetch(PDO::FETCH_ASSOC)) {
        $yesterdayStats[$row['status']] = $row['count'];
    }

    // Calculate response time
    $avgResponseTimeQuery = "
        SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours
        FROM exams
        WHERE status IN ('Approved', 'Rejected')
        AND approved_at IS NOT NULL
        AND DATE(approved_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ";
    $avgResponseTimeStmt = $conn->query($avgResponseTimeQuery);
    $avgResponseHours = $avgResponseTimeStmt->fetchColumn();
    $avgResponseHours = $avgResponseHours ? round($avgResponseHours, 1) : 0;

    // Calculate change in response time
    $prevAvgResponseTimeQuery = "
        SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours
        FROM exams
        WHERE status IN ('Approved', 'Rejected')
        AND approved_at IS NOT NULL
        AND DATE(approved_at) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
        AND DATE(approved_at) < DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ";
    $prevAvgResponseTimeStmt = $conn->query($prevAvgResponseTimeQuery);
    $prevAvgResponseHours = $prevAvgResponseTimeStmt->fetchColumn();
    $prevAvgResponseHours = $prevAvgResponseHours ? round($prevAvgResponseHours, 1) : 0;

    $responseTimeDiff = $prevAvgResponseHours - $avgResponseHours;

    // Calculate today's change from yesterday
    $approvedToday = $todayStats['Approved'] ?? 0;
    $approvedYesterday = $yesterdayStats['Approved'] ?? 0;
    $approvedChange = $approvedToday - $approvedYesterday;

    $rejectedToday = $todayStats['Rejected'] ?? 0;
    $rejectedYesterday = $yesterdayStats['Rejected'] ?? 0;
    $rejectedChange = $rejectedToday - $rejectedYesterday;

    // Return results
    echo json_encode([
        'success' => true,
        'exams' => $exams,
        'stats' => [
            'pending' => $stats['Pending'] ?? 0,
            'approved_today' => $approvedToday,
            'approved_change' => $approvedChange,
            'rejected_today' => $rejectedToday,
            'rejected_change' => $rejectedChange,
            'avg_response_hours' => $avgResponseHours,
            'response_time_diff' => $responseTimeDiff
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch exam approvals: ' . $e->getMessage()
    ]);
}
