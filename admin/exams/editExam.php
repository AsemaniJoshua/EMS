<?php
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
$currentPage = 'exams';
$pageTitle = "Edit Exam";

// In a real implementation, you would fetch the exam data from the database based on ID
// For now, we'll use mock data
$examId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($examId <= 0) {
    // Redirect to exams list if no valid ID provided
    header("Location: index.php");
    exit;
}

// Mock exam data (in a real app, this would come from the database)
$exam = [
    'id' => $examId,
    'title' => 'Final Mathematics Exam',
    'examCode' => 'MATH101-FALL-2023',
    'description' => 'End of semester examination covering all topics from the curriculum.',
    'departmentId' => 1, // Science
    'programId' => 1,    // Bachelor of Science
    'semesterId' => 1,   // Fall 2023
    'subjectId' => 1,    // Mathematics
    'courseId' => 1,     // Introduction to Calculus
    'teacherId' => 1,    // Dr. Alan Smith
    'status' => 'Draft',
    'duration' => 120,
    'passMark' => 60,
    'startDate' => '2023-12-15',
    'startTime' => '09:00',
    'endDate' => '2023-12-15',
    'endTime' => '11:00',
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
    <!-- Add SweetAlert CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                                <select name="subjectId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Subject</option>
                                    <option value="1" <?php echo $exam['subjectId'] == 1 ? 'selected' : ''; ?>>Mathematics</option>
                                    <option value="2" <?php echo $exam['subjectId'] == 2 ? 'selected' : ''; ?>>Physics</option>
                                    <option value="3" <?php echo $exam['subjectId'] == 3 ? 'selected' : ''; ?>>Chemistry</option>
                                    <option value="4" <?php echo $exam['subjectId'] == 4 ? 'selected' : ''; ?>>Biology</option>
                                    <option value="5" <?php echo $exam['subjectId'] == 5 ? 'selected' : ''; ?>>Computer Science</option>
                                    <option value="6" <?php echo $exam['subjectId'] == 6 ? 'selected' : ''; ?>>English</option>
                                    <option value="7" <?php echo $exam['subjectId'] == 7 ? 'selected' : ''; ?>>History</option>
                                    <option value="8" <?php echo $exam['subjectId'] == 8 ? 'selected' : ''; ?>>Geography</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                                <select name="departmentId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Department</option>
                                    <option value="1" <?php echo $exam['departmentId'] == 1 ? 'selected' : ''; ?>>Science</option>
                                    <option value="2" <?php echo $exam['departmentId'] == 2 ? 'selected' : ''; ?>>Arts</option>
                                    <option value="3" <?php echo $exam['departmentId'] == 3 ? 'selected' : ''; ?>>Commerce</option>
                                    <option value="4" <?php echo $exam['departmentId'] == 4 ? 'selected' : ''; ?>>Technology</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program *</label>
                                <select name="programId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Program</option>
                                    <option value="1" <?php echo $exam['programId'] == 1 ? 'selected' : ''; ?>>Bachelor of Science</option>
                                    <option value="2" <?php echo $exam['programId'] == 2 ? 'selected' : ''; ?>>Bachelor of Arts</option>
                                    <option value="3" <?php echo $exam['programId'] == 3 ? 'selected' : ''; ?>>Bachelor of Commerce</option>
                                    <option value="4" <?php echo $exam['programId'] == 4 ? 'selected' : ''; ?>>Bachelor of Technology</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                                <select name="semesterId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Semester</option>
                                    <option value="1" <?php echo $exam['semesterId'] == 1 ? 'selected' : ''; ?>>Fall 2023</option>
                                    <option value="2" <?php echo $exam['semesterId'] == 2 ? 'selected' : ''; ?>>Spring 2024</option>
                                    <option value="3" <?php echo $exam['semesterId'] == 3 ? 'selected' : ''; ?>>Summer 2024</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                                <select name="courseId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Course</option>
                                    <option value="1" <?php echo $exam['courseId'] == 1 ? 'selected' : ''; ?>>Introduction to Calculus</option>
                                    <option value="2" <?php echo $exam['courseId'] == 2 ? 'selected' : ''; ?>>Physics Mechanics</option>
                                    <option value="3" <?php echo $exam['courseId'] == 3 ? 'selected' : ''; ?>>Organic Chemistry</option>
                                    <option value="4" <?php echo $exam['courseId'] == 4 ? 'selected' : ''; ?>>Introduction to Programming</option>
                                    <option value="5" <?php echo $exam['courseId'] == 5 ? 'selected' : ''; ?>>Data Structures</option>
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
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Score (%) *</label>
                                    <input type="number" name="totalScore" min="1" max="100" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 60">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%) *</label>
                                    <input type="number" name="passingScore" min="1" max="100" required value="<?php echo $exam['passMark']; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 60">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Exam Settings</label>
                                    <div class="space-y-3">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="randomizeQuestions" <?php echo $exam['randomize'] ? 'checked' : ''; ?> class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                            <span class="ml-2 text-sm text-gray-700">Randomize question order</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="showResults" <?php echo $exam['showResults'] ? 'checked' : ''; ?> class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                            <span class="ml-2 text-sm text-gray-700">Show results immediately after exam</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="preventCheating" <?php echo $exam['antiCheating'] ? 'checked' : ''; ?> class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
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

            // Get form data
            const formData = new FormData(this);
            const examData = {};

            // Convert FormData to object
            for (let [key, value] of formData.entries()) {
                examData[key] = value;
            }

            // Validate required fields
            const requiredFields = ['title', 'examCode', 'subjectId', 'departmentId', 'programId', 'semesterId', 'courseId', 'startDate', 'startTime', 'endDate', 'endTime', 'duration', 'passingScore'];
            for (let field of requiredFields) {
                if (!examData[field]) {
                    const fieldName = field.replace(/([A-Z])/g, ' $1').toLowerCase();
                    showNotification(`Please fill in the ${fieldName} field.`, 'error');
                    return;
                }
            }

            // Validate dates
            const startDateTime = new Date(`${examData.startDate}T${examData.startTime}`);
            const endDateTime = new Date(`${examData.endDate}T${examData.endTime}`);

            if (endDateTime <= startDateTime) {
                showNotification('End time must be after start time', 'error');
                return;
            }

            // In a real application, you'd send this data to the server
            console.log('Updating exam:', examData);

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
                    // In a real app, you'd send a delete request to the server
                    console.log('Deleting exam:', examId);

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
    </script>
</body>

</html>