<?php
require_once __DIR__ . '/../../api/login/teacher/teacherSessionCheck.php';
require_once __DIR__ . '/../../api/config/database.php';
require_once __DIR__ . '/../components/teacherSidebar.php';
require_once __DIR__ . '/../components/teacherHeader.php';

$currentPage = 'profile';
$pageTitle = "Teacher Profile";

// Check teacher session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
  header('Location: /teacher/login/');
  exit;
}

// Database connection
$db = new Database();
$conn = $db->getConnection();
$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher information with department details
$stmt = $conn->prepare("
    SELECT t.*, d.name as department_name
    FROM teachers t
    LEFT JOIN departments d ON t.department_id = d.department_id
    WHERE t.teacher_id = :teacher_id
");
$stmt->execute(['teacher_id' => $teacher_id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
  $error = "Teacher profile not found.";
}

// Get teacher's exam statistics
$examStats = [];
if ($teacher) {
  // Total exams created
  $stmt = $conn->prepare("SELECT COUNT(*) as total_exams FROM exams WHERE teacher_id = :teacher_id");
  $stmt->execute(['teacher_id' => $teacher_id]);
  $examStats['total_exams'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_exams'];

  // Active exams
  $stmt = $conn->prepare("SELECT COUNT(*) as active_exams FROM exams WHERE teacher_id = :teacher_id AND status IN ('Approved', 'Completed')");
  $stmt->execute(['teacher_id' => $teacher_id]);
  $examStats['active_exams'] = $stmt->fetch(PDO::FETCH_ASSOC)['active_exams'];

  // Total students taught
  $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT er.student_id) as total_students 
        FROM exam_registrations er 
        JOIN exams e ON er.exam_id = e.exam_id 
        WHERE e.teacher_id = :teacher_id
    ");
  $stmt->execute(['teacher_id' => $teacher_id]);
  $examStats['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_students'];

  // Recent activity - last exam created
  $stmt = $conn->prepare("
        SELECT title, created_at 
        FROM exams 
        WHERE teacher_id = :teacher_id 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
  $stmt->execute(['teacher_id' => $teacher_id]);
  $examStats['last_exam'] = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-xl mx-auto">

      <!-- Page Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl flex items-center">
            <i class="fas fa-user-circle mr-3 text-emerald-600"></i>
            <?php echo $pageTitle; ?>
          </h1>
          <div class="flex space-x-3">
            <a href="edit.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
              <i class="fas fa-edit mr-2"></i>
              Edit Profile
            </a>
            <a href="../settings/" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
              <i class="fas fa-cog mr-2"></i>
              Settings
            </a>
          </div>
        </div>
        <p class="mt-2 text-sm text-gray-600">
          View and manage your teacher profile information, and track your teaching activities.
        </p>
      </div>

      <?php if (isset($error)): ?>
        <!-- Error Message -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-800"><?php echo htmlspecialchars($error); ?></p>
            </div>
          </div>
        </div>
      <?php elseif ($teacher): ?>
        <!-- Profile Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

          <!-- Left Column - Profile Information -->
          <div class="lg:col-span-2 space-y-8">

            <!-- Personal Information Card -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-user mr-2 text-blue-600"></i>
                  Personal Information
                </h3>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Full Name</label>
                    <p class="text-lg font-semibold text-gray-900">
                      <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Staff ID</label>
                    <p class="text-lg font-semibold text-gray-900">
                      <?php echo htmlspecialchars($teacher['staff_id']); ?>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Username</label>
                    <p class="text-lg font-medium text-gray-900">
                      <?php echo htmlspecialchars($teacher['username']); ?>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Email Address</label>
                    <p class="text-lg font-medium text-gray-900 break-all">
                      <?php echo htmlspecialchars($teacher['email']); ?>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Phone Number</label>
                    <p class="text-lg font-medium text-gray-900">
                      <?php echo htmlspecialchars($teacher['phone_number'] ?: 'Not provided'); ?>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Department</label>
                    <p class="text-lg font-medium text-gray-900">
                      <?php echo htmlspecialchars($teacher['department_name'] ?: 'Not assigned'); ?>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Account Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $teacher['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'; ?>">
                      <i class="fas fa-circle mr-1 text-xs"></i>
                      <?php echo ucfirst($teacher['status']); ?>
                    </span>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Password Reset Required</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $teacher['resetOnLogin'] ? 'bg-yellow-100 text-yellow-800' : 'bg-emerald-100 text-emerald-800'; ?>">
                      <i class="fas <?php echo $teacher['resetOnLogin'] ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?> mr-1 text-xs"></i>
                      <?php echo $teacher['resetOnLogin'] ? 'Required' : 'Not Required'; ?>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Account Information Card -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                  Account Information
                </h3>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Account Created</label>
                    <p class="text-lg font-medium text-gray-900">
                      <?php echo date('F j, Y \a\t g:i A', strtotime($teacher['created_at'])); ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                      <?php
                      $createdDate = new DateTime($teacher['created_at']);
                      $now = new DateTime();
                      $interval = $now->diff($createdDate);
                      echo $interval->format('%a days ago');
                      ?>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Last Updated</label>
                    <p class="text-lg font-medium text-gray-900">
                      <?php echo date('F j, Y \a\t g:i A', strtotime($teacher['updated_at'])); ?>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                      <?php
                      $updatedDate = new DateTime($teacher['updated_at']);
                      $interval = $now->diff($updatedDate);
                      echo $interval->format('%a days ago');
                      ?>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Statistics and Quick Actions -->
          <div class="space-y-8">

            <!-- Teaching Statistics Card -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-chart-bar mr-2 text-emerald-600"></i>
                  Teaching Statistics
                </h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 bg-emerald-100 rounded-lg p-2">
                        <i class="fas fa-clipboard-list text-emerald-600"></i>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-emerald-900">Total Exams</p>
                      </div>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600">
                      <?php echo $examStats['total_exams']; ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 bg-blue-100 rounded-lg p-2">
                        <i class="fas fa-check-circle text-blue-600"></i>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-blue-900">Active Exams</p>
                      </div>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">
                      <?php echo $examStats['active_exams']; ?>
                    </div>
                  </div>

                  <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 bg-purple-100 rounded-lg p-2">
                        <i class="fas fa-users text-purple-600"></i>
                      </div>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-purple-900">Students Taught</p>
                      </div>
                    </div>
                    <div class="text-2xl font-bold text-purple-600">
                      <?php echo $examStats['total_students']; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                  <i class="fas fa-bolt mr-2 text-yellow-600"></i>
                  Quick Actions
                </h3>
              </div>
              <div class="p-6">
                <div class="space-y-3">
                  <a href="../exam/createExam.php" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Exam
                  </a>
                  <a href="../exam/" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-eye mr-2"></i>
                    View My Exams
                  </a>
                  <a href="../results/" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-chart-line mr-2"></i>
                    View Results
                  </a>
                </div>
              </div>
            </div>

            <!-- Recent Activity Card -->
            <?php if (!empty($examStats['last_exam'])): ?>
              <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                  <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-clock mr-2 text-gray-600"></i>
                    Recent Activity
                  </h3>
                </div>
                <div class="p-6">
                  <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 bg-emerald-100 rounded-full p-2">
                      <i class="fas fa-file-alt text-emerald-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900">
                        Last Exam Created
                      </p>
                      <p class="text-sm text-gray-600 truncate">
                        <?php echo htmlspecialchars($examStats['last_exam']['title']); ?>
                      </p>
                      <p class="text-xs text-gray-500 mt-1">
                        <?php
                        $examDate = new DateTime($examStats['last_exam']['created_at']);
                        $interval = $now->diff($examDate);
                        echo $interval->format('%a days ago');
                        ?>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script>
    // Add any interactive functionality here if needed
    document.addEventListener('DOMContentLoaded', function() {
      // Success message if redirected from edit page
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('updated') === '1') {
        Swal.fire({
          title: 'Success!',
          text: 'Your profile has been updated successfully.',
          icon: 'success',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      }
    });
  </script>
</body>

</html>