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

// Set content type
header('Content-Type: application/json');

// Check if exam_id is provided
if (!isset($_POST['exam_id']) || empty($_POST['exam_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Exam ID is required']);
    exit;
}

$exam_id = intval($_POST['exam_id']);

// Database connection
$db = new Database();
$conn = $db->getConnection();

try {
    // Verify exam belongs to teacher
    $examStmt = $conn->prepare("SELECT exam_id FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id");
    $examStmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);

    if (!$examStmt->fetch()) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Access denied to this exam']);
        exit;
    }

    // Get student results for the exam
    $query = "
        SELECT 
            r.result_id,
            er.student_id,
            r.score_obtained,
            r.total_score,
            r.score_percentage,
            r.time_taken,
            r.completed_at,
            r.created_at,
            s.student_number,
            s.first_name,
            s.last_name,
            s.email
        FROM exam_registrations er
        JOIN results r ON er.registration_id = r.registration_id
        JOIN students s ON er.student_id = s.student_id
        WHERE er.exam_id = :exam_id
        ORDER BY r.score_percentage DESC, s.last_name, s.first_name
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute(['exam_id' => $exam_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the results
    foreach ($results as &$result) {
        // Convert time_taken from seconds to minutes if not null
        if ($result['time_taken']) {
            $result['time_taken_minutes'] = round($result['time_taken'] / 60, 1);
        } else {
            $result['time_taken_minutes'] = null;
        }

        // Add pass/fail status
        $result['status'] = $result['score_percentage'] >= 50 ? 'Pass' : 'Fail';

        // Format dates
        if ($result['completed_at']) {
            $result['completed_at_formatted'] = date('M j, Y g:i A', strtotime($result['completed_at']));
        }

        if ($result['created_at']) {
            $result['created_at_formatted'] = date('M j, Y g:i A', strtotime($result['created_at']));
        }
    }

    // Prepare response
    $response = [
        'status' => 'success',
        'results' => $results,
        'summary' => [
            'total_students' => count($results),
            'passed_students' => count(array_filter($results, function ($r) {
                return $r['score_percentage'] >= 50;
            })),
            'failed_students' => count(array_filter($results, function ($r) {
                return $r['score_percentage'] < 50;
            })),
            'avg_score' => count($results) > 0 ? round(array_sum(array_column($results, 'score_percentage')) / count($results), 1) : 0,
            'min_score' => count($results) > 0 ? min(array_column($results, 'score_percentage')) : 0,
            'max_score' => count($results) > 0 ? max(array_column($results, 'score_percentage')) : 0
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
