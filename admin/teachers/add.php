<?php
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
$currentPage = 'teachers';
$pageTitle = "Add New Teacher";
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
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Add New Teacher</h1>
                        <p class="mt-1 text-sm text-gray-500">Fill in the details below to add a new teacher to the system</p>
                    </div>
                </div>
            </div>

            <!-- Add Teacher Form -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Teacher Information</h3>
                </div>

                <form id="addTeacherForm" class="p-6 space-y-8">
                    <!-- Personal Information Section -->
                    <div class="border-b border-gray-100 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-user mr-2 text-emerald-600"></i>
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                <input type="text" name="firstName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter first name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                <input type="text" name="lastName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter last name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter email address">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phoneNumber" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter phone number">
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information Section -->
                    <div class="border-b border-gray-100 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                            Professional Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Staff ID *</label>
                                <input type="text" name="staffId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., TCH001">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                                <select name="departmentId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Department</option>
                                    <option value="1">Mathematics</option>
                                    <option value="2">Science</option>
                                    <option value="3">English</option>
                                    <option value="4">History</option>
                                    <option value="5">Computer Science</option>
                                    <option value="6">Physical Education</option>
                                    <option value="7">Arts</option>
                                    <option value="8">Music</option>
                                    <option value="9">Languages</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="active" checked class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700 flex items-center">
                                            <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                                            Active
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="inactive" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700 flex items-center">
                                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                            Inactive
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-key mr-2 text-purple-600"></i>
                            Account Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                                <input type="text" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter username">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                <div class="relative">
                                    <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter password">
                                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                                <div class="relative">
                                    <input type="password" name="confirmPassword" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Confirm password">
                                    <button type="button" onclick="togglePassword('confirmPassword')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="confirmPassword-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
                        <button type="button" onclick="window.location.href='index.php'" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Add Teacher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Toggle password visibility
        function togglePassword(fieldName) {
            const passwordField = document.querySelector(`input[name="${fieldName}"]`);
            const eyeIcon = document.getElementById(`${fieldName}-eye`);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Auto-generate username from email
        document.querySelector('input[name="email"]').addEventListener('blur', function() {
            const email = this.value;
            const usernameField = document.querySelector('input[name="username"]');

            if (email && !usernameField.value) {
                const username = email.split('@')[0];
                usernameField.value = username;
            }
        });

        // Auto-generate staff ID
        document.querySelector('select[name="departmentId"]').addEventListener('change', function() {
            const staffIdField = document.querySelector('input[name="staffId"]');
            const selectedDept = this.options[this.selectedIndex].text;

            if (selectedDept && selectedDept !== 'Select Department' && !staffIdField.value) {
                // Generate a simple staff ID - in real application, this would come from backend
                const deptCode = selectedDept.substring(0, 3).toUpperCase();
                const randomNum = Math.floor(Math.random() * 999) + 1;
                const staffId = `${deptCode}${randomNum.toString().padStart(3, '0')}`;
                staffIdField.value = staffId;
            }
        });

        // Form submission
        document.getElementById('addTeacherForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const teacherData = {};

            // Convert FormData to object
            for (let [key, value] of formData.entries()) {
                teacherData[key] = value;
            }

            // Validate passwords match
            if (teacherData.password !== teacherData.confirmPassword) {
                showNotification('Passwords do not match!', 'error');
                return;
            }

            // Validate required fields
            const requiredFields = ['firstName', 'lastName', 'email', 'staffId', 'departmentId', 'username', 'password'];
            for (let field of requiredFields) {
                if (!teacherData[field]) {
                    const fieldName = field.replace(/([A-Z])/g, ' $1').toLowerCase();
                    showNotification(`Please fill in the ${fieldName} field.`, 'error');
                    return;
                }
            }

            // Validate staff ID format
            if (!/^[A-Z]{3}\d{3}$/.test(teacherData.staffId)) {
                showNotification('Staff ID must be in format: ABC123 (3 letters followed by 3 numbers)', 'error');
                return;
            }

            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(teacherData.email)) {
                showNotification('Please enter a valid email address.', 'error');
                return;
            }

            // Validate password strength
            if (teacherData.password.length < 8) {
                showNotification('Password must be at least 8 characters long.', 'error');
                return;
            }

            // Here you would typically send the data to your backend
            console.log('Adding teacher:', teacherData);

            // Show success message
            showNotification('Teacher added successfully!', 'success');

            // Redirect back to teachers list after a short delay
            setTimeout(() => {
                window.location.href = 'index.php';
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

        // Real-time validation feedback
        document.querySelector('input[name="email"]').addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('border-red-300');
                this.classList.remove('border-gray-300');
            } else {
                this.classList.remove('border-red-300');
                this.classList.add('border-gray-300');
            }
        });

        document.querySelector('input[name="confirmPassword"]').addEventListener('input', function() {
            const password = document.querySelector('input[name="password"]').value;
            if (this.value && this.value !== password) {
                this.classList.add('border-red-300');
                this.classList.remove('border-gray-300');
            } else {
                this.classList.remove('border-red-300');
                this.classList.add('border-gray-300');
            }
        });
    </script>
</body>

</html>