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
            handleGetPrograms($conn);
            break;
        case 'create':
            handleCreateProgram($conn, $input);
            break;
        case 'update':
            handleUpdateProgram($conn, $input);
            break;
        case 'delete':
            handleDeleteProgram($conn, $input);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("Programs API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}

function handleGetPrograms($conn)
{
    try {
        $id = $_GET['id'] ?? null;

        if ($id) {
            // Get single program
            $stmt = $conn->prepare("
                SELECT p.*, d.name as department_name 
                FROM programs p 
                LEFT JOIN departments d ON p.department_id = d.department_id 
                WHERE p.program_id = ?
            ");
            $stmt->execute([$id]);
            $program = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($program) {
                echo json_encode(['success' => true, 'data' => $program]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Program not found']);
            }
        } else {
            // Get all programs with department info
            $stmt = $conn->prepare("
                SELECT p.*, d.name as department_name 
                FROM programs p 
                LEFT JOIN departments d ON p.department_id = d.department_id 
                ORDER BY d.name, p.name
            ");
            $stmt->execute();
            $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $programs]);
        }
    } catch (PDOException $e) {
        error_log("Get Programs Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error retrieving programs']);
    }
}

function handleCreateProgram($conn, $input)
{
    try {
        $name = trim($input['name'] ?? '');
        $departmentId = $input['department_id'] ?? null;
        $description = trim($input['description'] ?? '');

        if (empty($name) || !$departmentId) {
            echo json_encode(['success' => false, 'message' => 'Program name and department are required']);
            return;
        }

        // Check if department exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE department_id = ?");
        $stmt->execute([$departmentId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Selected department does not exist']);
            return;
        }

        // Check if program name already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM programs WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Program name already exists']);
            return;
        }

        // Insert new program
        $stmt = $conn->prepare("INSERT INTO programs (name, department_id, description) VALUES (?, ?, ?)");
        $stmt->execute([$name, $departmentId, $description]);

        echo json_encode(['success' => true, 'message' => 'Program created successfully']);
    } catch (PDOException $e) {
        error_log("Create Program Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error creating program']);
    }
}

function handleUpdateProgram($conn, $input)
{
    try {
        $programId = $input['program_id'] ?? null;
        $name = trim($input['name'] ?? '');
        $departmentId = $input['department_id'] ?? null;
        $description = trim($input['description'] ?? '');

        if (!$programId || empty($name) || !$departmentId) {
            echo json_encode(['success' => false, 'message' => 'Program ID, name, and department are required']);
            return;
        }

        // Check if program exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM programs WHERE program_id = ?");
        $stmt->execute([$programId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Program not found']);
            return;
        }

        // Check if department exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE department_id = ?");
        $stmt->execute([$departmentId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Selected department does not exist']);
            return;
        }

        // Check if name conflicts with another program
        $stmt = $conn->prepare("SELECT COUNT(*) FROM programs WHERE name = ? AND program_id != ?");
        $stmt->execute([$name, $programId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Program name already exists']);
            return;
        }

        // Update program
        $stmt = $conn->prepare("UPDATE programs SET name = ?, department_id = ?, description = ? WHERE program_id = ?");
        $stmt->execute([$name, $departmentId, $description, $programId]);

        echo json_encode(['success' => true, 'message' => 'Program updated successfully']);
    } catch (PDOException $e) {
        error_log("Update Program Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating program']);
    }
}

function handleDeleteProgram($conn, $input)
{
    try {
        $programId = $input['program_id'] ?? null;

        if (!$programId) {
            echo json_encode(['success' => false, 'message' => 'Program ID is required']);
            return;
        }

        // Check if program exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM programs WHERE program_id = ?");
        $stmt->execute([$programId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Program not found']);
            return;
        }

        // Check if program has related courses
        $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE program_id = ?");
        $stmt->execute([$programId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete program. It has related courses.']);
            return;
        }

        // Check if program has related students
        $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE program_id = ?");
        $stmt->execute([$programId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete program. It has related students.']);
            return;
        }

        // Delete program
        $stmt = $conn->prepare("DELETE FROM programs WHERE program_id = ?");
        $stmt->execute([$programId]);

        echo json_encode(['success' => true, 'message' => 'Program deleted successfully']);
    } catch (PDOException $e) {
        error_log("Delete Program Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error deleting program']);
    }
}
