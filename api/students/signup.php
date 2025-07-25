<?php
// API endpoint for student registration
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $requiredFields = [
        'first_name', 'last_name', 'username', 'index_number', 
        'email', 'date_of_birth', 'gender', 'department_id', 
        'program_id', 'password', 'confirm_password'
    ];
    
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            echo json_encode([
                'success' => false, 
                'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
            ]);
            exit;
        }
    }
    
    // Validate password match
    if ($input['password'] !== $input['confirm_password']) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit;
    }
    
    // Validate password strength
    if (strlen($input['password']) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    
    // Validate email format
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Validate username format
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $input['username'])) {
        echo json_encode(['success' => false, 'message' => 'Username can only contain letters, numbers, and underscores']);
        exit;
    }
    
    // Validate date of birth
    $dob = new DateTime($input['date_of_birth']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    
    if ($age < 10 || $age > 100) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid date of birth']);
        exit;
    }
    
    // Check for existing username, email, or index number
    $checkQuery = "
        SELECT 
            CASE 
                WHEN username = :username THEN 'username'
                WHEN email = :email THEN 'email'
                WHEN index_number = :index_number THEN 'index_number'
            END as field_type
        FROM students 
        WHERE username = :username OR email = :email OR index_number = :index_number
        LIMIT 1
    ";
    
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute([
        ':username' => $input['username'],
        ':email' => $input['email'],
        ':index_number' => $input['index_number']
    ]);
    
    if ($existing = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $fieldName = str_replace('_', ' ', $existing['field_type']);
        echo json_encode([
            'success' => false, 
            'message' => ucfirst($fieldName) . ' already exists. Please choose a different one.'
        ]);
        exit;
    }
    
    // Validate department and program exist and are related
    $deptProgramQuery = "
        SELECT d.department_id, p.program_id, p.name as program_name, d.name as department_name
        FROM departments d
        JOIN programs p ON d.department_id = p.department_id
        WHERE d.department_id = :department_id AND p.program_id = :program_id
    ";
    
    $stmt = $conn->prepare($deptProgramQuery);
    $stmt->execute([
        ':department_id' => $input['department_id'],
        ':program_id' => $input['program_id']
    ]);
    
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['success' => false, 'message' => 'Invalid department or program selection']);
        exit;
    }
    
    // Generate student ID (you might want to implement a different logic)
    $studentIdQuery = "SELECT MAX(student_id) as max_id FROM students";
    $stmt = $conn->prepare($studentIdQuery);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $newStudentId = ($result['max_id'] ?? 0) + 1;
    
    // Get a default level (you might want to make this selectable)
    $levelQuery = "SELECT level_id FROM levels ORDER BY level_id LIMIT 1";
    $stmt = $conn->prepare($levelQuery);
    $stmt->execute();
    $level = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$level) {
        echo json_encode(['success' => false, 'message' => 'No academic levels found. Please contact administrator.']);
        exit;
    }
    
    // Hash password
    $passwordHash = password_hash($input['password'], PASSWORD_DEFAULT);
    
    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Insert student
        $insertQuery = "
            INSERT INTO students (
                student_id, index_number, username, first_name, last_name, 
                email, phone_number, password_hash, date_of_birth, gender, 
                status, level_id, program_id, department_id, created_at
            ) VALUES (
                :student_id, :index_number, :username, :first_name, :last_name,
                :email, :phone_number, :password_hash, :date_of_birth, :gender,
                'active', :level_id, :program_id, :department_id, NOW()
            )
        ";
        
        $stmt = $conn->prepare($insertQuery);
        $stmt->execute([
            ':student_id' => $newStudentId,
            ':index_number' => $input['index_number'],
            ':username' => $input['username'],
            ':first_name' => $input['first_name'],
            ':last_name' => $input['last_name'],
            ':email' => $input['email'],
            ':phone_number' => $input['phone_number'] ?? null,
            ':password_hash' => $passwordHash,
            ':date_of_birth' => $input['date_of_birth'],
            ':gender' => $input['gender'],
            ':level_id' => $level['level_id'],
            ':program_id' => $input['program_id'],
            ':department_id' => $input['department_id']
        ]);
        
        // Create welcome notification
        $notificationQuery = "
            INSERT INTO notifications (user_id, message, created_at)
            VALUES (:user_id, :message, NOW())
        ";
        
        $welcomeMessage = "Welcome to EMS! Your account has been created successfully. You can now register for exams and track your progress.";
        
        $stmt = $conn->prepare($notificationQuery);
        $stmt->execute([
            ':user_id' => $newStudentId,
            ':message' => $welcomeMessage
        ]);
        
        $conn->commit();
        
        // Log successful registration
        error_log("New student registered: ID {$newStudentId}, Username: {$input['username']}, Email: {$input['email']}");
        
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully! You can now sign in with your credentials.',
            'data' => [
                'student_id' => $newStudentId,
                'username' => $input['username'],
                'email' => $input['email']
            ]
        ]);
        
    } catch (PDOException $e) {
        $conn->rollBack();
        
        // Check for specific constraint violations
        if ($e->getCode() == 23000) {
            if (strpos($e->getMessage(), 'username') !== false) {
                $message = 'Username already exists';
            } elseif (strpos($e->getMessage(), 'email') !== false) {
                $message = 'Email already exists';
            } elseif (strpos($e->getMessage(), 'index_number') !== false) {
                $message = 'Index number already exists';
            } else {
                $message = 'Registration failed due to duplicate data';
            }
        } else {
            $message = 'Registration failed. Please try again.';
        }
        
        error_log("Student registration failed: " . $e->getMessage());
        
        echo json_encode(['success' => false, 'message' => $message]);
    }
    
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Student registration error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error occurred. Please try again later.'
    ]);
}
?>
