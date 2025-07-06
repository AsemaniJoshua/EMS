<?php
/**
 * API for managing exam questions
 * 
 * Endpoints:
 * - GET /api/exam/questions.php?examId=X - Get all questions for an exam
 * - GET /api/exam/questions.php?id=X - Get a specific question
 * - POST /api/exam/questions.php - Create a new question
 * - PUT /api/exam/questions.php?id=X - Update a question
 * - DELETE /api/exam/questions.php?id=X - Delete a question
 */

// Set headers for JSON API
header('Content-Type: application/json');

// Include database connection
require_once __DIR__ . '/../config/database.php';

// Check request method
$method = $_SERVER['REQUEST_METHOD'];

// Mock database functions (in a real implementation, these would use the database)
function getExamQuestions($examId) {
    // In a real app, this would query the database
    // SELECT q.*, c.* FROM questions q
    // JOIN choices c ON q.question_id = c.question_id
    // WHERE q.exam_id = :examId
    
    // Mock data for demonstration
    return [
        [
            'id' => 1,
            'questionText' => "What is the derivative of f(x) = x²?",
            'choices' => [
                ['id' => 1, 'text' => "f'(x) = x", 'isCorrect' => false],
                ['id' => 2, 'text' => "f'(x) = 2x", 'isCorrect' => true],
                ['id' => 3, 'text' => "f'(x) = 2", 'isCorrect' => false],
                ['id' => 4, 'text' => "f'(x) = x²", 'isCorrect' => false]
            ]
        ],
        [
            'id' => 2,
            'questionText' => "Find the integral of g(x) = 2x.",
            'choices' => [
                ['id' => 5, 'text' => "G(x) = x² + C", 'isCorrect' => true],
                ['id' => 6, 'text' => "G(x) = 2x² + C", 'isCorrect' => false],
                ['id' => 7, 'text' => "G(x) = x + C", 'isCorrect' => false],
                ['id' => 8, 'text' => "G(x) = 2 ln|x| + C", 'isCorrect' => false]
            ]
        ]
    ];
}

function getQuestion($id) {
    // In a real app, this would query the database
    // SELECT q.*, c.* FROM questions q
    // JOIN choices c ON q.question_id = c.question_id
    // WHERE q.question_id = :id
    
    $questions = getExamQuestions(1); // Mock data
    foreach ($questions as $question) {
        if ($question['id'] == $id) {
            return $question;
        }
    }
    return null;
}

function createQuestion($data) {
    // In a real app, this would insert to the database
    // 1. Insert question
    // INSERT INTO questions (exam_id, question_text, sequence_number) 
    // VALUES (:examId, :questionText, :sequenceNumber)
    
    // 2. Get the question_id
    // $questionId = $pdo->lastInsertId();
    
    // 3. Insert choices
    // foreach ($data['choices'] as $choice) {
    //   INSERT INTO choices (question_id, choice_text, is_correct) 
    //   VALUES (:questionId, :choiceText, :isCorrect)
    // }
    
    // Mock response for demonstration
    return [
        'success' => true,
        'questionId' => rand(100, 999),
        'message' => 'Question created successfully'
    ];
}

function updateQuestion($id, $data) {
    // In a real app, this would update the database
    // 1. Update question
    // UPDATE questions SET question_text = :questionText 
    // WHERE question_id = :id
    
    // 2. Update choices
    // foreach ($data['choices'] as $choice) {
    //   UPDATE choices SET choice_text = :choiceText, is_correct = :isCorrect 
    //   WHERE choice_id = :choiceId
    // }
    
    // Mock response for demonstration
    return [
        'success' => true,
        'message' => 'Question updated successfully'
    ];
}

function deleteQuestion($id) {
    // In a real app, this would delete from the database
    // DELETE FROM questions WHERE question_id = :id
    // (assuming CASCADE DELETE for choices)
    
    // Mock response for demonstration
    return [
        'success' => true,
        'message' => 'Question deleted successfully'
    ];
}

// Handle the request based on method
switch ($method) {
    case 'GET':
        if (isset($_GET['examId'])) {
            $examId = intval($_GET['examId']);
            $questions = getExamQuestions($examId);
            echo json_encode([
                'success' => true,
                'questions' => $questions
            ]);
        } elseif (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $question = getQuestion($id);
            if ($question) {
                echo json_encode([
                    'success' => true,
                    'question' => $question
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Question not found'
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing examId or id parameter'
            ]);
        }
        break;
        
    case 'POST':
        // Get JSON input
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate data
        if (!$data || !isset($data['examId']) || !isset($data['questionText']) || !isset($data['choices']) || count($data['choices']) < 2) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request data'
            ]);
            break;
        }
        
        // Validate that at least one choice is marked as correct
        $hasCorrectAnswer = false;
        foreach ($data['choices'] as $choice) {
            if (isset($choice['isCorrect']) && $choice['isCorrect']) {
                $hasCorrectAnswer = true;
                break;
            }
        }
        
        if (!$hasCorrectAnswer) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'At least one choice must be marked as correct'
            ]);
            break;
        }
        
        // Create the question
        $result = createQuestion($data);
        
        if ($result['success']) {
            http_response_code(201); // Created
            echo json_encode($result);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create question'
            ]);
        }
        break;
        
    case 'PUT':
        // Validate question ID
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing question ID'
            ]);
            break;
        }
        
        $id = intval($_GET['id']);
        
        // Get JSON input
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate data
        if (!$data || !isset($data['questionText']) || !isset($data['choices']) || count($data['choices']) < 2) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request data'
            ]);
            break;
        }
        
        // Validate that at least one choice is marked as correct
        $hasCorrectAnswer = false;
        foreach ($data['choices'] as $choice) {
            if (isset($choice['isCorrect']) && $choice['isCorrect']) {
                $hasCorrectAnswer = true;
                break;
            }
        }
        
        if (!$hasCorrectAnswer) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'At least one choice must be marked as correct'
            ]);
            break;
        }
        
        // Update the question
        $result = updateQuestion($id, $data);
        
        if ($result['success']) {
            echo json_encode($result);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update question'
            ]);
        }
        break;
        
    case 'DELETE':
        // Validate question ID
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing question ID'
            ]);
            break;
        }
        
        $id = intval($_GET['id']);
        
        // Delete the question
        $result = deleteQuestion($id);
        
        if ($result['success']) {
            echo json_encode($result);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete question'
            ]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method Not Allowed'
        ]);
        break;
}
?>
