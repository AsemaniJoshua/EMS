<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Student ID is required.']);
    exit;
}

$student_id = intval($data['student_id']);

// Connect to DB
$db = new Database();
$conn = $db->getConnection();

try {
    // Check if the student exists
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = :student_id");
    $stmt->execute([':student_id' => $student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['status' => 'error', 'message' => 'Student not found.']);
        exit;
    }

    // Delete the student
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = :student_id");
    $stmt->execute([':student_id' => $student_id]);

    echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}