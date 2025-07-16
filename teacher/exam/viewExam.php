<?php
require_once __DIR__ . '/../../api/login/teacher/teacherSessionCheck.php';
require_once __DIR__ . '/../../api/config/database.php';
require_once __DIR__ . '/../components/teacherSidebar.php';
require_once __DIR__ . '/../components/teacherHeader.php';

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
$teacher_id = $_SESSION['teacher_id'];

// Fetch exam details with joins to related tables
$stmt = $conn->prepare(
  "SELECT e.exam_id, e.title, e.exam_code, e.description, e.duration_minutes, e.pass_mark, e.total_marks,
            e.start_datetime, e.end_datetime, e.status, e.randomize, e.show_results, e.anti_cheating, 
            e.created_at, e.max_attempts, c.title AS course_title, c.code AS course_code,
            p.name AS program_name, d.name AS department_name, s.name AS semester_name
     FROM exams e
     JOIN courses c ON e.course_id = c.course_id
     JOIN programs p ON e.program_id = p.program_id
     JOIN departments d ON e.department_id = d.department_id
     JOIN semesters s ON e.semester_id = s.semester_id
     WHERE e.exam_id = :exam_id AND e.teacher_id = :teacher_id"
);

$stmt->execute([':exam_id' => $examId, ':teacher_id' => $teacher_id]);
$exam = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
  header("Location: index.php");
  exit;
}

// Count questions for the exam
$stmt = $conn->prepare("SELECT COUNT(*) as question_count FROM questions WHERE exam_id = :exam_id");
$stmt->execute([':exam_id' => $examId]);
$questionCount = $stmt->fetch(PDO::FETCH_ASSOC)['question_count'];

