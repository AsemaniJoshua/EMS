<?php
require_once '../../login/admin/sessionCheck.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

try {
    $optimizedTables = [];
    $errors = [];

    // Get all tables in the database
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        try {
            // Optimize each table
            $stmt = $conn->query("OPTIMIZE TABLE `{$table}`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && strtolower($result['Msg_type']) === 'status') {
                $optimizedTables[] = $table;
            } else {
                $errors[] = "Failed to optimize table: {$table}";
            }
        } catch (PDOException $e) {
            $errors[] = "Error optimizing {$table}: " . $e->getMessage();
        }
    }

    // Run additional optimization queries
    try {
        // Analyze tables for better query optimization
        foreach ($tables as $table) {
            $conn->query("ANALYZE TABLE `{$table}`");
        }
    } catch (PDOException $e) {
        $errors[] = "Error during table analysis: " . $e->getMessage();
    }

    // Log optimization
    try {
        $stmt = $conn->prepare("
            INSERT INTO system_logs (log_type, message, admin_id, created_at) 
            VALUES ('optimization', ?, ?, NOW())
        ");
        $stmt->execute([
            "Database optimization completed. Tables optimized: " . count($optimizedTables),
            $_SESSION['admin_id'] ?? null
        ]);
    } catch (PDOException $e) {
        // Log table might not exist, ignore
    }

    if (count($optimizedTables) > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Database optimization completed successfully',
            'optimized_tables' => count($optimizedTables),
            'total_tables' => count($tables),
            'errors' => $errors
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database optimization failed',
            'errors' => $errors
        ]);
    }
} catch (Exception $e) {
    error_log("Optimization Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Optimization process failed: ' . $e->getMessage()
    ]);
}
