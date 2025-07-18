<?php
require_once '../../login/admin/sessionCheck.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Get logs with pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $offset = ($page - 1) * $limit;

        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // Base query
        $baseQuery = "FROM system_logs sl LEFT JOIN admins a ON sl.admin_id = a.admin_id";
        $whereConditions = [];
        $params = [];

        if (!empty($type)) {
            $whereConditions[] = "sl.log_type = ?";
            $params[] = $type;
        }

        if (!empty($search)) {
            $whereConditions[] = "(sl.message LIKE ? OR a.name LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

        // Get total count
        $countQuery = "SELECT COUNT(*) $baseQuery $whereClause";
        $stmt = $conn->prepare($countQuery);
        $stmt->execute($params);
        $totalLogs = $stmt->fetchColumn();

        // Get logs
        $logsQuery = "
            SELECT sl.*, a.name as admin_name 
            $baseQuery 
            $whereClause 
            ORDER BY sl.created_at DESC 
            LIMIT $limit OFFSET $offset
        ";
        $stmt = $conn->prepare($logsQuery);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get log type statistics
        $statsQuery = "
            SELECT log_type, COUNT(*) as count 
            FROM system_logs 
            GROUP BY log_type 
            ORDER BY count DESC
        ";
        $stmt = $conn->query($statsQuery);
        $typeStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'logs' => $logs,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $totalLogs,
                'pages' => ceil($totalLogs / $limit)
            ],
            'typeStats' => $typeStats
        ]);
    } elseif ($method === 'POST') {
        // Add a new log entry
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['log_type']) || !isset($data['message'])) {
            throw new Exception('Log type and message are required');
        }

        $stmt = $conn->prepare("
            INSERT INTO system_logs (log_type, message, admin_id, created_at) 
            VALUES (?, ?, ?, NOW())
        ");

        $stmt->execute([
            $data['log_type'],
            $data['message'],
            $_SESSION['admin_id']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Log entry added successfully'
        ]);
    } elseif ($method === 'DELETE') {
        // Clear logs (with optional filter)
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['log_id'])) {
            // Delete specific log
            $stmt = $conn->prepare("DELETE FROM system_logs WHERE log_id = ?");
            $stmt->execute([$data['log_id']]);
            $message = 'Log entry deleted successfully';
        } elseif (isset($data['older_than_days'])) {
            // Delete logs older than X days
            $days = (int)$data['older_than_days'];
            $stmt = $conn->prepare("DELETE FROM system_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
            $stmt->execute([$days]);
            $affected = $stmt->rowCount();
            $message = "Deleted $affected log entries older than $days days";
        } else {
            // Clear all logs (with confirmation)
            if (!isset($data['confirm']) || $data['confirm'] !== true) {
                throw new Exception('Confirmation required to clear all logs');
            }

            $stmt = $conn->query("DELETE FROM system_logs");
            $affected = $stmt->rowCount();
            $message = "Cleared all $affected log entries";
        }

        // Log the cleanup action
        $stmt = $conn->prepare("
            INSERT INTO system_logs (log_type, message, admin_id, created_at) 
            VALUES ('system', ?, ?, NOW())
        ");
        $stmt->execute([
            "System logs cleaned up: $message",
            $_SESSION['admin_id']
        ]);

        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
    } else {
        throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
