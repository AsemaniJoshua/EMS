<?php
require_once '../../login/admin/sessionCheck.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

try {
    // Create backup filename with timestamp
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = "ems_backup_{$timestamp}.json";
    $backupPath = __DIR__ . "/../../backups/";

    // Create backups directory if it doesn't exist
    if (!is_dir($backupPath)) {
        mkdir($backupPath, 0755, true);
    }

    $fullBackupPath = $backupPath . $backupFile;

    // Get all table data
    $backup = [
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0',
        'tables' => []
    ];

    $tables = ['departments', 'programs', 'courses', 'levels', 'semesters', 'admins', 'teachers', 'students'];

    foreach ($tables as $table) {
        try {
            $stmt = $conn->prepare("SELECT * FROM `{$table}`");
            $stmt->execute();
            $backup['tables'][$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Table might not exist, skip it
            $backup['tables'][$table] = [];
        }
    }

    // Save backup to file
    if (file_put_contents($fullBackupPath, json_encode($backup, JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => 'Backup created successfully',
            'filename' => $backupFile,
            'size' => filesize($fullBackupPath)
        ]);
    } else {
        throw new Exception('Failed to write backup file');
    }
} catch (Exception $e) {
    error_log("Backup Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create backup: ' . $e->getMessage()
    ]);
}
