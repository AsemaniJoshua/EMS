<?php
// API endpoint to mark notifications as read
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
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['notification_id'])) {
        // Mark specific notification as read
        $notification_id = intval($input['notification_id']);
        
        $stmt = $conn->prepare("
            UPDATE notifications 
            SET seen = 1 
            WHERE notification_id = :notification_id AND user_id = :user_id
        ");
        $stmt->bindParam(':notification_id', $notification_id);
        $stmt->bindParam(':user_id', $student_id);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    } else {
        // Mark all notifications as read
        $stmt = $conn->prepare("
            UPDATE notifications 
            SET seen = 1 
            WHERE user_id = :user_id AND seen = 0
        ");
        $stmt->bindParam(':user_id', $student_id);
        $stmt->execute();
        
        $affectedRows = $stmt->rowCount();
        
        echo json_encode([
            'success' => true,
            'message' => "Marked {$affectedRows} notifications as read"
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
