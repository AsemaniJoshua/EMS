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
