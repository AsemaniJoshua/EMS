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
            handleGetSemesters($conn);
            break;
        case 'create':
            handleCreateSemester($conn, $input);
            break;
        case 'update':
            handleUpdateSemester($conn, $input);
            break;
        case 'delete':
            handleDeleteSemester($conn, $input);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("Semesters API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}

function handleGetSemesters($conn)
{
    try {
        $id = $_GET['id'] ?? null;

        if ($id) {
            // Get single semester
            $stmt = $conn->prepare("SELECT * FROM semesters WHERE semester_id = ?");
            $stmt->execute([$id]);
            $semester = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($semester) {
                echo json_encode(['success' => true, 'data' => $semester]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Semester not found']);
            }
        } else {
            // Get all semesters
            $stmt = $conn->prepare("SELECT * FROM semesters ORDER BY start_date DESC, name");
            $stmt->execute();
            $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $semesters]);
        }
    } catch (PDOException $e) {
        error_log("Get Semesters Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error retrieving semesters']);
    }
}

function handleCreateSemester($conn, $input)
{
    try {
        $name = trim($input['name'] ?? '');
        $startDate = $input['start_date'] ?? null;
        $endDate = $input['end_date'] ?? null;

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Semester name is required']);
            return;
        }

        // Validate dates if provided
        if ($startDate && $endDate) {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);

            if ($start >= $end) {
                echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
                return;
            }
        }

        // Check if semester name already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM semesters WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Semester name already exists']);
            return;
        }

        // Insert new semester
        $stmt = $conn->prepare("INSERT INTO semesters (name, start_date, end_date) VALUES (?, ?, ?)");
        $stmt->execute([$name, $startDate ?: null, $endDate ?: null]);

        echo json_encode(['success' => true, 'message' => 'Semester created successfully']);
    } catch (PDOException $e) {
        error_log("Create Semester Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error creating semester']);
    }
}

function handleUpdateSemester($conn, $input)
{
    try {
        $semesterId = $input['semester_id'] ?? null;
        $name = trim($input['name'] ?? '');
        $startDate = $input['start_date'] ?? null;
        $endDate = $input['end_date'] ?? null;

        if (!$semesterId || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Semester ID and name are required']);
            return;
        }

        // Validate dates if provided
        if ($startDate && $endDate) {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);

            if ($start >= $end) {
                echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
                return;
            }
        }

        // Check if semester exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM semesters WHERE semester_id = ?");
        $stmt->execute([$semesterId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Semester not found']);
            return;
        }

        // Check if name conflicts with another semester
        $stmt = $conn->prepare("SELECT COUNT(*) FROM semesters WHERE name = ? AND semester_id != ?");
        $stmt->execute([$name, $semesterId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Semester name already exists']);
            return;
        }

        // Update semester
        $stmt = $conn->prepare("UPDATE semesters SET name = ?, start_date = ?, end_date = ? WHERE semester_id = ?");
        $stmt->execute([$name, $startDate ?: null, $endDate ?: null, $semesterId]);

        echo json_encode(['success' => true, 'message' => 'Semester updated successfully']);
    } catch (PDOException $e) {
        error_log("Update Semester Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating semester']);
    }
}

function handleDeleteSemester($conn, $input)
{
    try {
        $semesterId = $input['semester_id'] ?? null;

        if (!$semesterId) {
            echo json_encode(['success' => false, 'message' => 'Semester ID is required']);
            return;
        }

        // Check if semester exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM semesters WHERE semester_id = ?");
        $stmt->execute([$semesterId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Semester not found']);
            return;
        }

        // Check if semester has related courses
        $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE semester_id = ?");
        $stmt->execute([$semesterId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete semester. It has related courses.']);
            return;
        }

        // Check if semester has related exams
        $stmt = $conn->prepare("SELECT COUNT(*) FROM exams WHERE semester_id = ?");
        $stmt->execute([$semesterId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete semester. It has related exams.']);
            return;
        }

        // Delete semester
        $stmt = $conn->prepare("DELETE FROM semesters WHERE semester_id = ?");
        $stmt->execute([$semesterId]);

        echo json_encode(['success' => true, 'message' => 'Semester deleted successfully']);
    } catch (PDOException $e) {
        error_log("Delete Semester Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error deleting semester']);
    }
}
