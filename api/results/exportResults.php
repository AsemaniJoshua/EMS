<?php
// API endpoint to export results to CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="exam_results_export_' . date('Y-m-d') . '.csv"');
require_once '../config/database.php';

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Write the CSV header row
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
    
    // Fetch the results (without pagination)
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
    
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    // Fetch and write each row to the CSV file
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
    
} catch (Exception $e) {
    // In case of error, write an error message to the CSV
    fputcsv($output, ['Error: ' . $e->getMessage()]);
}
