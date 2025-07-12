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
$exam_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch exam details (only if owned by teacher)
$stmt = $conn->prepare('SELECT * FROM exams WHERE exam_id = :exam_id AND teacher_id = :teacher_id');
$stmt->execute(['exam_id' => $exam_id, 'teacher_id' => $teacher_id]);
$exam = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$exam) {
    echo '<div class="max-w-xl mx-auto mt-12 text-red-600 font-semibold">Exam not found or not owned by you.</div>';
    exit;
}

// Fetch all questions and options for this exam
$stmt = $conn->prepare('SELECT * FROM questions WHERE exam_id = :exam_id ORDER BY sequence_number ASC, question_id ASC');
$stmt->execute(['exam_id' => $exam_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all options for all questions
$optionsByQuestion = [];
if ($questions) {
    $questionIds = array_column($questions, 'question_id');
    $in = str_repeat('?,', count($questionIds) - 1) . '?';
    $stmt = $conn->prepare('SELECT * FROM choices WHERE question_id IN (' . $in . ')');
    $stmt->execute($questionIds);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $opt) {
        $optionsByQuestion[$opt['question_id']][] = $opt;
    }
}
?>
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6 text-gray-900">Manage Questions for: <span class="text-emerald-700"><?php echo htmlspecialchars($exam['title']); ?></span></h1>
    <button onclick="showAddQuestionForm()" class="mb-6 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold">Add New Question</button>
    <div id="add-question-form" class="hidden mb-8"></div>
    <div id="edit-question-form" class="hidden mb-8"></div>
    <div id="questions-list">
        <?php if (empty($questions)): ?>
            <div class="text-gray-500 mb-4">No questions added yet.</div>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 mb-8">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Options</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($questions as $i => $q): ?>
                        <tr id="question-row-<?php echo $q['question_id']; ?>">
                            <td class="px-4 py-2 text-sm text-gray-700"><?php echo $i + 1; ?></td>
                            <td class="px-4 py-2 text-sm text-gray-900"><?php echo htmlspecialchars($q['question_text']); ?></td>
                            <td class="px-4 py-2 text-sm">
                                <ul class="list-disc pl-5">
                                    <?php foreach ($optionsByQuestion[$q['question_id']] ?? [] as $opt): ?>
                                        <li class="<?php echo $opt['is_correct'] ? 'text-green-700 font-semibold' : 'text-gray-700'; ?>">
                                            <?php echo htmlspecialchars($opt['choice_text']); ?>
                                            <?php if ($opt['is_correct']): ?><span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Correct</span><?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td class="px-4 py-2 text-sm flex gap-2">
                                <button onclick="showEditQuestionForm(<?php echo $q['question_id']; ?>)" class="text-blue-600 hover:text-blue-900" title="Edit"><i class="fas fa-edit"></i></button>
                                <button onclick="deleteQuestion(<?php echo $q['question_id']; ?>)" class="text-red-600 hover:text-red-900" title="Delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <div id="question-msg" class="mt-4"></div>
</div>
<script>
const examId = <?php echo $exam_id; ?>;

function showAddQuestionForm() {
    document.getElementById('edit-question-form').classList.add('hidden');
    document.getElementById('add-question-form').innerHTML = getQuestionFormHTML();
    document.getElementById('add-question-form').classList.remove('hidden');
    bindQuestionForm('add');
}

function showEditQuestionForm(questionId) {
    fetch(`/api/exam/getQuestionWithOptions.php?id=${questionId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('add-question-form').classList.add('hidden');
                document.getElementById('edit-question-form').innerHTML = getQuestionFormHTML(data.question, data.options);
                document.getElementById('edit-question-form').classList.remove('hidden');
                bindQuestionForm('edit', questionId);
            } else {
                showMsg(data.message || 'Failed to load question.', 'red');
            }
        })
        .catch(() => showMsg('Network error. Please try again.', 'red'));
}

function getQuestionFormHTML(question = {}, options = []) {
    let opts = options.length ? options : [{choice_text: '', is_correct: 1}, {choice_text: '', is_correct: 0}, {choice_text: '', is_correct: 0}, {choice_text: '', is_correct: 0}];
    return `
        <form id="question-form" class="space-y-4 bg-gray-50 p-4 rounded-lg border">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Question Text *</label>
                <textarea name="question_text" required rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">${question.question_text || ''}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Options *</label>
                <div id="options-list">
                    ${opts.map((opt, i) => `
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" name="choice_text[]" value="${opt.choice_text || ''}" required class="flex-1 px-3 py-2 border border-gray-300 rounded focus:ring-emerald-500 focus:border-emerald-500">
                            <label class="flex items-center gap-1 text-xs">
                                <input type="radio" name="is_correct" value="${i}" ${opt.is_correct ? 'checked' : ''} required>
                                Correct
                            </label>
                            <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-700"><i class="fas fa-minus-circle"></i></button>
                        </div>
                    `).join('')}
                </div>
                <button type="button" onclick="addOption()" class="mt-2 text-emerald-600 hover:text-emerald-800"><i class="fas fa-plus-circle"></i> Add Option</button>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold">Save</button>
                <button type="button" onclick="cancelQuestionForm()" class="ml-2 px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    `;
}

function addOption() {
    const list = document.getElementById('options-list');
    const idx = list.children.length;
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="choice_text[]" required class="flex-1 px-3 py-2 border border-gray-300 rounded focus:ring-emerald-500 focus:border-emerald-500">
        <label class="flex items-center gap-1 text-xs">
            <input type="radio" name="is_correct" value="${idx}">
            Correct
        </label>
        <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-700"><i class="fas fa-minus-circle"></i></button>
    `;
    list.appendChild(div);
}
function removeOption(btn) {
    const row = btn.closest('div');
    row.remove();
}
function cancelQuestionForm() {
    document.getElementById('add-question-form').classList.add('hidden');
    document.getElementById('edit-question-form').classList.add('hidden');
}
function bindQuestionForm(mode, questionId = null) {
    document.getElementById('question-form').onsubmit = function(e) {
        e.preventDefault();
        const form = e.target;
        const data = Object.fromEntries(new FormData(form).entries());
        data.choice_text = Array.from(form.querySelectorAll('input[name="choice_text[]"]')).map(i => i.value);
        data.is_correct = form.querySelector('input[name="is_correct"]:checked')?.value;
        data.exam_id = examId;
        if (mode === 'edit') data.question_id = questionId;
        fetch(mode === 'add' ? '/api/exam/addQuestionWithOptions.php' : '/api/exam/editQuestionWithOptions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showMsg('Question saved successfully!', 'green');
                setTimeout(() => { window.location.reload(); }, 1000);
            } else {
                showMsg(data.message || 'Failed to save question.', 'red');
            }
        })
        .catch(() => showMsg('Network error. Please try again.', 'red'));
    };
}
function deleteQuestion(questionId) {
    if (!confirm('Are you sure you want to delete this question?')) return;
    fetch('/api/exam/deleteQuestion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ question_id: questionId, exam_id: examId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const row = document.getElementById('question-row-' + questionId);
            if (row) row.remove();
            showMsg('Question deleted successfully!', 'green');
        } else {
            showMsg(data.message || 'Failed to delete question.', 'red');
        }
    })
    .catch(() => showMsg('Network error. Please try again.', 'red'));
}
function showMsg(msg, color) {
    document.getElementById('question-msg').innerHTML = `<div class="text-${color}-600 font-semibold">${msg}</div>`;
}
</script> 