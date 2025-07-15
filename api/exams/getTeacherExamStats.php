<?php

/**
 * API Endpoint: Get Teacher Exam Statistics
 * Used to update dashboard statistics dynamically
 */

header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/database.php';

// Connect to the database
$db = new Database();
$conn = $db->getConnection();

try {
    $teacher_id = $_SESSION['teacher_id'];

    // Get exams for this teacher
    $stmt = $conn->prepare("
        SELECT status
        FROM exams
        WHERE teacher_id = :teacher_id
    ");
    $stmt->execute(['teacher_id' => $teacher_id]);
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate statistics
    $totalExams = count($exams);
    $activeExams = count(array_filter($exams, function ($exam) {
        return $exam['status'] === 'Approved';
    }));
    $pendingExams = count(array_filter($exams, function ($exam) {
        return $exam['status'] === 'Pending';
    }));
    $completedExams = count(array_filter($exams, function ($exam) {
        return $exam['status'] === 'Completed';
    }));
    $draftExams = count(array_filter($exams, function ($exam) {
        return $exam['status'] === 'Draft';
    }));
    $rejectedExams = count(array_filter($exams, function ($exam) {
        return $exam['status'] === 'Rejected';
    }));

    // Return the statistics
    echo json_encode([
        'status' => 'success',
        'stats' => [
            'total' => $totalExams,
            'approved' => $activeExams,
            'pending' => $pendingExams,
            'completed' => $completedExams,
            'draft' => $draftExams,
            'rejected' => $rejectedExams
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
