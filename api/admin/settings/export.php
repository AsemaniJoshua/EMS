<?php
require_once '../../login/admin/sessionCheck.php';
require_once '../../config/database.php';

// Set headers for file download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="EMS_System_Export_' . date('Y-m-d_H-i-s') . '.csv"');
header('Cache-Control: max-age=0');

$database = new Database();
$conn = $database->getConnection();

try {
    // Open output stream
    $output = fopen('php://output', 'w');

    // Write BOM for UTF-8
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Export Departments
    fputcsv($output, ['=== DEPARTMENTS ===']);
    fputcsv($output, ['ID', 'Name', 'Description']);

    $stmt = $conn->query("SELECT department_id, name, description FROM departments ORDER BY name");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fputcsv($output, []); // Empty line

    // Export Programs
    fputcsv($output, ['=== PROGRAMS ===']);
    fputcsv($output, ['ID', 'Name', 'Department', 'Description']);

    $stmt = $conn->query("
        SELECT p.program_id, p.name, d.name as department_name, p.description 
        FROM programs p 
        LEFT JOIN departments d ON p.department_id = d.department_id 
        ORDER BY d.name, p.name
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fputcsv($output, []); // Empty line

    // Export Courses
    fputcsv($output, ['=== COURSES ===']);
    fputcsv($output, ['ID', 'Code', 'Title', 'Department', 'Program', 'Level', 'Semester', 'Credits']);

    $stmt = $conn->query("
        SELECT c.course_id, c.code, c.title, d.name as department_name, 
               p.name as program_name, l.name as level_name, s.name as semester_name, c.credits
        FROM courses c 
        LEFT JOIN departments d ON c.department_id = d.department_id 
        LEFT JOIN programs p ON c.program_id = p.program_id 
        LEFT JOIN levels l ON c.level_id = l.level_id 
        LEFT JOIN semesters s ON c.semester_id = s.semester_id 
        ORDER BY d.name, c.code
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fputcsv($output, []); // Empty line

    // Export Levels
    fputcsv($output, ['=== LEVELS ===']);
    fputcsv($output, ['ID', 'Name']);

    $stmt = $conn->query("SELECT level_id, name FROM levels ORDER BY level_id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fputcsv($output, []); // Empty line

    // Export Semesters
    fputcsv($output, ['=== SEMESTERS ===']);
    fputcsv($output, ['ID', 'Name', 'Start Date', 'End Date']);

    $stmt = $conn->query("SELECT semester_id, name, start_date, end_date FROM semesters ORDER BY name");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fputcsv($output, []); // Empty line

    // Export Teachers Summary
    fputcsv($output, ['=== TEACHERS SUMMARY ===']);
    fputcsv($output, ['ID', 'Staff ID', 'Name', 'Email', 'Department', 'Status']);

    $stmt = $conn->query("
        SELECT t.teacher_id, t.staff_id, CONCAT(t.first_name, ' ', t.last_name) as name, 
               t.email, d.name as department_name, t.status
        FROM teachers t 
        LEFT JOIN departments d ON t.department_id = d.department_id 
        ORDER BY d.name, t.last_name, t.first_name
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fputcsv($output, []); // Empty line

    // Export Students Summary
    fputcsv($output, ['=== STUDENTS SUMMARY ===']);
    fputcsv($output, ['ID', 'Index Number', 'Name', 'Email', 'Department', 'Program', 'Level', 'Status']);

    $stmt = $conn->query("
        SELECT s.student_id, s.index_number, CONCAT(s.first_name, ' ', s.last_name) as name, 
               s.email, d.name as department_name, p.name as program_name, l.name as level_name, s.status
        FROM students s 
        LEFT JOIN departments d ON s.department_id = d.department_id 
        LEFT JOIN programs p ON s.program_id = p.program_id 
        LEFT JOIN levels l ON s.level_id = l.level_id 
        ORDER BY d.name, p.name, s.last_name, s.first_name
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fputcsv($output, []); // Empty line

    // Export System Statistics
    fputcsv($output, ['=== SYSTEM STATISTICS ===']);
    fputcsv($output, ['Metric', 'Count']);

    $stats = [
        'Total Departments' => "SELECT COUNT(*) FROM departments",
        'Total Programs' => "SELECT COUNT(*) FROM programs",
        'Total Courses' => "SELECT COUNT(*) FROM courses",
        'Total Levels' => "SELECT COUNT(*) FROM levels",
        'Total Semesters' => "SELECT COUNT(*) FROM semesters",
        'Total Teachers' => "SELECT COUNT(*) FROM teachers",
        'Active Teachers' => "SELECT COUNT(*) FROM teachers WHERE status = 'active'",
        'Total Students' => "SELECT COUNT(*) FROM students",
        'Active Students' => "SELECT COUNT(*) FROM students WHERE status = 'active'",
        'Total Exams' => "SELECT COUNT(*) FROM exams",
        'Pending Exams' => "SELECT COUNT(*) FROM exams WHERE status = 'Pending'",
        'Approved Exams' => "SELECT COUNT(*) FROM exams WHERE status = 'Approved'"
    ];

    foreach ($stats as $label => $query) {
        try {
            $stmt = $conn->query($query);
            $count = $stmt->fetchColumn();
            fputcsv($output, [$label, $count]);
        } catch (PDOException $e) {
            fputcsv($output, [$label, 'Error']);
        }
    }

    fputcsv($output, []); // Empty line
    fputcsv($output, ['Export generated on:', date('Y-m-d H:i:s')]);
    fputcsv($output, ['Generated by:', $_SESSION['admin_name'] ?? 'Unknown Admin']);

    // Log export activity
    try {
        $stmt = $conn->prepare("
            INSERT INTO system_logs (log_type, message, admin_id, created_at) 
            VALUES ('export', ?, ?, NOW())
        ");
        $stmt->execute([
            "System data export generated",
            $_SESSION['admin_id'] ?? null
        ]);
    } catch (PDOException $e) {
        // Log table might not exist, ignore
    }

    fclose($output);
} catch (Exception $e) {
    // If there's an error, return JSON instead
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Export failed: ' . $e->getMessage()
    ]);
}
