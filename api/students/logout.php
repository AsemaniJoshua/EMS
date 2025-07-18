<?php
// API endpoint for student logout
header('Content-Type: application/json');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

try {
    // Log the logout if student was logged in
    if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
        $student_id = $_SESSION['student_id'] ?? 'unknown';
        $student_email = $_SESSION['student_email'] ?? 'unknown';
        
        error_log("Student logout: ID {$student_id}, Email: {$student_email}");
    }
    
    // Clear all session variables
    $_SESSION = array();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully',
        'redirect_url' => '/student/login/'
    ]);
    
} catch (Exception $e) {
    error_log("Student logout error: " . $e->getMessage());
    
    // Even if there's an error, we should still try to clear the session
    session_unset();
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully',
        'redirect_url' => '/student/login/'
    ]);
}
?>
