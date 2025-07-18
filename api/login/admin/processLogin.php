<?php
// Prevent direct script access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'Direct access to this script is not allowed']);
    exit;
}

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
if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
    // Fallback to POST if JSON is invalid or not sent
    $input = [
        'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
        'password' => isset($_POST['password']) ? $_POST['password'] : '',
        'remember' => isset($_POST['remember']) ? (bool)$_POST['remember'] : false
    ];
}

// Validate required fields
if (empty($input['email']) || empty($input['password'])) {
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit;
}

try {
    // Include database connection
    require_once __DIR__ .'/../../../api/config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
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

    // Prepare SQL statement to find the admin
    $stmt = $conn->prepare("SELECT admin_id, email, username, first_name, last_name, password_hash FROM admins WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Store password hash separately and unset after verification
        $password_hash = $admin['password_hash'];
        unset($admin['password_hash']);

        // Verify password
        if (password_verify($password, $password_hash)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
            $_SESSION['admin_last_activity'] = time();

            // Set session expiry based on remember me option
            if ($remember) {
                // Set session expiry to 30 days if remember is selected
                $_SESSION['admin_session_expiry'] = time() + (30 * 24 * 60 * 60); // 30 days
            } else {
                // Set a default session expiry (e.g., 1 hour)
                $_SESSION['admin_session_expiry'] = time() + (1 * 60 * 60); // 1 hour
            }

            // Log successful login attempt
            // In a production system, use a dedicated logging system for better traceability and security
            error_log('Admin login successful for email: ' . $admin['email']);

            $response['status'] = 'success';
            $response['message'] = 'Login successful';
            $response['redirect'] = '/admin/dashboard/';
        } else {
            // Incorrect password
            $response['message'] = 'Invalid email or password';
            error_log('Admin login failed: Incorrect password for email: ' . $email);
        }
    } else {
        // Admin not found
        $response['message'] = 'Invalid email or password';
        error_log('Admin login failed: Email not found - ' . $email);
    }
} catch (Exception $e) {
    // Handle any exceptions
    // Log the error (in production, use a proper logging system)
    error_log('Login error: ' . $e->getMessage());
    $response = [
        'status' => 'error',
        'message' => "An error occurred while processing your request. Please try again later."
    ];
}

// Return JSON response
echo json_encode($response);
