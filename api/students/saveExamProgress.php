<?php
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
    
    $input = json_decode(file_get_contents('php://input'), true);
    $registration_id = isset($input['registration_id']) ? intval($input['registration_id']) : 0;
    $current_question = isset($input['current_question']) ? intval($input['current_question']) : 0;
    $time_remaining = isset($input['time_remaining']) ? intval($input['time_remaining']) : 0;
    $student_id = $_SESSION['student_id'];
    
    if ($registration_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid registration ID']);
        exit;
    }
    
    // Verify the registration belongs to the student
    $verifyQuery = "
        SELECT er.registration_id 
        FROM exam_registrations er
        JOIN exams e ON er.exam_id = e.exam_id
        WHERE er.registration_id = :registration_id 
        AND er.student_id = :student_id
        AND e.status = 'Approved'
    ";
    $stmt = $conn->prepare($verifyQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Invalid registration or access denied']);
        exit;
    }
    
    // Create exam_progress table if it doesn't exist
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS exam_progress (
            progress_id INT AUTO_INCREMENT PRIMARY KEY,
            registration_id INT NOT NULL,
            current_question INT DEFAULT 0,
            time_remaining INT DEFAULT 0,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (registration_id) REFERENCES exam_registrations(registration_id) ON DELETE CASCADE,
            UNIQUE KEY unique_registration (registration_id)
        )
    ";
    $conn->exec($createTableQuery);
    
    // Insert or update progress
    $progressQuery = "
        INSERT INTO exam_progress (registration_id, current_question, time_remaining, last_updated)
        VALUES (:registration_id, :current_question, :time_remaining, NOW())
        ON DUPLICATE KEY UPDATE
        current_question = :current_question,
        time_remaining = :time_remaining,
        last_updated = NOW()
    ";
    
    $stmt = $conn->prepare($progressQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->bindParam(':current_question', $current_question);
    $stmt->bindParam(':time_remaining', $time_remaining);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Progress saved successfully']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
