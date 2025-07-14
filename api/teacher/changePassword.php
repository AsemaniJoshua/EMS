<?php
// API endpoint to change teacher password
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

header('Content-Type: application/json');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
        exit;
    }
    
    $teacher_id = $_SESSION['teacher_id'];
    
    // Validate required fields
    if (!isset($input['current_password']) || !isset($input['new_password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password and new password are required']);
        exit;
    }
    
    $current_password = $input['current_password'];
    $new_password = $input['new_password'];
    
    // Validate new password length
    if (strlen($new_password) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'New password must be at least 8 characters long']);
        exit;
    }
    
    // Get current password hash
    $stmt = $conn->prepare("SELECT password_hash FROM teachers WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$teacher) {
        echo json_encode(['status' => 'error', 'message' => 'Teacher not found']);
        exit;
    }
    
    // Verify current password
    if (!password_verify($current_password, $teacher['password_hash'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
        exit;
    }
    
    // Hash new password
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $conn->prepare("UPDATE teachers SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE teacher_id = ?");
    $result = $stmt->execute([$new_password_hash, $teacher_id]);
    
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Password changed successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to change password'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 