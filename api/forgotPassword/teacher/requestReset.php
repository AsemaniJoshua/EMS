<?php
// requestReset.php - Handles password reset requests for teachers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Prevent direct script access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'Direct access to this script is not allowed']);
    exit;
}

// Include database connection
require_once __DIR__ . '/../../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Initialize response array
$response = [
    'status' => 'error',
    'message' => ''
];

// Process only if it's a POST request with JSON content
$input = json_decode(file_get_contents('php://input'), true);

// If input is empty, try getting from POST
if (empty($input)) {
    $input = [
        'contact' => isset($_POST['contact']) ? trim($_POST['contact']) : ''
    ];
}

// Validate required field
if (empty($input['contact'])) {
    $response['message'] = 'Email or phone number is required';
    echo json_encode($response);
    exit;
}

try {
    $contact = trim($input['contact']);

    // Check if contact is an email or phone number
    $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL);

    if ($isEmail) {
        // Check if email exists in the database
        $stmt = $conn->prepare("SELECT teacher_id, email, first_name FROM teachers WHERE email = :email");
        $stmt->bindParam(':email', $contact);
    } else {
        // Assume it's a phone number
        $stmt = $conn->prepare("SELECT teacher_id, email, first_name FROM teachers WHERE phone = :phone");
        $stmt->bindParam(':phone', $contact);
    }

    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        // Generate a random 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));

        // Store the OTP in the database with an expiration time (15 minutes)
        $expiryTime = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Check if there's an existing OTP for this teacher
        $checkStmt = $conn->prepare("SELECT id FROM password_reset_tokens WHERE teacher_id = :teacher_id");
        $checkStmt->bindParam(':teacher_id', $teacher['teacher_id']);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            // Update existing OTP record
            $updateStmt = $conn->prepare("UPDATE password_reset_tokens SET token = :token, expires_at = :expires_at, updated_at = NOW() WHERE teacher_id = :teacher_id");
            $updateStmt->bindParam(':token', $otp);
            $updateStmt->bindParam(':expires_at', $expiryTime);
            $updateStmt->bindParam(':teacher_id', $teacher['teacher_id']);
            $updateStmt->execute();
        } else {
            // Create new OTP record
            $insertStmt = $conn->prepare("INSERT INTO password_reset_tokens (teacher_id, token, expires_at) VALUES (:teacher_id, :token, :expires_at)");
            $insertStmt->bindParam(':teacher_id', $teacher['teacher_id']);
            $insertStmt->bindParam(':token', $otp);
            $insertStmt->bindParam(':expires_at', $expiryTime);
            $insertStmt->execute();
        }

        // In a production environment, send the OTP via email or SMS
        // For this example, we'll just return success
        // You would integrate with an email service or SMS gateway here

        $response['status'] = 'success';
        $response['message'] = 'OTP has been sent to your ' . ($isEmail ? 'email' : 'phone');

        // For development purposes only - remove in production
        $response['dev_otp'] = $otp;
    } else {
        $response['message'] = 'No account found with that ' . ($isEmail ? 'email' : 'phone number');
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred. Please try again later.';
    // Log the error (in production, use a proper logging system)
    error_log('Password reset request error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
