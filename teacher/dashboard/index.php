<?php
require_once __DIR__ . '/../../api/login/teacher/teacherSessionCheck.php';
require_once __DIR__ . '/../../api/config/database.php';
require_once __DIR__ . '/../components/teacherSidebar.php';
require_once __DIR__ . '/../components/teacherHeader.php';

$currentPage = 'dashboard';

$db = new Database();
$conn = $db->getConnection();
$teacher_id = $_SESSION['teacher_id'];

function getCount($conn, $query, $params)
{
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

$totalExams = getCount($conn, "SELECT COUNT(*) FROM exams WHERE teacher_id = :teacher_id", ['teacher_id' => $teacher_id]);
$pendingApprovals = getCount($conn, "SELECT COUNT(*) FROM exams WHERE teacher_id = :teacher_id AND status = 'Pending'", ['teacher_id' => $teacher_id]);
$totalStudents = getCount($conn, "SELECT COUNT(DISTINCT er.student_id) FROM exam_registrations er JOIN exams e ON er.exam_id = e.exam_id WHERE e.teacher_id = :teacher_id", ['teacher_id' => $teacher_id]);
$coursesTaught = getCount($conn, "SELECT COUNT(DISTINCT course_id) FROM exams WHERE teacher_id = :teacher_id", ['teacher_id' => $teacher_id]);

$stmt = $conn->prepare("SELECT e.exam_id, e.title, e.exam_code, e.status, e.start_datetime, e.duration_minutes, c.code AS course_code, c.title AS course_title FROM exams e JOIN courses c ON e.course_id = c.course_id WHERE e.teacher_id = :teacher_id ORDER BY e.created_at DESC LIMIT 5");
$stmt->execute(['teacher_id' => $teacher_id]);
$recentExams = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM exams WHERE teacher_id = :teacher_id GROUP BY status");
$stmt->execute(['teacher_id' => $teacher_id]);
$statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$examStatusChart = [
    'labels' => array_column($statusData, 'status'),
    'data' => array_map('intval', array_column($statusData, 'count'))
];

$stmt = $conn->prepare("SELECT e.title, AVG(r.score_percentage) as avg_score FROM results r JOIN exam_registrations er ON r.registration_id = er.registration_id JOIN exams e ON er.exam_id = e.exam_id WHERE e.teacher_id = :teacher_id GROUP BY e.exam_id ORDER BY e.created_at DESC LIMIT 5");
$stmt->execute(['teacher_id' => $teacher_id]);
$resultsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$resultsChart = [
    'labels' => array_column($resultsData, 'title'),
    'data' => array_map(fn($row) => round($row['avg_score'], 2), $resultsData)
];

$stmt = $conn->prepare("SELECT first_name, last_name FROM teachers WHERE teacher_id = :teacher_id");
$stmt->execute(['teacher_id' => $teacher_id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);
$teacherName = trim(($teacher['first_name'] ?? '') . ' ' . ($teacher['last_name'] ?? ''));
$_SESSION['teacher_name'] = $teacherName;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - EMS</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    <?php renderTeacherSidebar($currentPage); ?>
    <?php renderTeacherHeader(); ?>

    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Welcome, <?php echo htmlspecialchars($teacherName); ?></h1>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Total Exams</p>
                    <h2 class="text-2xl font-bold text-gray-700"><?php echo $totalExams; ?></h2>
                </div>
                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Pending Approvals</p>
                    <h2 class="text-2xl font-bold text-yellow-600"><?php echo $pendingApprovals; ?></h2>
                </div>
                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Students Taught</p>
                    <h2 class="text-2xl font-bold text-emerald-600"><?php echo $totalStudents; ?></h2>
                </div>
                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Courses Taught</p>
                    <h2 class="text-2xl font-bold text-purple-600"><?php echo $coursesTaught; ?></h2>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <div class="bg-white p-5 rounded-lg shadow">
                    <h2 class="font-semibold text-gray-700 mb-4">Exam Status Distribution</h2>
                    <canvas id="examStatusChart"></canvas>
                </div>

                <div class="bg-white p-5 rounded-lg shadow">
                    <h2 class="font-semibold text-gray-700 mb-4">Average Scores (Recent Exams)</h2>
                    <canvas id="resultsChart"></canvas>
                </div>
            </div>

            <!-- Recent Exams Section -->
            <div class="bg-white p-6 rounded-lg shadow mb-10">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Exams</h2>
                    <a href="/teacher/exam/" class="text-sm text-emerald-600 hover:text-emerald-800">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-700 border">
                        <thead class="bg-emerald-600 text-white">
                            <tr>
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Course</th>
                                <th class="px-4 py-3">Exam Code</th>
                                <th class="px-4 py-3">Start Time</th>
                                <th class="px-4 py-3">Duration</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recentExams as $exam): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 font-medium"><?php echo htmlspecialchars($exam['title']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($exam['course_code'] ?? ''); ?> <?php echo isset($exam['course_title']) ? '- ' . htmlspecialchars($exam['course_title']) : ''; ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($exam['exam_code']); ?></td>
                                    <td class="px-4 py-2"><?php echo isset($exam['start_datetime']) ? date("M d, Y - h:i A", strtotime($exam['start_datetime'])) : 'Not scheduled'; ?></td>
                                    <td class="px-4 py-2"><?php echo isset($exam['duration_minutes']) ? htmlspecialchars($exam['duration_minutes']) . ' mins' : 'N/A'; ?></td>
                                    <td class="px-4 py-2">
                                        <?php
                                        $status = $exam['status'] ?? 'Unknown';
                                        $badgeColor = match ($status) {
                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                            'Approved' => 'bg-green-100 text-green-800',
                                            'Rejected' => 'bg-red-100 text-red-800',
                                            'Draft' => 'bg-gray-100 text-gray-800',
                                            'Completed' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        ?>
                                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold <?php echo $badgeColor; ?>">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            <a href="/teacher/exam/view.php?id=<?php echo $exam['exam_id']; ?>" class="text-blue-600 hover:text-blue-800" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/teacher/exam/edit.php?id=<?php echo $exam['exam_id']; ?>" class="text-emerald-600 hover:text-emerald-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentExams)): ?>
                                <tr>
                                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                                        <div class="py-6">
                                            <i class="fas fa-clipboard-list text-gray-300 text-4xl mb-3"></i>
                                            <p class="mb-1">No recent exams found.</p>
                                            <a href="/teacher/exam/create.php" class="text-emerald-600 hover:text-emerald-800 font-medium">Create your first exam</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Exam Status Chart
            const examStatusCtx = document.getElementById('examStatusChart');
            if (examStatusCtx && <?php echo !empty($examStatusChart['labels']) ? 'true' : 'false'; ?>) {
                new Chart(examStatusCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($examStatusChart['labels'] ?? []); ?>,
                        datasets: [{
                            data: <?php echo json_encode($examStatusChart['data'] ?? []); ?>,
                            backgroundColor: [
                                '#10B981', // Approved - Emerald
                                '#F59E0B', // Pending - Amber
                                '#EF4444', // Rejected - Red
                                '#6B7280', // Draft - Gray
                                '#3B82F6' // Completed - Blue
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Results Chart
            const resultsCtx = document.getElementById('resultsChart');
            if (resultsCtx && <?php echo !empty($resultsChart['labels']) ? 'true' : 'false'; ?>) {
                new Chart(resultsCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($resultsChart['labels'] ?? []); ?>,
                        datasets: [{
                            label: 'Average Score (%)',
                            data: <?php echo json_encode($resultsChart['data'] ?? []); ?>,
                            backgroundColor: '#6366F1'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>