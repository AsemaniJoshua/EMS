<?php
// API endpoint to get exam results with filtering and pagination
header('Content-Type: application/json');
require_once '../config/database.php';

// Allow both GET and POST methods
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Please use GET or POST.']);
    exit();
}

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->getConnection();

    // Get parameters
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $resultsPerPage = isset($_REQUEST['resultsPerPage']) ? intval($_REQUEST['resultsPerPage']) : 100;
    $offset = ($page - 1) * $resultsPerPage;

    // Build filters
    $filters = [];
    $params = [];

    // Student filter (name or ID)
    if (!empty($_REQUEST['student'])) {
        $student = '%' . $_REQUEST['student'] . '%';
        $filters[] = '(s.first_name LIKE :student OR s.last_name LIKE :student OR s.index_number LIKE :student)';
        $params[':student'] = $student;
    }

    // Exam filter (title or code)
    if (!empty($_REQUEST['exam'])) {
        $exam = '%' . $_REQUEST['exam'] . '%';
        $filters[] = '(e.title LIKE :exam OR e.exam_code LIKE :exam)';
        $params[':exam'] = $exam;
    }

    // Department filter
    if (!empty($_REQUEST['department_id'])) {
        $filters[] = 'e.department_id = :department_id';
        $params[':department_id'] = intval($_REQUEST['department_id']);
    }

    // Program filter
    if (!empty($_REQUEST['program_id'])) {
        $filters[] = 'e.program_id = :program_id';
        $params[':program_id'] = intval($_REQUEST['program_id']);
    }

    // Course filter
    if (!empty($_REQUEST['course_id'])) {
        $filters[] = 'e.course_id = :course_id';
        $params[':course_id'] = intval($_REQUEST['course_id']);
    }

    // Status filter (pass/fail)
    if (!empty($_REQUEST['status'])) {
        if ($_REQUEST['status'] === 'pass') {
            $filters[] = 'r.score_percentage >= 50';
        } else if ($_REQUEST['status'] === 'fail') {
            $filters[] = 'r.score_percentage < 50';
        }
    }

    // Date range filters
    if (!empty($_REQUEST['date_from'])) {
        $filters[] = 'DATE(r.completed_at) >= :date_from';
        $params[':date_from'] = $_REQUEST['date_from'];
    }

    if (!empty($_REQUEST['date_to'])) {
        $filters[] = 'DATE(r.completed_at) <= :date_to';
        $params[':date_to'] = $_REQUEST['date_to'];
    }

    // Build WHERE clause
    $whereClause = '';
    if (!empty($filters)) {
        $whereClause = 'WHERE ' . implode(' AND ', $filters);
    }

    // Determine whether to show exam summaries or individual student results
    $resultType = isset($_REQUEST['result_type']) ? $_REQUEST['result_type'] : 'exams_summary';
    $isExamSummary = ($resultType === 'exams_summary');

    // Count query depends on result type
    if ($isExamSummary) {
        // Count total exams for pagination - only count exams that have at least one result
        $countQuery = "
            SELECT COUNT(DISTINCT e.exam_id) as total 
            FROM exams e
            JOIN exam_registrations er ON e.exam_id = er.exam_id
            JOIN results r ON er.registration_id = r.registration_id
            JOIN courses c ON e.course_id = c.course_id
            JOIN departments d ON e.department_id = d.department_id
            JOIN programs p ON e.program_id = p.program_id
            $whereClause
        ";
    } else {
        // Count total individual results for pagination
        $countQuery = "
            SELECT COUNT(*) as total 
            FROM results r
            JOIN exam_registrations er ON r.registration_id = er.registration_id
            JOIN students s ON er.student_id = s.student_id
            JOIN exams e ON er.exam_id = e.exam_id
            JOIN courses c ON e.course_id = c.course_id
            JOIN departments d ON e.department_id = d.department_id
            JOIN programs p ON e.program_id = p.program_id
            $whereClause
        ";
    }

    $countStmt = $conn->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalResults = $countStmt->fetchColumn();
    $totalPages = ceil($totalResults / $resultsPerPage);

    // Different queries based on result type
    if ($isExamSummary) {
        // Fetch exams with result stats (grouped by exam) - only fetch exams that have at least one completed result
        $query = "
            SELECT 
                e.exam_id,
                e.title as exam_title,
                e.exam_code,
                c.course_id,
                c.code as course_code,
                c.title as course_title,
                d.department_id,
                d.name as department_name,
                p.program_id,
                p.name as program_name,
                e.start_datetime AS date,
                (SELECT COUNT(er2.registration_id) FROM exam_registrations er2 WHERE er2.exam_id = e.exam_id) as total_students,
                COUNT(r.result_id) as submitted_results,
                MIN(r.score_percentage) as min_score,
                MAX(r.score_percentage) as max_score,
                AVG(r.score_percentage) as avg_score,
                ROUND((SUM(CASE WHEN r.score_percentage >= 50 THEN 1 ELSE 0 END) / NULLIF(COUNT(r.result_id), 0)) * 100, 1) as pass_rate,
                SUM(CASE WHEN r.score_percentage >= 50 THEN 1 ELSE 0 END) as pass_count,
                SUM(CASE WHEN r.score_percentage < 50 THEN 1 ELSE 0 END) as fail_count,
                MAX(r.completed_at) as last_completed
            FROM exams e
            JOIN exam_registrations er ON e.exam_id = er.exam_id
            JOIN results r ON er.registration_id = r.registration_id
            JOIN courses c ON e.course_id = c.course_id
            JOIN departments d ON e.department_id = d.department_id
            JOIN programs p ON e.program_id = p.program_id
            $whereClause
            GROUP BY e.exam_id
            HAVING COUNT(r.result_id) > 0
            ORDER BY e.start_datetime DESC, e.title ASC
            LIMIT :offset, :limit
        ";
    } else {
        // Fetch individual student results
        $query = "
            SELECT 
                r.result_id,
                CONCAT(s.first_name, ' ', s.last_name) as student_name,
                s.index_number,
                e.title as exam_title,
                e.exam_code,
                c.code as course_code,
                c.title as course_title,
                d.name as department_name,
                p.name as program_name,
                r.score_percentage,
                r.correct_answers,
                r.total_questions,
                DATE_FORMAT(r.completed_at, '%Y-%m-%d %H:%i') as completed_at
            FROM results r
            JOIN exam_registrations er ON r.registration_id = er.registration_id
            JOIN students s ON er.student_id = s.student_id
            JOIN exams e ON er.exam_id = e.exam_id
            JOIN courses c ON e.course_id = c.course_id
            JOIN departments d ON e.department_id = d.department_id
            JOIN programs p ON e.program_id = p.program_id
            $whereClause
            ORDER BY r.completed_at DESC
            LIMIT :offset, :limit
        ";
    }

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare pagination info
    $firstResult = $totalResults > 0 ? $offset + 1 : 0;
    $lastResult = min($offset + $resultsPerPage, $totalResults);

    // Return results
    echo json_encode([
        'success' => true,
        'results' => $results,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'results_per_page' => $resultsPerPage,
            'total_results' => $totalResults,
            'first_result' => $firstResult,
            'last_result' => $lastResult
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch results: ' . $e->getMessage()
    ]);
}
