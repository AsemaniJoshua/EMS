<?php
// API endpoint to update student profile information
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Start session and check authentication
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $student_id = $_SESSION['student_id'];
    
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required_fields = ['first_name', 'last_name', 'email', 'phone_number'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            exit;
        }
    }
    
    $first_name = trim($input['first_name']);
    $last_name = trim($input['last_name']);
    $email = trim($input['email']);
    $phone_number = trim($input['phone_number']);
    $date_of_birth = isset($input['date_of_birth']) ? $input['date_of_birth'] : null;
    $gender = isset($input['gender']) ? $input['gender'] : null;
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Validate gender if provided
    if ($gender && !in_array($gender, ['male', 'female'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid gender value']);
        exit;
    }
    
    // Validate date of birth if provided
    if ($date_of_birth) {
        $dob = DateTime::createFromFormat('Y-m-d', $date_of_birth);
        if (!$dob || $dob->format('Y-m-d') !== $date_of_birth) {
            echo json_encode(['success' => false, 'message' => 'Invalid date of birth format']);
            exit;
        }
        
        // Check if date is not in the future
        if ($dob > new DateTime()) {
            echo json_encode(['success' => false, 'message' => 'Date of birth cannot be in the future']);
            exit;
        }
        
        // Check if age is reasonable (between 10 and 100 years)
        $age = $dob->diff(new DateTime())->y;
        if ($age < 10 || $age > 100) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid date of birth']);
            exit;
        }
    }
    
    $conn->beginTransaction();
    
    // Check if email is already used by another student
    $emailCheckQuery = "SELECT student_id FROM students WHERE email = :email AND student_id != :student_id";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email address is already in use by another student']);
        exit;
    }
    
    // Update student profile
    $updateQuery = "
        UPDATE students 
        SET first_name = :first_name,
            last_name = :last_name,
            email = :email,
            phone_number = :phone_number,
            date_of_birth = :date_of_birth,
            gender = :gender,
            updated_at = NOW()
        WHERE student_id = :student_id
    ";
    
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':date_of_birth', $date_of_birth);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':student_id', $student_id);
    
    if ($stmt->execute()) {
        $conn->commit();
        
        // Update session data if needed
        $_SESSION['student_name'] = $first_name . ' ' . $last_name;
        $_SESSION['student_email'] = $email;
        
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'full_name' => $first_name . ' ' . $last_name,
                'email' => $email
            ]
        ]);
    } else {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }
    
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>

