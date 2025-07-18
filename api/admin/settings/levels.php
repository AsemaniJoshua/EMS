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
            handleGetLevels($conn);
            break;
        case 'create':
            handleCreateLevel($conn, $input);
            break;
        case 'update':
            handleUpdateLevel($conn, $input);
            break;
        case 'delete':
            handleDeleteLevel($conn, $input);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("Levels API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}

function handleGetLevels($conn)
{
    try {
        $id = $_GET['id'] ?? null;

        if ($id) {
            // Get single level
            $stmt = $conn->prepare("SELECT * FROM levels WHERE level_id = ?");
            $stmt->execute([$id]);
            $level = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($level) {
                echo json_encode(['success' => true, 'data' => $level]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Level not found']);
            }
        } else {
            // Get all levels
            $stmt = $conn->prepare("SELECT * FROM levels ORDER BY level_id");
            $stmt->execute();
            $levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $levels]);
        }
    } catch (PDOException $e) {
        error_log("Get Levels Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error retrieving levels']);
    }
}

function handleCreateLevel($conn, $input)
{
    try {
        $levelId = $input['level_id'] ?? null;
        $name = trim($input['name'] ?? '');

        if (!$levelId || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Level ID and name are required']);
            return;
        }

        // Validate level ID is numeric
        if (!is_numeric($levelId)) {
            echo json_encode(['success' => false, 'message' => 'Level ID must be numeric']);
            return;
        }

        // Check if level ID already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM levels WHERE level_id = ?");
        $stmt->execute([$levelId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Level ID already exists']);
            return;
        }

        // Check if level name already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM levels WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Level name already exists']);
            return;
        }

        // Insert new level
        $stmt = $conn->prepare("INSERT INTO levels (level_id, name) VALUES (?, ?)");
        $stmt->execute([$levelId, $name]);

        echo json_encode(['success' => true, 'message' => 'Level created successfully']);
    } catch (PDOException $e) {
        error_log("Create Level Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error creating level']);
    }
}

function handleUpdateLevel($conn, $input)
{
    try {
        $oldLevelId = $input['old_level_id'] ?? null;
        $newLevelId = $input['level_id'] ?? null;
        $name = trim($input['name'] ?? '');

        if (!$oldLevelId || !$newLevelId || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Level ID and name are required']);
            return;
        }

        // Validate new level ID is numeric
        if (!is_numeric($newLevelId)) {
            echo json_encode(['success' => false, 'message' => 'Level ID must be numeric']);
            return;
        }

        // Check if old level exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM levels WHERE level_id = ?");
        $stmt->execute([$oldLevelId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Level not found']);
            return;
        }

        // If level ID is changing, check if new ID already exists
        if ($oldLevelId != $newLevelId) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM levels WHERE level_id = ?");
            $stmt->execute([$newLevelId]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'New Level ID already exists']);
                return;
            }
        }

        // Check if name conflicts with another level
        $stmt = $conn->prepare("SELECT COUNT(*) FROM levels WHERE name = ? AND level_id != ?");
        $stmt->execute([$name, $oldLevelId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Level name already exists']);
            return;
        }

        // Begin transaction for potential ID update
        $conn->beginTransaction();

        try {
            if ($oldLevelId != $newLevelId) {
                // Update related tables first
                $stmt = $conn->prepare("UPDATE courses SET level_id = ? WHERE level_id = ?");
                $stmt->execute([$newLevelId, $oldLevelId]);

                $stmt = $conn->prepare("UPDATE students SET level_id = ? WHERE level_id = ?");
                $stmt->execute([$newLevelId, $oldLevelId]);

                // Delete old level and insert new one
                $stmt = $conn->prepare("DELETE FROM levels WHERE level_id = ?");
                $stmt->execute([$oldLevelId]);

                $stmt = $conn->prepare("INSERT INTO levels (level_id, name) VALUES (?, ?)");
                $stmt->execute([$newLevelId, $name]);
            } else {
                // Just update the name
                $stmt = $conn->prepare("UPDATE levels SET name = ? WHERE level_id = ?");
                $stmt->execute([$name, $oldLevelId]);
            }

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Level updated successfully']);
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    } catch (PDOException $e) {
        error_log("Update Level Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating level']);
    }
}

function handleDeleteLevel($conn, $input)
{
    try {
        $levelId = $input['level_id'] ?? null;

        if (!$levelId) {
            echo json_encode(['success' => false, 'message' => 'Level ID is required']);
            return;
        }

        // Check if level exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM levels WHERE level_id = ?");
        $stmt->execute([$levelId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Level not found']);
            return;
        }

        // Check if level has related courses
        $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE level_id = ?");
        $stmt->execute([$levelId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete level. It has related courses.']);
            return;
        }

        // Check if level has related students
        $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE level_id = ?");
        $stmt->execute([$levelId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete level. It has related students.']);
            return;
        }

        // Delete level
        $stmt = $conn->prepare("DELETE FROM levels WHERE level_id = ?");
        $stmt->execute([$levelId]);

        echo json_encode(['success' => true, 'message' => 'Level deleted successfully']);
    } catch (PDOException $e) {
        error_log("Delete Level Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error deleting level']);
    }
}
