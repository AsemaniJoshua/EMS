<?php
require_once '../../login/admin/sessionCheck.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

try {
    $cleanupResults = [];
    $totalCleaned = 0;

    // Clean old notifications (older than 30 days)
    try {
        $stmt = $conn->prepare("
            DELETE FROM notifications 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY) 
            AND seen = 1
        ");
        $stmt->execute();
        $cleanupResults['notifications'] = $stmt->rowCount();
        $totalCleaned += $stmt->rowCount();
    } catch (PDOException $e) {
        $cleanupResults['notifications'] = "Error: " . $e->getMessage();
    }

    // Clean expired password reset tokens
    try {
        $stmt = $conn->prepare("
            DELETE FROM password_reset_tokens 
            WHERE expires_at < NOW()
        ");
        $stmt->execute();
        $cleanupResults['password_tokens'] = $stmt->rowCount();
        $totalCleaned += $stmt->rowCount();
    } catch (PDOException $e) {
        $cleanupResults['password_tokens'] = "Error: " . $e->getMessage();
    }

    // Clean old system logs (older than 90 days) if table exists
    try {
        $stmt = $conn->prepare("
            DELETE FROM system_logs 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
        ");
        $stmt->execute();
        $cleanupResults['system_logs'] = $stmt->rowCount();
        $totalCleaned += $stmt->rowCount();
    } catch (PDOException $e) {
        // Table might not exist
        $cleanupResults['system_logs'] = "Table does not exist";
    }

    // Clean incomplete exam attempts (no results after 24 hours)
    try {
        $stmt = $conn->prepare("
            DELETE er FROM exam_registrations er
            LEFT JOIN results r ON er.registration_id = r.registration_id
            WHERE r.registration_id IS NULL 
            AND er.registered_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute();
        $cleanupResults['incomplete_exams'] = $stmt->rowCount();
        $totalCleaned += $stmt->rowCount();
    } catch (PDOException $e) {
        $cleanupResults['incomplete_exams'] = "Error: " . $e->getMessage();
    }

    // Clean orphaned student answers (answers without valid registrations)
    try {
        $stmt = $conn->prepare("
            DELETE sa FROM student_answers sa
            LEFT JOIN exam_registrations er ON sa.registration_id = er.registration_id
            WHERE er.registration_id IS NULL
        ");
        $stmt->execute();
        $cleanupResults['orphaned_answers'] = $stmt->rowCount();
        $totalCleaned += $stmt->rowCount();
    } catch (PDOException $e) {
        $cleanupResults['orphaned_answers'] = "Error: " . $e->getMessage();
    }

    // Optimize tables after cleanup
    try {
        $tables = ['notifications', 'password_reset_tokens', 'exam_registrations', 'student_answers'];
        foreach ($tables as $table) {
            $conn->query("OPTIMIZE TABLE `{$table}`");
        }
    } catch (PDOException $e) {
        // Ignore optimization errors
    }

    // Log cleanup activity
    try {
        $stmt = $conn->prepare("
            INSERT INTO system_logs (log_type, message, admin_id, created_at) 
            VALUES ('cleanup', ?, ?, NOW())
        ");
        $stmt->execute([
            "Data cleanup completed. Total records cleaned: {$totalCleaned}",
            $_SESSION['admin_id'] ?? null
        ]);
    } catch (PDOException $e) {
        // Log table might not exist, ignore
    }

    echo json_encode([
        'success' => true,
        'message' => 'Data cleanup completed successfully',
        'total_cleaned' => $totalCleaned,
        'details' => $cleanupResults
    ]);
} catch (Exception $e) {
    error_log("Cleanup Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Cleanup process failed: ' . $e->getMessage()
    ]);
}
