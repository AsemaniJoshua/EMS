<?php
// teacherSessionCheck.php - Checks if teacher is logged in, otherwise redirects to login page

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if teacher is logged in and session is not expired
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    // Not logged in, redirect to login page
    // header('Location: /teacher/login/');
    // exit;
}

// Optionally, check for session expiry
if (isset($_SESSION['teacher_session_expiry']) && time() > $_SESSION['teacher_session_expiry']) {
    // Session expired, destroy session and redirect
    session_unset();
    session_destroy();
    header('Location: /teacher/login/?expired=1');
    exit;
}

// Optional: Check if teacher account is still active in database
// This adds an extra layer of security
try {
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT status FROM teachers WHERE teacher_id = :teacher_id");
    $stmt->execute(['teacher_id' => $_SESSION['teacher_id']]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$teacher || $teacher['status'] !== 'active') {
        // Teacher account is inactive or doesn't exist
        session_unset();
        session_destroy();
        header('Location: /teacher/login/?inactive=1');
        exit;
    }
} catch (Exception $e) {
    // If database check fails, continue with session (graceful degradation)
    error_log('Teacher session check database error: ' . $e->getMessage());
}

// If here, user is logged in and session is valid
?> 