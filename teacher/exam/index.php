<<<<<<< HEAD
<?php include_once '../components/Sidebar.php'; ?>
<?php include_once '../components/Header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams - EMS Teacher</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Exams Management</h1>
                    <p class="mt-1 text-sm text-gray-500">Create, configure and monitor your examination activities</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="#"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-plus mr-2 -ml-1"></i>
                        New Exam
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-clipboard-list text-emerald-600 text-xl"></i>
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
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-clock text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Now</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">3</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-blue-600 font-medium">Currently</span>
                                            <span class="ml-1 text-gray-500">in progress</span>
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
                            <div class="flex-shrink-0 bg-yellow-50 rounded-lg p-3">
                                <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Approval</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">2</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-yellow-600 font-medium">Awaiting</span>
                                            <span class="ml-1 text-gray-500">review</span>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
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
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Exams by Status</h3>
                    <div style="height:220px;">
                        <canvas id="examsStatusChart" style="width:100%;height:220px;"></canvas>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Participation Rate</h3>
                    <div style="height:220px;">
                        <canvas id="participationChart" style="width:100%;height:220px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-12">
                        <button id="tab-upcoming"
                            class="tab-button border-emerald-500 text-emerald-600 whitespace-nowrap pb-4 px-2 border-b-2 font-medium text-sm mx-4">Upcoming
                            & Draft</button>
                        <button id="tab-active"
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-2 border-b-2 font-medium text-sm mx-4">Active</button>
                        <button id="tab-completed"
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-2 border-b-2 font-medium text-sm mx-4">Completed</button>
                    </nav>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div class="relative col-span-2">
                            <input type="text" id="searchExam" placeholder="Search exams..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <select id="filterSubject"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Subjects</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Physics">Physics</option>
                            <option value="Chemistry">Chemistry</option>
                            <option value="Biology">Biology</option>
                            <option value="English">English</option>
                            <option value="History">History</option>
                        </select>
                        <button
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-medium">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div id="tab-content">
                <!-- Upcoming & Draft Exams Tab -->
                <div id="content-upcoming"
                    class="tab-content bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Upcoming & Draft Exams</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Exam Title</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subject</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Algebra Basics
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">Mathematics</td>
                                    <td class="px-6 py-4 whitespace-nowrap">2024-04-20</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Draft</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap flex gap-3">
                                        <a href="viewExam.php" title="View" class="text-blue-600 hover:text-blue-800"><i
                                                class="fas fa-eye"></i></a>
                                        <a href="editExam.php" title="Edit"
                                            class="text-yellow-600 hover:text-yellow-800"><i
                                                class="fas fa-edit"></i></a>
                                        <a href="#" title="Delete" class="text-red-600 hover:text-red-800"><i
                                                class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <!-- More rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Active Exams Tab -->
                <div id="content-active"
                    class="tab-content bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Active Exams</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Exam Title</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subject</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Physics Quiz</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Physics</td>
                                    <td class="px-6 py-4 whitespace-nowrap">2024-04-25</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Active</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap flex gap-3">
                                        <a href="viewExam.php" title="View" class="text-blue-600 hover:text-blue-800"><i
                                                class="fas fa-eye"></i></a>
                                        <a href="editExam.php" title="Edit"
                                            class="text-yellow-600 hover:text-yellow-800"><i
                                                class="fas fa-edit"></i></a>
                                        <a href="#" title="Delete" class="text-red-600 hover:text-red-800"><i
                                                class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <!-- More rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Completed Exams Tab -->
                <div id="content-completed"
                    class="tab-content bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Completed Exams</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Exam Title</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subject</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Chemistry Final
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">Chemistry</td>
                                    <td class="px-6 py-4 whitespace-nowrap">2024-05-02</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Completed</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap flex gap-3">
                                        <a href="viewExam.php" title="View" class="text-blue-600 hover:text-blue-800"><i
                                                class="fas fa-eye"></i></a>
                                        <a href="editExam.php" title="Edit"
                                            class="text-yellow-600 hover:text-yellow-800"><i
                                                class="fas fa-edit"></i></a>
                                        <a href="#" title="Delete" class="text-red-600 hover:text-red-800"><i
                                                class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <!-- More rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        // Tab switching logic
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    tabs.forEach(t => t.classList.remove('border-emerald-500', 'text-emerald-600'));
                    this.classList.add('border-emerald-500', 'text-emerald-600');
                    contents.forEach(c => c.classList.add('hidden'));
                    const id = this.id.replace('tab-', 'content-');
                    document.getElementById(id).classList.remove('hidden');
                });
            });
            // Default to first tab
            tabs[0].click();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Chart rendering functions
        function renderCharts() {
            // Bar Chart: Exams by Status
            const statusCtx = document.getElementById('examsStatusChart').getContext('2d');
            if (window.statusChart) window.statusChart.destroy();
            window.statusChart = new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: ['Active', 'Pending', 'Completed'],
                    datasets: [{
                        label: 'Number of Exams',
                        data: [3, 2, 7],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(168, 85, 247, 0.8)'
                        ],
                        borderColor: [
                            'rgb(16, 185, 129)',
                            'rgb(251, 191, 36)',
                            'rgb(168, 85, 247)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Doughnut Chart: Participation Rate
            const partCtx = document.getElementById('participationChart').getContext('2d');
            if (window.partChart) window.partChart.destroy();
            window.partChart = new Chart(partCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Participated', 'Not Participated'],
                    datasets: [{
                        data: [85, 15],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // Tab switching logic and chart rendering
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    tabs.forEach(t => t.classList.remove('border-emerald-500', 'text-emerald-600'));
                    this.classList.add('border-emerald-500', 'text-emerald-600');
                    contents.forEach(c => c.classList.add('hidden'));
                    const id = this.id.replace('tab-', 'content-');
                    document.getElementById(id).classList.remove('hidden');
                    // Re-render charts after tab switch to ensure canvases are visible
                    setTimeout(renderCharts, 100);
                });
            });
            // Default to first tab and render charts
            tabs[0].click();
            setTimeout(renderCharts, 100);

            // SweetAlert2 for delete
            document.querySelectorAll('a[title="Delete"]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This exam will be deleted. This action cannot be undone!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire('Deleted!', 'The exam has been deleted.', 'success');
                            // Here you would add your AJAX or form submission to actually delete the exam
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
=======
<?php
// --- Secure session start and teacher authentication ---
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}
require_once __DIR__ . '/../../api/config/database.php';
// Redirect if accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    header("Location: ../index.php?page=exams");
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

// --- Fetch all exams for this teacher ---
$stmt = $conn->prepare("
    SELECT e.exam_id, e.title, e.exam_code, e.status, e.start_datetime, e.duration_minutes, c.code as course_code, c.title as course_title
    FROM exams e
    JOIN courses c ON e.course_id = c.course_id
    WHERE e.teacher_id = :teacher_id
    ORDER BY e.created_at DESC
");
$stmt->execute(['teacher_id' => $teacher_id]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Teacher Exams Management Page -->
<div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">My Exams</h1>
        <p class="mt-1 text-sm text-gray-500">Manage all your created exams here.</p>
    </div>
    <div>
        <a href="index.php?page=exams&action=create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            <i class="fas fa-plus mr-2 -ml-1"></i>
            Create New Exam
        </a>
    </div>
</div>

<div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">Exam Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($exams)): ?>
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    <i class="fas fa-info-circle mr-2"></i>
                    No exams found. <a href="index.php?page=exams&action=create" class="text-emerald-600 hover:text-emerald-700">Create your first exam</a>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($exams as $exam): ?>
                <tr id="exam-row-<?php echo $exam['exam_id']; ?>">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($exam['title']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($exam['course_code']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $exam['start_datetime'] ? date('M d, Y H:i', strtotime($exam['start_datetime'])) : 'Not set'; ?></td>
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
                        <a href="#" onclick="deleteExam(<?php echo $exam['exam_id']; ?>, this)" class="text-red-600 hover:text-red-900" title="Delete"><i class="fas fa-trash"></i></a>
                        <a href="index.php?page=exams&action=questions&id=<?php echo $exam['exam_id']; ?>" class="text-emerald-600 hover:text-emerald-900" title="View Questions"><i class="fas fa-question-circle"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Delete Exam via API (remove row on success)
function deleteExam(examId, el) {
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
            // Remove the row from the table
            const row = document.getElementById('exam-row-' + examId);
            if (row) row.remove();
            alert('Exam deleted successfully!');
        } else {
            alert(data.message || 'Failed to delete exam.');
        }
    })
    .catch(() => {
        alert('Network error. Please try again.');
    });
}
</script>
>>>>>>> origin/joshua
