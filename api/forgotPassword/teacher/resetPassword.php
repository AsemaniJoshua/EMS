<?php
// resetPassword.php - Resets the teacher's password after OTP verification
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
        'password' => isset($_POST['password']) ? $_POST['password'] : ''
    ];
}

// Validate required fields
if (empty($input['contact']) || empty($input['password'])) {
    $response['message'] = 'Contact and password are required';
    echo json_encode($response);
    exit;
}

try {
    $contact = trim($input['contact']);
    $password = $input['password'];

    // Validate password strength
    if (strlen($password) < 8) {
        $response['message'] = 'Password must be at least 8 characters long';
        echo json_encode($response);
        exit;
    }

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

        // Check if there is a verified OTP for this teacher
        $stmt = $conn->prepare("
            SELECT id FROM password_reset_tokens 
            WHERE teacher_id = :teacher_id 
            AND verified = 1 
            AND expires_at > NOW()
        ");
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            // OTP was verified, now update the password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $updateStmt = $conn->prepare("
                UPDATE teachers 
                SET password_hash = :password_hash, updated_at = NOW() 
                WHERE teacher_id = :teacher_id
            ");
            $updateStmt->bindParam(':password_hash', $password_hash);
            $updateStmt->bindParam(':teacher_id', $teacher_id);

            if ($updateStmt->execute()) {
                // Password updated successfully, now delete the used token
                $deleteStmt = $conn->prepare("
                    DELETE FROM password_reset_tokens 
                    WHERE teacher_id = :teacher_id
                ");
                $deleteStmt->bindParam(':teacher_id', $teacher_id);
                $deleteStmt->execute();

                $response['status'] = 'success';
                $response['message'] = 'Password has been reset successfully';
            } else {
                $response['message'] = 'Failed to update password';
            }
        } else {
            $response['message'] = 'No verified OTP found or OTP has expired';
        }
    } else {
        $response['message'] = 'No account found with that ' . ($isEmail ? 'email' : 'phone number');
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred. Please try again later.';
    // Log the error (in production, use a proper logging system)
    error_log('Password reset error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
