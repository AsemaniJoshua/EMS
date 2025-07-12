<?php
// --- Secure session start and teacher authentication ---
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}
require_once __DIR__ . '/../../api/config/database.php';
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header('Location: /teacher/login/');
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher data
$stmt = $conn->prepare("
    SELECT t.*, d.name as department_name 
    FROM teachers t 
    JOIN departments d ON t.department_id = d.department_id 
    WHERE t.teacher_id = :teacher_id
");
$stmt->execute(['teacher_id' => $teacher_id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    $_SESSION['error'] = "Teacher profile not found.";
    header('Location: /teacher/');
    exit;
}

// Fetch all departments for dropdown
$departments = $conn->query('SELECT department_id, name FROM departments ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

// Handle success/error messages
$success_message = $_SESSION['success'] ?? '';
$error_message = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md mt-8">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-6">My Profile</h1>
    
    <?php if ($success_message): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <p class="text-green-800"><?php echo htmlspecialchars($success_message); ?></p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <p class="text-red-800"><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Profile Information Section -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 mb-8">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Personal Information</h3>
        <form id="profile-form" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-1">Staff ID</label>
                <input type="text" id="staff_id" name="staff_id" value="<?php echo htmlspecialchars($teacher['staff_id']); ?>" disabled
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed shadow-sm sm:text-sm">
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($teacher['username']); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($teacher['phone_number'] ?? ''); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            <div class="md:col-span-2">
                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select id="department_id" name="department_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['department_id']; ?>" <?php if ($teacher['department_id'] == $dept['department_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($dept['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    <option value="active" <?php if ($teacher['status'] == 'active') echo 'selected'; ?>>Active</option>
                    <option value="inactive" <?php if ($teacher['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>
            <div class="md:col-span-2 flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors duration-200 shadow-md">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Section -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Change Password</h3>
        <form id="change-password-form" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
            </div>
            <div>
                <label for="confirm_new_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password" required minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            <div class="md:col-span-2 flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors duration-200 shadow-md">
                    <i class="fas fa-key mr-2"></i>Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<script src="profile.js"></script>
