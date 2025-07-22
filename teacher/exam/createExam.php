<?php
// --- Secure session start and teacher authentication ---
require_once __DIR__ . '/../../api/login/teacher/teacherSessionCheck.php';
require_once __DIR__ . '/../../api/config/database.php';
require_once __DIR__ . '/../components/teacherSidebar.php';
require_once __DIR__ . '/../components/teacherHeader.php';

$currentPage = 'exams';
$pageTitle = "Create New Exam";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$teacher_id = $_SESSION['teacher_id'];

$db = new Database();
$conn = $db->getConnection();
// Fetch departments
$stmt = $conn->prepare("SELECT department_id, name FROM departments ORDER BY name");
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch programs
$stmt = $conn->prepare("SELECT program_id, name, department_id FROM programs ORDER BY name");
$stmt->execute();
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch semesters
$stmt = $conn->prepare("SELECT semester_id, name FROM semesters ORDER BY name");
$stmt->execute();
$semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch levels
$stmt = $conn->prepare("SELECT level_id, name FROM levels ORDER BY level_id");
$stmt->execute();
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all courses
$stmt = $conn->prepare("
    SELECT DISTINCT c.course_id, c.code, c.title as name, c.department_id, c.program_id
    FROM courses c
    ORDER BY c.code
");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <button onclick="window.location.href='index.php'" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo $pageTitle; ?></h1>
                        <p class="mt-1 text-sm text-gray-500">Fill in the details to create a new examination</p>
                    </div>
                </div>
            </div>

            <!-- Create Exam Form -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Exam Information</h3>
                </div>

                <form id="createExamForm" class="p-6 space-y-8">
                  <input type="text" name="teacher_id" required class="hidden" value="<?php echo $teacher_id;?>">
                    <!-- Basic Exam Information Section -->
                    <div class="border-b border-gray-100 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-emerald-600"></i>
                            Basic Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title *</label>
                                <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter exam title">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Code *</label>
                                <input type="text" name="exam_code" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., CS101-F25-MID">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                                <select name="department_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo $department['department_id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program *</label>
                                <select name="program_id" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Program</option>
                                    <!-- Options will be populated by JavaScript based on department selection -->
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                                <select name="semester_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Semester</option>
                                    <?php foreach ($semesters as $semester): ?>
                                        <option value="<?php echo $semester['semester_id']; ?>"><?php echo htmlspecialchars($semester['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                                <select name="level_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Level</option>
                                    <?php foreach ($levels as $level): ?>
                                        <option value="<?php echo $level['level_id']; ?>"><?php echo htmlspecialchars($level['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                                <select name="course_id" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Course</option>
                                    <!-- Options will be populated by JavaScript based on department/program selection -->
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="Draft">Draft</option>
                                    <option value="Pending">Pending Approval</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Exam Settings Section -->
                    <div class="border-b border-gray-100 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-cog mr-2 text-blue-600"></i>
                            Exam Settings
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time *</label>
                                <input type="datetime-local" name="start_datetime" id="start_datetime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date & Time *</label>
                                <input type="datetime-local" name="end_datetime" id="end_datetime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) *</label>
                                <input type="number" name="duration_minutes" id="duration_minutes" min="15" max="240" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 120">
                            </div>

                            <script>
                                const startInput = document.getElementById('start_datetime');
                                const endInput = document.getElementById('end_datetime');
                                const durationInput = document.getElementById('duration_minutes');

                                function updateEndFromDuration() {
                                    const startVal = startInput.value;
                                    const duration = parseInt(durationInput.value);
                                    if (startVal && !isNaN(duration)) {
                                        const start = new Date(startVal);
                                        const end = new Date(start.getTime() + duration * 60000);
                                        endInput.value = end.toISOString().slice(0, 16);
                                    }
                                }

                                function updateDurationFromEnd() {
                                    const startVal = startInput.value;
                                    const endVal = endInput.value;
                                    if (startVal && endVal) {
                                        const start = new Date(startVal);
                                        const end = new Date(endVal);
                                        const duration = Math.round((end - start) / 60000);
                                        if (duration > 0) {
                                            durationInput.value = duration;
                                        }
                                    }
                                }

                                durationInput.addEventListener('input', updateEndFromDuration);
                                endInput.addEventListener('input', updateDurationFromEnd);
                                startInput.addEventListener('input', () => {
                                    if (durationInput.value) updateEndFromDuration();
                                    if (endInput.value) updateDurationFromEnd();
                                });
                            </script>


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                                <input type="number" name="total_marks" min="1" max="500" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 100" value="100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%) *</label>
                                <input type="number" name="passing_score" min="1" max="100" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 50.00" value="50.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Attempts</label>
                                <input type="number" name="max_attempts" min="1" max="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 1" value="1">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Settings</label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="randomize" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Randomize question order</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="show_results" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Show results immediately after exam</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="anti_cheating" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Enable anti-cheating measures</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exam Description -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-align-left mr-2 text-purple-600"></i>
                            Additional Information
                        </h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Description</label>
                            <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter a description or instructions for the exam"></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
                        <button type="button" onclick="window.location.href='index.php'" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Create Exam
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

        // Auto-generate exam code
        document.querySelectorAll('select[name="course_id"], select[name="semester_id"]').forEach(select => {
            select.addEventListener('change', function() {
                generateExamCode();
            });
        });

        function generateExamCode() {
            const courseSelect = document.querySelector('select[name="course_id"]');
            const semesterSelect = document.querySelector('select[name="semester_id"]');
            const examCodeField = document.querySelector('input[name="exam_code"]');

            if (courseSelect.value && semesterSelect.value && !examCodeField.value) {
                const courseText = courseSelect.options[courseSelect.selectedIndex].text;
                const semesterText = semesterSelect.options[semesterSelect.selectedIndex].text;

                // Extract course code and semester name for the exam code
                const courseCode = courseText.split(' ')[0]; // Gets the course code part before the dash
                const semesterCode = semesterText.split(' ')[0].toUpperCase();
                const year = new Date().getFullYear();

                // Generate exam code
                examCodeField.value = `${courseCode}-${semesterCode}-${year}`;
            }
        }

        // DOM elements
        const departmentSelect = document.querySelector('select[name="department_id"]');
        const programSelect = document.querySelector('select[name="program_id"]');
        const courseSelect = document.querySelector('select[name="course_id"]');
        const semesterSelect = document.querySelector('select[name="semester_id"]');
        const levelSelect = document.querySelector('select[name="level_id"]');

        // Handle department change to populate programs
        departmentSelect.addEventListener('change', function() {
            const departmentId = this.value;
            programSelect.innerHTML = '<option value="">Select Program</option>';
            programSelect.disabled = !departmentId;
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            courseSelect.disabled = true;

            if (departmentId) {
                // Fetch programs from API
                fetch(`/api/exams/getProgramsByDepartment.php?departmentId=${departmentId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.programs) {
                            data.programs.forEach(program => {
                                const option = document.createElement('option');
                                option.value = program.program_id;
                                option.textContent = program.name;
                                programSelect.appendChild(option);
                            });
                            programSelect.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching programs:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to load programs. Please try again.',
                            icon: 'error'
                        });
                    });
            }
        });

        // Handle program change to populate courses
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            courseSelect.disabled = !programId;

            if (programId) {
                // Fetch courses from API
                fetch(`/api/exams/getCoursesByProgram.php?programId=${programId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.courses) {
                            data.courses.forEach(course => {
                                const option = document.createElement('option');
                                option.value = course.course_id;
                                option.textContent = `${course.code} - ${course.name}`;
                                courseSelect.appendChild(option);
                            });
                            courseSelect.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching courses:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to load courses. Please try again.',
                            icon: 'error'
                        });
                    });
            }
        });

        // Date validation
        document.querySelector('input[name="end_datetime"]').addEventListener('change', function() {
            const startDateTime = document.querySelector('input[name="start_datetime"]').value;
            if (startDateTime && this.value <= startDateTime) {
                showNotification('End time must be after start time', 'error');
                this.value = '';
            }
        });

        // Duration auto-calculation
        document.querySelector('input[name="start_datetime"]').addEventListener('change', updateDuration);
        document.querySelector('input[name="end_datetime"]').addEventListener('change', updateDuration);

        function updateDuration() {
            const startDateTime = document.querySelector('input[name="start_datetime"]').value;
            const endDateTime = document.querySelector('input[name="end_datetime"]').value;
            const durationField = document.querySelector('input[name="duration_minutes"]');

            if (startDateTime && endDateTime) {
                const start = new Date(startDateTime);
                const end = new Date(endDateTime);
                const diffMinutes = Math.round((end - start) / (1000 * 60));

                if (diffMinutes > 0) {
                    durationField.value = diffMinutes;
                }
            }
        }

        // Form submission
        document.getElementById('createExamForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const examData = {};

            // Convert FormData to object
            for (let [key, value] of formData.entries()) {
                if (this.querySelector(`[name="${key}"]`).type === 'checkbox') {
                    examData[key] = this.querySelector(`[name="${key}"]`).checked ? 1 : 0;
                } else {
                    examData[key] = value;
                }
            }

            // Validate required fields
            const requiredFields = ['title', 'exam_code', 'department_id', 'program_id', 'semester_id', 'level_id', 'course_id', 'start_datetime', 'end_datetime', 'duration_minutes', 'total_marks', 'passing_score'];
            for (let field of requiredFields) {
                if (!examData[field]) {
                    const fieldName = field.replace(/_/g, ' ').replace(/([A-Z])/g, ' $1').toLowerCase();
                    showNotification(`Please fill in the ${fieldName} field.`, 'error');
                    return;
                }
            }

            // Validate dates
            const startDateTime = new Date(examData.start_datetime);
            const endDateTime = new Date(examData.end_datetime);

            if (endDateTime <= startDateTime) {
                showNotification('End time must be after start time', 'error');
                return;
            }

            // Split datetime into separate date and time fields for the backend
            // For start datetime
            const startDate = startDateTime.toISOString().split('T')[0]; // YYYY-MM-DD
            const startTime = startDateTime.toTimeString().split(' ')[0].substring(0, 5); // HH:MM

            // For end datetime
            const endDate = endDateTime.toISOString().split('T')[0]; // YYYY-MM-DD
            const endTime = endDateTime.toTimeString().split(' ')[0].substring(0, 5); // HH:MM

            // Add these fields to the exam data
            examData.start_date = startDate;
            examData.start_time = startTime;
            examData.end_date = endDate;
            examData.end_time = endTime;

            // Submit the form
            fetch('/api/exams/createExam.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(examData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Exam created successfully! You can now add questions to your exam.',
                            icon: 'success',
                            confirmButtonColor: '#10B981'
                        }).then(() => {
                            window.location.href = 'index.php';
                        });
                    } else {
                        showNotification(data.message || 'Failed to create exam', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Network error. Please try again.', 'error');
                });
        });

        // Notification system using SweetAlert2 toast
        function showNotification(message, type = 'info') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }

        // Helper function to initialize date fields with current date
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const today = now.toISOString().slice(0, 16); // Format for datetime-local

            // Set default start time to current time
            document.querySelector('input[name="start_datetime"]').value = today;

            // Set default end time to 2 hours later
            const twoHoursLater = new Date(now.getTime() + (2 * 60 * 60 * 1000));
            document.querySelector('input[name="end_datetime"]').value = twoHoursLater.toISOString().slice(0, 16);

            // Set default duration
            document.querySelector('input[name="duration_minutes"]').value = 120;
        });
    </script>
</body>

</html>