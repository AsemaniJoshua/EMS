<?php
// API endpoint to generate a comprehensive PDF report
header('Content-Type: text/html');
require_once '../config/database.php';

// Build filters from request parameters
$filters = [];
$params = [];
$filterDescriptions = [];

// Student filter (name or ID)
if (!empty($_REQUEST['student'])) {
    $student = '%' . $_REQUEST['student'] . '%';
    $filters[] = '(s.first_name LIKE :student OR s.last_name LIKE :student OR s.index_number LIKE :student)';
    $params[':student'] = $student;
    $filterDescriptions[] = "Student: " . $_REQUEST['student'];
}

// Exam filter (title or code)
if (!empty($_REQUEST['exam'])) {
    $exam = '%' . $_REQUEST['exam'] . '%';
    $filters[] = '(e.title LIKE :exam OR e.exam_code LIKE :exam)';
    $params[':exam'] = $exam;
    $filterDescriptions[] = "Exam: " . $_REQUEST['exam'];
}

// Department filter
$departmentName = '';
if (!empty($_REQUEST['department_id'])) {
    $filters[] = 'e.department_id = :department_id';
    $params[':department_id'] = intval($_REQUEST['department_id']);
}

// Program filter
$programName = '';
if (!empty($_REQUEST['program_id'])) {
    $filters[] = 'e.program_id = :program_id';
    $params[':program_id'] = intval($_REQUEST['program_id']);
}

// Course filter
$courseName = '';
if (!empty($_REQUEST['course_id'])) {
    $filters[] = 'e.course_id = :course_id';
    $params[':course_id'] = intval($_REQUEST['course_id']);
}

// Status filter (pass/fail)
if (!empty($_REQUEST['status'])) {
    if ($_REQUEST['status'] === 'pass') {
        $filters[] = 'r.score_percentage >= 50';
        $filterDescriptions[] = "Status: Passed";
    } else if ($_REQUEST['status'] === 'fail') {
        $filters[] = 'r.score_percentage < 50';
        $filterDescriptions[] = "Status: Failed";
    }
}

// Date range filters
if (!empty($_REQUEST['date_from'])) {
    $filters[] = 'DATE(r.completed_at) >= :date_from';
    $params[':date_from'] = $_REQUEST['date_from'];
    $filterDescriptions[] = "From Date: " . date('M j, Y', strtotime($_REQUEST['date_from']));
}

if (!empty($_REQUEST['date_to'])) {
    $filters[] = 'DATE(r.completed_at) <= :date_to';
    $params[':date_to'] = $_REQUEST['date_to'];
    $filterDescriptions[] = "To Date: " . date('M j, Y', strtotime($_REQUEST['date_to']));
}

