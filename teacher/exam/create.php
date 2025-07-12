<?php
// --- Secure session start and teacher authentication ---
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}
require_once __DIR__ . '/../../api/config/database.php';
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header('Location: /teacher/login/');
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$teacher_id = $_SESSION['teacher_id'];

// Fetch courses this teacher can create exams for
$stmt = $conn->prepare("
    SELECT DISTINCT c.course_id, c.code, c.title
    FROM courses c
    JOIN teacher_courses tc ON c.course_id = tc.course_id
    WHERE tc.teacher_id = :teacher_id
    ORDER BY c.code
");
$stmt->execute(['teacher_id' => $teacher_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6 text-gray-900">Create New Exam</h1>
    <form id="create-exam-form" class="space-y-6">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Exam Title *</label>
            <input type="text" id="title" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label for="exam_code" class="block text-sm font-medium text-gray-700 mb-1">Exam Code *</label>
            <input type="text" id="exam_code" name="exam_code" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Course *</label>
            <select id="course_id" name="course_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['code'] . ' - ' . $course['title']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
            <input type="number" id="duration_minutes" name="duration_minutes" min="15" max="240" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time *</label>
            <input type="datetime-local" id="start_datetime" name="start_datetime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-1">End Date & Time *</label>
            <input type="datetime-local" id="end_datetime" name="end_datetime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
            <textarea id="description" name="description" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"></textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold">Create Exam</button>
        </div>
    </form>
    <div id="exam-create-msg" class="mt-4"></div>
</div>
<script>
document.getElementById('create-exam-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const data = Object.fromEntries(new FormData(form).entries());
    fetch('/api/exam/createExam.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.getElementById('exam-create-msg');
        if (data.status === 'success') {
            msg.innerHTML = '<div class="text-green-600 font-semibold">Exam created successfully! Redirecting...</div>';
            setTimeout(() => { window.location.href = 'index.php?page=exams'; }, 1200);
        } else {
            msg.innerHTML = '<div class="text-red-600 font-semibold">' + (data.message || 'Failed to create exam.') + '</div>';
        }
    })
    .catch(() => {
        document.getElementById('exam-create-msg').innerHTML = '<div class="text-red-600 font-semibold">Network error. Please try again.</div>';
    });
});
</script> 