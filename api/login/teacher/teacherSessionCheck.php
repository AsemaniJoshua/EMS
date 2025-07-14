<?php
// teacherSessionCheck.php - Checks if teacher is logged in, otherwise redirects to login page

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    // Redirect to teacher login page
    header('Location: /teacher/login/');
    exit;
}

// Check session expiry
if (isset($_SESSION['teacher_session_expiry']) && time() > $_SESSION['teacher_session_expiry']) {
    // Session expired, destroy session and redirect to login
    session_destroy();
    header('Location: /teacher/login/');
    exit;
}

// Update last activity
$_SESSION['teacher_last_activity'] = time();

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
        session_destroy();
        header('Location: /teacher/login/');
        exit;
    }
} catch (Exception $e) {
    // If database check fails, continue with session (graceful degradation)
    error_log('Teacher session check database error: ' . $e->getMessage());
}
