<?php
// API endpoint to get courses by program and optionally by department
header('Content-Type: application/json');
require_once '../config/database.php';

// Allow both GET and POST methods
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Please use GET or POST.']);
    exit();
}

// Get parameters from request
$programId = 0;
$departmentId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request
    $programId = isset($_GET['programId']) ? intval($_GET['programId']) : 0;
    $departmentId = isset($_GET['departmentId']) ? intval($_GET['departmentId']) : 0;
} else {
    // Handle POST request (JSON or form data)
    $inputData = file_get_contents('php://input');
    if (!empty($inputData)) {
        // Try to decode as JSON
        $data = json_decode($inputData, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($data['programId'])) {
                $programId = intval($data['programId']);
            }
            if (isset($data['departmentId'])) {
                $departmentId = intval($data['departmentId']);
            }
        }
    }

    // If not from JSON or JSON parsing failed, try POST data
    if ($programId === 0 && isset($_POST['programId'])) {
        $programId = intval($_POST['programId']);
    }
    if ($departmentId === 0 && isset($_POST['departmentId'])) {
        $departmentId = intval($_POST['departmentId']);
    }
}    // Validate that we have at least one filter parameter
if ($programId <= 0 && $departmentId <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'At least one filter parameter (programId or departmentId) is required.',
        'parameters_received' => [
            'programId' => $programId,
            'departmentId' => $departmentId
        ]
    ]);
    exit();
}

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->getConnection();

    // Build query based on provided parameters
    $sql = "SELECT course_id, code, title, credits FROM courses WHERE 1=1";
    $params = [];

    if ($programId > 0) {
        $sql .= " AND program_id = :programId";
        $params[':programId'] = $programId;
    }

    if ($departmentId > 0) {
        $sql .= " AND department_id = :departmentId";
        $params[':departmentId'] = $departmentId;
    }

    $sql .= " ORDER BY code, title";

    // Execute query
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return response with additional debug info
    echo json_encode([
        'success' => true,
        'courses' => $courses,
        'query_info' => [
            'filters' => [
                'programId' => $programId > 0 ? $programId : null,
                'departmentId' => $departmentId > 0 ? $departmentId : null
            ],
            'count' => count($courses)
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch courses: ' . $e->getMessage()
    ]);
}
