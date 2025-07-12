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
$required = [
    'first_name', 'last_name', 'email', 'date_of_birth', 'gender', 'index_number',
    'program_id', 'department_id', 'level_id', 'username', 'password', 'status'
];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Sanitize input
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
$password = $data['password'];
$status = $data['status'];
$resetOnLogin = $data['resetOnLogin'];
$send_notification = $data['send_notification']; 

// Validate email
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
    exit;
}

// Validate password length
if (strlen($password) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters.']);
    exit;
}

// Connect to DB
$db = new Database();
$conn = $db->getConnection();

try {
    // Check for duplicate username, email, or index_number
    $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE username = :username OR email = :email OR index_number = :index_number");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':index_number' => $index_number
    ]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username, email, or index number already exists.']);
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert student
    $stmt = $conn->prepare(
        "INSERT INTO students 
            (first_name, last_name, email, phone_number, date_of_birth, gender, index_number, program_id, department_id, level_id, username, password_hash, status, resetOnLogin ,created_at) 
         VALUES 
            (:first_name, :last_name, :email, :phone_number, :date_of_birth, :gender, :index_number, :program_id, :department_id, :level_id, :username, :password_hash, :status, :resetOnLogin, NOW())"
    );
    $stmt->execute([
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
        ':password_hash' => $password_hash,
        ':status' => $status,
        ':resetOnLogin' => $resetOnLogin
    ]);

    if($send_notification === 'on') {
        // Send notification logic here (e.g., email)
        // This is a placeholder, implement actual notification logic as needed
        // mail($email, "Welcome to EMS", "Your account has been created successfully.");
    }

    echo json_encode(['status' => 'success', 'message' => 'Student added successfully!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}