// Build WHERE clause
$whereClause = '';
if (!empty($filters)) {
    $whereClause = 'WHERE ' . implode(' AND ', $filters);
}

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch department name if filter is applied
    if (!empty($_REQUEST['department_id'])) {
        $stmt = $conn->prepare("SELECT name FROM departments WHERE department_id = :id");
        $stmt->bindValue(':id', intval($_REQUEST['department_id']));
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $departmentName = $row['name'];
            $filterDescriptions[] = "Department: " . $departmentName;
        }
    }

    // Fetch program name if filter is applied
    if (!empty($_REQUEST['program_id'])) {
        $stmt = $conn->prepare("SELECT name FROM programs WHERE program_id = :id");
        $stmt->bindValue(':id', intval($_REQUEST['program_id']));
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $programName = $row['name'];
            $filterDescriptions[] = "Program: " . $programName;
        }
    }

    // Fetch course name if filter is applied
    if (!empty($_REQUEST['course_id'])) {
        $stmt = $conn->prepare("SELECT code, title FROM courses WHERE course_id = :id");
        $stmt->bindValue(':id', intval($_REQUEST['course_id']));
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $courseName = $row['code'] . ' - ' . $row['title'];
            $filterDescriptions[] = "Course: " . $courseName;
        }
    }

    // Get total count
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

    $countStmt = $conn->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalResults = $countStmt->fetchColumn();

    // Get statistics
    $statsQuery = "
        SELECT 
            AVG(r.score_percentage) as avg_score,
            MIN(r.score_percentage) as min_score,
            MAX(r.score_percentage) as max_score,
            COUNT(CASE WHEN r.score_percentage >= 50 THEN 1 END) as passed,
            COUNT(CASE WHEN r.score_percentage < 50 THEN 1 END) as failed
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN students s ON er.student_id = s.student_id
        JOIN exams e ON er.exam_id = e.exam_id
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON e.department_id = d.department_id
        JOIN programs p ON e.program_id = p.program_id
        $whereClause
    ";

    $statsStmt = $conn->prepare($statsQuery);
    foreach ($params as $key => $value) {
        $statsStmt->bindValue($key, $value);
    }
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    // Calculate pass rate
    $passRate = 0;
    if ($totalResults > 0) {
        $passRate = ($stats['passed'] / $totalResults) * 100;
    }

    // Fetch recent results (limit to 50 for the report)
    $query = "
        SELECT 
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
            DATE_FORMAT(r.completed_at, '%M %d, %Y') as completed_at
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN students s ON er.student_id = s.student_id
        JOIN exams e ON er.exam_id = e.exam_id
        JOIN courses c ON e.course_id = c.course_id
        JOIN departments d ON e.department_id = d.department_id
        JOIN programs p ON e.program_id = p.program_id
        $whereClause
        ORDER BY r.completed_at DESC
        LIMIT 50
    ";

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate HTML report
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Exam Results Report</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }

            .report-header {
                text-align: center;
                margin-bottom: 30px;
            }

            h1 {
                color: #2563eb;
                margin-bottom: 5px;
            }

            .report-date {
                color: #6b7280;
                font-style: italic;
                margin-bottom: 20px;
            }

            .report-filters {
                background-color: #f9fafb;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
                margin-bottom: 30px;
            }

            .stat-card {
                background-color: #f3f4f6;
                padding: 15px;
                border-radius: 5px;
                text-align: center;
            }

            .stat-card h3 {
                margin: 0;
                font-size: 14px;
                text-transform: uppercase;
                color: #6b7280;
            }

            .stat-card p {
                margin: 10px 0 0;
                font-size: 24px;
                font-weight: bold;
                color: #1f2937;
            }

            .pass-rate {
                color: #047857;
            }

            .fail-rate {
                color: #b91c1c;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 14px;
            }

            th {
                background-color: #f3f4f6;
                text-align: left;
                padding: 10px;
                border-bottom: 2px solid #d1d5db;
            }

            td {
                padding: 10px;
                border-bottom: 1px solid #e5e7eb;
            }

            tr:nth-child(even) {
                background-color: #f9fafb;
            }

            .score-cell {
                font-weight: bold;
            }

            .pass {
                color: #047857;
            }

            .fail {
                color: #b91c1c;
            }

            .print-btn {
                background-color: #2563eb;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                margin-top: 20px;
            }

            .report-footer {
                margin-top: 50px;
                text-align: center;
                color: #6b7280;
                font-size: 12px;
                border-top: 1px solid #e5e7eb;
                padding-top: 20px;
            }

            @media print {
                .print-btn {
                    display: none;
                }

                body {
                    padding: 0;
                    margin: 0;
                }
            }
        </style>
    </head>

    <body>
        <div class="report-header">
            <h1>Exam Results Report</h1>
            <div class="report-date">Generated on <?php echo date('F j, Y \a\t g:i A'); ?></div>
        </div>

        <?php if (!empty($filterDescriptions)): ?>
            <div class="report-filters">
                <strong>Filters applied:</strong> <?php echo implode(' | ', $filterDescriptions); ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Results</h3>
                <p><?php echo number_format($totalResults); ?></p>
            </div>
            <div class="stat-card">
                <h3>Average Score</h3>
                <p><?php echo number_format($stats['avg_score'] ?? 0, 1); ?>%</p>
            </div>
            <div class="stat-card">
                <h3>Pass Rate</h3>
                <p class="pass-rate"><?php echo number_format($passRate, 1); ?>%</p>
            </div>
            <div class="stat-card">
                <h3>Passed</h3>
                <p class="pass-rate"><?php echo number_format($stats['passed'] ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <h3>Failed</h3>
                <p class="fail-rate"><?php echo number_format($stats['failed'] ?? 0); ?></p>
            </div>
        </div>

        <h2>Results Summary</h2>
        <?php if (count($results) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Student ID</th>
                        <th>Exam</th>
                        <th>Course</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($result['index_number']); ?></td>
                            <td><?php echo htmlspecialchars($result['exam_title']); ?></td>
                            <td><?php echo htmlspecialchars($result['course_code'] . ' - ' . $result['course_title']); ?></td>
                            <td class="score-cell <?php echo $result['score_percentage'] >= 50 ? 'pass' : 'fail'; ?>">
                                <?php echo number_format($result['score_percentage'], 1); ?>%
                                (<?php echo $result['correct_answers']; ?>/<?php echo $result['total_questions']; ?>)
                            </td>
                            <td><?php echo htmlspecialchars($result['completed_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($totalResults > 50): ?>
                <p><em>Showing 50 of <?php echo number_format($totalResults); ?> results. Export to CSV to see all results.</em></p>
            <?php endif; ?>
        <?php else: ?>
            <p>No results match the selected criteria.</p>
        <?php endif; ?>

        <button class="print-btn" onclick="window.print()">Print Report</button>

        <div class="report-footer">
            <p>EMS - Examination Management System | Report generated on <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </body>

    </html>
<?php
} catch (Exception $e) {
    // In case of error, display an error message
    echo '<div style="color:red; padding: 20px; text-align:center;">';
    echo '<h1>Error Generating Report</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
