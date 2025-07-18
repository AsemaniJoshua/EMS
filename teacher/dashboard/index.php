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
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, <?php echo htmlspecialchars($teacherName); ?></h1>
                        <p class="text-gray-600">Here's an overview of your teaching activities and exam management.</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
                        <a href="/teacher/exam/createExam.php" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Create Exam
                        </a>
                        <a href="/teacher/exam/" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors duration-200">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            View All Exams
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Exams</p>
                            <h2 class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalExams; ?></h2>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pending Approvals</p>
                            <h2 class="text-3xl font-bold text-amber-600 mt-2"><?php echo $pendingApprovals; ?></h2>
                        </div>
                        <div class="p-3 bg-amber-100 rounded-full">
                            <i class="fas fa-clock text-amber-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Students Taught</p>
                            <h2 class="text-3xl font-bold text-emerald-600 mt-2"><?php echo $totalStudents; ?></h2>
                        </div>
                        <div class="p-3 bg-emerald-100 rounded-full">
                            <i class="fas fa-users text-emerald-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Courses Taught</p>
                            <h2 class="text-3xl font-bold text-purple-600 mt-2"><?php echo $coursesTaught; ?></h2>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-book text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Exam Status Distribution</h3>
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <i class="fas fa-chart-pie text-gray-600"></i>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="examStatusChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Average Scores (Recent Exams)</h3>
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <i class="fas fa-chart-bar text-gray-600"></i>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="resultsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Exams Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Recent Exams</h2>
                            <p class="text-sm text-gray-500 mt-1">Your latest exam activities</p>
                        </div>
                        <a href="/teacher/exam/" class="inline-flex items-center px-4 py-2 text-sm font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors duration-200">
                            <span>View All</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Code</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($recentExams as $exam): ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($exam['title']); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($exam['course_code'] ?? ''); ?></div>
                                            <?php if (isset($exam['course_title'])): ?>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($exam['course_title']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <?php echo htmlspecialchars($exam['exam_code']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo isset($exam['start_datetime']) ? date("M d, Y", strtotime($exam['start_datetime'])) : 'Not scheduled'; ?>
                                            <?php if (isset($exam['start_datetime'])): ?>
                                                <div class="text-xs text-gray-500"><?php echo date("h:i A", strtotime($exam['start_datetime'])); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo isset($exam['duration_minutes']) ? htmlspecialchars($exam['duration_minutes']) . ' mins' : 'N/A'; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $status = $exam['status'] ?? 'Unknown';
                                            $badgeColor = match ($status) {
                                                'Pending' => 'bg-amber-100 text-amber-800',
                                                'Approved' => 'bg-emerald-100 text-emerald-800',
                                                'Rejected' => 'bg-red-100 text-red-800',
                                                'Draft' => 'bg-gray-100 text-gray-800',
                                                'Completed' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badgeColor; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <a href="/teacher/exam/viewExam.php?id=<?php echo $exam['exam_id']; ?>" class="text-blue-600 hover:text-blue-800 transition-colors duration-150" title="View Exam">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/teacher/exam/editExam.php?id=<?php echo $exam['exam_id']; ?>" class="text-emerald-600 hover:text-emerald-800 transition-colors duration-150" title="Edit Exam">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recentExams)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-12">
                                            <div class="text-center">
                                                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">No exams yet</h3>
                                                <p class="text-gray-500 mb-6">Get started by creating your first exam.</p>
                                                <a href="/teacher/exam/createExam.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                                                    <i class="fas fa-plus mr-2"></i>
                                                    Create your first exam
                                                </a>
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
            // Chart.js default configuration
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = '#6B7280';

            // Exam Status Chart
            const examStatusCtx = document.getElementById('examStatusChart');
            if (examStatusCtx && <?php echo !empty($examStatusChart['labels']) ? 'true' : 'false'; ?>) {
                new Chart(examStatusCtx.getContext('2d'), {
                    type: 'doughnut',
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
                            borderWidth: 0,
                            cutout: '60%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1F2937',
                                titleColor: '#F9FAFB',
                                bodyColor: '#F9FAFB',
                                borderColor: '#374151',
                                borderWidth: 1,
                                cornerRadius: 8
                            }
                        }
                    }
                });
            } else if (examStatusCtx) {
                examStatusCtx.parentElement.innerHTML = '<div class="flex items-center justify-center h-64 text-gray-500"><div class="text-center"><i class="fas fa-chart-pie text-4xl mb-4"></i><p>No exam data available</p></div></div>';
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
                            backgroundColor: '#6366F1',
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1F2937',
                                titleColor: '#F9FAFB',
                                bodyColor: '#F9FAFB',
                                borderColor: '#374151',
                                borderWidth: 1,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: {
                                border: {
                                    display: false
                                },
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 0
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: 100,
                                border: {
                                    display: false
                                },
                                grid: {
                                    color: '#F3F4F6'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            } else if (resultsCtx) {
                resultsCtx.parentElement.innerHTML = '<div class="flex items-center justify-center h-64 text-gray-500"><div class="text-center"><i class="fas fa-chart-bar text-4xl mb-4"></i><p>No results data available</p></div></div>';
            }
        });
    </script>
</body>

</html>