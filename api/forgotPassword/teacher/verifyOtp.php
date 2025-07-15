<?php
// verifyOtp.php - Verifies the OTP for password reset
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
require_once __DIR__ . '/../config/database.php';
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
        'contact' => isset($_POST['contact']) ? trim($_POST['contact']) : '',
        'otp' => isset($_POST['otp']) ? trim($_POST['otp']) : ''
    ];
}

// Validate required fields
if (empty($input['contact']) || empty($input['otp'])) {
    $response['message'] = 'Contact and OTP are required';
    echo json_encode($response);
    exit;
}

try {
    $contact = trim($input['contact']);
    $otp = trim($input['otp']);

    // Check if contact is an email or phone number
    $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL);

    // First, get the teacher_id from the contact information
    if ($isEmail) {
        $stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE email = :email");
        $stmt->bindParam(':email', $contact);
    } else {
        $stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE phone = :phone");
        $stmt->bindParam(':phone', $contact);
    }

    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        $teacher_id = $teacher['teacher_id'];

        // Now check if the OTP matches and is still valid
        $stmt = $conn->prepare("
            SELECT id FROM password_reset_tokens 
            WHERE teacher_id = :teacher_id 
            AND token = :token 
            AND expires_at > NOW()
        ");
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->bindParam(':token', $otp);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            // OTP is valid - mark it as verified
            $updateStmt = $conn->prepare("
                UPDATE password_reset_tokens 
                SET verified = 1, updated_at = NOW() 
                WHERE teacher_id = :teacher_id AND token = :token
            ");
            $updateStmt->bindParam(':teacher_id', $teacher_id);
            $updateStmt->bindParam(':token', $otp);
            $updateStmt->execute();

            $response['status'] = 'success';
            $response['message'] = 'OTP verified successfully';
        } else {
            $response['message'] = 'Invalid or expired OTP';
        }
    } else {
        $response['message'] = 'No account found with that ' . ($isEmail ? 'email' : 'phone number');
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred. Please try again later.';
    // Log the error (in production, use a proper logging system)
    error_log('OTP verification error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
