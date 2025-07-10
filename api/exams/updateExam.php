<?php
// API endpoint to update an existing exam
header('Content-Type: application/json');
require_once '../config/database.php';

// Verify request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed. Please use POST.']);
    exit();
}

// Get data from request (supports both form POST and JSON)
$inputData = file_get_contents('php://input');
if (!empty($inputData)) {
    // Try to decode as JSON
    $data = json_decode($inputData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = null;
    }
}

// If not JSON or JSON parsing failed, try POST data
if (empty($data)) {
    $data = $_POST;
}

// Check if we have any data
if (empty($data)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'No data provided.']);
    exit();
}

// Validate required fields
$requiredFields = [
    'examId',
    'title',
    'examCode',
    'description',
    'departmentId',
    'programId',
    'semesterId',
    'courseId',
    'teacherId',
    'status',
    'duration',
    'passMark',
    'totalMarks',
    'startDate',
    'startTime',
    'endDate',
    'endTime'
];

foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || (empty($data[$field]) && $field !== 'description')) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => "Missing required field: $field"]);
        exit();
    }
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Format datetime strings
    $startDateTime = $data['startDate'] . ' ' . $data['startTime'] . ':00';
    $endDateTime = $data['endDate'] . ' ' . $data['endTime'] . ':00';

    // Convert boolean values
    $randomize = isset($data['randomize']) ? 1 : 0;
    $showResults = isset($data['showResults']) ? 1 : 0;
    $antiCheating = isset($data['antiCheating']) ? 1 : 0;

    // Begin transaction for data integrity
    $conn->beginTransaction();

    // Update exam record
    $sql = "UPDATE exams SET 
                title = :title, 
                exam_code = :examCode,
                description = :description,
                department_id = :departmentId,
                program_id = :programId,
                semester_id = :semesterId,
                course_id = :courseId,
                teacher_id = :teacherId,
                status = :status,
                duration_minutes = :duration,
                pass_mark = :passMark,
                total_marks = :totalMarks,
                start_datetime = :startDateTime,
                end_datetime = :endDateTime,
                randomize = :randomize,
                show_results = :showResults,
                anti_cheating = :antiCheating
            WHERE exam_id = :examId";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':title' => $data['title'],
        ':examCode' => $data['examCode'],
        ':description' => $data['description'],
        ':departmentId' => $data['departmentId'],
        ':programId' => $data['programId'],
        ':semesterId' => $data['semesterId'],
        ':courseId' => $data['courseId'],
        ':teacherId' => $data['teacherId'],
        ':status' => $data['status'],
        ':duration' => $data['duration'],
        ':passMark' => $data['passMark'],
        ':totalMarks' => $data['totalMarks'],
        ':startDateTime' => $startDateTime,
        ':endDateTime' => $endDateTime,
        ':randomize' => $randomize,
        ':showResults' => $showResults,
        ':antiCheating' => $antiCheating,
        ':examId' => $data['examId']
    ]);

    // Check if update was successful
    if ($stmt->rowCount() === 0) {
        throw new Exception('No exam was updated. The exam ID may be invalid.');
    }

    // Commit the transaction
    $conn->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Exam updated successfully.',
        'examId' => $data['examId']
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollback();
    }

    http_response_code(500); // Internal Server Error
    echo json_encode([
        'error' => 'Failed to update exam.',
        'message' => $e->getMessage()
    ]);
}
