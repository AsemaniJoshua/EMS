<?php
require_once '../config/database.php';
require_once '../login/teacher/teacherSessionCheck.php';

// Verify teacher session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$teacher_id = $_SESSION['teacher_id'];

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Set content type
header('Content-Type: application/json');

try {
    // Get filter parameters
    $course_id = $_POST['course_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $date_range = $_POST['date_range'] ?? '';
    $page = intval($_POST['page'] ?? 1);
    $resultsPerPage = intval($_POST['resultsPerPage'] ?? 10);

    // Validate page number
    if ($page < 1) $page = 1;
    if ($resultsPerPage < 1 || $resultsPerPage > 100) $resultsPerPage = 10;

    $offset = ($page - 1) * $resultsPerPage;

    // Build WHERE clause
    $whereConditions = ['e.teacher_id = :teacher_id'];
    $params = ['teacher_id' => $teacher_id];

    if (!empty($course_id)) {
        $whereConditions[] = 'e.course_id = :course_id';
        $params['course_id'] = $course_id;
    }

    if (!empty($status)) {
        $whereConditions[] = 'e.status = :status';
        $params['status'] = $status;
    }

    // Date range filter
    if (!empty($date_range)) {
        switch ($date_range) {
            case 'last_week':
                $whereConditions[] = 'e.start_datetime >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
                break;
            case 'last_month':
                $whereConditions[] = 'e.start_datetime >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                break;
            case 'last_3_months':
                $whereConditions[] = 'e.start_datetime >= DATE_SUB(NOW(), INTERVAL 3 MONTH)';
                break;
            case 'last_6_months':
                $whereConditions[] = 'e.start_datetime >= DATE_SUB(NOW(), INTERVAL 6 MONTH)';
                break;
        }
    }

    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

    // Main query to get exam results with statistics
    $query = "
        SELECT 
            e.exam_id,
            e.title,
            e.exam_code,
            e.start_datetime,
            e.end_datetime,
            e.status,
            e.pass_mark,
            c.course_id,
            c.code as course_code,
            c.title as course_title,
            COUNT(DISTINCT er.student_id) as total_students,
            COUNT(DISTINCT CASE WHEN r.score_percentage >= e.pass_mark THEN er.student_id END) as passed_students,
            ROUND(AVG(r.score_percentage), 1) as avg_score,
            MAX(r.completed_at) as last_result_date
        FROM exams e
        LEFT JOIN courses c ON e.course_id = c.course_id
        LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id
        LEFT JOIN results r ON er.registration_id = r.registration_id
        $whereClause
        GROUP BY e.exam_id, e.title, e.exam_code, e.start_datetime, e.end_datetime, 
                 e.status, e.pass_mark, c.course_id, c.code, c.title
        ORDER BY e.start_datetime DESC
        LIMIT :offset, :limit
    ";

    $stmt = $conn->prepare($query);

    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);

    $stmt->execute();
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countQuery = "
        SELECT COUNT(DISTINCT e.exam_id) as total
        FROM exams e
        LEFT JOIN courses c ON e.course_id = c.course_id
        LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id
        LEFT JOIN results r ON er.registration_id = r.registration_id
        $whereClause
    ";

    $countStmt = $conn->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalResults = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Calculate pagination info
    $totalPages = ceil($totalResults / $resultsPerPage);
    $firstResult = $totalResults > 0 ? $offset + 1 : 0;
    $lastResult = min($offset + $resultsPerPage, $totalResults);

    // Prepare response
    $response = [
        'status' => 'success',
        'exams' => $exams,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_results' => intval($totalResults),
            'results_per_page' => $resultsPerPage,
            'first_result' => $firstResult,
            'last_result' => $lastResult
        ]
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
