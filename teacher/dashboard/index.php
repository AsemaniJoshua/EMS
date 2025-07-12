<?php include_once '../components/Sidebar.php'; ?>
<?php include_once '../components/Header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EMS Teacher</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-4 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Teacher Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Welcome back! Here's your teaching and exam overview.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="../exam/index.php"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-plus mr-2 -ml-1"></i>
                        New Exam
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Exams</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">12</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+2</span>
                                            <span class="ml-1 text-gray-500">this month</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                                <i class="fas fa-user-graduate text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Students Taught</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">320</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+10</span>
                                            <span class="ml-1 text-gray-500">this term</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg. Student Score</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">84%</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+4%</span>
                                            <span class="ml-1 text-gray-500">this term</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-50 rounded-lg p-3">
                                <i class="fas fa-clock text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Grading</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">3</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-orange-600 font-medium">Needs review</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Student Performance Trend</h3>
                    <canvas id="performanceChart" height="180"></canvas>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Exam Participation</h3>
                    <canvas id="participationChart" height="180"></canvas>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Recent Activity -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-900">Recent Activity</h2>
                            <a href="#" class="text-sm text-emerald-600 hover:text-emerald-700">View all</a>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            <li class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-plus text-emerald-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1">Created new exam "Algebra
                                            Basics"</p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i> 1 hour ago
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">New</span>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1">Graded 20 student submissions
                                        </p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i> 3 hours ago
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check text-purple-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1">Published results for
                                            "Geometry Quiz"</p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i> 1 day ago
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Upcoming Exams -->
                <div>
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-900">Upcoming Exams</h2>
                            <a href="#" class="text-sm text-emerald-600 hover:text-emerald-700">See all</a>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Mathematics Midterm</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i class="far fa-calendar-alt mr-1"></i> Apr 20, 2024 • 10:00 AM
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Ready</span>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Physics Quiz</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i class="far fa-calendar-alt mr-1"></i> Apr 25, 2024 • 1:00 PM
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Chemistry Final</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i class="far fa-calendar-alt mr-1"></i> May 2, 2024 • 9:00 AM
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Draft</span>
                                </div>
                            </li>
                        </ul>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <a href="../exam/index.php"
                                class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-emerald-700 bg-white border border-emerald-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                <i class="fas fa-plus mr-2"></i> Add Exam
                            </a>
                        </div>
                    </div>
                </div>
            </div>
<?php
// --- Secure session start and teacher authentication ---
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}
require_once __DIR__ . '/../../api/config/database.php';
// Redirect if accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    header("Location: ../index.php?page=dashboard");
    exit;
}
// Check teacher session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header('Location: /teacher/login/');
    exit;
}

// --- Database connection ---
$db = new Database();
$conn = $db->getConnection();
$teacher_id = $_SESSION['teacher_id'];

// --- Dashboard Stats ---
// Total Exams Created
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM exams WHERE teacher_id = :teacher_id");
$stmt->execute(['teacher_id' => $teacher_id]);
$totalExams = $stmt->fetchColumn();

// Pending Approvals
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM exams WHERE teacher_id = :teacher_id AND status = 'Pending'");
$stmt->execute(['teacher_id' => $teacher_id]);
$pendingApprovals = $stmt->fetchColumn();

// Students Enrolled (unique students in teacher's exams)
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT er.student_id) as count
    FROM exam_registrations er
    JOIN exams e ON er.exam_id = e.exam_id
    WHERE e.teacher_id = :teacher_id
");
$stmt->execute(['teacher_id' => $teacher_id]);
$totalStudents = $stmt->fetchColumn();

// Courses Taught (distinct courses from teacher's exams)
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT course_id) as count
    FROM exams
    WHERE teacher_id = :teacher_id
");
$stmt->execute(['teacher_id' => $teacher_id]);
$coursesTaught = $stmt->fetchColumn();

