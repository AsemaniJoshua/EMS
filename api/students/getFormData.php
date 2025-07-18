<?php

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Custom error handler for exceptions
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
});
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "PHP Error [$errno]: $errstr in $errfile on line $errline"
    ]);
    exit;
});

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Load database config and class
$dbConfigPath = __DIR__ . '/../config/database.php';
if (!file_exists($dbConfigPath)) {
    throw new Exception('Database config file not found: ' . $dbConfigPath);
}
require_once $dbConfigPath;

if (!class_exists('Database')) {
    throw new Exception('Database class not found in database.php');
}

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    throw new Exception('Database connection failed');
}

try {
    // Fetch departments
    $departmentsQuery = "SELECT department_id, name, description FROM departments ORDER BY name";
    $stmt = $conn->prepare($departmentsQuery);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch programs with department info
    $programsQuery = "
        SELECT p.program_id, p.name, p.description, p.department_id, d.name as department_name
        FROM programs p
        JOIN departments d ON p.department_id = d.department_id
        ORDER BY d.name, p.name
    ";
    $stmt = $conn->prepare($programsQuery);
    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch levels
    $levelsQuery = "SELECT level_id, name FROM levels ORDER BY level_id";
    $stmt = $conn->prepare($levelsQuery);
    $stmt->execute();
    $levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'departments' => $departments,
            'programs' => $programs,
            'levels' => $levels
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
}
?>