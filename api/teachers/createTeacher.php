<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$firstName = trim($input['firstName'] ?? '');
$lastName = trim($input['lastName'] ?? '');
$email = trim($input['email'] ?? '');
$phoneNumber = trim($input['phoneNumber'] ?? '');
$staffId = trim($input['staffId'] ?? '');
$departmentId = intval($input['departmentId'] ?? 0);
$status = $input['status'] ?? 'active';
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$confirmPassword = $input['confirmPassword'] ?? '';

if (!$firstName || !$lastName || !$email || !$staffId || !$departmentId || !$username || !$password) {
    $response['message'] = 'Please fill in all required fields.';
    echo json_encode($response);
    exit;
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid email address.';
    echo json_encode($response);
    exit;
} elseif ($password !== $confirmPassword) {
    $response['message'] = 'Passwords do not match.';
    echo json_encode($response);
    exit;
} elseif (strlen($password) < 8) {
    $response['message'] = 'Password must be at least 8 characters.';
    echo json_encode($response);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    // Check for duplicate email, username, staffId
    $check = $conn->prepare("SELECT COUNT(*) FROM teachers WHERE email = ? OR username = ? OR staff_id = ?");
    $check->execute([$email, $username, $staffId]);
    if ($check->fetchColumn() > 0) {
        $response['message'] = 'A teacher with this email, username, or staff ID already exists.';
        echo json_encode($response);
        exit;
    }
    // Insert teacher
    $stmt = $conn->prepare("INSERT INTO teachers (teacher_id, staff_id, email, phone_number, username, first_name, last_name, password_hash, department_id, status) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $result = $stmt->execute([$staffId, $email, $phoneNumber, $username, $firstName, $lastName, $passwordHash, $departmentId, $status]);
    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Teacher added successfully!';
    } else {
        $response['message'] = 'Failed to add teacher. Please try again.';
    }
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}
echo json_encode($response);
