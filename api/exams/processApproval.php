<?php
header('Content-Type: application/json');
require_once '../config/database.php';
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Ensure we have a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get request body
$data = json_decode(file_get_contents('php://input'), true);

// Check required parameters
if (!isset($_GET['exam_id']) || !isset($data['action'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$examId = intval($_GET['exam_id']);
$action = $data['action'];
$comment = isset($data['comment']) ? $data['comment'] : '';
$adminId = $_SESSION['admin_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if exam exists
    $checkQuery = "SELECT exam_id, status FROM exams WHERE exam_id = :exam_id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':exam_id', $examId);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Exam not found'
        ]);
        exit;
    }

    $exam = $checkStmt->fetch(PDO::FETCH_ASSOC);

    // If exam is already approved or rejected
    if ($exam['status'] !== 'Pending' && $exam['status'] !== 'Draft') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Exam is already ' . $exam['status']
        ]);
        exit;
    }

    // Update exam status
    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        exit;
    }

    $updateQuery = "
        UPDATE exams 
        SET status = :status, 
            approved_by = :admin_id, 
            approved_at = NOW() 
        WHERE exam_id = :exam_id
    ";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':status', $status);
    $updateStmt->bindParam(':admin_id', $adminId);
    $updateStmt->bindParam(':exam_id', $examId);
    $updateStmt->execute();

    // Return success
    echo json_encode([
        'success' => true,
        'message' => 'Exam ' . strtolower($status) . ' successfully',
        'status' => $status
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process exam approval: ' . $e->getMessage()
    ]);
}
