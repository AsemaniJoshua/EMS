<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['choice_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Choice ID is required.']);
    exit;
}
$choice_id = intval($data['choice_id']);

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("DELETE FROM choices WHERE choice_id = :choice_id");
    $stmt->execute([':choice_id' => $choice_id]);
    echo json_encode(['status' => 'success', 'message' => 'Option deleted successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}