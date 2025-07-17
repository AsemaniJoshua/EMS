<?php
require_once '../../login/admin/sessionCheck.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

try {
    // Create backup filename with timestamp
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = "ems_backup_{$timestamp}.sql";
    $backupPath = __DIR__ . "/../../backups/";

    // Create backups directory if it doesn't exist
    if (!is_dir($backupPath)) {
        mkdir($backupPath, 0755, true);
    }

    $fullBackupPath = $backupPath . $backupFile;

    // Get database configuration
    $dbConfig = parse_ini_file(__DIR__ . '/../../config/database.ini', true);
    $host = $dbConfig['database']['host'] ?? 'localhost';
    $dbname = $dbConfig['database']['name'] ?? 'exam_management';
    $username = $dbConfig['database']['username'] ?? 'root';
    $password = $dbConfig['database']['password'] ?? '';

    // Create mysqldump command
    $command = sprintf(
        'mysqldump --host=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s',
        escapeshellarg($host),
        escapeshellarg($username),
        escapeshellarg($password),
        escapeshellarg($dbname),
        escapeshellarg($fullBackupPath)
    );

    // Execute backup command
    $output = [];
    $returnCode = 0;
    exec($command, $output, $returnCode);

    if ($returnCode === 0 && file_exists($fullBackupPath)) {
        // Log backup creation
        try {
            $stmt = $conn->prepare("
                INSERT INTO system_logs (log_type, message, admin_id, created_at) 
                VALUES ('backup', ?, ?, NOW())
            ");
            $stmt->execute([
                "Database backup created: {$backupFile}",
                $_SESSION['admin_id'] ?? null
            ]);
        } catch (PDOException $e) {
            // Log table might not exist, ignore
        }

        echo json_encode([
            'success' => true,
            'message' => 'Database backup created successfully',
            'filename' => $backupFile,
            'size' => formatBytes(filesize($fullBackupPath))
        ]);
    } else {
        // Try alternative backup method using PHP
        $backupContent = generatePHPBackup($conn);

        if ($backupContent && file_put_contents($fullBackupPath, $backupContent)) {
            echo json_encode([
                'success' => true,
                'message' => 'Database backup created successfully (PHP method)',
                'filename' => $backupFile,
                'size' => formatBytes(filesize($fullBackupPath))
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create database backup'
            ]);
        }
    }
} catch (Exception $e) {
    error_log("Backup Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Backup process failed: ' . $e->getMessage()
    ]);
}

function generatePHPBackup($conn)
{
    try {
        $backup = "-- EMS Database Backup\n";
        $backup .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";

        // Get all tables
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $backup .= "\n-- Table: {$table}\n";

            // Get table structure
            $stmt = $conn->query("SHOW CREATE TABLE `{$table}`");
            $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
            $backup .= $createTable['Create Table'] . ";\n\n";

            // Get table data
            $stmt = $conn->query("SELECT * FROM `{$table}`");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $backup .= "INSERT INTO `{$table}` VALUES (";
                $values = array_map(function ($value) use ($conn) {
                    return $value === null ? 'NULL' : $conn->quote($value);
                }, array_values($row));
                $backup .= implode(', ', $values);
                $backup .= ");\n";
            }
            $backup .= "\n";
        }

        return $backup;
    } catch (Exception $e) {
        error_log("PHP Backup Error: " . $e->getMessage());
        return false;
    }
}

function formatBytes($size, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }

    return round($size, $precision) . ' ' . $units[$i];
}
