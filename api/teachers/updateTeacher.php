<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Only POST requests are allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
    exit;
}

// Required fields validation
$requiredFields = ['teacherId', 'firstName', 'lastName', 'email', 'staffId', 'departmentId', 'username', 'status'];
$missing = [];

foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || trim($input[$field]) === '') {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields: ' . implode(', ', $missing)]);
    exit;
}

// Process input data
$teacherId = intval($input['teacherId']);
$firstName = trim($input['firstName']);
$lastName = trim($input['lastName']);
$email = trim($input['email']);
$phoneNumber = isset($input['phoneNumber']) ? trim($input['phoneNumber']) : '';
$staffId = trim($input['staffId']);
$departmentId = intval($input['departmentId']);
$status = trim($input['status']);
$username = trim($input['username']);
$password = isset($input['password']) ? trim($input['password']) : '';
$sendNotification = isset($input['send_notification']) && $input['send_notification'] === 'on';
$resetOnLogin = isset($input['resetOnLogin']) && $input['resetOnLogin'] === 'on';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Start transaction
    $conn->beginTransaction();
    
    // Check if teacher exists
    $stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE teacher_id = ?");
    $stmt->execute([$teacherId]);
    if (!$stmt->fetch()) {
        throw new Exception("Teacher not found");
    }

    // Check if email exists for another teacher
    $stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE email = ? AND teacher_id != ?");
    $stmt->execute([$email, $teacherId]);
    if ($stmt->fetch()) {
        throw new Exception("Email address is already in use by another teacher");
    }

    // Check if username exists for another teacher
    $stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE username = ? AND teacher_id != ?");
    $stmt->execute([$username, $teacherId]);
    if ($stmt->fetch()) {
        throw new Exception("Username is already in use by another teacher");
    }

    // Check if staff_id exists for another teacher
    $stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE staff_id = ? AND teacher_id != ?");
    $stmt->execute([$staffId, $teacherId]);
    if ($stmt->fetch()) {
        throw new Exception("Staff ID is already assigned to another teacher");
    }

    // Update teacher information
    $sql = "UPDATE teachers SET 
                first_name = ?,
                last_name = ?,
                email = ?,
                phone_number = ?,
                staff_id = ?,
                department_id = ?,
                status = ?,
                username = ?,
                resetOnLogin = ?";
    
    $params = [
        $firstName,
        $lastName,
        $email,
        $phoneNumber,
        $staffId,
        $departmentId,
        $status,
        $username,
        $resetOnLogin ? 1 : 0
    ];

    // If password is provided, update it as well
    if (!empty($password)) {
        $sql .= ", password_hash = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    // Add teacher ID to parameters
    $sql .= " WHERE teacher_id = ?";
    $params[] = $teacherId;

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        throw new Exception("No changes made to the teacher's information");
    }   
    // TODO: If send_notification is true, implement email notification logic
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Teacher updated successfully'
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>