<?php
// API endpoint to change student password
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
    
    // Validate required fields
    if (!isset($input['current_password']) || !isset($input['new_password']) || !isset($input['confirm_password'])) {
        echo json_encode(['success' => false, 'message' => 'All password fields are required']);
        exit;
    }
    
    $current_password = $input['current_password'];
    $new_password = $input['new_password'];
    $confirm_password = $input['confirm_password'];
    
    // Validate new password
    if (strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters long']);
        exit;
    }
    
    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'New password and confirm password do not match']);
        exit;
    }
    
    // Get current password hash
    $stmt = $conn->prepare("SELECT password_hash FROM students WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }
    
    // Verify current password
    if (!password_verify($current_password, $student['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }
    
    // Hash new password
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $updateQuery = "UPDATE students SET password_hash = :password_hash, updated_at = NOW() WHERE student_id = :student_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':password_hash', $new_password_hash);
    $stmt->bindParam(':student_id', $student_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to change password']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