// --- Recent Exams (5 most recent) ---
$stmt = $conn->prepare("
    SELECT e.exam_id, e.title, e.exam_code, e.status, e.start_datetime, e.duration_minutes, c.code as course_code, c.title as course_title
    FROM exams e
    JOIN courses c ON e.course_id = c.course_id
    WHERE e.teacher_id = :teacher_id
    ORDER BY e.created_at DESC
    LIMIT 5
");
$stmt->execute(['teacher_id' => $teacher_id]);
$recentExams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Teacher Name ---
$teacherName = $_SESSION['teacher_name'] ?? 'Teacher';

?>
<!-- Teacher Dashboard Main Content -->
<div class="mb-4 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">Welcome back, <?php echo htmlspecialchars($teacherName); ?>! Here's your teaching overview.</p>
    </div>
    <div class="mt-4 md:mt-0">
        <a href="index.php?page=exams&action=create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            <i class="fas fa-plus mr-2 -ml-1"></i>
            New Exam
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
        <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Exams Created</dt>
                    <dd><div class="text-xl font-semibold text-gray-900"><?php echo number_format($totalExams); ?></div></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
        <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Students Enrolled</dt>
                    <dd><div class="text-xl font-semibold text-gray-900"><?php echo number_format($totalStudents); ?></div></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
        <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-orange-50 rounded-lg p-3">
                <i class="fas fa-clock text-orange-600 text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Approvals</dt>
                    <dd><div class="text-xl font-semibold text-gray-900"><?php echo number_format($pendingApprovals); ?></div></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
        <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                <i class="fas fa-book text-emerald-600 text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Courses Taught</dt>
                    <dd><div class="text-xl font-semibold text-gray-900"><?php echo number_format($coursesTaught); ?></div></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Recent Exams & Status Table -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-900">Recent Exams & Status</h2>
                <a href="index.php?page=exams" class="text-sm text-emerald-600 hover:text-emerald-700">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">Exam Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($recentExams)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-info-circle mr-2"></i>
                                No exams created yet. <a href="index.php?page=exams&action=create" class="text-emerald-600 hover:text-emerald-700">Create your first exam</a>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recentExams as $exam): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($exam['title']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($exam['course_code']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $exam['duration_minutes'] ? $exam['duration_minutes'] . ' mins' : 'Not set'; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($exam['status']) {
                                        case 'Approved': $statusClass = 'bg-green-100 text-green-800'; $statusText = 'Approved'; break;
                                        case 'Pending': $statusClass = 'bg-orange-100 text-orange-800'; $statusText = 'Pending'; break;
                                        case 'Rejected': $statusClass = 'bg-red-100 text-red-800'; $statusText = 'Rejected'; break;
                                        case 'Draft': $statusClass = 'bg-gray-100 text-gray-800'; $statusText = 'Draft'; break;
                                        case 'Completed': $statusClass = 'bg-blue-100 text-blue-800'; $statusText = 'Completed'; break;
                                        default: $statusClass = 'bg-gray-100 text-gray-800'; $statusText = $exam['status'];
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                                    <a href="index.php?page=exams&action=edit&id=<?php echo $exam['exam_id']; ?>" class="text-blue-600 hover:text-blue-900" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="#" onclick="deleteExam(<?php echo $exam['exam_id']; ?>)" class="text-red-600 hover:text-red-900" title="Delete"><i class="fas fa-trash"></i></a>
                                    <a href="index.php?page=exams&action=questions&id=<?php echo $exam['exam_id']; ?>" class="text-emerald-600 hover:text-emerald-900" title="View Questions"><i class="fas fa-question-circle"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Quick Actions -->
    <div>
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-6 grid grid-cols-1 gap-4">
                <a href="index.php?page=exams&action=create" class="flex items-center justify-center py-3 px-4 rounded-lg bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition-colors duration-200 shadow-md">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Exam
                </a>
                <a href="index.php?page=results" class="flex items-center justify-center py-3 px-4 rounded-lg bg-blue-500 text-white font-semibold hover:bg-blue-600 transition-colors duration-200 shadow-md">
                    <i class="fas fa-list mr-2"></i>
                    View All Results
                </a>
                <a href="index.php?page=profile" class="flex items-center justify-center py-3 px-4 rounded-lg bg-purple-500 text-white font-semibold hover:bg-purple-600 transition-colors duration-200 shadow-md">
                    <i class="fas fa-user mr-2"></i>
                    Manage Profile
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Delete Exam via API
function deleteExam(examId) {
    if (!confirm('Are you sure you want to delete this exam?')) return;
    fetch('/api/exam/deleteExam.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ exam_id: examId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Exam deleted successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'Failed to delete exam.');
        }
    })
    .catch(() => {
        alert('Network error. Please try again.');
    });
}
</script>
