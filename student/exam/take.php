<?php
$pageTitle = "Take Exam";
$breadcrumb = "Take Exam";

// Start session and check authentication
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header('Location: /student/login/');
    exit;
}

$registration_id = isset($_GET['registration_id']) ? intval($_GET['registration_id']) : 0;
if ($registration_id <= 0) {
    header('Location: /student/exam/');
    exit;
}

require_once '../../api/config/database.php';

$student_id = $_SESSION['student_id'];
$db = new Database();
$conn = $db->getConnection();

// Verify student is registered for this exam using registration_id
$registrationQuery = "
    SELECT er.registration_id, e.exam_id, e.title, e.description, e.duration_minutes, 
           e.start_datetime, e.end_datetime, e.anti_cheating, e.total_marks,
           c.title as course_title, c.code as course_code
    FROM exam_registrations er
    JOIN exams e ON er.exam_id = e.exam_id
    JOIN courses c ON e.course_id = c.course_id
    WHERE er.registration_id = :registration_id AND er.student_id = :student_id
";

$stmt = $conn->prepare($registrationQuery);
$stmt->bindParam(':registration_id', $registration_id);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$exam = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
    header('Location: /student/exam/?error=not_registered');
    exit;
}

// Check if exam is active - using strtotime for reliable datetime parsing
$now = time();
$start_time = strtotime($exam['start_datetime']);
$end_time = strtotime($exam['end_datetime']);

// If parsing fails, log error and use fallback values to prevent false redirects
if ($start_time === false) {
    error_log("Failed to parse exam start time: {$exam['start_datetime']}");
    $start_time = $now - 3600; // Set to 1 hour ago to allow access
}

if ($end_time === false) {
    error_log("Failed to parse exam end time: {$exam['end_datetime']}");
    $end_time = $now + 86400; // Set to 24 hours from now to allow access
}

if ($now < $start_time) {
    header('Location: /student/exam/?error=not_started');
    exit;
}

if ($now > $end_time) {
    header('Location: /student/exam/?error=ended');
    exit;
}

// Check if already completed
$resultCheck = "SELECT result_id FROM results WHERE registration_id = :registration_id";
$stmt = $conn->prepare($resultCheck);
$stmt->bindParam(':registration_id', $exam['registration_id']);
$stmt->execute();

if ($stmt->fetch()) {
    header('Location: /student/results/?completed=1');
    exit;
}

