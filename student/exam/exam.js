// Student Exam JS

let questions = [];
let currentQuestion = 0;
let answers = {};
let timerInterval;
let timeLeft = 0;

document.addEventListener('DOMContentLoaded', function () {
    fetchQuestions();
    document.getElementById('prevBtn').onclick = showPrevQuestion;
    document.getElementById('nextBtn').onclick = showNextQuestion;
    document.getElementById('examForm').onsubmit = submitExam;
});

function fetchQuestions() {
    // Placeholder for backend API call
    fetch('/api/student/exam-questions')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            questions = data.questions || [];
            timeLeft = data.duration || 600; // seconds
            startTimer();
            showQuestion(0);
        })
        .catch(error => {
            showNotification('Failed to load questions (placeholder)', 'error');
        });
}

function showQuestion(index) {
    if (index < 0 || index >= questions.length) return;
    currentQuestion = index;
    const q = questions[index];
    const container = document.getElementById('questionContainer');
    let html = `<div class="mb-4 font-semibold">Q${index + 1}: ${q.text}</div>`;
    q.choices.forEach((choice, i) => {
        html += `<div class="mb-2"><label class="inline-flex items-center"><input type="radio" name="answer" value="${i}" ${answers[index] == i ? 'checked' : ''} class="form-radio text-blue-600"> <span class="ml-2">${choice}</span></label></div>`;
    });
    container.innerHTML = html;
    // Restore previous answer
    document.querySelectorAll('input[name=answer]').forEach(radio => {
        radio.onclick = () => { answers[index] = parseInt(radio.value); };
    });
}

function showPrevQuestion() {
    if (currentQuestion > 0) showQuestion(currentQuestion - 1);
}

function showNextQuestion() {
    if (currentQuestion < questions.length - 1) showQuestion(currentQuestion + 1);
}

function startTimer() {
    updateTimerDisplay();
    timerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            submitExam();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const min = String(Math.floor(timeLeft / 60)).padStart(2, '0');
    const sec = String(timeLeft % 60).padStart(2, '0');
    document.getElementById('timer').textContent = `Time: ${min}:${sec}`;
}

function submitExam(e) {
    if (e) e.preventDefault();
    clearInterval(timerInterval);
    // Placeholder for backend API call
    fetch('/api/student/submit-exam', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ answers })
    })
    .then(response => {
        if (!response.ok) throw new Error('Submission failed');
        return response.json();
    })
    .then(data => {
        showNotification('Exam submitted! (placeholder)', 'success');
        // TODO: Redirect to results or summary
    })
    .catch(error => {
        showNotification('Exam submission failed (placeholder)', 'error');
    });
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