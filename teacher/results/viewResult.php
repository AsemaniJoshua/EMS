<?php
require_once '../components/teacherSidebar.php';
require_once '../components/teacherHeader.php';
require_once '../../api/login/teacher/teacherSessionCheck.php';
require_once '../../api/config/database.php';

// Check if result_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
  header('Location: index.php');
  exit;
}

$result_id = intval($_GET['id']);
$teacher_id = $_SESSION['teacher_id'];

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Get result details and verify teacher has access
$stmt = $conn->prepare("
    SELECT 
        r.*,
        s.student_number,
        s.first_name,
        s.last_name,
        s.email,
        e.title as exam_title,
        e.exam_code,
        e.duration,
        c.code as course_code,
        c.title as course_title
    FROM results r
    JOIN students s ON r.student_id = s.student_id
    JOIN exams e ON r.exam_id = e.exam_id
    JOIN courses c ON e.course_id = c.course_id
    WHERE r.result_id = :result_id AND e.teacher_id = :teacher_id
");

$stmt->execute(['result_id' => $result_id, 'teacher_id' => $teacher_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
  header('Location: index.php?error=result_not_found');
  exit;
}

$currentPage = 'results';
$pageTitle = "Result Details - " . $result['first_name'] . ' ' . $result['last_name'];
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
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-4">
            <a href="examResults.php?exam_id=<?php echo $result['exam_id']; ?>" class="text-emerald-600 hover:text-emerald-700">
              <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Result Details</h1>
          </div>
          <div class="flex space-x-3">
            <button onclick="printResult()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
              <i class="fas fa-print mr-2"></i>
              Print
            </button>
          </div>
        </div>
      </div>

      <!-- Student & Exam Information -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Student Information -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
              <i class="fas fa-user mr-2 text-blue-600"></i>
              Student Information
            </h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-500">Full Name</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Student Number</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($result['student_number']); ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Email</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($result['email']); ?></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Exam Information -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
              <i class="fas fa-clipboard-list mr-2 text-emerald-600"></i>
              Exam Information
            </h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-500">Exam Title</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($result['exam_title']); ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Exam Code</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($result['exam_code']); ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Course</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($result['course_code'] . ' - ' . $result['course_title']); ?></p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Duration</label>
                <p class="mt-1 text-sm text-gray-900"><?php echo $result['duration']; ?> minutes</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Result Details -->
      <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-chart-bar mr-2 text-purple-600"></i>
            Performance Results
          </h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Score -->
            <div class="text-center">
              <div class="bg-blue-50 rounded-lg p-4">
                <div class="text-3xl font-bold text-blue-600"><?php echo $result['score_obtained']; ?>/<?php echo $result['total_score']; ?></div>
                <div class="text-sm text-gray-600 mt-1">Score Obtained</div>
              </div>
            </div>

            <!-- Percentage -->
            <div class="text-center">
              <div class="bg-<?php echo $result['score_percentage'] >= 50 ? 'emerald' : 'red'; ?>-50 rounded-lg p-4">
                <div class="text-3xl font-bold text-<?php echo $result['score_percentage'] >= 50 ? 'emerald' : 'red'; ?>-600"><?php echo $result['score_percentage']; ?>%</div>
                <div class="text-sm text-gray-600 mt-1">Percentage</div>
              </div>
            </div>

            <!-- Result Status -->
            <div class="text-center">
              <div class="bg-<?php echo $result['score_percentage'] >= 50 ? 'emerald' : 'red'; ?>-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-<?php echo $result['score_percentage'] >= 50 ? 'emerald' : 'red'; ?>-600">
                  <?php echo $result['score_percentage'] >= 50 ? 'PASS' : 'FAIL'; ?>
                </div>
                <div class="text-sm text-gray-600 mt-1">Result</div>
              </div>
            </div>

            <!-- Time Taken -->
            <div class="text-center">
              <div class="bg-yellow-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-yellow-600">
                  <?php echo $result['time_taken'] ? round($result['time_taken'] / 60, 1) : 'N/A'; ?>
                </div>
                <div class="text-sm text-gray-600 mt-1">Minutes Taken</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Submission Details -->
      <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-clock mr-2 text-gray-600"></i>
            Submission Details
          </h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-500">Started At</label>
              <p class="mt-1 text-sm text-gray-900">
                <?php echo $result['created_at'] ? date('F j, Y g:i A', strtotime($result['created_at'])) : 'N/A'; ?>
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500">Completed At</label>
              <p class="mt-1 text-sm text-gray-900">
                <?php echo $result['completed_at'] ? date('F j, Y g:i A', strtotime($result['completed_at'])) : 'Not completed'; ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    function printResult() {
      window.print();
    }
  </script>

  <!-- Print Styles -->
  <style>
    @media print {

      .sidebar,
      .header,
      .print-hide {
        display: none !important;
      }

      main {
        margin-left: 0 !important;
        padding-top: 0 !important;
      }

      .bg-gray-50 {
        background-color: white !important;
      }

      .shadow-sm,
      .border {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
      }
    }
  </style>
</body>

</html>