// Fetch questions and choices for the exam - FIXED: Removed the non-existent 'mark' column
$questions = [];
$stmt = $conn->prepare(
  "SELECT q.question_id, q.question_text, q.sequence_number
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
  <title><?php echo htmlspecialchars($exam['title']); ?> - EMS Teacher</title>
  <link rel="stylesheet" href="/src/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
  <?php renderTeacherSidebar($currentPage); ?>
  <?php renderTeacherHeader(); ?>

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
          <?php if ($exam['status'] !== 'Completed'): ?>
            <button onclick="editExam(<?php echo $examId; ?>)" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
              <i class="fas fa-edit mr-1"></i>Edit
            </button>

            <?php if ($exam['status'] === 'Draft'): ?>
              <button onclick="submitForApproval(<?php echo $examId; ?>)" class="px-3 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                <i class="fas fa-paper-plane mr-1"></i>Submit for Approval
              </button>
            <?php elseif ($exam['status'] === 'Approved'): ?>
              <button onclick="unpublishExam(<?php echo $examId; ?>)" class="px-3 py-2 rounded-lg bg-orange-600 text-white hover:bg-orange-700">
                <i class="fas fa-undo mr-1"></i>Unpublish
              </button>
            <?php endif; ?>

            <button onclick="deleteExam(<?php echo $examId; ?>)" class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
              <i class="fas fa-trash mr-1"></i>Delete
            </button>
          <?php else: ?>
            <button disabled class="px-3 py-2 rounded-lg bg-gray-400 text-white cursor-not-allowed">
              <i class="fas fa-edit mr-1"></i>Edit
            </button>
            <button disabled class="px-3 py-2 rounded-lg bg-gray-400 text-white cursor-not-allowed">
              <i class="fas fa-paper-plane mr-1"></i>Submit
            </button>
            <button disabled class="px-3 py-2 rounded-lg bg-gray-400 text-white cursor-not-allowed">
              <i class="fas fa-trash mr-1"></i>Delete
            </button>
          <?php endif; ?>
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
                <li><strong>Course:</strong> <?php echo htmlspecialchars($exam['course_code'] . ' - ' . $exam['course_title']); ?></li>
                <li><strong>Program:</strong> <?php echo htmlspecialchars($exam['program_name']); ?></li>
                <li><strong>Department:</strong> <?php echo htmlspecialchars($exam['department_name']); ?></li>
                <li><strong>Semester:</strong> <?php echo htmlspecialchars($exam['semester_name']); ?></li>
                <li><strong>Status:</strong>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php
                                    if ($exam['status'] === 'Approved') echo 'bg-emerald-100 text-emerald-800';
                                    elseif ($exam['status'] === 'Pending') echo 'bg-yellow-100 text-yellow-800';
                                    elseif ($exam['status'] === 'Rejected') echo 'bg-red-100 text-red-800';
                                    elseif ($exam['status'] === 'Draft') echo 'bg-gray-100 text-gray-800';
                                    else echo 'bg-blue-100 text-blue-800';
                                    ?>">
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
                <li><strong>Total Marks:</strong> <?php echo $exam['total_marks']; ?></li>
              </ul>
            </div>
          </div>

          <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500">Settings</h4>
            <div class="mt-2 flex flex-wrap gap-4">
              <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100">
                <i class="fas fa-redo mr-1"></i>
                Max Attempts: <?php echo $exam['max_attempts']; ?>
              </div>
              <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $exam['randomize'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'; ?>">
                <i class="fas fa-random mr-1"></i>
                <?php echo $exam['randomize'] ? 'Randomize Questions' : 'Fixed Question Order'; ?>
              </div>
              <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $exam['show_results'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'; ?>">
                <i class="fas fa-eye mr-1"></i>
                <?php echo $exam['show_results'] ? 'Show Results to Students' : 'Hide Results from Students'; ?>
              </div>
              <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $exam['anti_cheating'] ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-600'; ?>">
                <i class="fas fa-shield-alt mr-1"></i>
                <?php echo $exam['anti_cheating'] ? 'Anti-Cheating Enabled' : 'Anti-Cheating Disabled'; ?>
              </div>
            </div>
          </div>

          <?php if ($exam['description']): ?>
            <div class="mt-6">
              <h4 class="text-sm font-medium text-gray-500">Description</h4>
              <div class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-700">
                <?php echo nl2br(htmlspecialchars($exam['description'])); ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Questions and Answers Section -->
      <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-900">Exam Questions (<?php echo count($questions); ?>)</h3>
          <?php if ($exam['status'] !== 'Completed' && $exam['status'] !== 'Approved' && $exam['status'] !== 'Pending'): ?>
            <button onclick="toggleQuestionForm()" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
              <i class="fas fa-plus mr-2"></i>Add Question
            </button>
          <?php endif; ?>
        </div>

        <?php if ($exam['status'] !== 'Completed' && $exam['status'] !== 'Approved' && $exam['status'] !== 'Pending'): ?>
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
        <?php endif; ?>

        <div class="p-6">
          <?php if (count($questions) > 0): ?>
            <div class="space-y-6">
              <?php foreach ($questions as $index => $question): ?>
                <div class="border border-gray-200 rounded-lg p-4" id="question-<?php echo $question['question_id']; ?>">
                  <div class="question-display">
                    <div class="flex justify-between items-center mb-2">
                      <h4 class="text-sm font-medium text-gray-900">Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['question_text']); ?></h4>
                      <?php if ($exam['status'] !== 'Completed' && $exam['status'] !== 'Approved' && $exam['status'] !== 'Pending'): ?>
                        <div class="flex gap-2">
                          <button onclick="toggleEditForm(<?php echo $question['question_id']; ?>)" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit Question">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button onclick="deleteQuestion(<?php echo $question['question_id']; ?>)" class="text-red-600 hover:text-red-800 transition-colors" title="Delete Question">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      <?php endif; ?>
                    </div>

                    <?php if (!empty($question['choices'])): ?>
                      <div class="ml-4 space-y-2">
                        <?php foreach ($question['choices'] as $choiceIndex => $choice): ?>
                          <div class="flex items-start text-sm">
                            <span class="flex-shrink-0 w-6 h-6 mr-2 mt-0.5 rounded-full border flex items-center justify-center text-xs <?php echo $choice['is_correct'] ? 'bg-emerald-100 text-emerald-800 border-emerald-600' : 'bg-gray-100 text-gray-600 border-gray-400'; ?>">
                              <?php echo chr(65 + $choiceIndex); ?>
                            </span>
                            <span class="text-gray-700 <?php echo $choice['is_correct'] ? 'font-bold text-emerald-600' : ''; ?>">
                              <?php echo htmlspecialchars($choice['choice_text']); ?>
                              <?php if ($choice['is_correct']): ?>
                                <span class="ml-2 text-xs text-emerald-600">(Correct Answer)</span>
                              <?php endif; ?>
                            </span>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <?php if ($exam['status'] !== 'Completed' && $exam['status'] !== 'Approved' && $exam['status'] !== 'Pending'): ?>
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
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-6">
              <div class="text-gray-400 mb-2"><i class="fas fa-question-circle text-4xl"></i></div>
              <p class="text-gray-500 mb-4">No questions have been added to this exam yet.</p>
              <?php if ($exam['status'] !== 'Completed' && $exam['status'] !== 'Approved' && $exam['status'] !== 'Pending'): ?>
                <button onclick="toggleQuestionForm()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                  <i class="fas fa-plus mr-2"></i>Add Questions Now
                </button>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Registered Students -->
      <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-900">Registered Students (<?php echo count($registeredStudents); ?>)</h3>
        </div>
        <div class="p-6">
          <?php if (count($registeredStudents) > 0): ?>
            <div class="space-y-4">
              <?php foreach ($registeredStudents as $student): ?>
                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                  <span class="text-sm text-gray-700">
                    <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                    (<?php echo htmlspecialchars($student['index_number']); ?>)
                  </span>
                  <span class="text-sm text-gray-500"><?php echo htmlspecialchars($student['email']); ?></span>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $student['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600'; ?>">
                    <?php echo ucfirst($student['status']); ?>
                  </span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-6">
              <div class="text-gray-400 mb-2"><i class="fas fa-users text-4xl"></i></div>
              <p class="text-gray-500">No students have registered for this exam yet.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </main>

  <script>
    // Display error message if redirected from editExam.php with error parameter
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (isset($_GET['error']) && $_GET['error'] === 'completed'): ?>
        Swal.fire({
          title: 'Cannot Edit',
          text: 'Completed exams cannot be edited.',
          icon: 'error'
        });
      <?php endif; ?>
    });

    function editExam(examId) {
      // Check if exam is completed or approved
      const currentStatus = '<?php echo $exam['status']; ?>';
      if (currentStatus === 'Completed' || currentStatus === 'Approved' || currentStatus === 'Pending') {
        Swal.fire({
          title: 'Cannot Edit',
          text: 'This exam cannot be edited as it is ' + currentStatus.toLowerCase() + '.',
          icon: 'warning',
          confirmButtonColor: '#3085d6'
        });
        return;
      }

      window.location.href = `editExam.php?id=${examId}`;
    }

    function submitForApproval(examId) {
      // Check if exam has questions
      const questionCount = <?php echo count($questions); ?>;
      if (questionCount === 0) {
        Swal.fire({
          title: 'Cannot Submit',
          text: 'You need to add at least one question before submitting for approval.',
          icon: 'warning',
          confirmButtonColor: '#3085d6'
        });
        return;
      }

      Swal.fire({
        title: 'Submit for Approval?',
        text: 'Once submitted, this exam will be sent to an administrator for approval. You cannot edit the exam while it is under review.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, submit it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading indicator
          Swal.fire({
            title: 'Submitting...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          axios.post('/api/exams/submitExamForApproval.php', {
              exam_id: examId
            })
            .then(function(response) {
              Swal.close();
              if (response.data.status === 'success') {
                Swal.fire({
                  title: 'Submitted!',
                  text: 'The exam has been submitted for approval.',
                  icon: 'success',
                  confirmButtonColor: '#10b981'
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  title: 'Error',
                  text: response.data.message || 'Failed to submit the exam for approval.',
                  icon: 'error',
                  confirmButtonColor: '#3085d6'
                });
              }
            })
            .catch(function(error) {
              Swal.close();
              console.error('Error:', error);
              Swal.fire({
                title: 'Error',
                text: 'Network error. Please try again.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
              });
            });
        }
      });
    }

    function unpublishExam(examId) {
      Swal.fire({
        title: 'Unpublish Exam?',
        text: 'This will change the exam status to Draft so you can make changes.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, unpublish it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading indicator
          Swal.fire({
            title: 'Unpublishing...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          axios.post('/api/exams/submitExamForApproval.php', {
              exam_id: examId,
              action: 'unpublish'
            })
            .then(function(response) {
              Swal.close();
              if (response.data.status === 'success') {
                Swal.fire({
                  title: 'Unpublished!',
                  text: 'The exam is now in Draft status.',
                  icon: 'success',
                  confirmButtonColor: '#10b981'
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  title: 'Error',
                  text: response.data.message || 'Failed to unpublish the exam.',
                  icon: 'error',
                  confirmButtonColor: '#3085d6'
                });
              }
            })
            .catch(function(error) {
              Swal.close();
              console.error('Error:', error);
              Swal.fire({
                title: 'Error',
                text: 'Network error. Please try again.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
              });
            });
        }
      });
    }

    function deleteExam(examId) {
      // Check if exam is completed
      const currentStatus = '<?php echo $exam['status']; ?>';
      if (currentStatus === 'Completed' || currentStatus === 'Approved') {
        Swal.fire({
          title: 'Cannot Delete',
          text: 'This exam cannot be deleted as it is ' + currentStatus.toLowerCase() + '.',
          icon: 'warning',
          confirmButtonColor: '#3085d6'
        });
        return;
      }

      Swal.fire({
        title: 'Are you sure?',
        text: 'This exam will be deleted permanently. This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading indicator
          Swal.fire({
            title: 'Deleting...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          axios.post('/api/exams/deleteExam.php', {
              exam_id: examId
            })
            .then(function(response) {
              Swal.close();
              if (response.data.status === 'success') {
                Swal.fire({
                  title: 'Deleted!',
                  text: 'The exam has been deleted.',
                  icon: 'success',
                  confirmButtonColor: '#10b981'
                }).then(() => {
                  window.location.href = 'index.php';
                });
              } else {
                Swal.fire({
                  title: 'Error',
                  text: response.data.message || 'Failed to delete the exam.',
                  icon: 'error',
                  confirmButtonColor: '#3085d6'
                });
              }
            })
            .catch(function(error) {
              Swal.close();
              console.error('Error:', error);
              Swal.fire({
                title: 'Error',
                text: 'Network error. Please try again.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
              });
            });
        }
      });
    }

    // Question management functions
    let choiceCount = 0;

    function toggleQuestionForm() {
      const form = document.getElementById('questionForm');
      const isHidden = form.classList.contains('hidden');

      form.classList.toggle('hidden');

      if (isHidden) {
        // Clear the form first
        document.getElementById('newQuestionForm').reset();
        document.getElementById('choicesContainer').innerHTML = '';
        choiceCount = 0;

        // Add at least two choice fields by default
        addChoiceField();
        addChoiceField();
      }
    }

    function addChoiceField(text = '', isCorrect = false) {
      const container = document.getElementById('choicesContainer');
      const choiceId = `choice-${choiceCount++}`;

      const html = `
        <div class="flex gap-2 items-center">
          <input type="text" name="choices[]" value="${text}" placeholder="Choice text" class="flex-1 border rounded p-1" required />
          <label class="flex items-center gap-1 text-sm">
            <input type="radio" name="correct_choice" value="${choiceCount - 1}" ${isCorrect ? 'checked' : ''} required />
            Correct
          </label>
          <button type="button" onclick="this.parentElement.remove()" class="text-red-500"><i class="fas fa-trash-alt"></i></button>
        </div>`;

      container.insertAdjacentHTML('beforeend', html);
    }

    function addEditChoiceField(form, text = '', isCorrect = false) {
      const container = form.querySelector('.edit-choices-container');
      const inputs = container.querySelectorAll('input[name="choices[]"]');
      const index = inputs.length;

      const html = `
        <div class="flex gap-2 items-center">
          <input type="text" name="choices[]" value="${text}" placeholder="Choice text" class="flex-1 border rounded p-1" required />
          <label class="flex items-center gap-1 text-sm">
            <input type="radio" name="correct_choice" value="${index}" ${isCorrect ? 'checked' : ''} required />
            Correct
          </label>
          <button type="button" onclick="this.parentElement.remove()" class="text-red-500"><i class="fas fa-trash-alt"></i></button>
        </div>`;

      container.insertAdjacentHTML('beforeend', html);
    }

    // Initialize question form submission
    document.getElementById('newQuestionForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = e.target;
      const questionText = form.question_text.value.trim();
      const choiceInputs = form.querySelectorAll('input[name="choices[]"]');
      const correctIndex = form.querySelector('input[name="correct_choice"]:checked')?.value;

      if (!questionText || choiceInputs.length < 2 || correctIndex === undefined) {
        alert("Please enter a question, at least two choices, and select the correct one.");
        return;
      }

      const choices = Array.from(choiceInputs).map((input, i) => ({
        choice_text: input.value.trim(),
        is_correct: parseInt(correctIndex) === i
      }));

      axios.post('/api/exams/addQuestionWithOptions.php', {
        exam_id: <?php echo $examId; ?>,
        question_text: questionText,
        choices
      }).then(response => {
        if (response.data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Question added successfully!',
            timer: 1500,
            showConfirmButton: false
          });
          setTimeout(() => window.location.reload(), 1500);
        } else {
          Swal.fire('Error', response.data.message || "Error occurred", 'error');
        }
      }).catch(() => {
        Swal.fire('Error', "Server error occurred", 'error');
      });
    });

    // Toggle the edit form for a question
    function toggleEditForm(questionId) {
      const questionItem = document.getElementById(`question-${questionId}`);
      const displaySection = questionItem.querySelector('.question-display');
      const editSection = questionItem.querySelector('.question-edit-form');

      displaySection.classList.toggle('hidden');
      editSection.classList.toggle('hidden');
    }

    // Update an existing question
    function updateQuestion(e, questionId) {
      e.preventDefault();

      const form = e.target;
      const questionText = form.question_text.value.trim();
      const choiceInputs = form.querySelectorAll('input[name="choices[]"]');
      const correctIndex = form.querySelector('input[name="correct_choice"]:checked')?.value;

      if (!questionText || choiceInputs.length < 2 || correctIndex === undefined) {
        alert("Please enter a question, at least two choices, and select the correct one.");
        return false;
      }

      const choices = Array.from(choiceInputs).map((input, i) => ({
        choice_text: input.value.trim(),
        is_correct: parseInt(correctIndex) === i
      }));

      axios.post('/api/exams/editQuestionWithOptions.php', {
        question_id: questionId,
        question_text: questionText,
        choices
      }).then(response => {
        if (response.data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Question updated successfully!',
            timer: 1500,
            showConfirmButton: false
          });
          setTimeout(() => window.location.reload(), 1500);
        } else {
          Swal.fire('Error', response.data.message || "Error occurred", 'error');
        }
      }).catch(() => {
        Swal.fire('Error', "Server error occurred", 'error');
      });

      return false;
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
          axios.post('/api/exams/deleteQuestion.php', {
              question_id: questionId
            })
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