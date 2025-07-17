<?php
class Database
{
    // Local Database credentials
    private $host = "localhost";
    private $db_name = "exam_management"; // Change to your database name
    private $username = "root";   // Change if different
    private $password = "";       // Add password if set

    // Online Database credentials (update these with your actual online DB details)
    private $online_host = "sql307.infinityfree.com";
    private $online_db_name = "if0_36702081_exam_management";
    private $online_username = "if0_36702081";
    private $online_password = "9h7DhdmRJq";

    private $conn;

    // Get database connection with fallback to online database
    public function getConnection()
    {

        $this->conn = null;

        try {
            // Try local connection first
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // If local connection fails, try online connection
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->online_host . ";dbname=" . $this->online_db_name,
                    $this->online_username,
                    $this->online_password
                );
                $this->conn->exec("set names utf8");
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $online_exception) {
                echo "Connection error: " . $exception->getMessage();
                echo "<br>Online connection error: " . $online_exception->getMessage();
            }
        }

        return $this->conn;
    }
}
