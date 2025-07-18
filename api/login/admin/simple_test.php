<?php
header('Content-Type: application/json');

// Simple login test
try {
    require_once __DIR__ . '/../../../api/config/database.php';

    $database = new Database();
    $conn = $database->getConnection();

    echo json_encode([
        'status' => 'success',
        'message' => 'Connection test successful',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
