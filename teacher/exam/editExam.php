<?php
// --- Secure session start and teacher authentication ---
require_once __DIR__ . '/../../api/login/teacher/teacherSessionCheck.php';
require_once __DIR__ . '/../../api/config/database.php';
require_once __DIR__ . '/../components/teacherSidebar.php';
require_once __DIR__ . '/../components/teacherHeader.php';

$currentPage = 'exams';
$pageTitle = "Edit Exam";

// Check teacher session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
  header('Location: /teacher/login/');
  exit;
}

// Get exam ID from query
$examId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($examId <= 0) {
  // Redirect to exams list if no valid ID provided
  header("Location: index.php");
  exit;
}

// Initialize database connection
$db = new Database();
$conn = $db->getConnection();
$teacher_id = $_SESSION['teacher_id'];

// Fetch exam details from the database - Only allow teacher to edit their own exams
$stmt = $conn->prepare(
  "SELECT e.exam_id, e.title, e.exam_code, e.description, e.department_id, e.program_id,
            e.semester_id, e.course_id, e.teacher_id, e.status, e.duration_minutes,
            e.pass_mark, e.total_marks, e.start_datetime, e.end_datetime, 
            e.randomize, e.show_results, e.anti_cheating, e.max_attempts
     FROM exams e
     WHERE e.exam_id = :exam_id AND e.teacher_id = :teacher_id"
);
$stmt->execute([':exam_id' => $examId, ':teacher_id' => $teacher_id]);
$examData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$examData) {
  // Redirect if exam not found or doesn't belong to this teacher
  header("Location: index.php");
  exit;
}

// Check if exam can be edited
if ($examData['status'] === 'Completed' || $examData['status'] === 'Approved' || $examData['status'] === 'Pending') {
  // Redirect to view page with error message
  header("Location: viewExam.php?id=$examId&error=" . strtolower($examData['status']));
  exit;
}

// Format dates and times for the form
$startDateTime = new DateTime($examData['start_datetime']);
$endDateTime = new DateTime($examData['end_datetime']);

// Create exam object with properly formatted fields for the form
$exam = [
  'id' => $examData['exam_id'],
  'title' => $examData['title'],
  'examCode' => $examData['exam_code'],
  'description' => $examData['description'],
  'departmentId' => $examData['department_id'],
  'programId' => $examData['program_id'],
  'semesterId' => $examData['semester_id'],
  'courseId' => $examData['course_id'],
  'teacherId' => $examData['teacher_id'],
  'status' => $examData['status'],
  'duration' => $examData['duration_minutes'],
  'passMark' => $examData['pass_mark'],
  'totalMarks' => $examData['total_marks'],
  'startDateTime' => $startDateTime->format('Y-m-d\TH:i'),
  'endDateTime' => $endDateTime->format('Y-m-d\TH:i'),
  'randomize' => (bool)$examData['randomize'],
  'showResults' => (bool)$examData['show_results'],
  'antiCheating' => (bool)$examData['anti_cheating'],
  'maxAttempts' => $examData['max_attempts']
];

