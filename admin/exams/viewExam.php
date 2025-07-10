<?php
include_once __DIR__ . '/../../api/login/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';

$currentPage = 'exams';
$pageTitle = "Exam Details";

// Get exam ID from query
$examId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($examId <= 0) {
    header("Location: index.php");
    exit;
}

// Fetch exam details from the database
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare(
    "SELECT e.exam_id, e.title, e.exam_code, e.description, e.duration_minutes, e.pass_mark, e.start_datetime, e.end_datetime, 
            e.status, e.randomize, e.show_results, e.anti_cheating, e.created_at, 
            c.title AS course_title, p.name AS program_name, d.name AS department_name, 
            t.first_name AS teacher_first_name, t.last_name AS teacher_last_name
     FROM exams e
     JOIN courses c ON e.course_id = c.course_id
     JOIN programs p ON c.program_id = p.program_id
     JOIN departments d ON e.department_id = d.department_id
     JOIN teachers t ON e.teacher_id = t.teacher_id
     WHERE e.exam_id = :exam_id"
);
$stmt->execute([':exam_id' => $examId]);
$exam = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
    header("Location: index.php");
    exit;
}

// Fetch questions and choices for the exam
$questions = [];
$stmt = $conn->prepare(
    "SELECT q.question_id, q.question_text 
     FROM questions q 
     WHERE q.exam_id = :exam_id 
     ORDER BY q.sequence_number ASC"
);
$stmt->execute([':exam_id' => $examId]);
while ($question = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $choicesStmt = $conn->prepare(
        "SELECT choice_id, choice_text, is_correct 
         FROM choices 
         WHERE question_id = :question_id"
    );
    $choicesStmt->execute([':question_id' => $question['question_id']]);
    $question['choices'] = $choicesStmt->fetchAll(PDO::FETCH_ASSOC);
    $questions[] = $question;
}

