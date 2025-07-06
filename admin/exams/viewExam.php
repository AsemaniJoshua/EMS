<?php
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
$currentPage = 'exams';
$pageTitle = "Exam Details";

// In a real implementation, you would fetch the exam data from the database
// For now, we'll use mock data
$examId = isset($_GET['id']) ? intval($_GET['id']) : 1;
$exam = [
    'id' => $examId,
    'title' => 'Final Mathematics Exam',
    'examCode' => 'MATH101-FALL-2023',
    'description' => 'End of semester examination covering all topics from the curriculum.',
    'department' => 'Science',
    'program' => 'Bachelor of Science',
    'semester' => 'Fall 2023',
    'course' => 'Introduction to Calculus',
    'teacher' => 'Dr. Alan Smith',
    'status' => 'Draft',
    'duration' => 120,
    'passMark' => 60,
    'startDateTime' => '2023-12-15 09:00:00',
    'endDateTime' => '2023-12-15 11:00:00',
    'createdAt' => '2023-11-01 14:30:00',
    'randomize' => true,
    'showResults' => true,
    'antiCheating' => true
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <button onclick="window.location.href='index.php'" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo $exam['title']; ?></h1>
                        <p class="mt-1 text-sm text-gray-500">Exam Code: <?php echo $exam['examCode']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex justify-end space-x-3">
                <button onclick="window.location.href='createExam.php?id=<?php echo $examId; ?>'" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Exam
                </button>
                <button onclick="publishExam(<?php echo $examId; ?>)" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Publish Exam
                </button>
            </div>

            <!-- Tab Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button id="tab-details" class="tab-button border-emerald-500 text-emerald-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            Exam Details
                        </button>
                        <button id="tab-questions" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            Questions <span class="ml-1 px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs" id="question-count">0</span>
                        </button>
                        <button id="tab-preview" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            Exam Preview
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Content -->
            <div id="tab-content">
                <!-- Details Tab -->
                <div id="content-details" class="tab-content">
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Exam Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Basic Information</h4>
                                        <div class="mt-2 border rounded-lg overflow-hidden">
                                            <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Title</span>
                                                <span class="text-sm text-gray-900"><?php echo $exam['title']; ?></span>
                                            </div>
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Course</span>
                                                <span class="text-sm text-gray-900"><?php echo $exam['course']; ?></span>
                                            </div>
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Program</span>
                                                <span class="text-sm text-gray-900"><?php echo $exam['program']; ?></span>
                                            </div>
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Department</span>
                                                <span class="text-sm text-gray-900"><?php echo $exam['department']; ?></span>
                                            </div>
                                            <div class="px-4 py-3 flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Teacher</span>
                                                <span class="text-sm text-gray-900"><?php echo $exam['teacher']; ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Exam Schedule</h4>
                                        <div class="mt-2 border rounded-lg overflow-hidden">
                                            <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Start Date & Time</span>
                                                <span class="text-sm text-gray-900"><?php echo date('M j, Y g:i A', strtotime($exam['startDateTime'])); ?></span>
                                            </div>
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">End Date & Time</span>
                                                <span class="text-sm text-gray-900"><?php echo date('M j, Y g:i A', strtotime($exam['endDateTime'])); ?></span>
                                            </div>
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Duration</span>
                                                <span class="text-sm text-gray-900"><?php echo $exam['duration']; ?> minutes</span>
                                            </div>
                                            <div class="px-4 py-3 flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Semester</span>
                                                <span class="text-sm text-gray-900"><?php echo $exam['semester']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Settings</h4>
                                        <div class="mt-2 border rounded-lg overflow-hidden">
                                            <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Status</span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $exam['status'] === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                    <?php echo $exam['status']; ?>
                                                </span>
                                            </div>
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Exam Code</span>
                                                <span class="text-sm text-gray-900"><?php echo $exam['examCode']; ?></span>
                                            </div>

                                            

                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Passing Score</span>
                                                <span class="text-sm text-gray-900"><?php echo $exam['passMark']; ?>%</span>
                                            </div>
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Randomize Questions</span>
                                                <span class="inline-flex items-center">
                                                    <i class="fas <?php echo $exam['randomize'] ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-red-500'; ?>"></i>
                                                </span>
                                            </div>
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Show Results Immediately</span>
                                                <span class="inline-flex items-center">
                                                    <i class="fas <?php echo $exam['showResults'] ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-red-500'; ?>"></i>
                                                </span>
                                            </div>
                                            <div class="px-4 py-3 flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Anti-Cheating Measures</span>
                                                <span class="inline-flex items-center">
                                                    <i class="fas <?php echo $exam['antiCheating'] ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-red-500'; ?>"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Description</h4>
                                        <div class="mt-2 border rounded-lg p-4">
                                            <p class="text-sm text-gray-900"><?php echo $exam['description']; ?></p>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Created</h4>
                                        <div class="mt-2 border rounded-lg p-4">
                                            <p class="text-sm text-gray-900"><?php echo date('M j, Y g:i A', strtotime($exam['createdAt'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Tab -->
                <div id="content-questions" class="tab-content hidden">
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Questions</h3>
                            <button id="add-question-btn" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Add Question
                            </button>
                        </div>
                        
                        <div id="questions-container" class="p-6 space-y-6">
                            <!-- Questions will be loaded here dynamically -->
                            <div class="text-center py-6 text-gray-500" id="no-questions-message">
                                <i class="fas fa-question-circle text-4xl mb-2"></i>
                                <p>No questions added to this exam yet.</p>
                                <p class="text-sm">Click "Add Question" to create your first question.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Question Form (Hidden by default) -->
                    <div id="question-form-container" class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900" id="question-form-title">Add New Question</h3>
                        </div>
                        <form id="question-form" class="p-6 space-y-6">
                            <input type="hidden" name="questionId" id="question-id">
                            <input type="hidden" name="examId" value="<?php echo $examId; ?>">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Text *</label>
                                <textarea name="questionText" id="question-text" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter your question"></textarea>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-4">Answer Choices</label>
                                <div class="space-y-4" id="choices-container">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-6 mt-1">
                                            <input type="radio" name="correctChoice" value="0" checked class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                        </div>
                                        <div class="ml-3 w-full">
                                            <input type="text" name="choices[0]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Choice 1">
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex items-center h-6 mt-1">
                                            <input type="radio" name="correctChoice" value="1" class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                        </div>
                                        <div class="ml-3 w-full">
                                            <input type="text" name="choices[1]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Choice 2">
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex items-center h-6 mt-1">
                                            <input type="radio" name="correctChoice" value="2" class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                        </div>
                                        <div class="ml-3 w-full">
                                            <input type="text" name="choices[2]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Choice 3">
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex items-center h-6 mt-1">
                                            <input type="radio" name="correctChoice" value="3" class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                        </div>
                                        <div class="ml-3 w-full">
                                            <input type="text" name="choices[3]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Choice 4">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                                <button type="button" id="cancel-question-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                    <i class="fas fa-save mr-2"></i>
                                    Save Question
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Tab -->
                <div id="content-preview" class="tab-content hidden">
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Exam Preview</h3>
                            <p class="text-sm text-gray-500 mt-1">This is how the exam will appear to students.</p>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <h2 class="text-xl font-bold mb-2"><?php echo $exam['title']; ?></h2>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                                        Duration: <?php echo $exam['duration']; ?> minutes
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-purple-100 text-purple-800">
                                        Pass Mark: <?php echo $exam['passMark']; ?>%
                                    </span>
                                </div>
                                <p class="text-gray-700 mb-4"><?php echo $exam['description']; ?></p>
                                <hr class="my-4">
                            </div>
                            
                            <div id="preview-questions-container">
                                <!-- Preview questions will be loaded here -->
                                <div class="text-center py-6 text-gray-500" id="preview-no-questions-message">
                                    <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                                    <p>No questions to preview.</p>
                                    <p class="text-sm">Add questions in the Questions tab to see them here.</p>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg" disabled>
                                    Submit Exam
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-bold text-gray-900 mb-3" id="confirm-title">Delete Question</h3>
            <p class="text-gray-600 mb-6" id="confirm-message">Are you sure you want to delete this question? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button id="confirm-cancel" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button id="confirm-ok" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let questions = [];
        const examId = <?php echo $examId; ?>;
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            setupTabNavigation();
            loadQuestions();
            setupEventListeners();
        });

        // Tab navigation functionality
        function setupTabNavigation() {
            const tabs = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active state from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('border-emerald-500', 'text-emerald-600');
                        t.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    });
                    
                    // Add active state to clicked tab
                    tab.classList.add('border-emerald-500', 'text-emerald-600');
                    tab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show the corresponding content
                    const contentId = tab.id.replace('tab-', 'content-');
                    document.getElementById(contentId).classList.remove('hidden');
                    
                    // Special handling for preview tab
                    if (tab.id === 'tab-preview') {
                        loadPreview();
                    }
                });
            });
        }

        // Event listeners setup
        function setupEventListeners() {
            // Add question button
            document.getElementById('add-question-btn').addEventListener('click', () => {
                showQuestionForm();
            });
            
            // Cancel question form button
            document.getElementById('cancel-question-btn').addEventListener('click', () => {
                hideQuestionForm();
            });
            
            // Question form submission
            document.getElementById('question-form').addEventListener('submit', (e) => {
                e.preventDefault();
                saveQuestion();
            });
            
            // Confirmation modal buttons
            document.getElementById('confirm-cancel').addEventListener('click', () => {
                hideConfirmModal();
            });
            
            document.getElementById('confirm-ok').addEventListener('click', () => {
                if (window.confirmCallback) {
                    window.confirmCallback();
                }
                hideConfirmModal();
            });
        }

        // Load questions from the API
        function loadQuestions() {
            // In a real implementation, this would be an AJAX call to your API
            // For now, we'll use mock data
            fetch(`/api/exam/questions.php?examId=${examId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        questions = data.questions;
                        updateQuestionCount();
                        renderQuestions();
                    } else {
                        showNotification(data.message || 'Error loading questions', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading questions:', error);
                    
                    // For demonstration, create some mock data
                    questions = [
                        {
                            id: 1,
                            questionText: "What is the derivative of f(x) = x²?",
                            choices: [
                                { id: 1, text: "f'(x) = x", isCorrect: false },
                                { id: 2, text: "f'(x) = 2x", isCorrect: true },
                                { id: 3, text: "f'(x) = 2", isCorrect: false },
                                { id: 4, text: "f'(x) = x²", isCorrect: false }
                            ]
                        },
                        {
                            id: 2,
                            questionText: "Find the integral of g(x) = 2x.",
                            choices: [
                                { id: 5, text: "G(x) = x² + C", isCorrect: true },
                                { id: 6, text: "G(x) = 2x² + C", isCorrect: false },
                                { id: 7, text: "G(x) = x + C", isCorrect: false },
                                { id: 8, text: "G(x) = 2 ln|x| + C", isCorrect: false }
                            ]
                        }
                    ];
                    
                    updateQuestionCount();
                    renderQuestions();
                });
        }

        // Render questions in the questions tab
        function renderQuestions() {
            const container = document.getElementById('questions-container');
            const noQuestionsMessage = document.getElementById('no-questions-message');
            
            if (questions.length === 0) {
                noQuestionsMessage.classList.remove('hidden');
                container.innerHTML = '';
                return;
            }
            
            noQuestionsMessage.classList.add('hidden');
            
            // Clear the container first
            container.innerHTML = '';
            
            // Add each question
            questions.forEach((question, index) => {
                const questionElement = document.createElement('div');
                questionElement.className = 'border border-gray-200 rounded-lg';
                
                // Question header
                const header = document.createElement('div');
                header.className = 'bg-gray-50 px-4 py-3 flex justify-between items-center';
                
                const questionNumber = document.createElement('h4');
                questionNumber.className = 'text-sm font-medium text-gray-700';
                questionNumber.textContent = `Question ${index + 1}`;
                
                const actions = document.createElement('div');
                actions.className = 'flex items-center space-x-2';
                
                const editButton = document.createElement('button');
                editButton.className = 'text-blue-600 hover:text-blue-800 transition-colors';
                editButton.innerHTML = '<i class="fas fa-edit"></i>';
                editButton.addEventListener('click', () => editQuestion(question.id));
                
                const deleteButton = document.createElement('button');
                deleteButton.className = 'text-red-600 hover:text-red-800 transition-colors';
                deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                deleteButton.addEventListener('click', () => deleteQuestion(question.id));
                
                actions.appendChild(editButton);
                actions.appendChild(deleteButton);
                
                header.appendChild(questionNumber);
                header.appendChild(actions);
                
                // Question content
                const content = document.createElement('div');
                content.className = 'p-4';
                
                const questionText = document.createElement('p');
                questionText.className = 'text-sm text-gray-900 mb-4';
                questionText.textContent = question.questionText;
                
                content.appendChild(questionText);
                
                // Question choices
                const choicesContainer = document.createElement('div');
                choicesContainer.className = 'space-y-2';
                
                question.choices.forEach((choice, choiceIndex) => {
                    const choiceElement = document.createElement('div');
                    choiceElement.className = 'flex items-start';
                    
                    const radioWrapper = document.createElement('div');
                    radioWrapper.className = 'flex items-center h-5 mt-0.5';
                    
                    const radio = document.createElement('div');
                    radio.className = `h-4 w-4 rounded-full border ${choice.isCorrect ? 'bg-emerald-500 border-emerald-500' : 'border-gray-300'}`;
                    
                    radioWrapper.appendChild(radio);
                    
                    const labelWrapper = document.createElement('div');
                    labelWrapper.className = 'ml-3';
                    
                    const label = document.createElement('span');
                    label.className = `text-sm ${choice.isCorrect ? 'font-medium text-emerald-700' : 'text-gray-700'}`;
                    label.textContent = choice.text;
                    
                    labelWrapper.appendChild(label);
                    
                    choiceElement.appendChild(radioWrapper);
                    choiceElement.appendChild(labelWrapper);
                    
                    choicesContainer.appendChild(choiceElement);
                });
                
                content.appendChild(choicesContainer);
                
                questionElement.appendChild(header);
                questionElement.appendChild(content);
                
                container.appendChild(questionElement);
            });
        }

        // Show the question form for adding a new question
        function showQuestionForm(questionId = null) {
            const formContainer = document.getElementById('question-form-container');
            const formTitle = document.getElementById('question-form-title');
            const form = document.getElementById('question-form');
            
            // Reset the form
            form.reset();
            
            // If editing an existing question, populate the form
            if (questionId !== null) {
                const question = questions.find(q => q.id === questionId);
                if (question) {
                    document.getElementById('question-id').value = question.id;
                    document.getElementById('question-text').value = question.questionText;
                    
                    // Set the choices
                    question.choices.forEach((choice, index) => {
                        const choiceInput = document.querySelector(`input[name="choices[${index}]"]`);
                        if (choiceInput) {
                            choiceInput.value = choice.text;
                        }
                        
                        // Set the correct answer
                        if (choice.isCorrect) {
                            const radioInput = document.querySelector(`input[name="correctChoice"][value="${index}"]`);
                            if (radioInput) {
                                radioInput.checked = true;
                            }
                        }
                    });
                    
                    formTitle.textContent = 'Edit Question';
                } else {
                    formTitle.textContent = 'Add New Question';
                }
            } else {
                document.getElementById('question-id').value = '';
                formTitle.textContent = 'Add New Question';
            }
            
            formContainer.classList.remove('hidden');
            document.getElementById('question-text').focus();
        }

        // Hide the question form
        function hideQuestionForm() {
            document.getElementById('question-form-container').classList.add('hidden');
        }

        // Save a question (create or update)
        function saveQuestion() {
            const form = document.getElementById('question-form');
            const formData = new FormData(form);
            
            // Get form values
            const questionId = formData.get('questionId') || null;
            const questionText = formData.get('questionText');
            const correctChoice = parseInt(formData.get('correctChoice'));
            
            // Get choices
            const choices = [];
            for (let i = 0; i < 4; i++) {
                choices.push({
                    text: formData.get(`choices[${i}]`),
                    isCorrect: i === correctChoice
                });
            }
            
            // Create question object
            const questionData = {
                examId,
                questionText,
                choices
            };
            
            if (questionId) {
                questionData.id = parseInt(questionId);
            }
            
            // In a real implementation, this would be an AJAX call to your API
            console.log('Saving question:', questionData);
            
            // Mock API call
            const apiEndpoint = questionId ? `/api/exam/questions.php?id=${questionId}` : '/api/exam/questions.php';
            const method = questionId ? 'PUT' : 'POST';
            
            fetch(apiEndpoint, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(questionData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // For demo purposes, we'll just simulate a successful save
                    if (questionId) {
                        // Update existing question
                        const index = questions.findIndex(q => q.id === parseInt(questionId));
                        if (index !== -1) {
                            questions[index] = {
                                ...questions[index],
                                questionText,
                                choices: choices.map((choice, i) => ({
                                    id: questions[index].choices[i]?.id || Math.random() * 1000, // Mock ID
                                    text: choice.text,
                                    isCorrect: choice.isCorrect
                                }))
                            };
                        }
                    } else {
                        // Add new question
                        questions.push({
                            id: Math.floor(Math.random() * 1000), // Mock ID
                            questionText,
                            choices: choices.map((choice, i) => ({
                                id: Math.floor(Math.random() * 1000), // Mock ID
                                text: choice.text,
                                isCorrect: choice.isCorrect
                            }))
                        });
                    }
                    
                    updateQuestionCount();
                    renderQuestions();
                    hideQuestionForm();
                    showNotification(questionId ? 'Question updated successfully' : 'Question added successfully', 'success');
                } else {
                    showNotification(data.message || 'Error saving question', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving question:', error);
                
                // For demo purposes, simulate success
                if (questionId) {
                    // Update existing question
                    const index = questions.findIndex(q => q.id === parseInt(questionId));
                    if (index !== -1) {
                        questions[index] = {
                            ...questions[index],
                            questionText,
                            choices: choices.map((choice, i) => ({
                                id: questions[index].choices[i]?.id || Math.floor(Math.random() * 1000), // Mock ID
                                text: choice.text,
                                isCorrect: choice.isCorrect
                            }))
                        };
                    }
                } else {
                    // Add new question
                    questions.push({
                        id: Math.floor(Math.random() * 1000), // Mock ID
                        questionText,
                        choices: choices.map((choice, i) => ({
                            id: Math.floor(Math.random() * 1000), // Mock ID
                            text: choice.text,
                            isCorrect: choice.isCorrect
                        }))
                    });
                }
                
                updateQuestionCount();
                renderQuestions();
                hideQuestionForm();
                showNotification(questionId ? 'Question updated successfully' : 'Question added successfully', 'success');
            });
        }

        // Edit a question
        function editQuestion(questionId) {
            showQuestionForm(questionId);
        }

        // Delete a question
        function deleteQuestion(questionId) {
            showConfirmModal(
                'Delete Question',
                'Are you sure you want to delete this question? This action cannot be undone.',
                () => {
                    // In a real implementation, this would be an AJAX call to your API
                    fetch(`/api/exam/questions.php?id=${questionId}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the question from the array
                            questions = questions.filter(q => q.id !== questionId);
                            updateQuestionCount();
                            renderQuestions();
                            showNotification('Question deleted successfully', 'success');
                        } else {
                            showNotification(data.message || 'Error deleting question', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting question:', error);
                        
                        // For demo purposes, simulate success
                        questions = questions.filter(q => q.id !== questionId);
                        updateQuestionCount();
                        renderQuestions();
                        showNotification('Question deleted successfully', 'success');
                    });
                }
            );
        }

        // Update the question count badge
        function updateQuestionCount() {
            const badge = document.getElementById('question-count');
            badge.textContent = questions.length;
        }

        // Load the exam preview
        function loadPreview() {
            const container = document.getElementById('preview-questions-container');
            const noQuestionsMessage = document.getElementById('preview-no-questions-message');
            
            if (questions.length === 0) {
                noQuestionsMessage.classList.remove('hidden');
                container.innerHTML = '';
                return;
            }
            
            noQuestionsMessage.classList.add('hidden');
            
            // Clear the container first
            container.innerHTML = '';
            
            // Add each question
            questions.forEach((question, index) => {
                const questionElement = document.createElement('div');
                questionElement.className = 'mb-8';
                
                const questionHeader = document.createElement('div');
                questionHeader.className = 'mb-3';
                
                const questionNumber = document.createElement('h3');
                questionNumber.className = 'text-lg font-medium text-gray-900';
                questionNumber.textContent = `Question ${index + 1}`;
                
                questionHeader.appendChild(questionNumber);
                
                const questionText = document.createElement('p');
                questionText.className = 'mb-4 text-gray-800';
                questionText.textContent = question.questionText;
                
                const choicesContainer = document.createElement('div');
                choicesContainer.className = 'space-y-3';
                
                question.choices.forEach((choice, choiceIndex) => {
                    const choiceElement = document.createElement('div');
                    choiceElement.className = 'flex items-start';
                    
                    const radioWrapper = document.createElement('div');
                    radioWrapper.className = 'flex items-center h-5 mt-0.5';
                    
                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = `question-${question.id}`;
                    radio.className = 'h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500';
                    radio.value = choiceIndex;
                    
                    radioWrapper.appendChild(radio);
                    
                    const labelWrapper = document.createElement('div');
                    labelWrapper.className = 'ml-3';
                    
                    const label = document.createElement('span');
                    label.className = 'text-sm text-gray-700';
                    label.textContent = choice.text;
                    
                    labelWrapper.appendChild(label);
                    
                    choiceElement.appendChild(radioWrapper);
                    choiceElement.appendChild(labelWrapper);
                    
                    choicesContainer.appendChild(choiceElement);
                });
                
                questionElement.appendChild(questionHeader);
                questionElement.appendChild(questionText);
                questionElement.appendChild(choicesContainer);
                
                container.appendChild(questionElement);
            });
        }

        // Show the confirmation modal
        function showConfirmModal(title, message, callback) {
            const modal = document.getElementById('confirm-modal');
            document.getElementById('confirm-title').textContent = title;
            document.getElementById('confirm-message').textContent = message;
            window.confirmCallback = callback;
            modal.classList.remove('hidden');
        }

        // Hide the confirmation modal
        function hideConfirmModal() {
            document.getElementById('confirm-modal').classList.add('hidden');
            window.confirmCallback = null;
        }

        // Publish the exam
        function publishExam(examId) {
            if (questions.length === 0) {
                showNotification('Cannot publish an exam without questions', 'error');
                return;
            }
            
            showConfirmModal(
                'Publish Exam',
                'Are you sure you want to publish this exam? Once published, students will be able to see it.',
                () => {
                    // In a real implementation, this would be an AJAX call to your API
                    fetch(`/api/exam/publish.php?id=${examId}`, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Exam published successfully', 'success');
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 1500);
                        } else {
                            showNotification(data.message || 'Error publishing exam', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error publishing exam:', error);
                        showNotification('Exam published successfully', 'success');
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 1500);
                    });
                }
            );
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-emerald-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                warning: 'bg-orange-500'
            };

            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${colors[type] || colors.info} transform transition-all duration-300 ease-in-out`;
            toast.textContent = message;

            document.body.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }
    </script>
</body>

</html>