<?php
// API endpoint to export results to CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="exam_results_export_' . date('Y-m-d') . '.csv"');
require_once '../config/database.php';

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Determine the export type
$exportType = isset($_REQUEST['export_type']) ? $_REQUEST['export_type'] : 'student_results';

// Set appropriate headers based on export type
if ($exportType === 'exams_summary') {
    // For exam summary data (aggregated by exam)
    fputcsv($output, [
        'Exam Title',
        'Exam Code',
        'Course Code',
        'Course Title',
        'Department',
        'Program',
        'Total Students',
        'Submitted Results',
        'Last Completion Date',
        'Average Score (%)',
        'Min Score (%)',
        'Max Score (%)',
        'Pass Rate (%)',
        'Pass Count',
        'Fail Count'
    ]);
} else {
    // For individual student results (default)
    fputcsv($output, [
        'Student Name',
        'Student ID',
        'Exam Title',
        'Exam Code',
        'Course',
        'Department',
        'Program',
        'Score (%)',
        'Correct Answers',
        'Total Questions',
        'Status',
        'Completion Date'
    ]);
}

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->getConnection();

    // Build filters from request parameters
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

    // Specific exam ID filter
    if (!empty($_REQUEST['exam_id'])) {
        $filters[] = 'e.exam_id = :exam_id';
        $params[':exam_id'] = intval($_REQUEST['exam_id']);
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

    // Score range filters
    if (!empty($_REQUEST['score_min'])) {
        $filters[] = 'r.score_percentage >= :score_min';
        $params[':score_min'] = floatval($_REQUEST['score_min']);
    }

    if (!empty($_REQUEST['score_max'])) {
        $filters[] = 'r.score_percentage <= :score_max';
        $params[':score_max'] = floatval($_REQUEST['score_max']);
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

    // Fetch the results based on export type
    if ($exportType === 'exams_summary') {
        // For exam summary data (aggregated by exam)
        $query = "
            SELECT 
                e.title as exam_title,
                e.exam_code,
                c.code as course_code,
                c.title as course_title,
                d.name as department_name,
                p.name as program_name,
                COUNT(DISTINCT er.student_id) as total_students,
                COUNT(DISTINCT r.result_id) as submitted_results,
                MAX(r.completed_at) as last_completed,
                AVG(r.score_percentage) as avg_score,
                MIN(r.score_percentage) as min_score,
                MAX(r.score_percentage) as max_score,
                ROUND((SUM(CASE WHEN r.score_percentage >= 50 THEN 1 ELSE 0 END) / COUNT(r.result_id)) * 100, 1) as pass_rate,
                SUM(CASE WHEN r.score_percentage >= 50 THEN 1 ELSE 0 END) as pass_count,
                SUM(CASE WHEN r.score_percentage < 50 THEN 1 ELSE 0 END) as fail_count
            FROM exams e
            LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id
            LEFT JOIN results r ON er.registration_id = r.registration_id
            JOIN courses c ON e.course_id = c.course_id
            JOIN departments d ON e.department_id = d.department_id
            JOIN programs p ON e.program_id = p.program_id
            $whereClause
            GROUP BY e.exam_id
            ORDER BY e.title ASC
        ";
    } else {
        // For individual student results (default)
        $query = "
            SELECT 
                CONCAT(s.first_name, ' ', s.last_name) as student_name,
                s.index_number,
                e.title as exam_title,
                e.exam_code,
                c.code as course_code,
                CONCAT(c.code, ' - ', c.title) as course,
                d.name as department_name,
                p.name as program_name,
                r.score_percentage,
                r.correct_answers,
                r.total_questions,
                CASE WHEN r.score_percentage >= 50 THEN 'Passed' ELSE 'Failed' END as status,
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
        ";
    }

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    // Fetch and write each row to the CSV file
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($exportType === 'exams_summary') {
            // For exam summary data
            fputcsv($output, [
                $row['exam_title'],
                $row['exam_code'],
                $row['course_code'],
                $row['course_title'],
                $row['department_name'],
                $row['program_name'],
                $row['total_students'],
                $row['submitted_results'],
                $row['last_completed'],
                number_format($row['avg_score'], 1),
                number_format($row['min_score'], 1),
                number_format($row['max_score'], 1),
                number_format($row['pass_rate'], 1),
                $row['pass_count'],
                $row['fail_count']
            ]);
        } else {
            // For individual student results
            fputcsv($output, [
                $row['student_name'],
                $row['index_number'],
                $row['exam_title'],
                $row['exam_code'],
                $row['course'],
                $row['department_name'],
                $row['program_name'],
                $row['score_percentage'],
                $row['correct_answers'],
                $row['total_questions'],
                $row['status'],
                $row['completed_at']
            ]);
        }
    }
} catch (Exception $e) {
    // In case of error, write an error message to the CSV
    fputcsv($output, ['Error: ' . $e->getMessage()]);
}