// Fetch departments for dropdown
$deptStmt = $conn->query("SELECT department_id, name FROM departments ORDER BY name");
$departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch programs for dropdown
$progStmt = $conn->query("SELECT program_id, name, department_id FROM programs ORDER BY name");
$programs = $progStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch semesters for dropdown
$semStmt = $conn->query("SELECT semester_id, name FROM semesters ORDER BY name");
$semesters = $semStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch courses for dropdown
$courseStmt = $conn->query("SELECT course_id, title, code, department_id, program_id FROM courses ORDER BY code");
$courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle; ?> - EMS Teacher</title>
  <link rel="stylesheet" href="/src/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
  <?php renderTeacherSidebar($currentPage); ?>
  <?php renderTeacherHeader(); ?>

  <!-- Main content -->
  <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

      <!-- Page Header -->
      <div class="mb-6">
        <div class="flex items-center mb-4">
          <button onclick="window.location.href='viewExam.php?id=<?php echo $examId; ?>'" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
          </button>
          <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo $pageTitle; ?></h1>
          <span class="ml-4 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <?php echo ucfirst($exam['status']); ?>
          </span>
        </div>
        <p class="mt-2 text-sm text-gray-600">
          Update your exam details. Remember to save your changes before navigating away.
        </p>
      </div>

      <!-- Edit Exam Form -->
      <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-edit mr-2 text-blue-600"></i>
            Edit Exam Information
          </h3>
        </div>

        <form id="editExamForm" class="p-6 space-y-8">
          <input type="hidden" name="examId" value="<?php echo $exam['id']; ?>">

          <!-- Basic Information -->
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <i class="fas fa-info-circle mr-2 text-blue-600"></i>
              Basic Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($exam['title']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter exam title">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Code *</label>
                <input type="text" name="examCode" value="<?php echo htmlspecialchars($exam['examCode']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., MATH101-MID-2024">
              </div>
            </div>
          </div>

          <!-- Academic Information -->
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <i class="fas fa-graduation-cap mr-2 text-emerald-600"></i>
              Academic Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                <select name="departmentId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                  <option value="">Select Department</option>
                  <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo $dept['department_id']; ?>" <?php echo $dept['department_id'] == $exam['departmentId'] ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($dept['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Program *</label>
                <select name="programId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                  <option value="">Select Program</option>
                  <?php foreach ($programs as $prog): ?>
                    <option value="<?php echo $prog['program_id']; ?>" data-department="<?php echo $prog['department_id']; ?>" <?php echo $prog['program_id'] == $exam['programId'] ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($prog['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                <select name="semesterId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                  <option value="">Select Semester</option>
                  <?php foreach ($semesters as $sem): ?>
                    <option value="<?php echo $sem['semester_id']; ?>" <?php echo $sem['semester_id'] == $exam['semesterId'] ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($sem['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                <select name="courseId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                  <option value="">Select Course</option>
                  <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>" data-department="<?php echo $course['department_id']; ?>" data-program="<?php echo $course['program_id']; ?>" <?php echo $course['course_id'] == $exam['courseId'] ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($course['code'] . ' - ' . $course['title']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Schedule and Timing -->
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <i class="fas fa-clock mr-2 text-orange-600"></i>
              Schedule and Timing
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time *</label>
                <input type="datetime-local" name="startDateTime" value="<?php echo $exam['startDateTime']; ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date & Time *</label>
                <input type="datetime-local" name="endDateTime" value="<?php echo $exam['endDateTime']; ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                <div class="mt-1 text-xs text-gray-500">
                  <i class="fas fa-info-circle mr-1"></i>
                  Updates automatically when duration is changed
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) *</label>
                <input type="number" name="duration" value="<?php echo $exam['duration']; ?>" min="15" max="240" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 120">
                <div class="mt-1 text-xs text-gray-500">
                  <i class="fas fa-info-circle mr-1"></i>
                  Changes automatically when start/end times are modified
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Attempts</label>
                <input type="number" name="maxAttempts" value="<?php echo $exam['maxAttempts']; ?>" min="1" max="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 1">
              </div>
            </div>
          </div>

          <!-- Grading and Marks -->
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <i class="fas fa-calculator mr-2 text-red-600"></i>
              Grading and Marks
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                <input type="number" name="totalMarks" value="<?php echo $exam['totalMarks']; ?>" min="1" max="500" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 100">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pass Mark (%) *</label>
                <input type="number" name="passMark" value="<?php echo $exam['passMark']; ?>" min="1" max="100" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 50.00">
              </div>
            </div>
          </div>

          <!-- Exam Settings -->
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <i class="fas fa-cogs mr-2 text-purple-600"></i>
              Exam Settings
            </h3>
            <div class="space-y-4">
              <label class="flex items-center">
                <input type="checkbox" name="randomize" value="1" <?php echo $exam['randomize'] ? 'checked' : ''; ?> class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                <span class="ml-3 text-sm font-medium text-gray-700">Randomize Questions</span>
                <span class="ml-2 text-xs text-gray-500">(Present questions in random order for each student)</span>
              </label>
              <label class="flex items-center">
                <input type="checkbox" name="showResults" value="1" <?php echo $exam['showResults'] ? 'checked' : ''; ?> class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                <span class="ml-3 text-sm font-medium text-gray-700">Show Results to Students</span>
                <span class="ml-2 text-xs text-gray-500">(Allow students to see their results after completion)</span>
              </label>
              <label class="flex items-center">
                <input type="checkbox" name="antiCheating" value="1" <?php echo $exam['antiCheating'] ? 'checked' : ''; ?> class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                <span class="ml-3 text-sm font-medium text-gray-700">Enable Anti-Cheating</span>
                <span class="ml-2 text-xs text-gray-500">(Prevent tab switching and copy-paste during exam)</span>
              </label>
            </div>
          </div>

          <!-- Exam Description -->
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
              <i class="fas fa-align-left mr-2 text-indigo-600"></i>
              Additional Information
            </h3>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Exam Description</label>
              <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter a description or instructions for the exam"><?php echo htmlspecialchars($exam['description']); ?></textarea>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
            <button type="button" onclick="window.location.href='viewExam.php?id=<?php echo $examId; ?>'" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
              <i class="fas fa-times mr-2"></i>
              Cancel
            </button>
            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
              <i class="fas fa-save mr-2"></i>
              Update Exam
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <script>
    // Store data for JavaScript
    const programs = <?php echo json_encode($programs); ?>;
    const courses = <?php echo json_encode($courses); ?>;

    // Auto-calculation functions for date/time and duration
    function updateEndFromDuration() {
      const startInput = document.querySelector('input[name="startDateTime"]');
      const endInput = document.querySelector('input[name="endDateTime"]');
      const durationInput = document.querySelector('input[name="duration"]');

      const startVal = startInput.value;
      const duration = parseInt(durationInput.value);

      if (startVal && !isNaN(duration) && duration > 0) {
        const start = new Date(startVal);
        const end = new Date(start.getTime() + duration * 60000); // Add duration in milliseconds
        endInput.value = end.toISOString().slice(0, 16); // Format as datetime-local

        // Add visual feedback
        endInput.style.borderColor = '#10b981';
        setTimeout(() => {
          endInput.style.borderColor = '';
        }, 1000);
      }
    }

    function updateDurationFromEnd() {
      const startInput = document.querySelector('input[name="startDateTime"]');
      const endInput = document.querySelector('input[name="endDateTime"]');
      const durationInput = document.querySelector('input[name="duration"]');

      const startVal = startInput.value;
      const endVal = endInput.value;

      if (startVal && endVal) {
        const start = new Date(startVal);
        const end = new Date(endVal);
        const duration = Math.round((end - start) / 60000); // Duration in minutes

        if (duration > 0) {
          durationInput.value = duration;

          // Add visual feedback
          durationInput.style.borderColor = '#10b981';
          setTimeout(() => {
            durationInput.style.borderColor = '';
          }, 1000);
        } else {
          // Invalid duration - end time is before start time
          Swal.fire({
            title: 'Invalid Date',
            text: 'End date and time must be after start date and time',
            icon: 'error'
          });
          endInput.value = '';
        }
      }
    }

    function updateEndFromStart() {
      const startInput = document.querySelector('input[name="startDateTime"]');
      const endInput = document.querySelector('input[name="endDateTime"]');
      const durationInput = document.querySelector('input[name="duration"]');

      // If we have a duration, update end time when start time changes
      if (durationInput.value) {
        updateEndFromDuration();
      }
      // If we have an end time, update duration when start time changes
      else if (endInput.value) {
        updateDurationFromEnd();
      }
    }

    // Event listeners for auto-calculation
    document.querySelector('input[name="duration"]').addEventListener('input', updateEndFromDuration);
    document.querySelector('input[name="endDateTime"]').addEventListener('input', updateDurationFromEnd);
    document.querySelector('input[name="startDateTime"]').addEventListener('input', updateEndFromStart);

    // Additional validation on change events
    document.querySelector('input[name="endDateTime"]').addEventListener('change', function() {
      const startDateTime = document.querySelector('input[name="startDateTime"]').value;
      if (startDateTime && this.value && this.value <= startDateTime) {
        Swal.fire({
          title: 'Invalid Date',
          text: 'End date and time must be after start date and time',
          icon: 'error'
        });
        this.value = '';
      }
    });

    // Handle department change to populate programs
    document.querySelector('select[name="departmentId"]').addEventListener('change', function() {
      const departmentId = this.value;
      const programSelect = document.querySelector('select[name="programId"]');
      const courseSelect = document.querySelector('select[name="courseId"]');

      // Clear existing options
      programSelect.innerHTML = '<option value="">Select Program</option>';
      courseSelect.innerHTML = '<option value="">Select Course</option>';

      if (departmentId) {
        // Filter programs by department
        programs.forEach(program => {
          if (program.department_id == departmentId) {
            const option = document.createElement('option');
            option.value = program.program_id;
            option.textContent = program.name;
            programSelect.appendChild(option);
          }
        });
      }
    });

    // Handle program change to populate courses
    document.querySelector('select[name="programId"]').addEventListener('change', function() {
      const programId = this.value;
      const departmentId = document.querySelector('select[name="departmentId"]').value;
      const courseSelect = document.querySelector('select[name="courseId"]');

      // Clear existing options
      courseSelect.innerHTML = '<option value="">Select Course</option>';

      if (programId && departmentId) {
        // Filter courses by program and department
        courses.forEach(course => {
          if (course.program_id == programId && course.department_id == departmentId) {
            const option = document.createElement('option');
            option.value = course.course_id;
            option.textContent = course.code + ' - ' + course.title;
            courseSelect.appendChild(option);
          }
        });
      }
    });

    // Form submission
    document.getElementById('editExamForm').addEventListener('submit', function(e) {
      e.preventDefault();

      // Display loading indicator
      Swal.fire({
        title: 'Updating exam...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Get form data
      const formData = new FormData(this);
      const examData = {};

      // Convert FormData to object
      for (let [key, value] of formData.entries()) {
        examData[key] = value;
      }

      // Handle checkbox values explicitly
      examData.randomize = formData.has('randomize') ? 1 : 0;
      examData.showResults = formData.has('showResults') ? 1 : 0;
      examData.antiCheating = formData.has('antiCheating') ? 1 : 0;

      // Validate required fields
      const requiredFields = [
        'title', 'examCode', 'departmentId', 'programId', 'semesterId',
        'courseId', 'startDateTime', 'endDateTime', 'duration', 'passMark', 'totalMarks'
      ];

      for (let field of requiredFields) {
        if (!examData[field]) {
          Swal.close();
          const fieldName = field.replace(/([A-Z])/g, ' $1').toLowerCase();
          Swal.fire({
            title: 'Missing Information',
            text: `Please fill in the ${fieldName} field.`,
            icon: 'error'
          });
          return;
        }
      }

      // Validate dates
      const startDateTime = new Date(examData.startDateTime);
      const endDateTime = new Date(examData.endDateTime);

      if (endDateTime <= startDateTime) {
        Swal.close();
        Swal.fire({
          title: 'Invalid Date',
          text: 'End time must be after start time',
          icon: 'error'
        });
        return;
      }

      // Send data to the server using Axios
      axios.post('/api/exams/editExam.php', examData)
        .then(response => {
          Swal.close();
          console.log('Response:', response.data);

          if (response.data.status === 'success') {
            Swal.fire({
              title: 'Success!',
              text: 'Exam updated successfully!',
              icon: 'success',
              showConfirmButton: false,
              timer: 1500
            }).then(() => {
              window.location.href = 'viewExam.php?id=' + examData.examId;
            });
          } else {
            Swal.fire({
              title: 'Error',
              text: response.data.message || 'Error updating exam',
              icon: 'error'
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.close();

          const errorMsg = error.response && error.response.data && error.response.data.message ?
            error.response.data.message :
            'An error occurred while updating the exam';
          Swal.fire({
            title: 'Error',
            text: errorMsg,
            icon: 'error'
          });
        });
    });

    // Initialize cascading dropdowns on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Trigger change events to populate dropdowns properly
      const departmentSelect = document.querySelector('select[name="departmentId"]');
      const programSelect = document.querySelector('select[name="programId"]');

      if (departmentSelect.value) {
        departmentSelect.dispatchEvent(new Event('change'));

        // Wait for programs to populate, then trigger program change
        setTimeout(() => {
          if (programSelect.value) {
            programSelect.dispatchEvent(new Event('change'));
          }
        }, 100);
      }
    });
  </script>
</body>

</html>