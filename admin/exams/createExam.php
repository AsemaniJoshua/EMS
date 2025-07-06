<?php
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
$currentPage = 'exams';
$pageTitle = "Create New Exam";
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
                                <input type="text" name="examCode" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., MATH101-FINAL-2023">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                                <select name="subjectId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Subject</option>
                                    <option value="1">Mathematics</option>
                                    <option value="2">Physics</option>
                                    <option value="3">Chemistry</option>
                                    <option value="4">Biology</option>
                                    <option value="5">Computer Science</option>
                                    <option value="6">English</option>
                                    <option value="7">History</option>
                                    <option value="8">Geography</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                                <select name="departmentId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Department</option>
                                    <option value="1">Science</option>
                                    <option value="2">Arts</option>
                                    <option value="3">Commerce</option>
                                    <option value="4">Technology</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program *</label>
                                <select name="programId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Program</option>
                                    <option value="1">Bachelor of Science</option>
                                    <option value="2">Bachelor of Arts</option>
                                    <option value="3">Bachelor of Commerce</option>
                                    <option value="4">Bachelor of Technology</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                                <select name="semesterId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Semester</option>
                                    <option value="1">Fall 2023</option>
                                    <option value="2">Spring 2024</option>
                                    <option value="3">Summer 2024</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                                <select name="courseId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Course</option>
                                    <option value="1">Introduction to Calculus</option>
                                    <option value="2">Physics Mechanics</option>
                                    <option value="3">Organic Chemistry</option>
                                    <option value="4">Introduction to Programming</option>
                                    <option value="5">Data Structures</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="Draft">Draft</option>
                                    <option value="Pending">Pending Approval</option>
                                    <option value="Approved">Approved</option>
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
                                <input type="date" name="startDate" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                                <input type="time" name="startTime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                                <input type="date" name="endDate" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                                <input type="time" name="endTime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) *</label>
                                <input type="number" name="duration" min="15" max="240" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 120">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%) *</label>
                                <input type="number" name="passingScore" min="1" max="100" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., 60">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Settings</label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="randomizeQuestions" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Randomize question order</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="showResults" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
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

    <script>
        // Auto-generate exam code
        document.querySelectorAll('select[name="courseId"], select[name="semesterId"]').forEach(select => {
            select.addEventListener('change', function() {
                generateExamCode();
            });
        });

        function generateExamCode() {
            const courseSelect = document.querySelector('select[name="courseId"]');
            const semesterSelect = document.querySelector('select[name="semesterId"]');
            const examCodeField = document.querySelector('input[name="examCode"]');

            if (courseSelect.value && semesterSelect.value && !examCodeField.value) {
                const courseText = courseSelect.options[courseSelect.selectedIndex].text;
                const semesterText = semesterSelect.options[semesterSelect.selectedIndex].text;
                
                // Extract course code and semester name for the exam code
                const courseCode = courseText.split(' ')[0];
                const semesterCode = semesterText.split(' ')[0].toUpperCase();
                const year = new Date().getFullYear();
                
                // Generate exam code
                examCodeField.value = `${courseCode}-${semesterCode}-${year}`;
            }
        }

        // Date validation
        document.querySelector('input[name="endDate"]').addEventListener('change', function() {
            const startDate = document.querySelector('input[name="startDate"]').value;
            if (startDate && this.value < startDate) {
                showNotification('End date cannot be earlier than start date', 'error');
                this.value = startDate;
            }
        });

        // Form submission
        document.getElementById('createExamForm').addEventListener('submit', function(e) {
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
            console.log('Creating exam:', examData);
            
            // Show success message
            showNotification('Exam created successfully! Redirecting to question editor...', 'success');
            
            // Redirect to questions page after a short delay
            setTimeout(() => {
                window.location.href = 'editQuestions.php?examId=1'; // In a real app, you'd pass the new exam ID
            }, 2000);
        });

        // Notification system
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

        // Helper function to initialize date fields with current date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="startDate"]').value = today;
            document.querySelector('input[name="endDate"]').value = today;
        });
    </script>
</body>

</html>
