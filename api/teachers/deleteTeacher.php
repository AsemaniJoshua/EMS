<?php
// Prevent direct script access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'Direct access to this script is not allowed']);
    exit;
}

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$response = ['status' => 'error', 'message' => ''];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$teacherId = isset($input['teacherId']) ? intval($input['teacherId']) : 0;

if ($teacherId <= 0) {
    $response['message'] = 'Invalid teacher ID';
    echo json_encode($response);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Begin transaction for data integrity
    $conn->beginTransaction();
    
    // First, delete from teacher_courses to avoid foreign key constraints
    $stmt = $conn->prepare("DELETE FROM teacher_courses WHERE teacher_id = ?");
    $stmt->execute([$teacherId]);
    
    // Next, check if there are any exams created by this teacher
    $examStmt = $conn->prepare("SELECT COUNT(*) FROM exams WHERE teacher_id = ?");
    $examStmt->execute([$teacherId]);
    $examCount = $examStmt->fetchColumn();
    
    if ($examCount > 0) {
        // Don't allow deletion if teacher has exams
        $conn->rollBack();
        $response['message'] = 'Cannot delete teacher because they have created exams. Please reassign or delete the exams first.';
        echo json_encode($response);
        exit;
    }
    
    // Now delete the teacher
    $stmt = $conn->prepare("DELETE FROM teachers WHERE teacher_id = ?");
    $stmt->execute([$teacherId]);
    
    // Check if the teacher was actually deleted
    if ($stmt->rowCount() === 0) {
        $conn->rollBack();
        $response['message'] = 'Teacher not found or already deleted';
        echo json_encode($response);
        exit;
    }
    
    // Commit the transaction
    $conn->commit();
    
    $response = [
        'status' => 'success',
        'message' => 'Teacher deleted successfully'
    ];
    
} catch (PDOException $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);