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
    $student_id = $_SESSION['student_id'];
    
    if ($registration_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid registration ID']);
        exit;
    }
    
    // Verify the registration belongs to the student
    $verifyQuery = "
        SELECT er.registration_id 
        FROM exam_registrations er
        WHERE er.registration_id = :registration_id 
        AND er.student_id = :student_id
    ";
    $stmt = $conn->prepare($verifyQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Invalid registration or access denied']);
        exit;
    }
    
    // Get progress
    $progressQuery = "
        SELECT current_question, time_remaining, last_updated
        FROM exam_progress
        WHERE registration_id = :registration_id
    ";
    $stmt = $conn->prepare($progressQuery);
    $stmt->bindParam(':registration_id', $registration_id);
    $stmt->execute();
    
    $progress = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($progress) {
        echo json_encode([
            'success' => true,
            'progress' => $progress
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'progress' => null
        ]);
    }
    
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
