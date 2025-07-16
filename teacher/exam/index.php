<?php
require_once __DIR__ . '/../../api/login/teacher/teacherSessionCheck.php';
require_once __DIR__ . '/../../api/config/database.php';
require_once __DIR__ . '/../components/teacherSidebar.php';
require_once __DIR__ . '/../components/teacherHeader.php';

$currentPage = 'exams';

// --- Database connection ---
$db = new Database();
$conn = $db->getConnection();
$teacher_id = $_SESSION['teacher_id'];    // --- Fetch all exams for this teacher ---
$stmt = $conn->prepare("
    SELECT e.exam_id, e.title, e.exam_code, e.status, e.start_datetime, e.duration_minutes, 
           c.course_id, c.title as course_name, c.code as course_code, 
           d.name as department_name, p.name as program_name, s.name as semester_name
    FROM exams e
    JOIN courses c ON e.course_id = c.course_id
    JOIN departments d ON e.department_id = d.department_id
    JOIN programs p ON e.program_id = p.program_id
    JOIN semesters s ON e.semester_id = s.semester_id
    WHERE e.teacher_id = :teacher_id
    ORDER BY e.created_at DESC
");
$stmt->execute(['teacher_id' => $teacher_id]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$totalExams = count($exams);
$activeExams = count(array_filter($exams, function ($exam) {
    return $exam['status'] === 'Approved';
}));
$pendingExams = count(array_filter($exams, function ($exam) {
    return $exam['status'] === 'Pending';
}));
$completedExams = count(array_filter($exams, function ($exam) {
    return $exam['status'] === 'Completed';
}));
$draftExams = count(array_filter($exams, function ($exam) {
    return $exam['status'] === 'Draft';
}));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams - EMS Teacher</title>
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
            <!-- Page Header -->
            <div class="mb-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Exams Management</h1>
                    <p class="mt-1 text-sm text-gray-500">Create, configure and monitor your examination activities</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="createExam.php"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-plus mr-2 -ml-1"></i>
                        New Exam
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 stats-card" data-stat-type="total">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-clipboard-list text-emerald-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Exams</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900 stat-value"><?php echo $totalExams; ?></div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">All time</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 stats-card" data-stat-type="approved">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                                <i class="fas fa-check text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Exams</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900 stat-value"><?php echo $activeExams; ?></div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-green-600 font-medium">Approved</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 stats-card" data-stat-type="pending">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-50 rounded-lg p-3">
                                <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Approval</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900 stat-value"><?php echo $pendingExams; ?></div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-yellow-600 font-medium">Awaiting</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 stats-card" data-stat-type="completed">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900 stat-value"><?php echo $completedExams; ?></div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-purple-600 font-medium">Finished</span>
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
                    <h3 class="text-lg font-semibold mb-4">Exam Progress</h3>
                    <div style="height:220px;">
                        <canvas id="progressChart" style="width:100%;height:220px;"></canvas>
                    </div>
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
                        <select id="filterStatus"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="Draft">Draft</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Completed">Completed</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <button id="filterBtn"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-medium">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div> <!-- Exams Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">All Exams</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program/Semester</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="examTableBody">
                            <?php if (empty($exams)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        No exams found. <a href="createExam.php" class="text-emerald-600 hover:text-emerald-700">Create your first exam</a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($exams as $exam): ?>
                                    <tr id="exam-row-<?php echo $exam['exam_id']; ?>" class="exam-row">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($exam['title']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo ($exam['course_code'] . '<br>' . $exam['course_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo ($exam['program_name'] . ' <br> ' . $exam['semester_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php
                                            $timestamp = strtotime($exam['start_datetime']);
                                            echo ($exam['start_datetime'] && $timestamp !== false)
                                                ? date('M d, Y h:i A', $timestamp)
                                                : 'Not set';
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $exam['duration_minutes'] ? $exam['duration_minutes'] . ' mins' : 'Not set'; ?></td>
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
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-2">
                                                <a href="viewExam.php?id=<?php echo $exam['exam_id']; ?>" class="text-blue-600 hover:text-blue-900" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($exam['status'] === 'Draft' || $exam['status'] === 'Rejected'): ?>
                                                    <a href="editExam.php?id=<?php echo $exam['exam_id']; ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($exam['status'] !== 'Completed'): ?>
                                                    <a href="#" data-exam-id="<?php echo $exam['exam_id']; ?>" class="text-red-600 hover:text-red-900 delete-exam-btn" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</body>
<script>
    // Chart rendering functions
    function renderCharts() {
        const statusData = {
            draft: <?php echo $draftExams; ?>,
            pending: <?php echo $pendingExams; ?>,
            approved: <?php echo $activeExams; ?>,
            completed: <?php echo $completedExams; ?>,
            rejected: <?php echo count(array_filter($exams, function ($exam) {
                            return $exam['status'] === 'Rejected';
                        })); ?>
        };

        // Bar Chart: Exams by Status
        const statusCtx = document.getElementById('examsStatusChart').getContext('2d');
        if (window.statusChart) window.statusChart.destroy();
        window.statusChart = new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['Draft', 'Pending', 'Approved', 'Completed', 'Rejected'],
                datasets: [{
                    label: 'Number of Exams',
                    data: [statusData.draft, statusData.pending, statusData.approved, statusData.completed, statusData.rejected],
                    backgroundColor: [
                        'rgba(156, 163, 175, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgb(156, 163, 175)',
                        'rgb(251, 191, 36)',
                        'rgb(34, 197, 94)',
                        'rgb(168, 85, 247)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Doughnut Chart: Exam Progress
        const progressCtx = document.getElementById('progressChart').getContext('2d');
        if (window.progressChart) window.progressChart.destroy();
        window.progressChart = new Chart(progressCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending', 'Draft', 'Rejected'],
                datasets: [{
                    data: [statusData.completed, statusData.approved, statusData.pending, statusData.draft, statusData.rejected],
                    backgroundColor: [
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(156, 163, 175, 0.8)',
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
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Search and Filter functionality
    function filterExams() {
        const searchTerm = document.getElementById('searchExam').value.toLowerCase();
        const statusFilter = document.getElementById('filterStatus').value;
        const rows = document.querySelectorAll('.exam-row');

        rows.forEach(row => {
            const title = row.querySelector('td:first-child').textContent.toLowerCase();
            const course = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const program = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(6) span').textContent;

            const matchesSearch = title.includes(searchTerm) ||
                course.includes(searchTerm) ||
                program.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Delete Exam via SweetAlert2
    function deleteExam(examId, el) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This exam will be deleted permanently. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Use axios for consistency with other parts of the application
                axios.post('/api/exams/deleteExam.php', {
                        exam_id: examId
                    })
                    .then(function(response) {
                        const data = response.data;
                        if (data.status === 'success') {
                            const row = document.getElementById('exam-row-' + examId);
                            if (row) row.remove();

                            // Recalculate stats and update charts
                            updateStats();
                            renderCharts();

                            Swal.fire(
                                'Deleted!',
                                'The exam has been deleted successfully.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message || 'Failed to delete exam.',
                                'error'
                            );
                        }
                    })
                    .catch(function(error) {
                        console.error('Delete exam error:', error);
                        Swal.fire(
                            'Error!',
                            'Network error. Please try again.',
                            'error'
                        );
                    });
            }
        });
    }

    // Function to update stats after a successful deletion
    function updateStats() {
        axios.get('/api/exams/getTeacherExamStats.php')
            .then(function(response) {
                const data = response.data;
                if (data.status === 'success') {
                    document.querySelectorAll('.stats-card').forEach(card => {
                        const statType = card.dataset.statType;
                        if (data.stats[statType] !== undefined) {
                            card.querySelector('.stat-value').textContent = data.stats[statType];
                        }
                    });
                }
            })
            .catch(function(error) {
                console.error('Error updating stats:', error);
            });
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Render charts
        setTimeout(renderCharts, 100);

        // Setup event listeners
        var searchExam = document.getElementById('searchExam');
        if (searchExam) {
            searchExam.addEventListener('input', filterExams);
        }

        var filterStatus = document.getElementById('filterStatus');
        if (filterStatus) {
            filterStatus.addEventListener('change', filterExams);
        }

        var filterBtn = document.getElementById('filterBtn');
        if (filterBtn) {
            filterBtn.addEventListener('click', filterExams);
        }

        // Attach delete event listeners
        document.querySelectorAll('.delete-exam-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const examId = this.getAttribute('data-exam-id');
                deleteExam(examId, this);
            });
        });
    });
</script>
</body>

</html>