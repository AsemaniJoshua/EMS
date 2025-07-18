<?php
// Debug page to test settings functionality
require_once '../../api/login/admin/sessionCheck.php';
require_once '../../api/config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h1>Debug: Admin Settings</h1>";

// Test database connection
echo "<h2>Database Connection</h2>";
try {
    $stmt = $conn->prepare("SELECT 1");
    $stmt->execute();
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test departments table
echo "<h2>Departments Table</h2>";
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM departments");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Departments table exists with {$count} records<br>";

    $stmt = $conn->prepare("SELECT * FROM departments LIMIT 3");
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($departments, true) . "</pre>";
} catch (Exception $e) {
    echo "❌ Departments table error: " . $e->getMessage() . "<br>";
}

// Test programs table
echo "<h2>Programs Table</h2>";
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM programs");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Programs table exists with {$count} records<br>";
} catch (Exception $e) {
    echo "❌ Programs table error: " . $e->getMessage() . "<br>";
}

// Test courses table
echo "<h2>Courses Table</h2>";
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM courses");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Courses table exists with {$count} records<br>";
} catch (Exception $e) {
    echo "❌ Courses table error: " . $e->getMessage() . "<br>";
}

// Test levels table
echo "<h2>Levels Table</h2>";
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM levels");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Levels table exists with {$count} records<br>";
} catch (Exception $e) {
    echo "❌ Levels table error: " . $e->getMessage() . "<br>";
}

// Test semesters table
echo "<h2>Semesters Table</h2>";
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM semesters");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Semesters table exists with {$count} records<br>";
} catch (Exception $e) {
    echo "❌ Semesters table error: " . $e->getMessage() . "<br>";
}

// Test API endpoints
echo "<h2>API Endpoints</h2>";
$endpoints = [
    'departments.php',
    'programs.php',
    'courses.php',
    'levels.php',
    'semesters.php',
    'backup.php'
];

foreach ($endpoints as $endpoint) {
    $path = "../../api/admin/settings/{$endpoint}";
    if (file_exists($path)) {
        echo "✅ {$endpoint} exists<br>";
    } else {
        echo "❌ {$endpoint} missing<br>";
    }
}

echo "<h2>Session Info</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";
