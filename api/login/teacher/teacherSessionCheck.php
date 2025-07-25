<?php
// teacherSessionCheck.php - Checks if teacher is logged in, otherwise redirects to login page
ini_set('session.cookie_path', '/');
session_start();
// Start session if not already started
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

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
