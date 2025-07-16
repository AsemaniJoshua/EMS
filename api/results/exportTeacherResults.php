<?php
require_once '../config/database.php';
require_once '../login/teacher/teacherSessionCheck.php';

// Verify teacher session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    http_response_code(401);
    echo "Unauthorized access";
    exit;
}

$teacher_id = $_SESSION['teacher_id'];

// Database connection
$db = new Database();
$conn = $db->getConnection();

try {
    // Get parameters
    $exam_id = $_GET['exam_id'] ?? '';
    $export_type = $_GET['export_type'] ?? 'teacher_exams_summary';
    $course_id = $_GET['course_id'] ?? '';
    $status = $_GET['status'] ?? '';
    $date_range = $_GET['date_range'] ?? '';

    // Determine what to export
    if (!empty($exam_id)) {
        // Export specific exam results
        exportExamResults($conn, $teacher_id, $exam_id);
    } else {
        // Export exams summary
        exportExamsSummary($conn, $teacher_id, $course_id, $status, $date_range);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Export error: " . $e->getMessage();
}

function exportExamResults($conn, $teacher_id, $exam_id)
{
    // Verify exam belongs to teacher
    $examStmt = $conn->prepare("SELECT title, exam_code FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id");
    $examStmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);
    $exam = $examStmt->fetch(PDO::FETCH_ASSOC);

    if (!$exam) {
        http_response_code(404);
        echo "Exam not found or access denied";
        exit;
    }

    // Get student results for the exam
    $query = "
        SELECT 
            s.student_id,
            s.first_name,
            s.last_name,
            s.student_number,
            r.score_obtained,
            r.total_score,
            r.score_percentage,
            r.time_taken,
            r.completed_at,
            CASE WHEN r.score_percentage >= 50 THEN 'Pass' ELSE 'Fail' END as result_status
        FROM results r
        JOIN students s ON r.student_id = s.student_id
        WHERE r.exam_id = :exam_id
        ORDER BY s.last_name, s.first_name
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute(['exam_id' => $exam_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers for CSV download
    $filename = "exam_results_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $exam['exam_code']) . "_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create file handle
    $output = fopen('php://output', 'w');

    // Add BOM for UTF-8
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Add exam info header
    fputcsv($output, ['Exam Results Export']);
    fputcsv($output, ['Exam Title:', $exam['title']]);
    fputcsv($output, ['Exam Code:', $exam['exam_code']]);
    fputcsv($output, ['Export Date:', date('Y-m-d H:i:s')]);
    fputcsv($output, ['Total Students:', count($results)]);
    fputcsv($output, []); // Empty row

    // Add headers
    fputcsv($output, [
        'Student ID',
        'Student Number',
        'First Name',
        'Last Name',
        'Score Obtained',
        'Total Score',
        'Percentage',
        'Result',
        'Time Taken (minutes)',
        'Completed At'
    ]);

    // Add data rows
    foreach ($results as $result) {
        $timeTaken = $result['time_taken'] ? round($result['time_taken'] / 60, 1) : 'N/A';

        fputcsv($output, [
            $result['student_id'],
            $result['student_number'],
            $result['first_name'],
            $result['last_name'],
            $result['score_obtained'],
            $result['total_score'],
            $result['score_percentage'] . '%',
            $result['result_status'],
            $timeTaken,
            $result['completed_at'] ? date('Y-m-d H:i:s', strtotime($result['completed_at'])) : 'N/A'
        ]);
    }

    fclose($output);
}

function exportExamsSummary($conn, $teacher_id, $course_id, $status, $date_range)
{
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

    // Query for exams summary
    $query = "
        SELECT 
            e.exam_id,
            e.title,
            e.exam_code,
            e.start_datetime,
            e.end_datetime,
            e.duration,
            e.status,
            c.code as course_code,
            c.title as course_title,
            d.name as department_name,
            p.name as program_name,
            COUNT(DISTINCT r.student_id) as total_students,
            COUNT(DISTINCT CASE WHEN r.score_percentage >= 50 THEN r.student_id END) as passed_students,
            ROUND(AVG(r.score_percentage), 1) as avg_score,
            MIN(r.score_percentage) as min_score,
            MAX(r.score_percentage) as max_score,
            ROUND((COUNT(DISTINCT CASE WHEN r.score_percentage >= 50 THEN r.student_id END) / 
                   NULLIF(COUNT(DISTINCT r.student_id), 0)) * 100, 1) as pass_rate
        FROM exams e
        LEFT JOIN courses c ON e.course_id = c.course_id
        LEFT JOIN programs p ON c.program_id = p.program_id
        LEFT JOIN departments d ON p.department_id = d.department_id
        LEFT JOIN results r ON e.exam_id = r.exam_id
        $whereClause
        GROUP BY e.exam_id, e.title, e.exam_code, e.start_datetime, e.end_datetime, 
                 e.duration, e.status, c.code, c.title, d.name, p.name
        ORDER BY e.start_datetime DESC
    ";

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
    }
    $stmt->execute();
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers for CSV download
    $filename = "teacher_exams_summary_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create file handle
    $output = fopen('php://output', 'w');

    // Add BOM for UTF-8
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Add export info header
    fputcsv($output, ['Teacher Exams Summary Export']);
    fputcsv($output, ['Export Date:', date('Y-m-d H:i:s')]);
    fputcsv($output, ['Total Exams:', count($exams)]);
    fputcsv($output, []); // Empty row

    // Add headers
    fputcsv($output, [
        'Exam ID',
        'Title',
        'Exam Code',
        'Course Code',
        'Course Title',
        'Department',
        'Program',
        'Start Date',
        'End Date',
        'Duration (minutes)',
        'Status',
        'Total Students',
        'Passed Students',
        'Pass Rate (%)',
        'Average Score (%)',
        'Min Score (%)',
        'Max Score (%)'
    ]);

    // Add data rows
    foreach ($exams as $exam) {
        fputcsv($output, [
            $exam['exam_id'],
            $exam['title'],
            $exam['exam_code'],
            $exam['course_code'],
            $exam['course_title'],
            $exam['department_name'],
            $exam['program_name'],
            $exam['start_datetime'] ? date('Y-m-d H:i', strtotime($exam['start_datetime'])) : 'N/A',
            $exam['end_datetime'] ? date('Y-m-d H:i', strtotime($exam['end_datetime'])) : 'N/A',
            $exam['duration'],
            $exam['status'],
            $exam['total_students'] ?: 0,
            $exam['passed_students'] ?: 0,
            $exam['pass_rate'] ?: 0,
            $exam['avg_score'] ?: 'N/A',
            $exam['min_score'] ?: 'N/A',
            $exam['max_score'] ?: 'N/A'
        ]);
    }

    fclose($output);
}
