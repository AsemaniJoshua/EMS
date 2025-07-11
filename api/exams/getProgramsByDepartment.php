<?php
// API endpoint to get programs by department
header('Content-Type: application/json');
require_once '../config/database.php';

// Allow both GET and POST methods
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Please use GET or POST.']);
    exit();
}

// Get department ID from request
$departmentId = 0;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request
    $departmentId = isset($_GET['departmentId']) ? intval($_GET['departmentId']) : 0;
} else {
    // Handle POST request (JSON or form data)
    $inputData = file_get_contents('php://input');
    if (!empty($inputData)) {
        // Try to decode as JSON
        $data = json_decode($inputData, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['departmentId'])) {
            $departmentId = intval($data['departmentId']);
        }
    }

    // If not from JSON or JSON parsing failed, try POST data
    if ($departmentId === 0 && isset($_POST['departmentId'])) {
        $departmentId = intval($_POST['departmentId']);
    }
}

// Validate department ID
if ($departmentId <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'Valid department ID is required.',
        'parameters_received' => [
            'departmentId' => $departmentId,
            'request_method' => $_SERVER['REQUEST_METHOD']
        ]
    ]);
    exit();
}

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->getConnection();

    // Query to get programs by department
    $stmt = $conn->prepare(
        "SELECT program_id, name, description 
         FROM programs 
         WHERE department_id = :departmentId 
         ORDER BY name"
    );
    $stmt->execute([':departmentId' => $departmentId]);
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return response with additional debug info
    echo json_encode([
        'success' => true,
        'programs' => $programs,
        'query_info' => [
            'departmentId' => $departmentId,
            'count' => count($programs)
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch programs: ' . $e->getMessage()
    ]);
}
