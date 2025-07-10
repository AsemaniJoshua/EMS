<?php
include_once __DIR__ . '/../../api/login/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';

$currentPage = 'exams';
$pageTitle = "Edit Exam";

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

// Fetch exam details from the database
$stmt = $conn->prepare(
    "SELECT e.exam_id, e.title, e.exam_code, e.description, e.department_id, e.program_id,
            e.semester_id, e.course_id, e.teacher_id, e.status, e.duration_minutes,
            e.pass_mark, e.total_marks, e.start_datetime, e.end_datetime, 
            e.randomize, e.show_results, e.anti_cheating
     FROM exams e
     WHERE e.exam_id = :exam_id"
);
$stmt->execute([':exam_id' => $examId]);
$examData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$examData) {
    // Redirect if exam not found
    header("Location: index.php");
    exit;
}

// Check if exam is completed - don't allow editing completed exams
if ($examData['status'] === 'Completed') {
    // Redirect to view page with error message
    header("Location: viewExam.php?id=$examId&error=completed");
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
    'startDate' => $startDateTime->format('Y-m-d'),
    'startTime' => $startDateTime->format('H:i'),
    'endDate' => $endDateTime->format('Y-m-d'),
    'endTime' => $endDateTime->format('H:i'),
    'randomize' => (bool)$examData['randomize'],
    'showResults' => (bool)$examData['show_results'],
    'antiCheating' => (bool)$examData['anti_cheating']
];

