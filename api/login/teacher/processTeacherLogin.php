<?php
ini_set('session.cookie_path', '/');
session_start();
// Prevent direct script access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'Direct access to this script is not allowed']);
    exit;
}



// Include database connection
require_once __DIR__ . '/../../../api/config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Allow CORS for development (adjust in production)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Initialize response array
$response = [
    'status' => 'error',
    'message' => '',
    'redirect' => ''
];

// Process only if it's a POST request with JSON content
$input = json_decode(file_get_contents('php://input'), true);

// If input is empty, try getting from POST
if (empty($input)) {
    $input = [
        'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
        'password' => isset($_POST['password']) ? $_POST['password'] : '',
        'remember' => isset($_POST['remember']) ? (bool)$_POST['remember'] : false
    ];
}

// Debug: Log the received input
error_log('Teacher login attempt - Email: ' . ($input['email'] ?? 'NOT SET') . ', Password length: ' . strlen($input['password'] ?? ''));

// Validate required fields
if (empty($input['email']) || empty($input['password'])) {
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit;
}

try {
    // Sanitize inputs
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $password = $input['password'];
    $remember = isset($input['remember']) ? (bool)$input['remember'] : false;

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format';
        echo json_encode($response);
        exit;
    }

    // Debug: Log the sanitized email
    error_log('Teacher login - Sanitized email: ' . $email);

    // Prepare SQL statement to find the teacher
    $stmt = $conn->prepare("SELECT teacher_id, email, username, first_name, last_name, password_hash, status FROM teachers WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    error_log('Teacher login - Database query result count: ' . $stmt->rowCount());

    if ($stmt->rowCount() === 1) {
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Log teacher found info (without password)
        error_log('Teacher login - Teacher found: ID=' . $teacher['teacher_id'] . ', Status=' . $teacher['status']);

        // Check if teacher is active
        if ($teacher['status'] !== 'active') {
            $response['message'] = 'Your account is not active. Please contact the administrator.';
            echo json_encode($response);
            exit;
        }

        // Debug: Log password verification attempt
        error_log('Teacher login - Attempting password verification for teacher ID: ' . $teacher['teacher_id']);

        // Verify password
        if (password_verify($password, $teacher['password_hash'])) {
            error_log('Teacher login - Password verification SUCCESS for teacher ID: ' . $teacher['teacher_id']);
            
            // Start session if not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Set session variables
            $_SESSION['teacher_logged_in'] = true;
            $_SESSION['teacher_id'] = $teacher['teacher_id'];
            $_SESSION['teacher_username'] = $teacher['username'];
            $_SESSION['teacher_email'] = $teacher['email'];
            $_SESSION['teacher_name'] = $teacher['first_name'] . ' ' . $teacher['last_name'];
            $_SESSION['teacher_last_activity'] = time();

            // Set session expiry based on remember me option
            if ($remember) {
                // Set session expiry to 30 days if remember is selected
                $_SESSION['teacher_session_expiry'] = time() + (30 * 24 * 60 * 60); // 30 days
            } else {
                // Set a default session expiry (e.g., 1 hour)
                $_SESSION['teacher_session_expiry'] = time() + (1 * 60 * 60); // 1 hour
            }

            // Log successful login attempt
            error_log('Teacher login - Session created successfully for teacher ID: ' . $teacher['teacher_id']);

            $response['status'] = 'success';
            $response['message'] = 'Login successful';
            $response['redirect'] = '/teacher/dashboard/'; // Redirect to teacher dashboard
        } else {
            // Incorrect password
            error_log('Teacher login - Password verification FAILED for teacher ID: ' . $teacher['teacher_id']);
            $response['message'] = 'Invalid email or password';
        }
    } else {
        // Teacher not found
        error_log('Teacher login - No teacher found with email: ' . $email);
        $response['message'] = 'Invalid email or password';
    }

    $stmt = null;
} catch(Exception $e) {
    // Handle any exceptions
    error_log('Teacher login - Exception: ' . $e->getMessage());
    $response['message'] = 'An error occurred. Please try again later.';
    // Log the error (in production, use a proper logging system)
    error_log('Teacher login error: ' . $e->getMessage());
}

// Debug: Log final response
error_log('Teacher login - Final response: ' . json_encode($response));

// Return JSON response
echo json_encode($response);
