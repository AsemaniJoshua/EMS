<?php
// API endpoint to get student results for a specific exam
header('Content-Type: application/json');
require_once '../config/database.php';

// Validate exam ID
$examId = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
if ($examId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid exam ID is required.']);
    exit();
}

// Get filters
$studentFilter = isset($_GET['student']) ? $_GET['student'] : '';
$scoreMin = isset($_GET['score_min']) ? floatval($_GET['score_min']) : 0;
$scoreMax = isset($_GET['score_max']) ? floatval($_GET['score_max']) : 100;
$status = isset($_GET['status']) ? $_GET['status'] : '';

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->getConnection();

    // Get exam details
    $examQuery = "
        SELECT 
            e.exam_id, 
            e.title, 
            e.exam_code, 
            e.start_datetime AS date, 
            e.total_questions,
            c.code as course_code, 
            c.title as course_title,
            d.name as department_name,
            p.name as program_name,
            (SELECT COUNT(DISTINCT er.student_id) FROM exam_registrations er WHERE er.exam_id = e.exam_id) as registered_students,
            (SELECT COUNT(DISTINCT r.result_id) FROM results r JOIN exam_registrations er ON r.registration_id = er.registration_id WHERE er.exam_id = e.exam_id) as submitted_results
        FROM exams e
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON e.department_id = d.department_id
        JOIN programs p ON e.program_id = p.program_id
        WHERE e.exam_id = :exam_id
    ";

    $examStmt = $conn->prepare($examQuery);
    $examStmt->bindValue(':exam_id', $examId, PDO::PARAM_INT);
    $examStmt->execute();
    $examDetails = $examStmt->fetch(PDO::FETCH_ASSOC);

    if (!$examDetails) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Exam not found.']);
        exit();
    }

    // Build query to get student results for this exam
    $params = [];
    $filters = ['er.exam_id = :exam_id'];
    $params[':exam_id'] = $examId;

    // Apply student filter if provided
    if (!empty($studentFilter)) {
        $filters[] = '(s.first_name LIKE :student OR s.last_name LIKE :student OR s.index_number LIKE :student)';
        $params[':student'] = '%' . $studentFilter . '%';
    }

    // Apply score filter
    $filters[] = 'r.score_percentage >= :score_min AND r.score_percentage <= :score_max';
    $params[':score_min'] = $scoreMin;
    $params[':score_max'] = $scoreMax;

    // Apply status filter if provided
    if ($status === 'pass') {
        $filters[] = 'r.score_percentage >= 50';
    } elseif ($status === 'fail') {
        $filters[] = 'r.score_percentage < 50';
    }

    $whereClause = 'WHERE ' . implode(' AND ', $filters);

    // Query to get student results
    $resultsQuery = "
        SELECT 
            r.result_id,
            r.total_questions,
            r.correct_answers,
            r.incorrect_answers,
            r.score_percentage,
            DATE_FORMAT(r.completed_at, '%M %d, %Y %H:%i') as completed_at,
            s.student_id,
            CONCAT(s.first_name, ' ', s.last_name) as student_name,
            s.index_number,
            s.email,
            p.name as program_name
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN students s ON er.student_id = s.student_id
        JOIN programs p ON s.program_id = p.program_id
        $whereClause
        ORDER BY r.score_percentage DESC, s.last_name ASC
    ";

    $resultsStmt = $conn->prepare($resultsQuery);
    foreach ($params as $key => $value) {
        $resultsStmt->bindValue($key, $value);
    }
    $resultsStmt->execute();
    $studentResults = $resultsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get statistics for this exam
    $statsQuery = "
        SELECT 
            COUNT(r.result_id) as total_results,
            MIN(r.score_percentage) as min_score,
            MAX(r.score_percentage) as max_score,
            AVG(r.score_percentage) as avg_score,
            SUM(CASE WHEN r.score_percentage >= 50 THEN 1 ELSE 0 END) as pass_count,
            SUM(CASE WHEN r.score_percentage < 50 THEN 1 ELSE 0 END) as fail_count
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        WHERE er.exam_id = :exam_id
    ";

    $statsStmt = $conn->prepare($statsQuery);
    $statsStmt->bindValue(':exam_id', $examId, PDO::PARAM_INT);
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    // Add pass rate to stats
    $stats['pass_rate'] = $stats['total_results'] > 0 ?
        round(($stats['pass_count'] / $stats['total_results']) * 100, 1) : 0;

    // Add score distribution
    $distributionQuery = "
        SELECT 
            CASE 
                WHEN r.score_percentage >= 90 THEN '90-100'
                WHEN r.score_percentage >= 80 THEN '80-89'
                WHEN r.score_percentage >= 70 THEN '70-79'
                WHEN r.score_percentage >= 60 THEN '60-69'
                WHEN r.score_percentage >= 50 THEN '50-59'
                WHEN r.score_percentage >= 40 THEN '40-49'
                WHEN r.score_percentage >= 30 THEN '30-39'
                WHEN r.score_percentage >= 20 THEN '20-29'
                WHEN r.score_percentage >= 10 THEN '10-19'
                ELSE '0-9'
            END as range_label,
            COUNT(*) as count
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        WHERE er.exam_id = :exam_id
        GROUP BY range_label
        ORDER BY range_label DESC
    ";

    $distStmt = $conn->prepare($distributionQuery);
    $distStmt->bindValue(':exam_id', $examId, PDO::PARAM_INT);
    $distStmt->execute();
    $distribution = $distStmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data
    echo json_encode([
        'success' => true,
        'exam' => $examDetails,
        'statistics' => $stats,
        'distribution' => $distribution,
        'results' => $studentResults
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch results: ' . $e->getMessage()
    ]);
}