// Fetch registered students for the exam
$registeredStudents = [];
$stmt = $conn->prepare(
    "SELECT s.student_id, s.first_name, s.last_name, s.index_number, s.email, s.status 
     FROM exam_registrations er
     JOIN students s ON er.student_id = s.student_id
     WHERE er.exam_id = :exam_id"
);
$stmt->execute([':exam_id' => $examId]);
$registeredStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

            <!-- Page Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <button onclick="window.location.href='index.php'" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <span class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo htmlspecialchars($exam['title']); ?></span>
                    <span class="ml-2 text-sm text-gray-500">Exam Code: <?php echo htmlspecialchars($exam['exam_code']); ?></span>
                </div>
                <div class="flex gap-2">
                    <button onclick="editExam(<?php echo $examId; ?>)" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"><i class="fas fa-edit mr-1"></i>Edit</button>
                    <button onclick="publishExam(<?php echo $examId; ?>)" class="px-3 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700"><i class="fas fa-paper-plane mr-1"></i>Publish</button>
                    <button onclick="deleteExam(<?php echo $examId; ?>)" class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700"><i class="fas fa-trash mr-1"></i>Delete</button>
                </div>
            </div>

            <!-- Exam Details -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Exam Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Basic Information</h4>
                            <ul class="mt-2 text-sm text-gray-700 space-y-2">
                                <li><strong>Course:</strong> <?php echo htmlspecialchars($exam['course_title']); ?></li>
                                <li><strong>Program:</strong> <?php echo htmlspecialchars($exam['program_name']); ?></li>
                                <li><strong>Department:</strong> <?php echo htmlspecialchars($exam['department_name']); ?></li>
                                <li><strong>Teacher:</strong> <?php echo htmlspecialchars($exam['teacher_first_name'] . ' ' . $exam['teacher_last_name']); ?></li>
                                <li><strong>Status:</strong>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $exam['status'] === 'Approved' ? 'bg-emerald-100 text-emerald-800' : ($exam['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600'); ?>">
                                        <?php echo ucfirst($exam['status']); ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Schedule</h4>
                            <ul class="mt-2 text-sm text-gray-700 space-y-2">
                                <li><strong>Start Date:</strong> <?php echo date('M d, Y H:i', strtotime($exam['start_datetime'])); ?></li>
                                <li><strong>End Date:</strong> <?php echo date('M d, Y H:i', strtotime($exam['end_datetime'])); ?></li>
                                <li><strong>Duration:</strong> <?php echo $exam['duration_minutes']; ?> minutes</li>
                                <li><strong>Pass Mark:</strong> <?php echo $exam['pass_mark']; ?>%</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registered Students -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Registered Students</h3>
                </div>
                <div class="p-6">
                    <?php if (count($registeredStudents) > 0): ?>
                        <ul class="space-y-4">
                            <?php foreach ($registeredStudents as $student): ?>
                                <li class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                                    <span class="text-sm text-gray-700">
                                        <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                                        (<?php echo htmlspecialchars($student['index_number']); ?>)
                                    </span>
                                    <span class="text-sm text-gray-500"><?php echo htmlspecialchars($student['email']); ?></span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $student['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600'; ?>">
                                        <?php echo ucfirst($student['status']); ?>
                                    </span>
                                    <button onclick="deleteRegisteredStudent(<?php echo $examId; ?>, <?php echo $student['student_id']; ?>)" class="ml-4 text-red-600 hover:text-red-800 transition-colors" title="Remove Student">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">No students have registered for this exam yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Questions and Choices -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Exam Questions</h3>
                    <button onclick="toggleQuestionForm()" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Question
                    </button>updat3
                </div>
                <div id="questionForm" class="hidden mx-6 mt-6 bg-gray-50 p-4 border rounded-lg">
                    <form id="newQuestionForm">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                        <textarea name="question_text" class="w-full border rounded p-2 mb-4" rows="3" required></textarea>

                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Choices</label>
                            <div id="choicesContainer" class="space-y-2">
                                <!-- Choice inputs will go here -->
                            </div>
                            <button type="button" onclick="addChoiceField()" class="text-xs text-emerald-600 hover:underline mt-2">
                                <i class="fas fa-plus mr-1"></i>Add Option
                            </button>
                        </div>

                        <button type="submit" class="mt-4 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                            Submit Question
                        </button>
                        <button type="button" onclick="toggleQuestionForm()" class="mt-4 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 ml-2">
                            Cancel
                        </button>
                    </form>
                </div>
                
                <div class="p-6">
                    <?php if (count($questions) > 0): ?>
                        <ul class="space-y-6">
                            <?php foreach ($questions as $question): ?>
                                <li class="border border-gray-200 rounded-lg p-4" id="question-<?php echo $question['question_id']; ?>">
                                    <div class="question-display">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($question['question_text']); ?></h4>
                                            <div class="flex gap-2">
                                                <button onclick="toggleEditForm(<?php echo $question['question_id']; ?>)" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit Question">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteQuestion(<?php echo $question['question_id']; ?>)" class="text-red-600 hover:text-red-800 transition-colors" title="Delete Question">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <ul class="space-y-2">
                                            <?php foreach ($question['choices'] as $choice): ?>
                                                <li class="flex items-center justify-between text-sm text-gray-700 <?php echo $choice['is_correct'] ? 'font-bold text-emerald-600' : ''; ?>">
                                                    <span>
                                                        <?php echo htmlspecialchars($choice['choice_text']); ?>
                                                        <?php if ($choice['is_correct']): ?>
                                                            <span class="ml-2 text-xs text-emerald-600">(Correct Answer)</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <div class="question-edit-form hidden mt-4">
                                        <form onsubmit="return updateQuestion(event, <?php echo $question['question_id']; ?>)">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                                            <textarea name="question_text" class="w-full border rounded p-2 mb-4" rows="3" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>

                                            <div class="mb-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Choices</label>
                                                <div class="edit-choices-container space-y-2">
                                                    <?php foreach ($question['choices'] as $index => $choice): ?>
                                                        <div class="flex gap-2 items-center">
                                                            <input type="text" name="choices[]" value="<?php echo htmlspecialchars($choice['choice_text']); ?>" class="flex-1 border rounded p-1" required />
                                                            <label class="flex items-center gap-1 text-sm">
                                                                <input type="radio" name="correct_choice" value="<?php echo $index; ?>" <?php echo $choice['is_correct'] ? 'checked' : ''; ?> required />
                                                                Correct
                                                            </label>
                                                            <button type="button" onclick="this.parentElement.remove()" class="text-red-500"><i class="fas fa-trash-alt"></i></button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <button type="button" onclick="addEditChoiceField(this.closest('form'))" class="text-xs text-emerald-600 hover:underline mt-2">
                                                    <i class="fas fa-plus mr-1"></i>Add Option
                                                </button>
                                            </div>

                                            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                Save Changes
                                            </button>
                                            <button type="button" onclick="toggleEditForm(<?php echo $question['question_id']; ?>)" class="mt-4 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 ml-2">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">No questions have been added to this exam yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="./scripts/viewExam.js"></script>
</body>

</html>