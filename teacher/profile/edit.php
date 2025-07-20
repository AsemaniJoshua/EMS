<?php
require_once __DIR__ . '/../../api/login/teacher/teacherSessionCheck.php';
require_once __DIR__ . '/../../api/config/database.php';
require_once __DIR__ . '/../components/teacherSidebar.php';
require_once __DIR__ . '/../components/teacherHeader.php';

$currentPage = 'profile';
$pageTitle = "Edit Profile";

// Check teacher session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
  header('Location: /teacher/login/');
  exit;
}

// Database connection
$db = new Database();
$conn = $db->getConnection();
$teacher_id = $_SESSION['teacher_id'];

// Initialize variables
$success = false;
$password_success = false;
$password_error = '';
$error = '';

// Fetch departments for dropdown
$departments = [];
try {
  $stmt = $conn->query('SELECT department_id, name FROM departments ORDER BY name');
  $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Check if departments exist
  if (empty($departments)) {
    $error = 'No departments found. Please contact the administrator.';
  }
} catch (PDOException $e) {
  $error = 'Database error: Unable to load departments. Please try again later.';
  error_log("Department fetch error: " . $e->getMessage());
}

// Fetch teacher data
$teacher = null;
if (!$error) {
  try {
    $stmt = $conn->prepare('SELECT * FROM teachers WHERE teacher_id = :teacher_id');
    $stmt->execute(['teacher_id' => $teacher_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$teacher) {
      $error = "Teacher profile not found. Please contact the administrator.";
    }
  } catch (PDOException $e) {
    $error = 'Database error: Unable to load profile. Please try again later.';
    error_log("Teacher fetch error for ID $teacher_id: " . $e->getMessage());
  }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $teacher && !$error) {
  // CSRF Protection (basic implementation)
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $error = 'Invalid request. Please try again.';
  } elseif (empty($departments)) {
    $error = 'Cannot process request: Departments not available. Please refresh the page.';
  } else {
    // Profile update
    if (isset($_POST['update_profile'])) {
      $first_name = trim($_POST['first_name'] ?? '');
      $last_name = trim($_POST['last_name'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $phone_number = trim($_POST['phone_number'] ?? '');
      $department_id = $_POST['department_id'] ?? '';

      // Validation
      $validation_errors = [];
      if (empty($first_name)) $validation_errors[] = "First name is required.";
      if (empty($last_name)) $validation_errors[] = "Last name is required.";
      if (empty($email)) $validation_errors[] = "Email is required.";
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $validation_errors[] = "Valid email is required.";
      if (empty($department_id)) $validation_errors[] = "Department is required.";

      // Check if email is already taken by another teacher
      if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailCheckStmt = $conn->prepare('SELECT teacher_id FROM teachers WHERE email = :email AND teacher_id != :teacher_id');
        $emailCheckStmt->execute(['email' => $email, 'teacher_id' => $teacher_id]);
        if ($emailCheckStmt->fetch()) {
          $validation_errors[] = "This email is already registered to another teacher.";
        }
      }

      // Check if department exists
      if (!empty($department_id)) {
        $deptCheckStmt = $conn->prepare('SELECT department_id FROM departments WHERE department_id = :department_id');
        $deptCheckStmt->execute(['department_id' => $department_id]);
        if (!$deptCheckStmt->fetch()) {
          $validation_errors[] = "Selected department does not exist.";
        }
      }

      // Validate phone number format if provided
      if (!empty($phone_number) && !preg_match('/^[\d\s\-\+\(\)]{10,20}$/', $phone_number)) {
        $validation_errors[] = "Please enter a valid phone number.";
      }

      if (empty($validation_errors)) {
        try {
          $updateStmt = $conn->prepare('
            UPDATE teachers 
            SET first_name = :first_name, 
                last_name = :last_name, 
                email = :email, 
                phone_number = :phone_number, 
                department_id = :department_id, 
                updated_at = NOW() 
            WHERE teacher_id = :teacher_id
          ');
          $updateStmt->execute([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone_number' => !empty($phone_number) ? $phone_number : null,
            'department_id' => $department_id,
            'teacher_id' => $teacher_id
          ]);

          // Update session data if relevant fields changed
          $_SESSION['teacher_name'] = $first_name . ' ' . $last_name;
          $_SESSION['teacher_email'] = $email;

          // Regenerate CSRF token after successful profile update
          $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

          // Redirect with success message
          header('Location: index.php?updated=1');
          exit;
        } catch (PDOException $e) {
          $error = 'Database error: Unable to update profile. Please try again.';
          error_log("Profile update error for teacher $teacher_id: " . $e->getMessage());
        }
      } else {
        $error = implode('<br>', $validation_errors);
      }
    }

    // Password change
    if (isset($_POST['change_password'])) {
      $current_password = $_POST['current_password'] ?? '';
      $new_password = $_POST['new_password'] ?? '';
      $confirm_password = $_POST['confirm_password'] ?? '';

      if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $password_error = 'All password fields are required.';
      } elseif ($new_password !== $confirm_password) {
        $password_error = 'New passwords do not match.';
      } elseif (strlen($new_password) < 8) {
        $password_error = 'New password must be at least 8 characters long.';
      } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $new_password)) {
        $password_error = 'Password must contain at least one uppercase letter, one lowercase letter, and one number.';
      } else {
        try {
          // Fetch current hash
          $stmt = $conn->prepare('SELECT password_hash FROM teachers WHERE teacher_id = :teacher_id');
          $stmt->execute(['teacher_id' => $teacher_id]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          if ($row && password_verify($current_password, $row['password_hash'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $updatePassStmt = $conn->prepare('
              UPDATE teachers 
              SET password_hash = :password_hash, 
                  updated_at = NOW() 
              WHERE teacher_id = :teacher_id
            ');
            $updatePassStmt->execute([
              'password_hash' => $new_hash,
              'teacher_id' => $teacher_id
            ]);
            $password_success = true;

            // Clear password fields after successful change
            $_POST['current_password'] = '';
            $_POST['new_password'] = '';
            $_POST['confirm_password'] = '';

            // Regenerate CSRF token after successful password change
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
          } else {
            $password_error = 'Current password is incorrect.';
          }
        } catch (PDOException $e) {
          $password_error = 'Database error: Unable to change password. Please try again.';
          error_log("Password change error for teacher $teacher_id: " . $e->getMessage());
        }
      }
    }
  }
}

// Generate CSRF token for forms
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
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
    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-4xl mx-auto">

      <!-- Page Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-4">
            <a href="index.php" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
              <i class="fas fa-arrow-left mr-2"></i>
              Back to Profile
            </a>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl flex items-center">
              <i class="fas fa-user-edit mr-3 text-emerald-600"></i>
              <?php echo $pageTitle; ?>
            </h1>
          </div>
        </div>
        <p class="mt-2 text-sm text-gray-600">
          Update your personal information and change your password.
        </p>
      </div>

      <?php if ($error): ?>
        <!-- Error Message -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-800"><?php echo nl2br(htmlspecialchars($error)); ?></p>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <!-- Success Message -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-check-circle text-emerald-400"></i>
            </div>
            <div class="ml-3">
              <p class="text-sm text-emerald-800">Profile updated successfully!</p>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($teacher): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

          <!-- Left Column - Profile Form -->
          <div class="lg:col-span-2">

            <!-- Personal Information Form -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-user mr-2 text-blue-600"></i>
                  Personal Information
                </h3>
                <p class="text-sm text-gray-600 mt-1">Update your basic profile information</p>
              </div>
              <form method="POST" class="p-6">
                <input type="hidden" name="update_profile" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                      First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                      id="first_name"
                      name="first_name"
                      value="<?php echo htmlspecialchars(isset($_POST['first_name']) ? $_POST['first_name'] : $teacher['first_name']); ?>"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                      required>
                  </div>
                  <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                      Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                      id="last_name"
                      name="last_name"
                      value="<?php echo htmlspecialchars(isset($_POST['last_name']) ? $_POST['last_name'] : $teacher['last_name']); ?>"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                      required>
                  </div>
                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                      Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                      id="email"
                      name="email"
                      value="<?php echo htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : $teacher['email']); ?>"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                      required>
                  </div>
                  <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                      Phone Number
                    </label>
                    <input type="tel"
                      id="phone_number"
                      name="phone_number"
                      value="<?php echo htmlspecialchars(isset($_POST['phone_number']) ? $_POST['phone_number'] : $teacher['phone_number']); ?>"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                      placeholder="Enter phone number">
                  </div>
                  <div class="md:col-span-2">
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                      Department <span class="text-red-500">*</span>
                    </label>
                    <select name="department_id"
                      id="department_id"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                      required>
                      <option value="">Select Department</option>
                      <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['department_id']; ?>"
                          <?php
                          // Preserve POST data if form was submitted, otherwise use teacher's current department
                          $selected_dept = isset($_POST['department_id']) ? $_POST['department_id'] : $teacher['department_id'];
                          if ($selected_dept == $dept['department_id']) echo 'selected';
                          ?>>
                          <?php echo htmlspecialchars($dept['name']); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="flex justify-end pt-6">
                  <button type="submit" onclick="return confirm('Are you sure you want to update your profile information?')" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Save Changes
                  </button>
                </div>
              </form>
            </div>

            <!-- Password Change Form -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mt-8">
              <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-key mr-2 text-yellow-700"></i>
                  Change Password
                </h3>
                <p class="text-sm text-gray-600 mt-1">Update your account password for security</p>
              </div>

              <?php if ($password_success): ?>
                <div class="bg-emerald-50 border-b border-emerald-200 p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <i class="fas fa-check-circle text-emerald-400"></i>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm text-emerald-800">Password updated successfully!</p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ($password_error): ?>
                <div class="bg-red-50 border-b border-red-200 p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm text-red-800"><?php echo $password_error; ?></p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <form method="POST" class="p-6">
                <input type="hidden" name="change_password" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="space-y-6">
                  <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                      Current Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                      id="current_password"
                      name="current_password"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                      required>
                  </div>
                  <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                      New Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                      id="new_password"
                      name="new_password"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                      required>
                    <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters with uppercase, lowercase, and number</p>
                  </div>
                  <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                      Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                      id="confirm_password"
                      name="confirm_password"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                      required>
                  </div>
                </div>
                <div class="flex justify-end pt-6">
                  <button type="submit" onclick="return confirm('Are you sure you want to change your password? You will need to use the new password for future logins.')" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-white bg-emerald-500 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200" onclick="return confirm('Are you sure you want to change your password? You will need to use the new password for future logins.')" >
                    <i class="fas fa-key mr-2"></i>
                    Change Password
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Right Column - Account Info & Guidelines -->
          <div class="space-y-8">

            <!-- Account Information -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-info-circle mr-2 text-emerald-600"></i>
                  Account Information
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-500 mb-1">Staff ID</label>
                  <p class="text-sm font-semibold text-gray-900">
                    <?php echo htmlspecialchars($teacher['staff_id']); ?>
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500 mb-1">Username</label>
                  <p class="text-sm font-semibold text-gray-900">
                    <?php echo htmlspecialchars($teacher['username']); ?>
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500 mb-1">Account Status</label>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $teacher['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'; ?>">
                    <i class="fas fa-circle mr-1 text-xs"></i>
                    <?php echo ucfirst($teacher['status']); ?>
                  </span>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                  <p class="text-sm text-gray-900">
                    <?php echo date('M j, Y', strtotime($teacher['updated_at'])); ?>
                  </p>
                </div>
              </div>
            </div>

            <!-- Security Guidelines -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
              <h4 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-shield-alt mr-2"></i>
                Security Guidelines
              </h4>
              <ul class="text-xs text-blue-800 space-y-2">
                <li class="flex items-start">
                  <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-600"></i>
                  Use a strong password with at least 8 characters
                </li>
                <li class="flex items-start">
                  <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-600"></i>
                  Include uppercase, lowercase, numbers, and symbols
                </li>
                <li class="flex items-start">
                  <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-600"></i>
                  Don't share your password with anyone
                </li>
                <li class="flex items-start">
                  <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-600"></i>
                  Change your password regularly
                </li>
                <li class="flex items-start">
                  <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-600"></i>
                  Keep your contact information up to date
                </li>
              </ul>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-bolt mr-2 text-yellow-600"></i>
                  Quick Actions
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <a href="index.php" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                  <i class="fas fa-user mr-2"></i>
                  View Profile
                </a>
                <a href="../dashboard/" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                  <i class="fas fa-tachometer-alt mr-2"></i>
                  Dashboard
                </a>
                <a href="../exam/" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                  <i class="fas fa-clipboard-list mr-2"></i>
                  My Exams
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <!-- Teacher Not Found -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-8 text-center">
          <i class="fas fa-user-times text-4xl text-gray-400 mb-4"></i>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Teacher Profile Not Found</h3>
          <p class="text-gray-600 mb-4">We couldn't find your teacher profile. Please contact the administrator.</p>
          <a href="../dashboard/" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition-colors duration-200">
            <i class="fas fa-home mr-2"></i>
            Return to Dashboard
          </a>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Password confirmation validation
      const newPassword = document.getElementById('new_password');
      const confirmPassword = document.getElementById('confirm_password');

      function validatePasswords() {
        if (newPassword.value && confirmPassword.value) {
          if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
          } else {
            confirmPassword.setCustomValidity('');
          }
        }
      }

      function validatePasswordStrength() {
        const password = newPassword.value;
        const strengthRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

        if (password && !strengthRegex.test(password)) {
          newPassword.setCustomValidity('Password must contain at least one uppercase letter, one lowercase letter, and one number');
        } else {
          newPassword.setCustomValidity('');
        }
        validatePasswords(); // Also check confirmation match
      }

      newPassword?.addEventListener('input', validatePasswordStrength);
      confirmPassword?.addEventListener('input', validatePasswords);

      // Email validation
      const emailField = document.getElementById('email');
      emailField?.addEventListener('blur', function() {
        const email = this.value;
        if (email && !isValidEmail(email)) {
          this.setCustomValidity('Please enter a valid email address');
        } else {
          this.setCustomValidity('');
        }
      });

      function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      }

      // Phone number validation
      const phoneField = document.getElementById('phone_number');
      phoneField?.addEventListener('input', function() {
        const phone = this.value;
        if (phone && !/^[\d\s\-\+\(\)]{10,20}$/.test(phone)) {
          this.setCustomValidity('Please enter a valid phone number');
        } else {
          this.setCustomValidity('');
        }
      });

      // Form submission with loading state
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        form.addEventListener('submit', function(e) {
          const submitButton = form.querySelector('button[type="submit"]');
          if (submitButton) {
            // Validate form before submission
            if (!form.checkValidity()) {
              e.preventDefault();
              return;
            }

            submitButton.disabled = true;
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

            // Re-enable button after 5 seconds as fallback
            setTimeout(() => {
              submitButton.disabled = false;
              submitButton.innerHTML = originalText;
            }, 5000);
          }
        });
      });

      // Re-enable forms if there are PHP errors on page load
      <?php if ($error || $password_error): ?>
        setTimeout(() => {
          const buttons = document.querySelectorAll('button[type="submit"]');
          buttons.forEach(button => {
            button.disabled = false;
            const icon = button.querySelector('i');
            if (icon && icon.classList.contains('fa-spinner')) {
              if (button.innerHTML.includes('Save Changes')) {
                button.innerHTML = '<i class="fas fa-save mr-2"></i>Save Changes';
              } else if (button.innerHTML.includes('Change Password')) {
                button.innerHTML = '<i class="fas fa-key mr-2"></i>Change Password';
              }
            }
          });
        }, 100);
      <?php endif; ?>

      // Show password strength indicator
      if (newPassword) {
        const strengthIndicator = document.createElement('div');
        strengthIndicator.id = 'password-strength';
        strengthIndicator.className = 'text-xs mt-1';
        newPassword.parentNode.appendChild(strengthIndicator);

        newPassword.addEventListener('input', function() {
          const password = this.value;
          let strength = 0;
          let feedback = '';

          if (password.length >= 8) strength++;
          if (/[a-z]/.test(password)) strength++;
          if (/[A-Z]/.test(password)) strength++;
          if (/\d/.test(password)) strength++;
          if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

          switch (strength) {
            case 0:
            case 1:
              feedback = '<span class="text-red-500">Weak password</span>';
              break;
            case 2:
            case 3:
              feedback = '<span class="text-yellow-500">Medium password</span>';
              break;
            case 4:
            case 5:
              feedback = '<span class="text-green-500">Strong password</span>';
              break;
          }

          strengthIndicator.innerHTML = password ? feedback : '';
        });
      }
    });
  </script>
</body>

</html>