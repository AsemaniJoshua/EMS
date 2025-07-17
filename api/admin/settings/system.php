<?php
require_once '../../login/admin/sessionCheck.php';
require_once '../../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$database = new Database();
$conn = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = '';

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
} else if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
}

try {
    switch ($action) {
        case 'get':
            handleGetSystemConfig($conn);
            break;
        case 'save':
            handleSaveSystemConfig($conn, $input);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("System API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}

function handleGetSystemConfig($conn)
{
    try {
        // For now, return default values. In a real system, these would be stored in a system_settings table
        $config = [
            'defaultDuration' => 120,
            'autoFinalize' => '1',
            'notificationMethod' => 'email',
            'backupFrequency' => 'weekly',
            'maintenanceMode' => '0',
            'maxAttempts' => 3
        ];

        // Try to get from a system_settings table if it exists
        try {
            $stmt = $conn->prepare("
                SELECT setting_key, setting_value 
                FROM system_settings 
                WHERE setting_key IN ('defaultDuration', 'autoFinalize', 'notificationMethod', 'backupFrequency', 'maintenanceMode', 'maxAttempts')
            ");
            $stmt->execute();
            $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            // Override defaults with database values
            foreach ($settings as $key => $value) {
                $config[$key] = $value;
            }
        } catch (PDOException $e) {
            // Table might not exist, use defaults
        }

        echo json_encode(['success' => true, 'data' => $config]);
    } catch (PDOException $e) {
        error_log("Get System Config Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error retrieving system configuration']);
    }
}

function handleSaveSystemConfig($conn, $input)
{
    try {
        $settings = [
            'defaultDuration' => $input['defaultDuration'] ?? 120,
            'autoFinalize' => $input['autoFinalize'] ?? '1',
            'notificationMethod' => $input['notificationMethod'] ?? 'email',
            'backupFrequency' => $input['backupFrequency'] ?? 'weekly',
            'maintenanceMode' => $input['maintenanceMode'] ?? '0',
            'maxAttempts' => $input['maxAttempts'] ?? 3
        ];

        // Validate settings
        if (!is_numeric($settings['defaultDuration']) || $settings['defaultDuration'] < 1) {
            echo json_encode(['success' => false, 'message' => 'Invalid default duration']);
            return;
        }

        if (!in_array($settings['autoFinalize'], ['0', '1'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid auto-finalize setting']);
            return;
        }

        if (!in_array($settings['notificationMethod'], ['email', 'sms', 'both', 'none'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid notification method']);
            return;
        }

        if (!in_array($settings['backupFrequency'], ['daily', 'weekly', 'monthly'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid backup frequency']);
            return;
        }

        if (!in_array($settings['maintenanceMode'], ['0', '1'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid maintenance mode setting']);
            return;
        }

        if (!is_numeric($settings['maxAttempts']) || $settings['maxAttempts'] < 1 || $settings['maxAttempts'] > 10) {
            echo json_encode(['success' => false, 'message' => 'Invalid max attempts (1-10)']);
            return;
        }

        // Create system_settings table if it doesn't exist
        try {
            $conn->exec("
                CREATE TABLE IF NOT EXISTS system_settings (
                    setting_id INT AUTO_INCREMENT PRIMARY KEY,
                    setting_key VARCHAR(100) UNIQUE NOT NULL,
                    setting_value TEXT,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");
        } catch (PDOException $e) {
            // Table creation failed, but continue
        }

        // Save each setting
        foreach ($settings as $key => $value) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO system_settings (setting_key, setting_value) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ");
                $stmt->execute([$key, $value]);
            } catch (PDOException $e) {
                // Individual setting save failed, log and continue
                error_log("Failed to save setting {$key}: " . $e->getMessage());
            }
        }

        echo json_encode(['success' => true, 'message' => 'System configuration saved successfully']);
    } catch (PDOException $e) {
        error_log("Save System Config Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error saving system configuration']);
    }
}
