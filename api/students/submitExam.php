<?php
// API endpoint to submit exam and calculate results
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Start session and check authentication
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $student_id = $_SESSION['student_id'];
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    $registration_id = isset($input['registration_id']) ? intval($input['registration_id']) : 0;
    
    if ($registration_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid registration ID']);
        exit;
    }
    
    // Verify registration belongs to student
    $registrationQuery = "
        SELECT er.registration_id, er.exam_id, e.title, e.show_results, e.pass_mark
        FROM exam_registrations er
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.registration_id = :registration_id AND er.student_id = :student_id
    ";
    
    $stmt = $conn->prepare($registrationQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$registration) {
        echo json_encode(['success' => false, 'message' => 'Invalid registration']);
        exit;
    }
    
    // Check if already submitted
    $resultCheck = "SELECT result_id FROM results WHERE registration_id = :registration_id";
    $stmt = $conn->prepare($resultCheck);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Exam already submitted']);
        exit;
    }
    
    $conn->beginTransaction();
    
    // Calculate results
    $resultsQuery = "
        SELECT 
            COUNT(q.question_id) as total_questions,
            COUNT(sa.answer_id) as answered_questions,
            SUM(CASE WHEN c.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers
        FROM questions q
        LEFT JOIN student_answers sa ON q.question_id = sa.question_id AND sa.registration_id = :registration_id
        LEFT JOIN choices c ON sa.choice_id = c.choice_id
        WHERE q.exam_id = :exam_id
    ";
    
    $stmt = $conn->prepare($resultsQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->bindParam(':exam_id', $registration['exam_id']);
    $stmt->execute();
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $total_questions = $results['total_questions'];
    $correct_answers = $results['correct_answers'];
    $incorrect_answers = $results['answered_questions'] - $correct_answers;
    $score_percentage = $total_questions > 0 ? ($correct_answers / $total_questions) * 100 : 0;
    
    // Insert result
    $insertResultQuery = "
        INSERT INTO results (registration_id, total_questions, correct_answers, incorrect_answers, score_percentage, completed_at)
        VALUES (:registration_id, :total_questions, :correct_answers, :incorrect_answers, :score_percentage, NOW())
    ";
    
    $stmt = $conn->prepare($insertResultQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->bindParam(':total_questions', $total_questions);
    $stmt->bindParam(':correct_answers', $correct_answers);
    $stmt->bindParam(':incorrect_answers', $incorrect_answers);
    $stmt->bindParam(':score_percentage', $score_percentage);
    $stmt->execute();
    
    $result_id = $conn->lastInsertId();
    
    $conn->commit();
    
    // Prepare response data
    $responseData = [
        'result_id' => $result_id,
        'total_questions' => $total_questions,
        'correct_answers' => $correct_answers,
        'incorrect_answers' => $incorrect_answers,
        'score_percentage' => round($score_percentage, 2),
        'pass_mark' => $registration['pass_mark'],
        'passed' => $score_percentage >= $registration['pass_mark'],
                'show_results' => $registration['show_results']
    ];
    
    echo json_encode([
        'success' => true,
        'message' => 'Exam submitted successfully',
        'data' => $responseData
    ]);
    
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>

