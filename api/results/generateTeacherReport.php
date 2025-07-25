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

// Get teacher information
$teacherStmt = $conn->prepare("SELECT first_name, last_name, staff_id FROM teachers WHERE teacher_id = :teacher_id");
$teacherStmt->execute(['teacher_id' => $teacher_id]);
$teacher = $teacherStmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    http_response_code(404);
    echo "Teacher not found";
    exit;
}

try {
    // Get parameters
    $exam_id = $_GET['exam_id'] ?? '';
    $report_type = $_GET['report_type'] ?? 'teacher_exams_summary';
    $course_id = $_GET['course_id'] ?? '';
    $status = $_GET['status'] ?? '';
    $date_range = $_GET['date_range'] ?? '';

    // Determine what report to generate
    if (!empty($exam_id)) {
        // Generate specific exam report
        generateExamReport($conn, $teacher_id, $teacher, $exam_id);
    } else {
        // Generate exams summary report
        generateExamsSummaryReport($conn, $teacher_id, $teacher, $course_id, $status, $date_range);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Report generation error: " . $e->getMessage();
}

function generateExamReport($conn, $teacher_id, $teacher, $exam_id)
{
    // Verify exam belongs to teacher and get exam details
    $examStmt = $conn->prepare("
        SELECT e.*, c.code as course_code, c.title as course_title,
               d.name as department_name, p.name as program_name
        FROM exams e
        LEFT JOIN courses c ON e.course_id = c.course_id
        LEFT JOIN programs p ON c.program_id = p.program_id
        LEFT JOIN departments d ON p.department_id = d.department_id
        WHERE e.exam_id = :exam_id AND e.teacher_id = :teacher_id
    ");
    $examStmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);
    $exam = $examStmt->fetch(PDO::FETCH_ASSOC);

    if (!$exam) {
        http_response_code(404);
        echo "Exam not found or access denied";
        exit;
    }

    // Get exam statistics
    $statsStmt = $conn->prepare("
        SELECT 
            COUNT(r.result_id) as total_students,
            COUNT(CASE WHEN r.score_percentage >= e.pass_mark THEN 1 END) as passed_students,
            ROUND(AVG(r.score_percentage), 1) as avg_score,
            MIN(r.score_percentage) as min_score,
            MAX(r.score_percentage) as max_score,
            AVG(r.total_questions) as avg_questions_attempted
        FROM results r 
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.exam_id = :exam_id
    ");
    $statsStmt->execute(['exam_id' => $exam_id]);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    // Get score distribution
    $distributionStmt = $conn->prepare("
        SELECT 
            CASE 
                WHEN r.score_percentage >= 90 THEN '90-100'
                WHEN r.score_percentage >= 80 THEN '80-89'
                WHEN r.score_percentage >= 70 THEN '70-79'
                WHEN r.score_percentage >= 60 THEN '60-69'
                WHEN r.score_percentage >= 50 THEN '50-59'
                ELSE 'Below 50'
            END as score_range,
            COUNT(*) as count
        FROM results r 
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        WHERE er.exam_id = :exam_id
        GROUP BY score_range
        ORDER BY MIN(r.score_percentage) DESC
    ");
    $distributionStmt->execute(['exam_id' => $exam_id]);
    $distribution = $distributionStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get student results
    $resultsStmt = $conn->prepare("
        SELECT 
            s.index_number,
            s.first_name,
            s.last_name,
            r.correct_answers,
            r.total_questions,
            r.score_percentage,
            r.completed_at,
            CASE WHEN r.score_percentage >= e.pass_mark THEN 'Pass' ELSE 'Fail' END as result_status
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN students s ON er.student_id = s.student_id
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.exam_id = :exam_id
        ORDER BY r.score_percentage DESC, s.last_name, s.first_name
    ");
    $resultsStmt->execute(['exam_id' => $exam_id]);
    $results = $resultsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate HTML report
    generateExamReportHTML($exam, $teacher, $stats, $distribution, $results);
}

function generateExamsSummaryReport($conn, $teacher_id, $teacher, $course_id, $status, $date_range)
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

    // Get teacher's exam summary
    $query = "
        SELECT 
            e.exam_id,
            e.title,
            e.exam_code,
            e.start_datetime,
            e.end_datetime,
            e.duration_minutes,
            e.status,
            c.code as course_code,
            c.title as course_title,
            COUNT(DISTINCT er.student_id) as total_students,
            COUNT(DISTINCT CASE WHEN r.score_percentage >= e.pass_mark THEN er.student_id END) as passed_students,
            ROUND(AVG(r.score_percentage), 1) as avg_score,
            ROUND((COUNT(DISTINCT CASE WHEN r.score_percentage >= e.pass_mark THEN er.student_id END) / 
                   NULLIF(COUNT(DISTINCT er.student_id), 0)) * 100, 1) as pass_rate
        FROM exams e
        LEFT JOIN courses c ON e.course_id = c.course_id
        LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id
        LEFT JOIN results r ON er.registration_id = r.registration_id
        $whereClause
        GROUP BY e.exam_id, e.title, e.exam_code, e.start_datetime, e.end_datetime, 
                 e.duration_minutes, e.status, c.code, c.title
        ORDER BY e.start_datetime DESC
    ";

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
    }
    $stmt->execute();
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get overall statistics
    $overallStats = [
        'total_exams' => count($exams),
        'total_students' => array_sum(array_column($exams, 'total_students')),
        'avg_pass_rate' => count($exams) > 0 ? round(array_sum(array_column($exams, 'pass_rate')) / count($exams), 1) : 0,
        'avg_score' => count($exams) > 0 ? round(array_sum(array_column($exams, 'avg_score')) / count($exams), 1) : 0
    ];

    // Generate HTML report
    generateSummaryReportHTML($teacher, $exams, $overallStats, $course_id, $status, $date_range);
}

function generateExamReportHTML($exam, $teacher, $stats, $distribution, $results)
{
    $filename = "exam_report_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $exam['exam_code']) . "_" . date('Y-m-d') . ".html";

    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $passRate = $stats['total_students'] > 0 ? round(($stats['passed_students'] / $stats['total_students']) * 100, 1) : 0;

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Report - ' . htmlspecialchars($exam['title']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box { background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #2563eb; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-value { font-size: 32px; font-weight: bold; color: #2563eb; }
        .stat-label { color: #6b7280; font-size: 14px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #374151; }
        tr:hover { background: #f9fafb; }
        .pass { color: #059669; font-weight: bold; }
        .fail { color: #dc2626; font-weight: bold; }
        .section-title { font-size: 20px; font-weight: bold; color: #1f2937; margin: 30px 0 15px 0; }
        @media print {
            body { margin: 0; }
            .header { page-break-after: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">EMS - Examination Management System</div>
        <h1>Exam Performance Report</h1>
        <p>Generated on ' . date('F j, Y \a\t g:i A') . '</p>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Exam Information</h3>
            <p><strong>Title:</strong> ' . htmlspecialchars($exam['title']) . '</p>
            <p><strong>Code:</strong> ' . htmlspecialchars($exam['exam_code']) . '</p>
            <p><strong>Course:</strong> ' . htmlspecialchars($exam['course_code'] . ' - ' . $exam['course_title']) . '</p>
            <p><strong>Status:</strong> ' . htmlspecialchars($exam['status']) . '</p>
        </div>
        <div class="info-box">
            <h3>Teacher Information</h3>
            <p><strong>Name:</strong> ' . htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) . '</p>
            <p><strong>Staff ID:</strong> ' . htmlspecialchars($teacher['staff_id']) . '</p>
            <p><strong>Department:</strong> ' . htmlspecialchars($exam['department_name'] ?? 'N/A') . '</p>
            <p><strong>Program:</strong> ' . htmlspecialchars($exam['program_name'] ?? 'N/A') . '</p>
        </div>
    </div>

    <div class="section-title">Exam Statistics</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">' . $stats['total_students'] . '</div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">' . $stats['passed_students'] . '</div>
            <div class="stat-label">Passed</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">' . $passRate . '%</div>
            <div class="stat-label">Pass Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">' . ($stats['avg_score'] ?: 'N/A') . '%</div>
            <div class="stat-label">Average Score</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">' . ($stats['min_score'] ?: 'N/A') . '%</div>
            <div class="stat-label">Minimum Score</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">' . ($stats['max_score'] ?: 'N/A') . '%</div>
            <div class="stat-label">Maximum Score</div>
        </div>
    </div>';

    if (!empty($distribution)) {
        echo '<div class="section-title">Score Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>Score Range</th>
                    <th>Number of Students</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($distribution as $range) {
            $percentage = $stats['total_students'] > 0 ? round(($range['count'] / $stats['total_students']) * 100, 1) : 0;
            echo '<tr>
                <td>' . htmlspecialchars($range['score_range']) . '</td>
                <td>' . $range['count'] . '</td>
                <td>' . $percentage . '%</td>
            </tr>';
        }

        echo '</tbody></table>';
    }

    if (!empty($results)) {
        echo '<div class="section-title">Student Results</div>
        <table>
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>Name</th>
                    <th>Score</th>
                    <th>Percentage</th>
                    <th>Result</th>
                    <th>Completed At</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($results as $result) {
            $resultClass = $result['result_status'] === 'Pass' ? 'pass' : 'fail';

            echo '<tr>
                <td>' . htmlspecialchars($result['index_number']) . '</td>
                <td>' . htmlspecialchars($result['first_name'] . ' ' . $result['last_name']) . '</td>
                <td>' . $result['correct_answers'] . '/' . $result['total_questions'] . '</td>
                <td>' . $result['score_percentage'] . '%</td>
                <td class="' . $resultClass . '">' . $result['result_status'] . '</td>
                <td>' . ($result['completed_at'] ? date('M j, Y g:i A', strtotime($result['completed_at'])) : 'N/A') . '</td>
            </tr>';
        }

        echo '</tbody></table>';
    }

    echo '</body></html>';
}

function generateSummaryReportHTML($teacher, $exams, $overallStats, $course_id, $status, $date_range)
{
    $filename = "teacher_summary_report_" . date('Y-m-d') . ".html";

    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Exams Summary Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 10px; }
        .info-box { background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #2563eb; margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-value { font-size: 32px; font-weight: bold; color: #2563eb; }
        .stat-label { color: #6b7280; font-size: 14px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #374151; }
        tr:hover { background: #f9fafb; }
        .section-title { font-size: 20px; font-weight: bold; color: #1f2937; margin: 30px 0 15px 0; }
        .status-completed { color: #059669; }
        .status-approved { color: #2563eb; }
        .status-draft { color: #6b7280; }
        .status-pending { color: #d97706; }
        @media print {
            body { margin: 0; }
            .header { page-break-after: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">EMS - Examination Management System</div>
        <h1>Teacher Exams Summary Report</h1>
        <p>Generated on ' . date('F j, Y \a\t g:i A') . '</p>
    </div>

    <div class="info-box">
        <h3>Teacher Information</h3>
        <p><strong>Name:</strong> ' . htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) . '</p>
        <p><strong>Staff ID:</strong> ' . htmlspecialchars($teacher['staff_id']) . '</p>
        <p><strong>Report Period:</strong> ' . getDateRangeText($date_range) . '</p>
        <p><strong>Status Filter:</strong> ' . ($status ? htmlspecialchars($status) : 'All Statuses') . '</p>
    </div>

    <div class="section-title">Overall Statistics</div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">' . $overallStats['total_exams'] . '</div>
            <div class="stat-label">Total Exams</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">' . $overallStats['total_students'] . '</div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">' . $overallStats['avg_pass_rate'] . '%</div>
            <div class="stat-label">Average Pass Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">' . $overallStats['avg_score'] . '%</div>
            <div class="stat-label">Average Score</div>
        </div>
    </div>';

    if (!empty($exams)) {
        echo '<div class="section-title">Exam Details</div>
        <table>
            <thead>
                <tr>
                    <th>Exam Title</th>
                    <th>Course</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Students</th>
                    <th>Pass Rate</th>
                    <th>Avg Score</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($exams as $exam) {
            $statusClass = 'status-' . strtolower($exam['status']);

            echo '<tr>
                <td>
                    <strong>' . htmlspecialchars($exam['title']) . '</strong><br>
                    <small>' . htmlspecialchars($exam['exam_code']) . '</small>
                </td>
                <td>' . htmlspecialchars($exam['course_code'] . ' - ' . $exam['course_title']) . '</td>
                <td>' . ($exam['start_datetime'] ? date('M j, Y', strtotime($exam['start_datetime'])) : 'N/A') . '</td>
                <td class="' . $statusClass . '">' . htmlspecialchars($exam['status']) . '</td>
                <td>' . ($exam['total_students'] ?: 0) . '</td>
                <td>' . ($exam['pass_rate'] ?: 0) . '%</td>
                <td>' . ($exam['avg_score'] ?: 'N/A') . '%</td>
            </tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<div class="section-title">No Exams Found</div>
        <p>No exams match the selected criteria.</p>';
    }

    echo '</body></html>';
}

function getDateRangeText($date_range)
{
    switch ($date_range) {
        case 'last_week':
            return 'Last Week';
        case 'last_month':
            return 'Last Month';
        case 'last_3_months':
            return 'Last 3 Months';
        case 'last_6_months':
            return 'Last 6 Months';
        default:
            return 'All Time';
    }
}
