<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Get POST data (support both JSON and form-data)
$data = $_POST;
if (empty($data)) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (!is_array($data)) $data = [];
}

// Validate required fields
$required = ['student_id', 'first_name', 'last_name', 'email', 'date_of_birth', 'gender', 'index_number', 'program_id', 'department_id', 'level_id', 'username', 'status'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Sanitize input
$student_id = intval($data['student_id']);
$first_name = trim($data['first_name']);
$last_name = trim($data['last_name']);
$email = trim($data['email']);
$phone_number = isset($data['phone_number']) ? trim($data['phone_number']) : null;
$date_of_birth = $data['date_of_birth'];
$gender = $data['gender'];
$index_number = trim($data['index_number']);
$program_id = intval($data['program_id']);
$department_id = intval($data['department_id']);
$level_id = intval($data['level_id']);
$username = trim($data['username']);
$password = isset($data['password']) ? $data['password'] : null;
$status = $data['status'];
$resetOnLogin = isset($data['resetOnLogin']) ? 1 : 0;
$send_notification = isset($data['send_notification']) ? 1 : 0;

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
    exit;
}

// Validate password length if provided
if ($password && strlen($password) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters.']);
    exit;
}

// Connect to DB
$db = new Database();
$conn = $db->getConnection();

try {
    // Check for duplicate username, email, or index_number (excluding the current student)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE (username = :username OR email = :email OR index_number = :index_number) AND student_id != :student_id");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':index_number' => $index_number,
        ':student_id' => $student_id
    ]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username, email, or index number already exists.']);
        exit;
    }

    // Update student data
    $query = "UPDATE students SET 
                first_name = :first_name, 
                last_name = :last_name, 
                email = :email, 
                phone_number = :phone_number, 
                date_of_birth = :date_of_birth, 
                gender = :gender, 
                index_number = :index_number, 
                program_id = :program_id, 
                department_id = :department_id, 
                level_id = :level_id, 
                username = :username, 
                status = :status, 
                resetOnLogin = :resetOnLogin, 
                updated_at = NOW()";

    // Include password update if provided
    if ($password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password_hash = :password_hash";
    }

    $query .= " WHERE student_id = :student_id";

    $stmt = $conn->prepare($query);

    $params = [
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':phone_number' => $phone_number,
        ':date_of_birth' => $date_of_birth,
        ':gender' => $gender,
        ':index_number' => $index_number,
        ':program_id' => $program_id,
        ':department_id' => $department_id,
        ':level_id' => $level_id,
        ':username' => $username,
        ':status' => $status,
        ':resetOnLogin' => $resetOnLogin,
        ':student_id' => $student_id
    ];

    if ($password) {
        $params[':password_hash'] = $password_hash;
    }

    $stmt->execute($params);

    if ($send_notification) {
        // Send notification logic here (e.g., email)
        // This is a placeholder, implement actual notification logic as needed
        // mail($email, "Profile Updated", "Your profile has been updated successfully.");
    }

    echo json_encode(['status' => 'success', 'message' => 'Student updated successfully!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}