<?php
// API endpoint to update teacher profile
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

header('Content-Type: application/json');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
        exit;
    }
    
    $teacher_id = $_SESSION['teacher_id'];
    
    // Validate required fields
    $required_fields = ['username', 'first_name', 'last_name', 'email', 'department_id', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || trim($input[$field]) === '') {
            echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
            exit;
        }
    }
    
    // Sanitize input
    $username = trim($input['username']);
    $first_name = trim($input['first_name']);
    $last_name = trim($input['last_name']);
    $email = trim($input['email']);
    $phone_number = isset($input['phone_number']) ? trim($input['phone_number']) : null;
    $department_id = intval($input['department_id']);
    $status = trim($input['status']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }
    
    // Validate status
    if (!in_array($status, ['active', 'inactive'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
        exit;
    }
    
    // Check if department exists
    $stmt = $conn->prepare("SELECT department_id FROM departments WHERE department_id = ?");
    $stmt->execute([$department_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid department']);
        exit;
    }
    
    // Check if email exists for another teacher
    $stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE email = ? AND teacher_id != ?");
    $stmt->execute([$email, $teacher_id]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Email address is already in use by another teacher']);
        exit;
    }
    
    // Check if username exists for another teacher
    $stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE username = ? AND teacher_id != ?");
    $stmt->execute([$username, $teacher_id]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Username is already in use by another teacher']);
        exit;
    }
    
    // Update teacher information
    $sql = "UPDATE teachers SET 
                username = ?,
                first_name = ?,
                last_name = ?,
                email = ?,
                phone_number = ?,
                department_id = ?,
                status = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE teacher_id = ?";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        $username,
        $first_name,
        $last_name,
        $email,
        $phone_number,
        $department_id,
        $status,
        $teacher_id
    ]);
    
    if ($result) {
        // Update session data
        $_SESSION['teacher_username'] = $username;
        $_SESSION['teacher_email'] = $email;
        $_SESSION['teacher_name'] = $first_name . ' ' . $last_name;
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update profile'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 