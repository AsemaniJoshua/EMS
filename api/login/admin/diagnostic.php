<?php
// Simple diagnostic script to check what's causing the Internal Server Error

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>EMS Login Diagnostic</h1>";

// Test 1: Basic PHP functionality
echo "<h2>1. PHP Basic Test</h2>";
echo "✅ PHP is working<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Test 2: Database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    require_once __DIR__ . '/../../../api/config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    echo "✅ Database connection successful<br>";

    // Test if admins table exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admins");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "✅ Admins table exists with {$count} records<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 3: Session functionality
echo "<h2>3. Session Test</h2>";
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo "✅ Session started successfully<br>";
    echo "Session ID: " . session_id() . "<br>";
} catch (Exception $e) {
    echo "❌ Session error: " . $e->getMessage() . "<br>";
}

// Test 4: JSON functionality
echo "<h2>4. JSON Test</h2>";
$test_data = ['status' => 'success', 'message' => 'Test'];
$json = json_encode($test_data);
if ($json !== false) {
    echo "✅ JSON encoding works<br>";
    echo "Test JSON: " . $json . "<br>";
} else {
    echo "❌ JSON encoding failed<br>";
}

// Test 5: File permissions
echo "<h2>5. File Permissions Test</h2>";
$login_file = __DIR__ . '/processLogin.php';
if (file_exists($login_file)) {
    echo "✅ processLogin.php exists<br>";
    echo "File permissions: " . substr(sprintf('%o', fileperms($login_file)), -4) . "<br>";
    if (is_readable($login_file)) {
        echo "✅ File is readable<br>";
    } else {
        echo "❌ File is not readable<br>";
    }
} else {
    echo "❌ processLogin.php does not exist<br>";
}

// Test 6: Include path test
echo "<h2>6. Include Path Test</h2>";
$config_path = __DIR__ . '/../../../api/config/database.php';
if (file_exists($config_path)) {
    echo "✅ Database config file exists<br>";
} else {
    echo "❌ Database config file missing<br>";
    echo "Looking for: " . $config_path . "<br>";
}

echo "<h2>7. Server Information</h2>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Current Directory: " . __DIR__ . "<br>";

// Test 8: Try to simulate the login process
echo "<h2>8. Login Process Simulation</h2>";
try {
    // Simulate the login input
    $test_input = [
        'email' => 'admin@app.ems.com',
        'password' => 'test'
    ];

    echo "✅ Input simulation prepared<br>";

    // Test password hashing
    $test_hash = password_hash('test', PASSWORD_DEFAULT);
    echo "✅ Password hashing works<br>";

    if (password_verify('test', $test_hash)) {
        echo "✅ Password verification works<br>";
    } else {
        echo "❌ Password verification failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Login simulation error: " . $e->getMessage() . "<br>";
}

echo "<p><strong>Diagnostic complete!</strong></p>";
