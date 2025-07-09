<?php
// sessionCheck.php - Checks if admin is logged in, otherwise redirects to login page

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in and session is not expired
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Not logged in, redirect to login page
    header('Location: /admin/login/index.php');
    exit;
}

// Optionally, check for session expiry
if (isset($_SESSION['admin_session_expiry']) && time() > $_SESSION['admin_session_expiry']) {
    // Session expired, destroy session and redirect
    session_unset();
    session_destroy();
    header('Location: /admin/login/index.php?expired=1');
    exit;
}
// If here, user is logged in and session is valid
