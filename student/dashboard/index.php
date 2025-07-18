<?php
$pageTitle = "Student Dashboard";
$breadcrumb = "Dashboard";

// Start session and check authentication
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

// Check if student is logged in
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header('Location: /student/login/');
    exit;
}

require_once '../../api/config/database.php';
$db = new Database();
$conn = $db->getConnection();

$student_id = $_SESSION['student_id'];

// Get student information
$studentQuery = "
    SELECT s.*, p.name as program_name, d.name as department_name, l.name as level_name
    FROM students s
    JOIN programs p ON s.program_id = p.program_id
    JOIN departments d ON s.department_id = d.department_id
    JOIN levels l ON s.level_id = l.level_id
    WHERE s.student_id = :student_id
";
$stmt = $conn->prepare($studentQuery);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch real statistics from database

// 1. Total Registered Exams
$stmt = $conn->prepare("SELECT COUNT(*) FROM exam_registrations WHERE student_id = :student_id");
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$registeredExams = $stmt->fetchColumn();

// 2. This month's registered exams
$stmt = $conn->prepare("
    SELECT COUNT(*) FROM exam_registrations 
    WHERE student_id = :student_id 
    AND MONTH(registered_at) = MONTH(CURRENT_DATE())
    AND YEAR(registered_at) = YEAR(CURRENT_DATE())
");
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$thisMonthExams = $stmt->fetchColumn();

// 3. Average Score
$stmt = $conn->prepare("
    SELECT AVG(r.score_percentage) 
    FROM results r 
    JOIN exam_registrations er ON r.registration_id = er.registration_id 
    WHERE er.student_id = :student_id
");
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$averageScore = round($stmt->fetchColumn() ?: 0, 1);

// 4. Last term's average for comparison
$stmt = $conn->prepare("
    SELECT AVG(r.score_percentage) 
    FROM results r 
    JOIN exam_registrations er ON r.registration_id = er.registration_id 
    JOIN exams e ON er.exam_id = e.exam_id
    WHERE er.student_id = :student_id 
    AND e.created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH)
");
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$lastTermAverage = round($stmt->fetchColumn() ?: 0, 1);
$scoreImprovement = $averageScore - $lastTermAverage;

// 5. Pending Exams (Registered but not yet taken)
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM exam_registrations er
    JOIN exams e ON er.exam_id = e.exam_id
    LEFT JOIN results r ON er.registration_id = r.registration_id
    WHERE er.student_id = :student_id 
    AND r.result_id IS NULL
    AND e.status = 'Approved'
    AND NOW() BETWEEN e.start_datetime AND e.end_datetime
");
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$pendingExams = $stmt->fetchColumn();

// 6. Completed Exams
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM results r 
    JOIN exam_registrations er ON r.registration_id = er.registration_id 
    WHERE er.student_id = :student_id
");
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$completedExams = $stmt->fetchColumn();

// 7. This week's completed exams
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM results r 
    JOIN exam_registrations er ON r.registration_id = er.registration_id 
    WHERE er.student_id = :student_id 
    AND WEEK(r.completed_at) = WEEK(CURRENT_DATE())
    AND YEAR(r.completed_at) = YEAR(CURRENT_DATE())
");
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$thisWeekCompleted = $stmt->fetchColumn();

// Fetch upcoming exams
$upcomingExamsQuery = "
    SELECT e.exam_id, e.title, e.start_datetime, e.status,
           CASE 
               WHEN er.registration_id IS NOT NULL THEN 'Registered'
               ELSE 'Available'
           END as registration_status
    FROM exams e
    JOIN courses c ON e.course_id = c.course_id
    LEFT JOIN exam_registrations er ON e.exam_id = er.exam_id AND er.student_id = :student_id
    WHERE e.status = 'Approved'
    AND e.start_datetime > NOW()
    AND (c.program_id = :program_id OR c.department_id = :department_id)
    ORDER BY e.start_datetime ASC
    LIMIT 5
";
$stmt = $conn->prepare($upcomingExamsQuery);
$stmt->bindParam(':student_id', $student_id);
$stmt->bindParam(':program_id', $student['program_id']);
$stmt->bindParam(':department_id', $student['department_id']);
$stmt->execute();
$upcomingExams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent results
$recentResultsQuery = "
    SELECT r.score_percentage, r.completed_at, e.title, e.pass_mark,
           CASE WHEN r.score_percentage >= e.pass_mark THEN 'Passed' ELSE 'Failed' END as status
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    WHERE er.student_id = :student_id
    ORDER BY r.completed_at DESC
    LIMIT 5
";
$stmt = $conn->prepare($recentResultsQuery);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$recentResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent activity
$recentActivityQuery = "
    SELECT 
        'exam_completed' as activity_type,
        e.title as activity_title,
        r.completed_at as activity_date,
        CONCAT('Scored ', ROUND(r.score_percentage, 1), '%') as activity_description
    FROM results r
    JOIN exam_registrations er ON r.registration_id = er.registration_id
    JOIN exams e ON er.exam_id = e.exam_id
    WHERE er.student_id = :student_id
    
    UNION ALL
    
    SELECT 
        'exam_registered' as activity_type,
        e.title as activity_title,
        er.registered_at as activity_date,
        'Registered for exam' as activity_description
    FROM exam_registrations er
    JOIN exams e ON er.exam_id = e.exam_id
    WHERE er.student_id = :student_id
    AND er.registered_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    
    ORDER BY activity_date DESC
    LIMIT 6
";
$stmt = $conn->prepare($recentActivityQuery);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Student</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    
    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-4 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Welcome back, <?php echo htmlspecialchars($student['first_name']); ?>!
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">Here's your student overview for today.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                        <?php echo htmlspecialchars($student['program_name']); ?>
                    </span>
                </div>
            </div>
            
            <!-- Enrollment Key Input -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-emerald-700 mb-1">Join a Course or Exam</h2>
                    <p class="text-gray-600 text-sm">Enter your enrollment key to access upcoming exams for your course.</p>
                </div>
                <form id="enrollForm" class="flex gap-2 w-full md:w-auto">
                    <input id="enrollKey" type="text" required placeholder="Enrollment Key" 
                           class="px-4 py-2 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent flex-1" />
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                        Submit
                    </button>
                </form>
            </div>
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100" data-stat="registered-exams">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Registered Exams</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900"><?php echo $registeredExams; ?></div>
                                    <div class="mt-1 flex items-baseline text-sm">
                                        <span class="text-emerald-600 font-medium">+<?php echo $thisMonthExams; ?></span>
                                        <span class="ml-1 text-gray-500">this month</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100" data-stat="average-score">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                            <i class="fas fa-chart-line text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900"><?php echo $averageScore; ?>%</div>
                                    <div class="mt-1 flex items-baseline text-sm">
                                        <span class="text-<?php echo $scoreImprovement >= 0 ? 'emerald' : 'red'; ?>-600 font-medium">
                                            <?php echo $scoreImprovement >= 0 ? '+' : ''; ?><?php echo $scoreImprovement; ?>%
                                        </span>
                                        <span class="ml-1 text-gray-500">from last term</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100" data-stat="pending-exams">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-yellow-50 rounded-lg p-3">
                            <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Exams</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900"><?php echo $pendingExams; ?></div>
                                    <div class="mt-1 flex items-baseline text-sm">
                                        <span class="text-yellow-600 font-medium">Available</span>
                                        <span class="ml-1 text-gray-500">now</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100" data-stat="completed-exams">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                            <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Completed Exams</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900"><?php echo $completedExams; ?></div>
                                    <div class="mt-1 flex items-baseline text-sm">
                                        <span class="text-purple-600 font-medium">+<?php echo $thisWeekCompleted; ?></span>
                                        <span class="ml-1 text-gray-500">this week</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Upcoming Exams -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-900">Upcoming Exams</h2>
                            <a href="../exam/index.php" class="text-sm text-emerald-600 hover:text-emerald-700">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="upcomingExamsTable" class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($upcomingExams)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                                No upcoming exams available
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($upcomingExams as $exam): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo htmlspecialchars($exam['title']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo date('Y-m-d H:i', strtotime($exam['start_datetime'])); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 rounded <?php echo $exam['registration_status'] == 'Registered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?> text-xs font-semibold">
                                                        <?php echo $exam['registration_status']; ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php if ($exam['registration_status'] == 'Registered'): ?>
                                                        <button onclick="window.location.href='../exam/take.php?id=<?php echo $exam['exam_id']; ?>'" 
                                                                class="text-emerald-600 hover:text-emerald-700 transition-colors duration-200" 
                                                                title="Take Exam">
                                                            <i class="fas fa-play text-lg cursor-pointer"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button onclick="registerForExam(<?php echo $exam['exam_id']; ?>)" 
                                                                class="text-blue-600 hover:text-blue-700 transition-colors duration-200" 
                                                                title="Register for Exam">
                                                            <i class="fas fa-plus text-lg cursor-pointer"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Recent Results -->
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-900">Recent Results</h2>
                            <a href="../results/index.php" class="text-sm text-emerald-600 hover:text-emerald-700">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="recentResultsTable" class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($recentResults)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                                No exam results available
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentResults as $result): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo htmlspecialchars($result['title']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo round($result['score_percentage'], 1); ?>%
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 rounded <?php echo $result['status'] == 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> text-xs font-semibold">
                                                        <?php echo $result['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo date('Y-m-d', strtotime($result['completed_at'])); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Activity & Announcements -->
                <div class="flex flex-col gap-6">
                    <!-- Recent Activity Timeline -->
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                        <ul class="relative border-l-2 border-emerald-200 pl-6 space-y-4">
                            <?php if (empty($recentActivity)): ?>
                                <li class="text-gray-500">No recent activity</li>
                            <?php else: ?>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <li>
                                        <span class="absolute -left-3 top-1 w-3 h-3 <?php echo $activity['activity_type'] == 'exam_completed' ? 'bg-green-500' : 'bg-emerald-500'; ?> rounded-full"></span>
                                        <span class="font-semibold <?php echo $activity['activity_type'] == 'exam_completed' ? 'text-green-700' : 'text-emerald-700'; ?>">
                                            <?php echo htmlspecialchars($activity['activity_title']); ?>
                                        </span> - 
                                        <span class="text-gray-700"><?php echo htmlspecialchars($activity['activity_description']); ?></span>
                                        <span class="text-gray-500 text-sm block">
                                            <?php echo date('M j, Y', strtotime($activity['activity_date'])); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- Announcements -->
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Announcements</h3>
                        <ul class="space-y-3 text-gray-700 text-sm">
                            <li class="flex items-start">
                                <i class="fas fa-bullhorn text-emerald-600 mt-1 mr-3 flex-shrink-0"></i>
                                <span>Check your email regularly for exam notifications and updates.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-calendar-check text-blue-600 mt-1 mr-3 flex-shrink-0"></i>
                                <span>Make sure to register for upcoming exams before the deadline.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-question-circle text-yellow-600 mt-1 mr-3 flex-shrink-0"></i>
                                <span>Contact your instructor if you have any questions about exam content.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-book text-purple-600 mt-1 mr-3 flex-shrink-0"></i>
                                <span>Review your course materials before taking any exam.</span>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl p-6 text-white">
                        <h3 class="text-lg font-semibold mb-4">Quick Stats</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span>Success Rate:</span>
                                <span class="font-semibold">
                                    <?php 
                                    $passedCount = 0;
                                    $totalResults = count($recentResults);
                                    foreach ($recentResults as $result) {
                                        if ($result['status'] == 'Passed') $passedCount++;
                                    }
                                    $successRate = $totalResults > 0 ? round(($passedCount / $totalResults) * 100, 1) : 0;
                                    echo $successRate;
                                    ?>%
                                </span>
                                                            </div>
                            <div class="flex justify-between">
                                <span>Program:</span>
                                <span class="font-semibold"><?php echo htmlspecialchars($student['program_name']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Level:</span>
                                <span class="font-semibold"><?php echo htmlspecialchars($student['level_name']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Department:</span>
                                <span class="font-semibold"><?php echo htmlspecialchars($student['department_name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Include the updated dashboard JavaScript -->
    <script src="dashboard.js"></script>
    
    <script>
        // Initialize dashboard functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to stat cards
            const statCards = document.querySelectorAll('[data-stat]');
            statCards.forEach(card => {
                card.addEventListener('click', function() {
                    const statType = this.dataset.stat;
                    // You can add navigation or detailed view functionality here
                    console.log('Clicked on stat:', statType);
                });
            });
            
            // Auto-refresh dashboard data every 5 minutes
            setInterval(refreshDashboardData, 5 * 60 * 1000);
        });
        
        // Function to refresh dashboard data
        function refreshDashboardData() {
            fetch('/api/student/getDashboardStats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateDashboardStats(data.data);
                    }
                })
                .catch(error => {
                    console.error('Failed to refresh dashboard data:', error);
                });
        }
        
        // Function to update dashboard statistics
        function updateDashboardStats(data) {
            // Update stat cards
            const registeredExamsCard = document.querySelector('[data-stat="registered-exams"] .text-xl');
            if (registeredExamsCard) {
                registeredExamsCard.textContent = data.registered_exams;
            }
            
            const averageScoreCard = document.querySelector('[data-stat="average-score"] .text-xl');
            if (averageScoreCard) {
                averageScoreCard.textContent = data.average_score + '%';
            }
            
            const pendingExamsCard = document.querySelector('[data-stat="pending-exams"] .text-xl');
            if (pendingExamsCard) {
                pendingExamsCard.textContent = data.pending_exams;
            }
            
            const completedExamsCard = document.querySelector('[data-stat="completed-exams"] .text-xl');
            if (completedExamsCard) {
                completedExamsCard.textContent = data.completed_exams;
            }
            
            // Update tables if needed
            updateUpcomingExamsTable(data.upcoming_exams);
            updateRecentResultsTable(data.recent_results);
        }
        
        // Function to update upcoming exams table
        function updateUpcomingExamsTable(exams) {
            const tbody = document.getElementById('upcomingExamsTable');
            if (!tbody) return;
            
            if (exams.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No upcoming exams available
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = exams.map(exam => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${escapeHtml(exam.title)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${formatDateTime(exam.start_datetime)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 rounded ${exam.registration_status === 'Registered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'} text-xs font-semibold">
                            ${exam.registration_status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${exam.registration_status === 'Registered' ? 
                            `<button onclick="window.location.href='../exam/take.php?id=${exam.exam_id}'" 
                                    class="text-emerald-600 hover:text-emerald-700 transition-colors duration-200" 
                                    title="Take Exam">
                                <i class="fas fa-play text-lg cursor-pointer"></i>
                            </button>` :
                            `<button onclick="registerForExam(${exam.exam_id})" 
                                    class="text-blue-600 hover:text-blue-700 transition-colors duration-200" 
                                    title="Register for Exam">
                                <i class="fas fa-plus text-lg cursor-pointer"></i>
                            </button>`
                        }
                    </td>
                </tr>
            `).join('');
        }
        
        // Function to update recent results table
        function updateRecentResultsTable(results) {
            const tbody = document.getElementById('recentResultsTable');
            if (!tbody) return;
            
            if (results.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No exam results available
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = results.map(result => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${escapeHtml(result.title)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${Math.round(result.score_percentage * 10) / 10}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 rounded ${result.status === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-xs font-semibold">
                            ${result.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${formatDate(result.completed_at)}
                    </td>
                </tr>
            `).join('');
        }
        
        // Utility functions
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
        }
    </script>
</body>
</html>


