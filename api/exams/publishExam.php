<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get data from request (supports both form POST and JSON)
$inputData = file_get_contents('php://input');
if (!empty($inputData)) {
    // Try to decode as JSON
    $data = json_decode($inputData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = null;
    }
}

// If not JSON or JSON parsing failed, try POST data
if (empty($data)) {
    $data = $_POST;
}

// Check exam ID from either source
$exam_id = 0;
if (isset($data['exam_id'])) {
    $exam_id = intval($data['exam_id']);
} else if (isset($data['examId'])) {
    $exam_id = intval($data['examId']);
}

if ($exam_id <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Valid exam ID is required.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    // First, get current exam status
    $statusStmt = $conn->prepare("SELECT status FROM exams WHERE exam_id = :exam_id");
    $statusStmt->execute([':exam_id' => $exam_id]);
    $currentStatus = $statusStmt->fetchColumn();

    // Determine new status based on current status
    $newStatus = 'Approved';  // Default action is to publish
    $message = 'Exam published successfully.';

    if ($currentStatus === 'Approved') {
        $newStatus = 'Pending';  // If already published, then unpublish
        $message = 'Exam unpublished and set to pending.';
    } else if ($currentStatus === 'Completed') {
        http_response_code(403); // Forbidden
        echo json_encode(['success' => false, 'message' => 'Cannot modify a completed exam.']);
        exit;
    }

    // Update exam status
    $stmt = $conn->prepare("UPDATE exams SET status = :status WHERE exam_id = :exam_id");
    $stmt->execute([
        ':status' => $newStatus,
        ':exam_id' => $exam_id
    ]);

    echo json_encode([
        'success' => true,
        'status' => 'success',
        'message' => $message,
        'newStatus' => $newStatus
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
