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
            handleGetCourses($conn);
            break;
        case 'create':
            handleCreateCourse($conn, $input);
            break;
        case 'update':
            handleUpdateCourse($conn, $input);
            break;
        case 'delete':
            handleDeleteCourse($conn, $input);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("Courses API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}

function handleGetCourses($conn)
{
    try {
        $id = $_GET['id'] ?? null;

        if ($id) {
            // Get single course
            $stmt = $conn->prepare("
                SELECT c.*, d.name as department_name, p.name as program_name, 
                       l.name as level_name, s.name as semester_name 
                FROM courses c 
                LEFT JOIN departments d ON c.department_id = d.department_id 
                LEFT JOIN programs p ON c.program_id = p.program_id 
                LEFT JOIN levels l ON c.level_id = l.level_id 
                LEFT JOIN semesters s ON c.semester_id = s.semester_id 
                WHERE c.course_id = ?
            ");
            $stmt->execute([$id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($course) {
                echo json_encode(['success' => true, 'data' => $course]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Course not found']);
            }
        } else {
            // Get all courses with related info
            $stmt = $conn->prepare("
                SELECT c.*, d.name as department_name, p.name as program_name, 
                       l.name as level_name, s.name as semester_name 
                FROM courses c 
                LEFT JOIN departments d ON c.department_id = d.department_id 
                LEFT JOIN programs p ON c.program_id = p.program_id 
                LEFT JOIN levels l ON c.level_id = l.level_id 
                LEFT JOIN semesters s ON c.semester_id = s.semester_id 
                ORDER BY d.name, p.name, c.code
            ");
            $stmt->execute();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $courses]);
        }
    } catch (PDOException $e) {
        error_log("Get Courses Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error retrieving courses']);
    }
}

function handleCreateCourse($conn, $input)
{
    try {
        $code = trim($input['code'] ?? '');
        $title = trim($input['title'] ?? '');
        $departmentId = $input['department_id'] ?? null;
        $programId = $input['program_id'] ?? null;
        $levelId = $input['level_id'] ?? null;
        $semesterId = $input['semester_id'] ?? null;
        $credits = $input['credits'] ?? null;

        if (empty($code) || empty($title) || !$departmentId || !$programId || !$levelId || !$semesterId || !$credits) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            return;
        }

        // Validate foreign keys
        $validationQueries = [
            'department' => "SELECT COUNT(*) FROM departments WHERE department_id = ?",
            'program' => "SELECT COUNT(*) FROM programs WHERE program_id = ?",
            'level' => "SELECT COUNT(*) FROM levels WHERE level_id = ?",
            'semester' => "SELECT COUNT(*) FROM semesters WHERE semester_id = ?"
        ];

        $validationValues = [$departmentId, $programId, $levelId, $semesterId];

        foreach ($validationQueries as $key => $query) {
            $stmt = $conn->prepare($query);
            $stmt->execute([array_shift($validationValues)]);
            if ($stmt->fetchColumn() == 0) {
                echo json_encode(['success' => false, 'message' => "Selected {$key} does not exist"]);
                return;
            }
        }

        // Check if course code already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE code = ?");
        $stmt->execute([$code]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Course code already exists']);
            return;
        }

        // Insert new course
        $stmt = $conn->prepare("
            INSERT INTO courses (code, title, department_id, program_id, level_id, semester_id, credits) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$code, $title, $departmentId, $programId, $levelId, $semesterId, $credits]);

        echo json_encode(['success' => true, 'message' => 'Course created successfully']);
    } catch (PDOException $e) {
        error_log("Create Course Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error creating course']);
    }
}

function handleUpdateCourse($conn, $input)
{
    try {
        $courseId = $input['course_id'] ?? null;
        $code = trim($input['code'] ?? '');
        $title = trim($input['title'] ?? '');
        $departmentId = $input['department_id'] ?? null;
        $programId = $input['program_id'] ?? null;
        $levelId = $input['level_id'] ?? null;
        $semesterId = $input['semester_id'] ?? null;
        $credits = $input['credits'] ?? null;

        if (!$courseId || empty($code) || empty($title) || !$departmentId || !$programId || !$levelId || !$semesterId || !$credits) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            return;
        }

        // Check if course exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE course_id = ?");
        $stmt->execute([$courseId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Course not found']);
            return;
        }

        // Validate foreign keys
        $validationQueries = [
            'department' => "SELECT COUNT(*) FROM departments WHERE department_id = ?",
            'program' => "SELECT COUNT(*) FROM programs WHERE program_id = ?",
            'level' => "SELECT COUNT(*) FROM levels WHERE level_id = ?",
            'semester' => "SELECT COUNT(*) FROM semesters WHERE semester_id = ?"
        ];

        $validationValues = [$departmentId, $programId, $levelId, $semesterId];

        foreach ($validationQueries as $key => $query) {
            $stmt = $conn->prepare($query);
            $stmt->execute([array_shift($validationValues)]);
            if ($stmt->fetchColumn() == 0) {
                echo json_encode(['success' => false, 'message' => "Selected {$key} does not exist"]);
                return;
            }
        }

        // Check if code conflicts with another course
        $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE code = ? AND course_id != ?");
        $stmt->execute([$code, $courseId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Course code already exists']);
            return;
        }

        // Update course
        $stmt = $conn->prepare("
            UPDATE courses SET code = ?, title = ?, department_id = ?, program_id = ?, 
                   level_id = ?, semester_id = ?, credits = ? 
            WHERE course_id = ?
        ");
        $stmt->execute([$code, $title, $departmentId, $programId, $levelId, $semesterId, $credits, $courseId]);

        echo json_encode(['success' => true, 'message' => 'Course updated successfully']);
    } catch (PDOException $e) {
        error_log("Update Course Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating course']);
    }
}

function handleDeleteCourse($conn, $input)
{
    try {
        $courseId = $input['course_id'] ?? null;

        if (!$courseId) {
            echo json_encode(['success' => false, 'message' => 'Course ID is required']);
            return;
        }

        // Check if course exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE course_id = ?");
        $stmt->execute([$courseId]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Course not found']);
            return;
        }

        // Check if course has related exams
        $stmt = $conn->prepare("SELECT COUNT(*) FROM exams WHERE course_id = ?");
        $stmt->execute([$courseId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete course. It has related exams.']);
            return;
        }

        // Check if course has related teacher assignments
        $stmt = $conn->prepare("SELECT COUNT(*) FROM teacher_courses WHERE course_id = ?");
        $stmt->execute([$courseId]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete course. It has teacher assignments.']);
            return;
        }

        // Delete course
        $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        $stmt->execute([$courseId]);

        echo json_encode(['success' => true, 'message' => 'Course deleted successfully']);
    } catch (PDOException $e) {
        error_log("Delete Course Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error deleting course']);
    }
}