// Get question count
$questionCountQuery = "SELECT COUNT(*) FROM questions WHERE exam_id = :exam_id";
$stmt = $conn->prepare($questionCountQuery);
$stmt->bindParam(':exam_id', $exam['exam_id']);
$stmt->execute();
$questionCount = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Student</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if ($exam['anti_cheating']): ?>
    <style>
        /* Anti-cheating styles */
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Disable right-click context menu */
        .no-context-menu {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
    <?php endif; ?>
</head>
<body class="bg-gray-50 min-h-screen <?php echo $exam['anti_cheating'] ? 'no-context-menu' : ''; ?>">
    
    <!-- Exam Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($exam['title']); ?></h1>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($exam['course_code'] . ' - ' . $exam['course_title']); ?></p>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Time Remaining</div>
                        <div id="timer" class="text-lg font-bold text-red-600">--:--</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-500">Progress</div>
                        <div id="progress" class="text-lg font-bold text-blue-600">0/<?php echo $questionCount; ?></div>
                    </div>
                    <button id="submitExamBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                        Submit Exam
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="pt-20 pb-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Loading State -->
            <div id="loadingState" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Loading exam questions...</p>
            </div>

            <!-- Exam Content -->
            <div id="examContent" class="hidden">
                <!-- Question Navigation -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Question Navigation</h2>
                    </div>
                    <div class="p-6">
                        <div id="questionNav" class="grid grid-cols-10 gap-2">
                            <!-- Question navigation buttons will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- Current Question -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex justify-between items-center">
                            <h2 id="questionTitle" class="text-lg font-semibold text-gray-900">Question 1</h2>
                            <div class="flex space-x-2">
                                <button id="prevBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition-colors duration-200" disabled>
                                    <i class="fas fa-chevron-left mr-2"></i>Previous
                                </button>
                                <button id="nextBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                                    Next<i class="fas fa-chevron-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="questionContent">
                            <!-- Question content will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div id="errorState" class="hidden text-center py-12">
                <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Error Loading Exam</h3>
                <p id="errorMessage" class="text-gray-600 mb-4"></p>
                <button onclick="location.reload()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
                    Try Again
                </button>
            </div>
        </div>
    </main>

    <script>
        // Exam taking functionality
        let examData = null;
        let currentQuestionIndex = 0;
        let answers = {};
        let timerInterval = null;
        let timeLeft = 0;
        let autoSaveInterval = null;

        // Anti-cheating measures
        <?php if ($exam['anti_cheating']): ?>
        let tabSwitchCount = 0;
        let maxTabSwitches = 3;

        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Disable F12, Ctrl+Shift+I, Ctrl+U, etc.
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.shiftKey && e.key === 'C') ||
                (e.ctrlKey && e.key === 'U') ||
                (e.ctrlKey && e.key === 'S')) {
                e.preventDefault();
                return false;
            }
        });

        // Detect tab switching
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                tabSwitchCount++;
                if (tabSwitchCount >= maxTabSwitches) {
                    Swal.fire({
                        title: 'Exam Violation',
                        text: 'You have switched tabs too many times. Your exam will be submitted automatically.',
                        icon: 'warning',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });
                    setTimeout(submitExam, 3000);
                } else {
                    Swal.fire({
                        title: 'Warning',
                        text: `Tab switching detected (${tabSwitchCount}/${maxTabSwitches}). Please stay on this page.`,
                        icon: 'warning',
                        timer: 4000,
                        showConfirmButton: false
                    });
                }
            }
        });

        // Prevent copy/paste
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            return false;
        });

        document.addEventListener('paste', function(e) {
            e.preventDefault();
            return false;
        });
        <?php endif; ?>

        // Initialize exam
        document.addEventListener('DOMContentLoaded', function() {
            loadExamData();
            
            // Event listeners
            document.getElementById('prevBtn').addEventListener('click', previousQuestion);
            document.getElementById('nextBtn').addEventListener('click', nextQuestion);
            document.getElementById('submitExamBtn').addEventListener('click', confirmSubmitExam);
        });

        // Load exam data
        function loadExamData() {
            fetch(`/api/students/getExamQuestions.php?registration_id=<?php echo $registration_id; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        examData = data.data;
                        answers = examData.existing_answers || {};
                        timeLeft = examData.time_left_seconds;
                        
                        initializeExam();
                        startTimer();
                        startAutoSave();
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading exam:', error);
                    showError('Failed to load exam data. Please try again.');
                });
        }

        // Initialize exam interface
        function initializeExam() {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('examContent').classList.remove('hidden');
            
            // Create question navigation
            createQuestionNavigation();
            
            // Show first question
            showQuestion(0);
        }

        // Create question navigation buttons
        function createQuestionNavigation() {
            const nav = document.getElementById('questionNav');
            nav.innerHTML = '';
            
            examData.questions.forEach((question, index) => {
                const button = document.createElement('button');
                button.className = 'w-10 h-10 rounded-lg font-semibold text-sm transition-colors duration-200';
                button.textContent = index + 1;
                button.onclick = () => showQuestion(index);
                
                updateQuestionNavButton(button, index);
                nav.appendChild(button);
            });
        }

        // Update question navigation button style
        function updateQuestionNavButton(button, index) {
            const isAnswered = answers[examData.questions[index].question_id];
            const isCurrent = index === currentQuestionIndex;
            
            if (isCurrent) {
                button.className = 'w-10 h-10 rounded-lg font-semibold text-sm transition-colors duration-200 bg-blue-600 text-white';
            } else if (isAnswered) {
                button.className = 'w-10 h-10 rounded-lg font-semibold text-sm transition-colors duration-200 bg-green-100 text-green-800 hover:bg-green-200';
            } else {
                                button.className = 'w-10 h-10 rounded-lg font-semibold text-sm transition-colors duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200';
            }
        }

        // Show specific question
        function showQuestion(index) {
            if (index < 0 || index >= examData.questions.length) return;
            
            currentQuestionIndex = index;
            const question = examData.questions[index];
            
            // Update question title
            document.getElementById('questionTitle').textContent = `Question ${index + 1} of ${examData.questions.length}`;
            
            // Update question content
            const content = document.getElementById('questionContent');
            content.innerHTML = `
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">${question.question_text}</h3>
                    <div class="space-y-3">
                        ${question.choices.map(choice => `
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                <input type="radio" name="question_${question.question_id}" value="${choice.choice_id}" 
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300"
                                       ${answers[question.question_id] == choice.choice_id ? 'checked' : ''}
                                       onchange="saveAnswer(${question.question_id}, ${choice.choice_id})">
                                <span class="ml-3 text-gray-900">${choice.choice_text}</span>
                            </label>
                        `).join('')}
                    </div>
                </div>
            `;
            
            // Update navigation buttons
            document.getElementById('prevBtn').disabled = index === 0;
            document.getElementById('nextBtn').textContent = index === examData.questions.length - 1 ? 'Finish' : 'Next';
            
            // Update question navigation
            updateQuestionNavigation();
            
            // Update progress
            const answeredCount = Object.keys(answers).length;
            document.getElementById('progress').textContent = `${answeredCount}/${examData.questions.length}`;
        }

        // Update all question navigation buttons
        function updateQuestionNavigation() {
            const buttons = document.querySelectorAll('#questionNav button');
            buttons.forEach((button, index) => {
                updateQuestionNavButton(button, index);
            });
        }

        // Save answer
        function saveAnswer(questionId, choiceId) {
            answers[questionId] = choiceId;
            
            // Update navigation
            updateQuestionNavigation();
            
            // Update progress
            const answeredCount = Object.keys(answers).length;
            document.getElementById('progress').textContent = `${answeredCount}/${examData.questions.length}`;
            
            // Save to server
            fetch('/api/students/saveExamAnswer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    registration_id: examData.registration_id,
                    question_id: questionId,
                    choice_id: choiceId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to save answer:', data.message);
                }
            })
            .catch(error => {
                console.error('Error saving answer:', error);
            });
        }

        // Navigation functions
        function previousQuestion() {
            if (currentQuestionIndex > 0) {
                showQuestion(currentQuestionIndex - 1);
            }
        }

        function nextQuestion() {
            if (currentQuestionIndex < examData.questions.length - 1) {
                showQuestion(currentQuestionIndex + 1);
            } else {
                confirmSubmitExam();
            }
        }

        // Timer functions
        function startTimer() {
            updateTimerDisplay();
            timerInterval = setInterval(() => {
                timeLeft--;
                updateTimerDisplay();
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    Swal.fire({
                        title: 'Time Up!',
                        text: 'Your exam time has expired. Submitting automatically...',
                        icon: 'warning',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });
                    setTimeout(submitExam, 4000);
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;
            
            let display = '';
            if (hours > 0) {
                display = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            } else {
                display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
            
            document.getElementById('timer').textContent = display;
            
            // Change color when time is running low
            const timerElement = document.getElementById('timer');
            if (timeLeft <= 300) { // 5 minutes
                timerElement.className = 'text-lg font-bold text-red-600';
            } else if (timeLeft <= 600) { // 10 minutes
                timerElement.className = 'text-lg font-bold text-yellow-600';
            } else {
                timerElement.className = 'text-lg font-bold text-green-600';
            }
        }

        // Auto-save functionality
        function startAutoSave() {
            autoSaveInterval = setInterval(() => {
                // Auto-save is handled by individual answer saves
                console.log('Auto-save check - answers saved individually');
            }, 30000); // Every 30 seconds
        }

        // Submit exam confirmation
        function confirmSubmitExam() {
            const answeredCount = Object.keys(answers).length;
            const totalQuestions = examData.questions.length;
            const unansweredCount = totalQuestions - answeredCount;
            
            let message = `You have answered ${answeredCount} out of ${totalQuestions} questions.`;
            if (unansweredCount > 0) {
                message += ` ${unansweredCount} questions are unanswered.`;
            }
            message += ' Are you sure you want to submit your exam?';
            
            Swal.fire({
                title: 'Submit Exam?',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Submit',
                cancelButtonText: 'Continue Exam'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitExam();
                }
            });
        }

        // Submit exam
        function submitExam() {
            // Clear intervals
            if (timerInterval) clearInterval(timerInterval);
            if (autoSaveInterval) clearInterval(autoSaveInterval);
            
            // Show loading
            Swal.fire({
                title: 'Submitting Exam...',
                text: 'Please wait while we process your submission.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/api/students/submitExam.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    registration_id: examData.registration_id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Exam Submitted!',
                        text: 'Your exam has been submitted successfully.',
                        icon: 'success',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'View Results'
                    }).then(() => {
                        window.location.href = `/student/results/view.php?result_id=${data.data.result_id}`;
                    });
                } else {
                    Swal.fire({
                        title: 'Submission Failed',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    });
                }
            })
            .catch(error => {
                console.error('Error submitting exam:', error);
                Swal.fire({
                    title: 'Submission Error',
                    text: 'An error occurred while submitting your exam. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            });
        }

        // Show error state
        function showError(message) {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('errorState').classList.remove('hidden');
            document.getElementById('errorMessage').textContent = message;
        }

        // Prevent page refresh/close without warning
        window.addEventListener('beforeunload', function(e) {
            if (examData && timerInterval) {
                e.preventDefault();
                e.returnValue = 'Are you sure you want to leave? Your exam progress may be lost.';
                return e.returnValue;
            }
        });
    </script>
</body>
</html>

