<?php
// sessionCheck.php - Checks if student is logged in, otherwise redirects to login page

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

// Check if student is logged in and session is not expired
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    // Not logged in, redirect to login page
    header('Location: /student/login/');
    exit;
}

// Check for session expiry
if (isset($_SESSION['session_expiry']) && time() > $_SESSION['session_expiry']) {
    // Session expired, destroy session and redirect
    session_unset();
    session_destroy();
    header('Location: /student/login/?message=' . urlencode('Your session has expired. Please login again.'));
    exit;
}

// Check if student account is still active (optional periodic check)
if (isset($_SESSION['last_activity_check']) && (time() - $_SESSION['last_activity_check']) > 3600) {
    // Check every hour
    require_once __DIR__ . '/../../config/database.php';
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT status FROM students WHERE student_id = :student_id");
        $stmt->bindParam(':student_id', $_SESSION['student_id']);
        $stmt->execute();
        
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student || $student['status'] !== 'active') {
            // Account no longer active
            session_unset();
            session_destroy();
            header('Location: /student/login/?message=' . urlencode('Your account is no longer active. Please contact the administrator.'));
            exit;
        }
        
        $_SESSION['last_activity_check'] = time();
        
    } catch (Exception $e) {
        // Log error but don't interrupt user session
        error_log("Session check error: " . $e->getMessage());
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Extend session if user is active
if (isset($_SESSION['session_expiry'])) {
    $_SESSION['session_expiry'] = time() + (24 * 60 * 60); // Extend by 24 hours
}
?>

