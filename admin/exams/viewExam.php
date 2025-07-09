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
            c.title AS course_title, d.name AS department_name, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name
     FROM exams e
     JOIN courses c ON e.course_id = c.course_id
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

// Fetch questions for the exam
$questions = [];
$stmt = $conn->prepare(
    "SELECT q.question_id, q.question_text 
     FROM questions q 
     WHERE q.exam_id = :exam_id 
     ORDER BY q.sequence_number ASC"
);
$stmt->execute([':exam_id' => $examId]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $questions[] = $row;
}
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
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo htmlspecialchars($exam['title']); ?></h1>
                        <p class="mt-1 text-sm text-gray-500">Exam Code: <?php echo htmlspecialchars($exam['exam_code']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex justify-end space-x-3">
                <button onclick="editExam(<?php echo $examId; ?>)" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Exam
                </button>
                <button onclick="publishExam(<?php echo $examId; ?>)" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Publish Exam
                </button>
                <button onclick="deleteExam(<?php echo $examId; ?>)" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Exam
                </button>
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

            <!-- Questions Section -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Questions</h3>
                    <button onclick="addQuestion(<?php echo $examId; ?>)" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Question
                    </button>
                </div>
                <div class="p-6">
                    <?php if (count($questions) > 0): ?>
                        <ul class="space-y-4">
                            <?php foreach ($questions as $question): ?>
                                <li class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                                    <span class="text-sm text-gray-700"><?php echo htmlspecialchars($question['question_text']); ?></span>
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editQuestion(<?php echo $question['question_id']; ?>)" class="text-blue-600 hover:text-blue-800 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteQuestion(<?php echo $question['question_id']; ?>)" class="text-red-600 hover:text-red-800 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">No questions added to this exam yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        function editExam(examId) {
            window.location.href = `editExam.php?id=${examId}`;
        }

        function publishExam(examId) {
            Swal.fire({
                title: 'Publish Exam',
                text: 'Are you sure you want to publish this exam? Once published, students will be able to see it.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, publish it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/api/exams/publishExam.php', { exam_id: examId })
                        .then(response => {
                            if (response.data.status === 'success') {
                                Swal.fire('Published!', response.data.message, 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                Swal.fire('Error!', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred while publishing the exam.', 'error');
                        });
                }
            });
        }

        function deleteExam(examId) {
            Swal.fire({
                title: 'Delete Exam',
                text: 'Are you sure you want to delete this exam? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/api/exams/deleteExam.php', { exam_id: examId })
                        .then(response => {
                            if (response.data.status === 'success') {
                                Swal.fire('Deleted!', response.data.message, 'success');
                                setTimeout(() => window.location.href = 'index.php', 1500);
                            } else {
                                Swal.fire('Error!', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred while deleting the exam.', 'error');
                        });
                }
            });
        }

        function addQuestion(examId) {
            window.location.href = `addQuestion.php?exam_id=${examId}`;
        }

        function editQuestion(questionId) {
            window.location.href = `editQuestion.php?id=${questionId}`;
        }

        function deleteQuestion(questionId) {
            Swal.fire({
                title: 'Delete Question',
                text: 'Are you sure you want to delete this question? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/api/exams/deleteQuestion.php', { question_id: questionId })
                        .then(response => {
                            if (response.data.status === 'success') {
                                Swal.fire('Deleted!', response.data.message, 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                Swal.fire('Error!', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred while deleting the question.', 'error');
                        });
                }
            });
        }
    </script>
</body>

</html>


