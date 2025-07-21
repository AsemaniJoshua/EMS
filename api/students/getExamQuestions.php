<?php
// API endpoint to get exam questions for student
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
    $exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
    $registration_id = isset($_GET['registration_id']) ? intval($_GET['registration_id']) : 0;
    
    // If registration_id is provided, use it to get exam_id and verify student
    if ($registration_id > 0) {
        $regQuery = "SELECT er.registration_id, er.exam_id, er.student_id, e.title, e.duration_minutes, e.start_datetime, e.end_datetime, e.randomize, e.status FROM exam_registrations er JOIN exams e ON er.exam_id = e.exam_id WHERE er.registration_id = :registration_id";
        $stmt = $conn->prepare($regQuery);
        $stmt->bindParam(':registration_id', $registration_id);
        $stmt->execute();
        $registration = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$registration || $registration['student_id'] != $student_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid registration or access denied']);
            exit;
        }
        $exam_id = $registration['exam_id'];
        // Overwrite for use below
        $_exam = $registration;
    } else {
        if ($exam_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Valid exam ID is required']);
            exit;
        }
        // Verify student is registered for this exam
        $registrationQuery = "
            SELECT er.registration_id, e.title, e.duration_minutes, e.start_datetime, e.end_datetime,
                   e.randomize, e.status
            FROM exam_registrations er
            JOIN exams e ON er.exam_id = e.exam_id
            WHERE er.exam_id = :exam_id AND er.student_id = :student_id
        ";
        $stmt = $conn->prepare($registrationQuery);
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        $registration = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$registration) {
            echo json_encode(['success' => false, 'message' => 'You are not registered for this exam']);
            exit;
        }
        $_exam = $registration;
        $registration_id = $registration['registration_id'];
    }
    
    // Check if exam is active
    $now = new DateTime();
    $start_time = new DateTime($_exam['start_datetime']);
    $end_time = new DateTime($_exam['end_datetime']);
    
    if ($now < $start_time) {
        echo json_encode(['success' => false, 'message' => 'Exam has not started yet']);
        exit;
    }
    if ($now > $end_time) {
        echo json_encode(['success' => false, 'message' => 'Exam has ended']);
        exit;
    }
    if ($_exam['status'] !== 'Approved') {
        echo json_encode(['success' => false, 'message' => 'Exam is not available']);
        exit;
    }
    // Check if student has already completed this exam
    $resultCheck = "SELECT result_id FROM results WHERE registration_id = :registration_id";
    $stmt = $conn->prepare($resultCheck);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->execute();
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already completed this exam']);
        exit;
    }
    // Get questions and choices
    $questionsQuery = "
        SELECT q.question_id, q.question_text, q.sequence_number
        FROM questions q
        WHERE q.exam_id = :exam_id
        ORDER BY " . ($_exam['randomize'] ? 'RAND()' : 'q.sequence_number ASC, q.question_id ASC');
    $stmt = $conn->prepare($questionsQuery);
    $stmt->bindParam(':exam_id', $exam_id);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get choices for each question
    foreach ($questions as &$question) {
        $choicesQuery = "
            SELECT choice_id, choice_text
            FROM choices
            WHERE question_id = :question_id
            ORDER BY " . ($_exam['randomize'] ? 'RAND()' : 'choice_id ASC');
        $stmt = $conn->prepare($choicesQuery);
        $stmt->bindParam(':question_id', $question['question_id']);
        $stmt->execute();
        $question['choices'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Calculate remaining time
    $remaining_seconds = $end_time->getTimestamp() - $now->getTimestamp();
    $duration_seconds = $_exam['duration_minutes'] * 60;
    $time_left = min($remaining_seconds, $duration_seconds);
    // Get any existing answers
    $existingAnswersQuery = "
        SELECT question_id, choice_id
        FROM student_answers
        WHERE registration_id = :registration_id
    ";
    $stmt = $conn->prepare($existingAnswersQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->execute();
    $existingAnswers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $answersMap = [];
    foreach ($existingAnswers as $answer) {
        $answersMap[$answer['question_id']] = $answer['choice_id'];
    }
    echo json_encode([
        'success' => true,
        'data' => [
            'registration_id' => $registration_id,
            'exam_title' => $_exam['title'],
            'duration_minutes' => $_exam['duration_minutes'],
            'time_left_seconds' => $time_left,
            'questions' => $questions,
            'existing_answers' => $answersMap
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>

