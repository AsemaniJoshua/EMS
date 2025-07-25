<?php
header('Content-Type: application/json');
require_once '../config/database.php';

session_start();
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

$adminId = $_SESSION['admin_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get all pending exams
    $pendingQuery = "SELECT exam_id FROM exams WHERE status = 'Pending'";
    $pendingStmt = $conn->query($pendingQuery);
    $pendingExams = $pendingStmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($pendingExams)) {
        echo json_encode([
            'success' => true,
            'message' => 'No pending exams found to approve',
            'approvedCount' => 0
        ]);
        exit;
    }

    // Update all pending exams to approved
    $updateQuery = "
        UPDATE exams 
        SET status = 'Approved', 
            approved_by = :admin_id, 
            approved_at = NOW() 
        WHERE status = 'Pending'
    ";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':admin_id', $adminId);
    $updateStmt->execute();

    $approvedCount = $updateStmt->rowCount();

    // Return success
    echo json_encode([
        'success' => true,
        'message' => $approvedCount . ' exams have been approved successfully',
        'approvedCount' => $approvedCount
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process bulk exam approval: ' . $e->getMessage()
    ]);
}
