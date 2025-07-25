<?php
// API endpoint for student login
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Start session
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Also support form data
    if (!$input) {
        $input = $_POST;
    }
    
    // Validate required fields
    if (empty($input['email']) || empty($input['password'])) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    $email = trim($input['email']);
    $password = $input['password'];
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Get student by email
    $query = "
        SELECT s.*, p.name as program_name, d.name as department_name, l.name as level_name
        FROM students s
        JOIN programs p ON s.program_id = p.program_id
        JOIN departments d ON s.department_id = d.department_id
        JOIN levels l ON s.level_id = l.level_id
        WHERE s.email = :email
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        // Log failed login attempt
        error_log("Failed login attempt for email: {$email} - Student not found");
        
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Check if account is active
    if ($student['status'] !== 'active') {
        $statusMessage = $student['status'] === 'inactive' ? 
            'Your account is inactive. Please contact the administrator.' :
            'Your account status does not allow login. Please contact the administrator.';
            
        echo json_encode(['success' => false, 'message' => $statusMessage]);
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $student['password_hash'])) {
        // Log failed login attempt
        error_log("Failed login attempt for email: {$email} - Invalid password");
        
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Check if password reset is required
    if ($student['resetOnLogin']) {
        echo json_encode([
            'success' => false, 
            'message' => 'Password reset required. Please contact the administrator.',
            'requires_reset' => true
        ]);
        exit;
    }
    
    // Set session variables
    $_SESSION['student_logged_in'] = true;
    $_SESSION['student_id'] = $student['student_id'];
    $_SESSION['student_username'] = $student['username'];
    $_SESSION['student_email'] = $student['email'];
    $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];
    $_SESSION['student_program'] = $student['program_name'];
    $_SESSION['student_department'] = $student['department_name'];
    $_SESSION['student_level'] = $student['level_name'];
    $_SESSION['login_time'] = time();
    
    // Set session expiry (24 hours)
    $_SESSION['session_expiry'] = time() + (24 * 60 * 60);
    
    // Update last login time
    $updateQuery = "UPDATE students SET updated_at = NOW() WHERE student_id = :student_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':student_id', $student['student_id']);
    $stmt->execute();
    
    // Log successful login
    error_log("Successful login for student: ID {$student['student_id']}, Email: {$email}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'student_id' => $student['student_id'],
            'username' => $student['username'],
            'name' => $student['first_name'] . ' ' . $student['last_name'],
            'email' => $student['email'],
            'program' => $student['program_name'],
            'department' => $student['department_name'],
            'level' => $student['level_name'],
            'redirect_url' => '/student/dashboard/'
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Student login database error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Login failed due to server error. Please try again.'
    ]);
} catch (Exception $e) {
    error_log("Student login error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error occurred. Please try again later.'
    ]);
}
?>