// Fetch departments for dropdown
$deptStmt = $conn->query("SELECT department_id, name FROM departments ORDER BY name");
$departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch programs for dropdown
$progStmt = $conn->query("SELECT program_id, name FROM programs ORDER BY name");
$programs = $progStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch semesters for dropdown
$semStmt = $conn->query("SELECT semester_id, name FROM semesters ORDER BY name");
$semesters = $semStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch courses for dropdown
$courseStmt = $conn->query("SELECT course_id, title, code FROM courses ORDER BY title");
$courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch teachers for dropdown
$teacherStmt = $conn->query("SELECT teacher_id, first_name, last_name FROM teachers WHERE status = 'active' ORDER BY last_name, first_name");
$teachers = $teacherStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Add SweetAlert CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
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
                    <button onclick="window.location.href='viewExam.php?id=<?php echo $examId; ?>'" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo $pageTitle; ?></h1>
                        <p class="mt-1 text-sm text-gray-500">Modify the exam settings and details</p>
                    </div>
                </div>
            </div>

            <!-- Edit Exam Form -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Exam Information</h3>
                </div>

                <form id="editExamForm" class="p-6 space-y-8">
                    <input type="hidden" name="examId" value="<?php echo $exam['id']; ?>">

                    <!-- Basic Exam Information Section -->
                    <div class="border-b border-gray-100 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-emerald-600"></i>
                            Basic Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title *</label>
                                <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" value="<?php echo htmlspecialchars($exam['title']); ?>" placeholder="Enter exam title">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Code *</label>
                                <input type="text" name="examCode" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" value="<?php echo htmlspecialchars($exam['examCode']); ?>" placeholder="e.g., MATH101-FINAL-2023">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                                <select name="departmentId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['department_id']; ?>" <?php echo ($exam['departmentId'] == $dept['department_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program *</label>
                                <select name="programId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Program</option>
                                    <?php foreach ($programs as $program): ?>
                                        <option value="<?php echo $program['program_id']; ?>" <?php echo ($exam['programId'] == $program['program_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($program['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                                <select name="semesterId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Semester</option>
                                    <?php foreach ($semesters as $semester): ?>
                                        <option value="<?php echo $semester['semester_id']; ?>" <?php echo ($exam['semesterId'] == $semester['semester_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($semester['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                                <select name="courseId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Course</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['course_id']; ?>" <?php echo ($exam['courseId'] == $course['course_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['code'] . ' - ' . $course['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teacher *</label>
                                <select name="teacherId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Teacher</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?php echo $teacher['teacher_id']; ?>" <?php echo ($exam['teacherId'] == $teacher['teacher_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="Draft" <?php echo $exam['status'] == 'Draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="Pending" <?php echo $exam['status'] == 'Pending' ? 'selected' : ''; ?>>Pending Approval</option>
                                    <option value="Approved" <?php echo $exam['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
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
                                <input type="date" name="startDate" required value="<?php echo $exam['startDate']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                                <input type="time" name="startTime" required value="<?php echo $exam['startTime']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                                <input type="date" name="endDate" required value="<?php echo $exam['endDate']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                                <input type="time" name="endTime" required value="<?php echo $exam['endTime']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) *</label>
                                <input type="number" name="duration" min="1" required value="<?php echo $exam['duration']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 90">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                                <input type="number" name="totalMarks" min="1" required value="<?php echo $exam['totalMarks']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%) *</label>
                                <input type="number" name="passMark" min="1" max="100" required value="<?php echo $exam['passMark']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 60">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Settings</label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="randomize" <?php echo $exam['randomize'] ? 'checked' : ''; ?> class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Randomize question order</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="showResults" <?php echo $exam['showResults'] ? 'checked' : ''; ?> class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Show results immediately after exam</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="antiCheating" <?php echo $exam['antiCheating'] ? 'checked' : ''; ?> class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
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
                            <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter a description or instructions for the exam"><?php echo htmlspecialchars($exam['description']); ?></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between space-x-4 pt-6 border-t border-gray-100">
                        <div>
                            <button type="button" onclick="confirmDelete(<?php echo $examId; ?>)" class="inline-flex items-center px-6 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Exam
                            </button>
                        </div>
                        <div class="flex space-x-4">
                            <button type="button" onclick="window.location.href='viewExam.php?id=<?php echo $examId; ?>'" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </button>
                            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Date validation
        document.querySelector('input[name="endDate"]').addEventListener('change', function() {
            const startDate = document.querySelector('input[name="startDate"]').value;
            if (startDate && this.value < startDate) {
                showNotification('End date cannot be earlier than start date', 'error');
                this.value = startDate;
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
                'courseId', 'teacherId', 'startDate', 'startTime', 'endDate',
                'endTime', 'duration', 'passMark', 'totalMarks'
            ];

            for (let field of requiredFields) {
                if (!examData[field]) {
                    Swal.close();
                    const fieldName = field.replace(/([A-Z])/g, ' $1').toLowerCase();
                    showNotification(`Please fill in the ${fieldName} field.`, 'error');
                    return;
                }
            }

            // Validate dates
            const startDateTime = new Date(`${examData.startDate}T${examData.startTime}`);
            const endDateTime = new Date(`${examData.endDate}T${examData.endTime}`);

            if (endDateTime <= startDateTime) {
                Swal.close();
                showNotification('End time must be after start time', 'error');
                return;
            }

            // Debug log
            console.log('Submitting data:', examData);

            // Send data to the server using Axios
            axios.post('../../api/exams/updateExam.php', examData)
                .then(response => {
                    console.log('Response:', response.data);
                    Swal.close(); // Close loading indicator

                    if (response.data.success) {
                        // Show success message with SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Exam updated successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Redirect to exam view
                            window.location.href = 'viewExam.php?id=' + examData.examId;
                        });
                    } else {
                        showNotification(response.data.message || 'Error updating exam', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.close(); // Close loading indicator

                    const errorMsg = error.response && error.response.data && error.response.data.message ?
                        error.response.data.message :
                        'An error occurred while updating the exam';
                    showNotification(errorMsg, 'error');
                });
        });

        // Confirmation modal for delete using SweetAlert
        function confirmDelete(examId) {
            Swal.fire({
                title: 'Delete Exam',
                text: 'Are you sure you want to delete this exam? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Display loading indicator
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send delete request to the API
                    axios.post('../../api/exams/deleteExam.php', {
                            examId: examId
                        })
                        .then(response => {
                            console.log('Delete response:', response.data);
                            Swal.close();

                            if (response.data.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Exam deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Redirect to exams list
                                    window.location.href = 'index.php';
                                });
                            } else {
                                showNotification(response.data.message || 'Error deleting exam', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.close();

                            const errorMsg = error.response && error.response.data && error.response.data.message ?
                                error.response.data.message :
                                'An error occurred while deleting the exam';
                            showNotification(errorMsg, 'error');
                        });
                }
            });
        }

        // Notification system using SweetAlert
        function showNotification(message, type = 'info') {
            // Map our types to SweetAlert types
            const sweetAlertTypes = {
                success: 'success',
                error: 'error',
                info: 'info',
                warning: 'warning'
            };

            Swal.fire({
                title: '',
                text: message,
                icon: sweetAlertTypes[type] || 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        }

        // Populate related fields when department changes
        document.querySelector('select[name="departmentId"]').addEventListener('change', function() {
            const departmentId = this.value;
            if (departmentId) {
                // Show confirmation if user is changing department and already has a program/course selected
                const programSelect = document.querySelector('select[name="programId"]');
                const courseSelect = document.querySelector('select[name="courseId"]');

                if (programSelect.value || (courseSelect.value && courseSelect.value !== '')) {
                    // Show confirmation before changing
                    if (!confirm('Changing the department will reset your program and course selections. Continue?')) {
                        // User cancelled, revert to previous department
                        this.value = this.getAttribute('data-previous-value') || '';
                        return;
                    }
                }

                // Store current value for potential revert
                this.setAttribute('data-previous-value', departmentId);

                // Show loading indicator for program dropdown
                programSelect.disabled = true;
                programSelect.innerHTML = '<option value="">Loading programs...</option>';

                // Show loading indicator for course dropdown
                courseSelect.disabled = true;
                courseSelect.innerHTML = '<option value="">Select Program First</option>';

                console.log('Fetching programs for department:', departmentId);

                // Fetch programs for the selected department
                axios.get('../../api/exams/getProgramsByDepartment.php', {
                        params: {
                            departmentId: departmentId
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            const programs = response.data.programs;

                            // Populate programs dropdown
                            programSelect.innerHTML = '<option value="">Select Program</option>';
                            programs.forEach(program => {
                                const option = document.createElement('option');
                                option.value = program.program_id;
                                option.textContent = program.name;
                                programSelect.appendChild(option);
                            });

                            // If we have the original program ID and it's in the new list, select it
                            const originalProgramId = '<?php echo $exam['programId']; ?>';
                            if (originalProgramId) {
                                const exists = Array.from(programSelect.options).some(option => option.value === originalProgramId);
                                if (exists) {
                                    console.log('Found original program ID in new options, setting value and triggering change');
                                    programSelect.value = originalProgramId;
                                    // Trigger change event to load courses
                                    const event = new Event('change');
                                    programSelect.dispatchEvent(event);
                                } else {
                                    console.log('Original program ID not found in new options');
                                    // If the original program is not in the list, we should still update courses dropdown
                                    // with whatever program is now selected (if any)
                                    if (programSelect.value) {
                                        programSelect.dispatchEvent(new Event('change'));
                                    }
                                }
                            }
                        } else {
                            showNotification('Failed to load programs: ' + response.data.message, 'error');
                            programSelect.innerHTML = '<option value="">Select Program</option>';
                        }
                        programSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error fetching programs:', error);
                        showNotification('Failed to load programs', 'error');
                        programSelect.innerHTML = '<option value="">Select Program</option>';
                        programSelect.disabled = false;
                    });
            }
        });

        // Populate related courses when program changes
        document.querySelector('select[name="programId"]').addEventListener('change', function() {
            const programId = this.value;
            const departmentId = document.querySelector('select[name="departmentId"]').value;

            // Get the course select element
            const courseSelect = document.querySelector('select[name="courseId"]');

            if (programId) {
                // Show loading indicator
                courseSelect.disabled = true;
                courseSelect.innerHTML = '<option value="">Loading courses...</option>';

                console.log('Fetching courses for program:', programId, 'and department:', departmentId);

                // Fetch courses for the selected program and department
                axios.get('../../api/exams/getCoursesByProgram.php', {
                        params: {
                            programId: programId,
                            departmentId: departmentId
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            const courses = response.data.courses;
                            console.log('Received', courses.length, 'courses');

                            // Populate courses dropdown
                            courseSelect.innerHTML = '<option value="">Select Course</option>';
                            courses.forEach(course => {
                                const option = document.createElement('option');
                                option.value = course.course_id;
                                option.textContent = `${course.code} - ${course.title}`;
                                courseSelect.appendChild(option);
                            });

                            // If we have the original course ID and it's in the new list, select it
                            const originalCourseId = '<?php echo $exam['courseId']; ?>';
                            if (originalCourseId) {
                                const exists = Array.from(courseSelect.options).some(option => option.value === originalCourseId);
                                if (exists) {
                                    console.log('Found original course ID in new options, setting value:', originalCourseId);
                                    courseSelect.value = originalCourseId;
                                } else {
                                    console.log('Original course ID not found in new course options:', originalCourseId);
                                    // If there's only one course option besides the default, select it automatically
                                    if (courseSelect.options.length === 2) {
                                        console.log('Only one course available, selecting it automatically');
                                        courseSelect.selectedIndex = 1; // Select the first non-default option
                                    }
                                }
                            }
                        } else {
                            showNotification('Failed to load courses: ' + response.data.message, 'error');
                            courseSelect.innerHTML = '<option value="">Select Course</option>';
                        }
                        courseSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error fetching courses:', error);
                        showNotification('Failed to load courses', 'error');
                        courseSelect.innerHTML = '<option value="">Select Course</option>';
                        courseSelect.disabled = false;
                    });
            } else {
                // If no program selected, clear and disable courses dropdown
                courseSelect.innerHTML = '<option value="">Select Program First</option>';
                courseSelect.disabled = true;
            }
        });

        // Initialize cascading dropdowns on page load
        document.addEventListener('DOMContentLoaded', function() {
            // If we have department and program values selected, trigger their change events
            const departmentSelect = document.querySelector('select[name="departmentId"]');
            const programSelect = document.querySelector('select[name="programId"]');
            const courseSelect = document.querySelector('select[name="courseId"]');

            // Store original values before triggering cascading events
            const originalDepartmentId = departmentSelect.value;
            const originalProgramId = programSelect.value;
            const originalCourseId = courseSelect.value;

            console.log('Original values on load:', {
                departmentId: originalDepartmentId,
                programId: originalProgramId,
                courseId: originalCourseId
            });

            if (originalDepartmentId) {
                // First, trigger department change event to load programs
                departmentSelect.dispatchEvent(new Event('change'));

                // We don't need to manually trigger program change because
                // the department's change handler will do this when it restores 
                // the original program value and triggers its change event
            }
        });
    </script>
</body>

</html>