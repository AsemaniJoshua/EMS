<?php
include_once __DIR__ . '/../../api/login/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
include_once __DIR__ . '/../../api/config/database.php';
$currentPage = 'teachers';
$pageTitle = "Edit Teacher";

// Fetch teacher data from DB
$teacherId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$teacher = null;

if ($teacherId > 0) {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch teacher data
    $stmt = $conn->prepare(
        "SELECT t.*, d.name AS department_name 
         FROM teachers t 
         JOIN departments d ON t.department_id = d.department_id 
         WHERE t.teacher_id = ?"
    );
    $stmt->execute([$teacherId]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch departments for dropdown
$departments = [];
$stmt = $conn->query("SELECT department_id, name FROM departments ORDER BY name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $departments[] = $row;
}

if (!$teacher) {
    // Redirect to teachers list if teacher not found
    header("Location: index.php");
    exit;
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
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Edit Teacher</h1>
                        <p class="mt-1 text-sm text-gray-500">Update information for <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Form Actions - Top -->
            <div class="mb-6 flex justify-end space-x-3">
                <button onclick="window.location.href='view.php?id=<?php echo $teacher['teacher_id']; ?>'" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    View Profile
                </button>
            </div>

            <!-- Edit Teacher Form -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Teacher Information</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $teacher['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800'; ?>">
                        <span class="w-2 h-2 <?php echo $teacher['status'] === 'active' ? 'bg-emerald-500' : 'bg-gray-500'; ?> rounded-full mr-2"></span>
                        <?php echo ucfirst($teacher['status']); ?>
                    </span>
                </div>

                <form id="editTeacherForm" class="p-6 space-y-8">
                    <input type="hidden" name="teacherId" value="<?php echo $teacher['teacher_id']; ?>">

                    <!-- Personal Information Section -->
                    <div class="border-b border-gray-100 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-user mr-2 text-emerald-600"></i>
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                    <input type="text" name="firstName" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter first name">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                    <input type="text" name="lastName" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter last name">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter email address">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phoneNumber" value="<?php echo htmlspecialchars($teacher['phone_number']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter phone number">
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
                                <input type="text" name="staffId" value="<?php echo htmlspecialchars($teacher['staff_id']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="e.g., TCH001">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                                <select name="departmentId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['department_id']; ?>" <?php echo $teacher['department_id'] == $dept['department_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="active" <?php echo $teacher['status'] === 'active' ? 'checked' : ''; ?> class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700 flex items-center">
                                            <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                                            Active
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="inactive" <?php echo $teacher['status'] === 'inactive' ? 'checked' : ''; ?> class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300">
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
                    <div class="border-b border-gray-100 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-key mr-2 text-amber-600"></i>
                            Account Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($teacher['username']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter username">
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Change Password</h4>
                            <p class="text-sm text-gray-500 mb-4">Leave blank to keep current password</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <div class="relative">
                                        <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter new password">
                                        <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye" id="password-eye"></i>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                    <div class="relative">
                                        <input type="password" name="confirmPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Confirm new password">
                                        <button type="button" onclick="togglePassword('confirmPassword')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye" id="confirmPassword-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-cog mr-2 text-gray-600"></i>
                            Additional Options
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="send_notification" name="send_notification" type="checkbox" class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="send_notification" class="font-medium text-gray-700">Send notification email</label>
                                    <p class="text-gray-500">Send an email to the teacher notifying them of these changes.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="reset_password" name="reset_password" type="checkbox" class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="reset_password" class="font-medium text-gray-700">Force password reset on next login</label>
                                    <p class="text-gray-500">The teacher will be required to set a new password when they next log in.</p>
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
                        <button type="submit" id="submitBtn" class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <script>
        // Toast notifications system
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

        // Form submission
        document.getElementById('editTeacherForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');

            // Disable button and show loading spinner
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i><span>Saving...</span>';

            // Get form data
            const formData = new FormData(this);
            const data = {};

            // Validate required fields
            const requiredFields = ['firstName', 'lastName', 'email', 'staffId', 'departmentId', 'username'];
            for (let field of requiredFields) {
                if (!data[field]) {
                    const fieldName = field.replace(/([A-Z])/g, ' $1').toLowerCase();
                    Toast.fire({
                        icon: 'error',
                        title: `Please fill in the ${fieldName} field.`
                    });

                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Changes';
                    return;
                }
            }

            // Check if passwords match if changing password
            if (data.password && data.password !== data.confirmPassword) {
                Toast.fire({
                    icon: 'error',
                    title: 'Passwords do not match!'
                });

                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Changes';
                return;
            }

            // Validate password strength if changing password
            if (data.password && data.password.length < 8) {
                Toast.fire({
                    icon: 'error',
                    title: 'Password must be at least 8 characters long.'
                });

                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Changes';
                return;
            }

            // Send the update request to the server
            axios.post('/api/teachers/updateTeacher.php', data)
                .then(function(response) {
                    if (response.data.status === 'success') {
                        Toast.fire({
                            icon: 'success',
                            title: 'Teacher information updated successfully!'
                        });

                        // Redirect back to view page after a short delay
                        setTimeout(() => {
                            window.location.href = `view.php?id=${data.teacherId}`;
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.data.message || 'An error occurred during the update.'
                        });

                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Changes';
                    }
                })
                .catch(function(error) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Server error. Please try again.'
                    });
                    console.error(error);

                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Changes';
                });
        });
    </script>
</body>

</html>