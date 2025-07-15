<?php

/**
 * API Endpoint: Get Courses by Program
 * Returns all courses filtered by program
 */

header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/database.php';

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Get program ID from query parameter
$program_id = $_GET['program_id'] ?? null;

if (!$program_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Program ID is required'
    ]);
    exit;
}

// Connect to the database
$db = new Database();
$conn = $db->getConnection();

try {
    // Get all courses for the specified program
    $stmt = $conn->prepare("
        SELECT DISTINCT c.course_id, c.code, c.title as name 
        FROM courses c
        WHERE c.program_id = :program_id
        ORDER BY c.code
    ");
    $stmt->execute([
        'program_id' => $program_id
    ]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'courses' => $courses
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
