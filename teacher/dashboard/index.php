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
