<?php
include_once __DIR__ . '/../../api/login/admin/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';

$currentPage = 'exams';
$pageTitle = "Create New Exam";

// Initialize database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch departments
$stmt = $conn->prepare("SELECT department_id as id, name FROM departments ORDER BY name");
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch programs
$stmt = $conn->prepare("SELECT program_id as id, name, department_id FROM programs ORDER BY name");
$stmt->execute();
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch levels
$stmt = $conn->prepare("SELECT level_id as id, name FROM levels ORDER BY name");
$stmt->execute();
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch semesters
$stmt = $conn->prepare("SELECT semester_id as id, name FROM semesters ORDER BY name");
$stmt->execute();
$semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all courses
$stmt = $conn->prepare("
    SELECT course_id as id, code, title as name, department_id, program_id
    FROM courses
    ORDER BY code
");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch teachers (admin can assign exams to teachers)
$stmt = $conn->prepare("
    SELECT teacher_id as id, CONCAT(first_name, ' ', last_name) as name 
    FROM teachers 
    ORDER BY first_name
");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                <input type="text" name="exam_code" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter exam code">
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                                <select name="department_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?= $department['id'] ?>"><?= htmlspecialchars($department['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program *</label>
                                <select name="program_id" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Program</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                                <select name="level_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Level</option>
                                    <?php foreach ($levels as $level): ?>
                                        <option value="<?= $level['id'] ?>"><?= htmlspecialchars($level['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                                <select name="semester_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Semester</option>
                                    <?php foreach ($semesters as $semester): ?>
                                        <option value="<?= $semester['id'] ?>"><?= htmlspecialchars($semester['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                                <select name="course_id" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Course</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Teacher</label>
                                <select name="teacher_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Teacher (Optional)</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="Draft">Draft</option>
                                    <option value="Pending">Pending Approval</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Active">Active</option>
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                                <input type="date" name="start_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="startDate">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                                <input type="time" name="start_time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="startTime">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Minutes) *</label>
                                <input type="number" name="duration" min="15" max="480" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 120" id="duration">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date (Auto-calculated)</label>
                                <input type="date" name="end_date" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600" id="endDate">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time (Auto-calculated)</label>
                                <input type="time" name="end_time" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600" id="endTime">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                                <input type="number" name="total_marks" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 100" value="100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%) *</label>
                                <input type="number" name="passing_score" min="1" max="100" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 60" value="50">
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
                                        <input type="checkbox" name="preventCheating" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
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
                            Save and Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store all data from PHP
            const departments = <?php echo json_encode($departments); ?>;
            const programs = <?php echo json_encode($programs); ?>;
            const courses = <?php echo json_encode($courses); ?>;
            const levels = <?php echo json_encode($levels); ?>;
            const semesters = <?php echo json_encode($semesters); ?>;

            // DOM elements
            const departmentSelect = document.querySelector('select[name="department_id"]');
            const programSelect = document.querySelector('select[name="program_id"]');
            const courseSelect = document.querySelector('select[name="course_id"]');
            const levelSelect = document.querySelector('select[name="level_id"]');
            const semesterSelect = document.querySelector('select[name="semester_id"]');

            // Start date/time inputs for calculating duration
            const startDateInput = document.querySelector('input[name="start_date"]');
            const startTimeInput = document.querySelector('input[name="start_time"]');
            const endDateInput = document.querySelector('input[name="end_date"]');
            const endTimeInput = document.querySelector('input[name="end_time"]');
            const durationInput = document.querySelector('input[name="duration_minutes"]');
            const examCodeInput = document.querySelector('input[name="exam_code"]');

            // Initialize date fields with current date
            const today = new Date().toISOString().split('T')[0];
            if (startDateInput) startDateInput.value = today;
            if (endDateInput) endDateInput.value = today;

            // Handle department change - update programs dropdown
            departmentSelect.addEventListener('change', function() {
                const departmentId = this.value;

                // Clear and disable dependent dropdowns
                programSelect.innerHTML = '<option value="">Select Program</option>';
                programSelect.disabled = !departmentId;

                courseSelect.innerHTML = '<option value="">Select Course</option>';
                courseSelect.disabled = true;

                // Filter programs by department_id
                if (departmentId) {
                    const filteredPrograms = programs.filter(p => p.department_id == departmentId);

                    filteredPrograms.forEach(program => {
                        const option = document.createElement('option');
                        option.value = program.id;
                        option.textContent = program.name;
                        programSelect.appendChild(option);
                    });
                }
            });

            // Handle program change - update courses dropdown
            programSelect.addEventListener('change', function() {
                const programId = this.value;

                // Clear and disable courses dropdown
                courseSelect.innerHTML = '<option value="">Select Course</option>';
                courseSelect.disabled = !programId;

                // Filter courses by program_id
                if (programId) {
                    const filteredCourses = courses.filter(c => c.program_id == programId);

                    filteredCourses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = `${course.code} - ${course.name}`;
                        courseSelect.appendChild(option);
                    });
                }
            });

            // Course change handler (no auto-generation of exam code)
            courseSelect.addEventListener('change', function() {
                // No automatic exam code generation
            });
            semesterSelect.addEventListener('change', function() {
                // No automatic exam code generation
            }); // Calculate duration when start/end dates change
            function updateDuration() {
                if (startDateInput && startTimeInput && endDateInput && endTimeInput) {
                    const startDate = startDateInput.value;
                    const startTime = startTimeInput.value;
                    const endDate = endDateInput.value;
                    const endTime = endTimeInput.value;

                    if (startDate && startTime && endDate && endTime) {
                        const start = moment(`${startDate} ${startTime}`);
                        const end = moment(`${endDate} ${endTime}`);

                        if (end.isAfter(start)) {
                            const durationMinutes = end.diff(start, 'minutes');
                            durationInput.value = durationMinutes;
                        } else {
                            // End time is before start time
                            durationInput.value = '';
                            Swal.fire({
                                title: 'Error',
                                text: 'End time must be after start time',
                                icon: 'error'
                            });
                        }
                    }
                }
            }

            // Add event listeners for date/time changes
            if (startDateInput) startDateInput.addEventListener('change', updateDuration);
            if (startTimeInput) startTimeInput.addEventListener('change', updateDuration);
            if (endDateInput) endDateInput.addEventListener('change', updateDuration);
            if (endTimeInput) endTimeInput.addEventListener('change', updateDuration);

            // Form submission handler
            document.getElementById('createExamForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                axios.post('../api/exams/createExam.php', formData)
                    .then(response => {
                        if (response.data.status === 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Exam created successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = 'index.php';
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message || 'Failed to create exam.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An unexpected error occurred. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            });
        });
    </script>
</body>

</html>