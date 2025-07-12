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

// Fetch all exams for this teacher (for filter dropdown)
$stmt = $conn->prepare("
    SELECT e.exam_id, e.title, c.code as course_code
    FROM exams e
    JOIN courses c ON e.course_id = c.course_id
    WHERE e.teacher_id = :teacher_id
    ORDER BY e.created_at DESC
");
$stmt->execute(['teacher_id' => $teacher_id]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all departments (for filter dropdown)
$departments = $conn->query('SELECT department_id, name FROM departments ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

// Get filters from GET or default
$selected_exam = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
$selected_department = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;
$student_search = trim($_GET['student_search'] ?? '');

// Fetch results for the selected exam (with filters)
$results = [];
if ($selected_exam) {
    $query = "
        SELECT s.first_name, s.last_name, s.index_number, d.name as department, r.score_percentage, r.correct_answers, r.incorrect_answers, r.completed_at, r.result_id
        FROM results r
        JOIN exam_registrations er ON r.registration_id = er.registration_id
        JOIN students s ON er.student_id = s.student_id
        JOIN departments d ON s.department_id = d.department_id
        WHERE er.exam_id = :exam_id
    ";
    $params = ['exam_id' => $selected_exam];
    if ($selected_department) {
        $query .= " AND s.department_id = :department_id";
        $params['department_id'] = $selected_department;
    }
    if ($student_search) {
        $query .= " AND (s.first_name LIKE :search OR s.last_name LIKE :search OR s.index_number LIKE :search)";
        $params['search'] = "%$student_search%";
    }
    $query .= " ORDER BY r.completed_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6 text-gray-900">Exam Results</h1>
    <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
            <select name="exam_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">All Exams</option>
                <?php foreach ($exams as $exam): ?>
                    <option value="<?php echo $exam['exam_id']; ?>" <?php if ($selected_exam == $exam['exam_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($exam['course_code'] . ' - ' . $exam['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <select name="department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo $dept['department_id']; ?>" <?php if ($selected_department == $dept['department_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($dept['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Student Search</label>
            <input type="text" name="student_search" value="<?php echo htmlspecialchars($student_search); ?>" placeholder="Name or Index" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold">Filter</button>
        </div>
    </form>
    <div class="flex justify-end mb-4">
        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold" onclick="alert('Export to CSV coming soon!')">
            <i class="fas fa-file-csv mr-2"></i> Export CSV
        </button>
    </div>
    <?php if ($selected_exam && empty($results)): ?>
        <div class="text-gray-500 mb-4">No results found for the selected filters.</div>
    <?php elseif ($selected_exam): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Index/ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score (%)</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Incorrect</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed At</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($results as $res): ?>
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900"><?php echo htmlspecialchars($res['first_name'] . ' ' . $res['last_name']); ?></td>
                            <td class="px-4 py-2 text-sm text-gray-700"><?php echo htmlspecialchars($res['index_number']); ?></td>
                            <td class="px-4 py-2 text-sm text-gray-700"><?php echo htmlspecialchars($res['department']); ?></td>
                            <td class="px-4 py-2 text-sm text-gray-900 font-semibold"><?php echo $res['score_percentage'] !== null ? number_format($res['score_percentage'], 2) : '-'; ?></td>
                            <td class="px-4 py-2 text-sm text-green-700"><?php echo $res['correct_answers']; ?></td>
                            <td class="px-4 py-2 text-sm text-red-700"><?php echo $res['incorrect_answers']; ?></td>
                            <td class="px-4 py-2 text-sm text-gray-500"><?php echo $res['completed_at'] ? date('M d, Y H:i', strtotime($res['completed_at'])) : '-'; ?></td>
                            <td class="px-4 py-2 text-sm text-right">
                                <button class="text-blue-600 hover:text-blue-900 font-semibold" onclick="viewResultDetails(<?php echo $res['result_id']; ?>)">
                                    <i class="fas fa-eye mr-1"></i> View Details
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-gray-500 mb-4">Select an exam to view results.</div>
    <?php endif; ?>
</div>

<!-- Result Details Modal -->
<div id="resultModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-6 relative">
        <button onclick="document.getElementById('resultModal').classList.add('hidden')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
        <div id="modalContent">
            <!-- Details will be loaded here by JS -->
        </div>
    </div>
</div>
