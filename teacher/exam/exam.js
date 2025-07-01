// Teacher Exam Management JS

document.addEventListener('DOMContentLoaded', function () {
    fetchQuestions();
    document.getElementById('questionForm').onsubmit = addQuestion;
});

function fetchQuestions() {
    // Placeholder for backend API call
    fetch('/api/teacher/questions')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            populateQuestions(data.questions || []);
        })
        .catch(error => {
            showNotification('Failed to load questions (placeholder)', 'error');
        });
}

function populateQuestions(questions) {
    const list = document.getElementById('questionsList');
    list.innerHTML = '';
    if (questions.length === 0) {
        list.innerHTML = '<li class="text-gray-500">No questions added yet.</li>';
        return;
    }
    questions.forEach((q, idx) => {
        const li = document.createElement('li');
        li.className = 'bg-gray-100 p-3 rounded flex justify-between items-center';
        li.innerHTML = `<span>${q.text}</span><button onclick="editQuestion(${idx})" class="text-blue-500 hover:underline">Edit</button>`;
        list.appendChild(li);
    });
}

function addQuestion(e) {
    e.preventDefault();
    const form = e.target;
    const question = form.question.value;
    const choices = [form.choice1.value, form.choice2.value, form.choice3.value, form.choice4.value];
    const correct = form.correct.value;
    // Placeholder for backend API call
    fetch('/api/teacher/questions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ question, choices, correct })
    })
    .then(response => {
        if (!response.ok) throw new Error('Add failed');
        return response.json();
    })
    .then(data => {
        showNotification('Question added! (placeholder)', 'success');
        fetchQuestions();
        form.reset();
    })
    .catch(error => {
        showNotification('Failed to add question (placeholder)', 'error');
    });
}

function editQuestion(idx) {
    // Placeholder for edit logic
    showNotification('Edit question feature coming soon!', 'info');
}

function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
    };
    const toast = document.createElement('div');
    toast.className = `fixed top-5 right-5 px-4 py-2 rounded shadow text-white z-50 ${colors[type] || colors.info}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
} 