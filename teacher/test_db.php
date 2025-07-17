<?php
require_once '../api/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check exam details
    $stmt = $conn->prepare('SELECT exam_id, title, pass_mark FROM exams WHERE exam_id = 1');
    $stmt->execute();
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Exam Details:\n";
    echo "ID: " . $exam['exam_id'] . "\n";
    echo "Title: " . $exam['title'] . "\n";
    echo "Pass Mark: " . $exam['pass_mark'] . "\n\n";

    // Check results structure
    $stmt = $conn->prepare('SELECT * FROM results LIMIT 1');
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Results table columns:\n";
    if ($result) {
        foreach (array_keys($result) as $column) {
            echo "- " . $column . "\n";
        }
    } else {
        echo "No results found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
