<?php
// Create password_reset_tokens table

// Include database connection
require_once __DIR__ . '/database.php';
$database = new Database();
$conn = $database->getConnection();

try {
    // Check if the table already exists
    $checkTable = $conn->query("SHOW TABLES LIKE 'password_reset_tokens'");
    if ($checkTable->rowCount() === 0) {
        // Create the password_reset_tokens table
        $sql = "
        CREATE TABLE password_reset_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            teacher_id INT NOT NULL,
            student_id INT NULL,
            admin_id INT NULL,
            token VARCHAR(10) NOT NULL,
            verified TINYINT(1) DEFAULT 0,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE CASCADE,
            FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
            FOREIGN KEY (admin_id) REFERENCES admins(admin_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $conn->exec($sql);
        echo "Table 'password_reset_tokens' created successfully\n";
    } else {
        echo "Table 'password_reset_tokens' already exists\n";
    }
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
