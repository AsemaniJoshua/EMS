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
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header('Location: /teacher/login/');
    exit;
}

$currentPage = 'dashboard';

// Include components and database connection
require_once __DIR__ . '/../../api/config/database.php';
require_once __DIR__ . '/../components/teacherSidebar.php';
require_once __DIR__ . '/../components/teacherHeader.php';
require_once __DIR__ . '/../components/teacherFooter.php';

// Create database connection
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
$stmt = $conn->prepare("SELECT first_name, last_name FROM teachers WHERE teacher_id = :teacher_id");
$stmt->execute(['teacher_id' => $teacher_id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);
$teacherName = ($teacher['first_name'] ?? '') . ' ' . ($teacher['last_name'] ?? '');
$_SESSION['teacher_name'] = $teacherName;

// --- Exam Status Data for Chart ---
$stmt = $conn->prepare("
    SELECT status, COUNT(*) as count 
    FROM exams 
    WHERE teacher_id = :teacher_id 
    GROUP BY status
");
$stmt->execute(['teacher_id' => $teacher_id]);
$examStatusData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$examStatusLabels = [];
$examStatusCounts = [];

foreach ($examStatusData as $item) {
    $examStatusLabels[] = $item['status'];
    $examStatusCounts[] = $item['count'];
}

$examStatusChart = [
    'labels' => $examStatusLabels,
    'data' => $examStatusCounts
];

// --- Recent Results Data for Chart ---
$stmt = $conn->prepare("
    SELECT e.title, AVG(r.score_percentage) as avg_score
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    WHERE e.teacher_id = :teacher_id
    GROUP BY e.exam_id
    ORDER BY e.created_at DESC
    LIMIT 5
");
$stmt->execute(['teacher_id' => $teacher_id]);
$resultsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$resultsLabels = [];
$resultsScores = [];

foreach ($resultsData as $item) {
    $resultsLabels[] = $item['title'];
    $resultsScores[] = round($item['avg_score'], 2);
}

$resultsChart = [
    'labels' => $resultsLabels,
    'data' => $resultsScores
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - EMS</title>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="/src/output.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Dashboard JS -->
    <script src="dashboard.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-20 w-64 bg-emerald-800 shadow-lg transform transition-transform lg:translate-x-0 -translate-x-full" id="sidebar">
            <div class="flex items-center justify-center h-16 bg-emerald-900">
                <span class="text-white font-bold text-xl">EMS Teacher</span>
            </div>
            <nav class="mt-5">
                <div class="px-2 space-y-1">
                    <a href="/teacher/dashboard/" class="group flex items-center px-2 py-3 text-base font-medium rounded-md bg-emerald-900 text-white">
                        <i class="fas fa-tachometer-alt mr-3 text-emerald-300"></i>
                        Dashboard
                    </a>

                    <a href="/teacher/exam/" class="group flex items-center px-2 py-3 text-base font-medium rounded-md text-emerald-100 hover:bg-emerald-700 hover:text-white">
                        <i class="fas fa-file-alt mr-3 text-emerald-300"></i>
                        Exams
                    </a>

                    <a href="/teacher/results/" class="group flex items-center px-2 py-3 text-base font-medium rounded-md text-emerald-100 hover:bg-emerald-700 hover:text-white">
                        <i class="fas fa-chart-bar mr-3 text-emerald-300"></i>
                        Results
                    </a>

                    <a href="/teacher/profile/" class="group flex items-center px-2 py-3 text-base font-medium rounded-md text-emerald-100 hover:bg-emerald-700 hover:text-white">
                        <i class="fas fa-user mr-3 text-emerald-300"></i>
                        Profile
                    </a>

                    <div class="pt-4 mt-4 border-t border-emerald-700">
                        <a href="/api/login/logout.php" class="group flex items-center px-2 py-3 text-base font-medium rounded-md text-emerald-100 hover:bg-emerald-700 hover:text-white">
                            <i class="fas fa-sign-out-alt mr-3 text-emerald-300"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Mobile sidebar overlay -->
        <div class="fixed inset-0 z-10 bg-gray-600 opacity-0 pointer-events-none transition-opacity lg:hidden" id="sidebarOverlay"></div>

        <!-- Content area -->
        <div class="flex flex-col flex-1 overflow-x-hidden overflow-y-auto lg:pl-64">
            <!-- Top navigation -->
            <header class="bg-white shadow-sm z-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <!-- Left section -->
                        <div class="flex items-center">
                            <!-- Mobile menu button -->
                            <button type="button" class="lg:hidden text-gray-500 hover:text-gray-600 focus:outline-none" id="mobileSidebarToggle">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>

                        <!-- Right section -->
                        <div class="flex items-center space-x-4">
                            <!-- Profile dropdown -->
                            <div class="relative">
                                <div>
                                    <button type="button" class="flex items-center space-x-2 text-sm rounded-full focus:outline-none" id="profileMenuButton">
                                        <span class="sr-only">Open user menu</span>
                                        <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                            <i class="fas fa-user text-emerald-600"></i>
                                        </div>
                                        <span class="hidden md:inline-block text-gray-700">
                                            <?php echo htmlspecialchars($teacherName); ?>
                                        </span>
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Profile dropdown menu (hidden by default) -->
                                <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" id="profileMenu" role="menu" aria-orientation="vertical" aria-labelledby="profileMenuButton" tabindex="-1">
                                    <div class="py-1" role="none">
                                        <a href="/teacher/profile/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                                        <a href="/api/login/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <!-- Teacher Dashboard Main Content -->
                <div class="mb-6 md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-500">Welcome back, <?php echo htmlspecialchars($teacherName); ?>! Here's your teaching overview.</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="/teacher/exam/create.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <i class="fas fa-plus mr-2 -ml-1"></i>
                            New Exam
                        </a>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                        <div class="p-5 flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Exams</dt>
                                    <dd>
                                        <div class="text-2xl font-semibold text-gray-900"><?php echo number_format($totalExams); ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                        <div class="p-5 flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                                <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Students Enrolled</dt>
                                    <dd>
                                        <div class="text-2xl font-semibold text-gray-900"><?php echo number_format($totalStudents); ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                        <div class="p-5 flex items-center">
                            <div class="flex-shrink-0 bg-orange-100 rounded-full p-3">
                                <i class="fas fa-clock text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Approvals</dt>
                                    <dd>
                                        <div class="text-2xl font-semibold text-gray-900"><?php echo number_format($pendingApprovals); ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                        <div class="p-5 flex items-center">
                            <div class="flex-shrink-0 bg-emerald-100 rounded-full p-3">
                                <i class="fas fa-book text-emerald-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Courses Taught</dt>
                                    <dd>
                                        <div class="text-2xl font-semibold text-gray-900"><?php echo number_format($coursesTaught); ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts & Tables Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Recent Exams Table -->
                    <div class="lg:col-span-2 bg-white shadow rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="font-medium text-lg text-gray-900">Recent Exams</h2>
                                <a href="/teacher/exam/" class="text-sm text-emerald-600 hover:text-emerald-700">View all</a>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Title</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($recentExams)): ?>
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                <div class="flex items-center justify-center">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    <span>No exams created yet.</span>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="/teacher/exam/create.php" class="text-emerald-600 hover:text-emerald-700 font-medium">Create your first exam</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentExams as $exam): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($exam['title']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($exam['course_code']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $exam['duration_minutes'] ? $exam['duration_minutes'] . ' mins' : 'Not set'; ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch ($exam['status']) {
                                                        case 'Approved':
                                                            $statusClass = 'bg-green-100 text-green-800';
                                                            $statusText = 'Approved';
                                                            break;
                                                        case 'Pending':
                                                            $statusClass = 'bg-orange-100 text-orange-800';
                                                            $statusText = 'Pending';
                                                            break;
                                                        case 'Rejected':
                                                            $statusClass = 'bg-red-100 text-red-800';
                                                            $statusText = 'Rejected';
                                                            break;
                                                        case 'Draft':
                                                            $statusClass = 'bg-gray-100 text-gray-800';
                                                            $statusText = 'Draft';
                                                            break;
                                                        case 'Completed':
                                                            $statusClass = 'bg-blue-100 text-blue-800';
                                                            $statusText = 'Completed';
                                                            break;
                                                        default:
                                                            $statusClass = 'bg-gray-100 text-gray-800';
                                                            $statusText = $exam['status'];
                                                    }
                                                    ?>
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-3">
                                                        <a href="/teacher/exam/edit.php?id=<?php echo $exam['exam_id']; ?>" class="text-blue-600 hover:text-blue-900" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button onclick="deleteExam(<?php echo $exam['exam_id']; ?>)" class="text-red-600 hover:text-red-900" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <a href="/teacher/exam/questions.php?id=<?php echo $exam['exam_id']; ?>" class="text-emerald-600 hover:text-emerald-900" title="View Questions">
                                                            <i class="fas fa-question-circle"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white shadow rounded-lg border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h2 class="font-medium text-lg text-gray-900">Quick Actions</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <a href="/teacher/exam/create.php" class="flex items-center justify-center py-3 px-4 rounded-md bg-emerald-600 text-white font-medium hover:bg-emerald-700 transition-colors duration-200 shadow-sm">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Create New Exam
                            </a>
                            <a href="/teacher/results/" class="flex items-center justify-center py-3 px-4 rounded-md bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                                <i class="fas fa-chart-bar mr-2"></i>
                                View Results
                            </a>
                            <a href="/teacher/profile/" class="flex items-center justify-center py-3 px-4 rounded-md bg-purple-600 text-white font-medium hover:bg-purple-700 transition-colors duration-200 shadow-sm">
                                <i class="fas fa-user mr-2"></i>
                                Update Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Exam Status Chart -->
                    <div class="bg-white shadow rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Exam Status Distribution</h3>
                        <div class="relative" style="height: 250px;">
                            <?php if (empty($examStatusLabels)): ?>
                                <div class="flex items-center justify-center h-full text-gray-500">
                                    <div class="text-center">
                                        <i class="fas fa-chart-pie text-3xl mb-2"></i>
                                        <p>No exam data available yet</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <canvas id="examStatusChart" data-chart='<?php echo json_encode($examStatusChart); ?>'></canvas>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Student Progress Chart -->
                    <div class="bg-white shadow rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Student Performance</h3>
                        <div class="relative" style="height: 250px;">
                            <?php if (empty($resultsLabels)): ?>
                                <div class="flex items-center justify-center h-full text-gray-500">
                                    <div class="text-center">
                                        <i class="fas fa-chart-bar text-3xl mb-2"></i>
                                        <p>No results data available yet</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <canvas id="studentProgressChart" data-chart='<?php echo json_encode($resultsChart); ?>'></canvas>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-8">
                <div class="text-center text-sm text-gray-500">
                    &copy; <?php echo date('Y'); ?> Exam Management System. All rights reserved.
                </div>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile sidebar toggle
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');

            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    sidebarOverlay.classList.toggle('opacity-0');
                    sidebarOverlay.classList.toggle('pointer-events-none');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('opacity-0');
                    sidebarOverlay.classList.add('pointer-events-none');
                });
            }

            // Profile dropdown
            const profileMenuButton = document.getElementById('profileMenuButton');
            const profileMenu = document.getElementById('profileMenu');

            if (profileMenuButton && profileMenu) {
                profileMenuButton.addEventListener('click', function() {
                    profileMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (!profileMenuButton.contains(event.target) && !profileMenu.contains(event.target)) {
                        profileMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>

</html>
</script>