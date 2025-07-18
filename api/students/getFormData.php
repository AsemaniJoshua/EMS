<?php
// API endpoint to get form data for student registration
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get departments
    $departmentsQuery = "SELECT department_id, name, description FROM departments ORDER BY name";
    $stmt = $conn->prepare($departmentsQuery);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get programs with department info
    $programsQuery = "
        SELECT p.program_id, p.name, p.description, p.department_id, d.name as department_name
        FROM programs p
        JOIN departments d ON p.department_id = d.department_id
        ORDER BY d.name, p.name
    ";
    $stmt = $conn->prepare($programsQuery);
    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get levels
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
    error_log("Get form data error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to load form data'
    ]);
} catch (Exception $e) {
    error_log("Get form data error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error occurred'
    ]);
}
?>
