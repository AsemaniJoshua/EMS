<?php
require_once '../../login/admin/sessionCheck.php';
require_once '../../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$database = new Database();
$conn = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = '';

// Get action from query params for GET requests
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
} else if ($method === 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
}

try {
    switch ($action) {
        case 'get':
            handleGetDepartments($conn);
            break;
        case 'create':
            handleCreateDepartment($conn, $input);
            break;
        case 'update':
            handleUpdateDepartment($conn, $input);
            break;
        case 'delete':
            handleDeleteDepartment($conn, $input);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("Departments API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}

function handleGetDepartments($conn)
{
    try {
        $id = $_GET['id'] ?? null;

        if ($id) {
            // Get single department
            $stmt = $conn->prepare("SELECT * FROM departments WHERE department_id = ?");
            $stmt->execute([$id]);
            $department = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($department) {
                echo json_encode(['success' => true, 'data' => $department]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Department not found']);
            }
        } else {
            // Get all departments
            $stmt = $conn->prepare("SELECT * FROM departments ORDER BY name");
            $stmt->execute();
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $departments]);
        }
    } catch (PDOException $e) {
        error_log("Get Departments Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error retrieving departments']);
    }
}

function handleCreateDepartment($conn, $input)
{
    try {
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Department name is required']);
            return;
        }

        // Check if department name already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Department name already exists']);
            return;
        }

        // Insert new department
        $stmt = $conn->prepare("INSERT INTO departments (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);

        echo json_encode(['success' => true, 'message' => 'Department created successfully']);
    } catch (PDOException $e) {
        error_log("Create Department Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error creating department']);
    }
}

function handleUpdateDepartment($conn, $input)
{
    try {
        $departmentId = $input['department_id'] ?? null;
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');

        if (!$departmentId || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Department ID and name are required']);
            return;
        }

        // Check if department exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE department_id = ?");
        $stmt->execute([$departmentId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Department not found']);
            return;
        }

        // Check if name conflicts with another department
        $stmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE name = ? AND department_id != ?");
        $stmt->execute([$name, $departmentId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Department name already exists']);
            return;
        }

        // Update department
        $stmt = $conn->prepare("UPDATE departments SET name = ?, description = ? WHERE department_id = ?");
        $stmt->execute([$name, $description, $departmentId]);

        echo json_encode(['success' => true, 'message' => 'Department updated successfully']);
    } catch (PDOException $e) {
        error_log("Update Department Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating department']);
    }
}

function handleDeleteDepartment($conn, $input)
{
    try {
        $departmentId = $input['department_id'] ?? null;

        if (!$departmentId) {
            echo json_encode(['success' => false, 'message' => 'Department ID is required']);
            return;
        }

        // Check if department exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE department_id = ?");
        $stmt->execute([$departmentId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Department not found']);
            return;
        }

        // Check if department has related programs
        $stmt = $conn->prepare("SELECT COUNT(*) FROM programs WHERE department_id = ?");
        $stmt->execute([$departmentId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete department. It has related programs.']);
            return;
        }

        // Check if department has related courses
        $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE department_id = ?");
        $stmt->execute([$departmentId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete department. It has related courses.']);
            return;
        }

        // Check if department has related teachers
        $stmt = $conn->prepare("SELECT COUNT(*) FROM teachers WHERE department_id = ?");
        $stmt->execute([$departmentId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete department. It has related teachers.']);
            return;
        }

        // Delete department
        $stmt = $conn->prepare("DELETE FROM departments WHERE department_id = ?");
        $stmt->execute([$departmentId]);

        echo json_encode(['success' => true, 'message' => 'Department deleted successfully']);
    } catch (PDOException $e) {
        error_log("Delete Department Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error deleting department']);
    }
}
