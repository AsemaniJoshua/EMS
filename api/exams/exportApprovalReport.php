<?php
require_once '../config/database.php';
session_start();
// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get status filter if provided
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    $statusFilter = $status === 'all' ? '' : " AND e.status = :status";

    $query = "
        SELECT 
            e.exam_id,
            e.exam_code, 
            e.title as exam_title,
            e.description,
            e.status,
            e.duration_minutes,
            e.start_datetime,
            e.end_datetime,
            e.created_at,
            e.approved_at,
            CONCAT(a.first_name, ' ', a.last_name) as approved_by_name,
            c.code as course_code,
            c.title as course_title,
            d.name as department_name,
            p.name as program_name,
            s.name as semester_name,
            CONCAT(t.first_name, ' ', t.last_name) as teacher_name,
            (SELECT COUNT(*) FROM questions q WHERE q.exam_id = e.exam_id) as question_count
        FROM exams e
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON e.department_id = d.department_id
        JOIN programs p ON e.program_id = p.program_id
        JOIN semesters s ON e.semester_id = s.semester_id
        JOIN teachers t ON e.teacher_id = t.teacher_id
        LEFT JOIN admins a ON e.approved_by = a.admin_id
        WHERE 1=1" . $statusFilter . "
        ORDER BY e.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    if ($status !== 'all') {
        $stmt->bindParam(':status', $status);
    }
    $stmt->execute();
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers for CSV download
    $filename = 'exam_approvals_' . strtolower($status) . '_' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add BOM for Excel UTF-8 compatibility
    fputs($output, "\xEF\xBB\xBF");

    // Define CSV headers
    $headers = [
        'Exam ID',
        'Exam Code',
        'Exam Title',
        'Status',
        'Department',
        'Program',
        'Course Code',
        'Course Title',
        'Semester',
        'Teacher',
        'Duration (mins)',
        'Questions',
        'Start Date',
        'End Date',
        'Created On',
        'Approved/Rejected On',
        'Approved/Rejected By'
    ];

    // Write headers to CSV
    fputcsv($output, $headers);

    // Write data rows
    foreach ($exams as $exam) {
        $row = [
            $exam['exam_id'],
            $exam['exam_code'],
            $exam['exam_title'],
            $exam['status'],
            $exam['department_name'],
            $exam['program_name'],
            $exam['course_code'],
            $exam['course_title'],
            $exam['semester_name'],
            $exam['teacher_name'],
            $exam['duration_minutes'],
            $exam['question_count'],
            $exam['start_datetime'],
            $exam['end_datetime'],
            $exam['created_at'],
            $exam['approved_at'],
            $exam['approved_by_name']
        ];
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to generate report: ' . $e->getMessage()
    ]);
}
