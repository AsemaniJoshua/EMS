<?php
require_once __DIR__ . '/../../../api/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    echo "<h2>Admin Users in Database:</h2>";
    $stmt = $conn->prepare("SELECT admin_id, email, username, first_name, last_name FROM admins");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Email</th><th>Username</th><th>Name</th></tr>";
    foreach ($admins as $admin) {
        echo "<tr>";
        echo "<td>" . $admin['admin_id'] . "</td>";
        echo "<td>" . $admin['email'] . "</td>";
        echo "<td>" . $admin['username'] . "</td>";
        echo "<td>" . $admin['first_name'] . " " . $admin['last_name'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
