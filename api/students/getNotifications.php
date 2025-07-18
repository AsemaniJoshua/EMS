<?php
// API endpoint to get student notifications
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
    $countOnly = isset($_GET['count_only']) && $_GET['count_only'] == '1';
    
    if ($countOnly) {
        // Return only unread count
        $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND seen = 0");
        $stmt->bindParam(':user_id', $student_id);
        $stmt->execute();
        $unreadCount = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    } else {
        // Return notifications list
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        
        $stmt = $conn->prepare("
            SELECT notification_id, message, created_at, seen 
            FROM notifications 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':user_id', $student_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
                        'success' => true,
            'notifications' => $notifications
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

