<?php
// API endpoint to save individual exam answer
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
    $question_id = isset($input['question_id']) ? intval($input['question_id']) : 0;
    $choice_id = isset($input['choice_id']) ? intval($input['choice_id']) : 0;
    
    if ($registration_id <= 0 || $question_id <= 0 || $choice_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }
    
    // Verify registration belongs to student
    $registrationCheck = "
        SELECT er.registration_id, e.end_datetime
        FROM exam_registrations er
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.registration_id = :registration_id AND er.student_id = :student_id
    ";
    
    $stmt = $conn->prepare($registrationCheck);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$registration) {
        echo json_encode(['success' => false, 'message' => 'Invalid registration']);
        exit;
    }
    
    // Check if exam is still active
    if (new DateTime() > new DateTime($registration['end_datetime'])) {
        echo json_encode(['success' => false, 'message' => 'Exam has ended']);
        exit;
    }
    
    // Check if student has already completed this exam
    $resultCheck = "SELECT result_id FROM results WHERE registration_id = :registration_id";
    $stmt = $conn->prepare($resultCheck);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Exam already completed']);
        exit;
    }
    
    // Verify question and choice exist and are valid
    $validationQuery = "
        SELECT q.question_id
        FROM questions q
        JOIN choices c ON q.question_id = c.question_id
        JOIN exams e ON q.exam_id = e.exam_id
        JOIN exam_registrations er ON e.exam_id = er.exam_id
        WHERE q.question_id = :question_id 
        AND c.choice_id = :choice_id 
        AND er.registration_id = :registration_id
    ";
    
    $stmt = $conn->prepare($validationQuery);
    $stmt->bindParam(':question_id', $question_id);
    $stmt->bindParam(':choice_id', $choice_id);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Invalid question or choice']);
        exit;
    }
    
    // Save or update answer
    $conn->beginTransaction();
    
    // Check if answer already exists
    $existingAnswerQuery = "
        SELECT answer_id FROM student_answers 
        WHERE registration_id = :registration_id AND question_id = :question_id
    ";
    
    $stmt = $conn->prepare($existingAnswerQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->bindParam(':question_id', $question_id);
    $stmt->execute();
    $existingAnswer = $stmt->fetch();
    
    if ($existingAnswer) {
        // Update existing answer
        $updateQuery = "
            UPDATE student_answers 
            SET choice_id = :choice_id, answered_at = NOW()
            WHERE answer_id = :answer_id
        ";
        
        $stmt = $conn->prepare($updateQuery);
        $stmt->bindParam(':choice_id', $choice_id);
        $stmt->bindParam(':answer_id', $existingAnswer['answer_id']);
        $stmt->execute();
    } else {
        // Insert new answer
        $insertQuery = "
            INSERT INTO student_answers (registration_id, question_id, choice_id, answered_at)
            VALUES (:registration_id, :question_id, :choice_id, NOW())
        ";
        
        $stmt = $conn->prepare($insertQuery);
        $stmt->bindParam(':registration_id', $registration_id);
        $stmt->bindParam(':question_id', $question_id);
        $stmt->bindParam(':choice_id', $choice_id);
        $stmt->execute();
    }
    
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Answer saved successfully']);
    
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
