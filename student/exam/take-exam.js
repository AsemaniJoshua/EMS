// Student Exam JS - Complete implementation with initialization

let questions = [];
let currentQuestion = 0;
let answers = {};
let timerInterval;
let timeLeft = 0;
let examData = {};
let registrationId = 0;
let autoSaveInterval;

document.addEventListener('DOMContentLoaded', function () {
    // Check if exam data is available
    if (typeof window.examData !== 'undefined') {
        examData = window.examData;
        registrationId = examData.registration_id;
        initializeExam();
    } else {
        showNotification('Exam data not found', 'error');
    }
});

function initializeExam() {
    // Setup navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (prevBtn) prevBtn.onclick = showPrevQuestion;
    if (nextBtn) nextBtn.onclick = showNextQuestion;
    if (submitBtn) submitBtn.onclick = submitExam;
    
    // Load exam data
    loadExamData();
    
    // Setup auto-save every 30 seconds
    autoSaveInterval = setInterval(autoSaveProgress, 30000);
    
    // Setup keyboard shortcuts
    setupKeyboardShortcuts();
    
    // Prevent accidental navigation
    setupNavigationPrevention();
}

function loadExamData() {
    fetch('/api/student/getExamData.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            exam_id: examData.exam_id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            examData = { ...examData, ...data.exam };
            registrationId = data.registration_id;
            timeLeft = data.time_remaining;
            
            // Load questions
            loadQuestions();
            
            // Load previous progress if continuing
            loadExamProgress();
            
            // Start timer
            startTimer();
            
            // Update exam info display
            updateExamInfo();
        } else {
            showNotification(data.message, 'error');
            setTimeout(() => {
                window.location.href = '/student/exam/';
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error loading exam data:', error);
        showNotification('Failed to load exam data', 'error');
    });
}

function loadQuestions() {
    fetch('/api/student/getExamQuestions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            exam_id: examData.exam_id,
            registration_id: registrationId,
            continue_exam: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            questions = data.questions;
            answers = data.existing_answers || {};
            
            // Show first question
            showQuestion(0);
            
            // Update question navigation
            updateQuestionNavigation();
            
            // Hide loading
            hideLoading();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error loading questions:', error);
        showNotification('Failed to load questions', 'error');
    });
}

function loadExamProgress() {
    fetch('/api/student/getExamProgress.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            registration_id: registrationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.progress) {
            if (data.progress.current_question > 0) {
                currentQuestion = data.progress.current_question;
            }
            if (data.progress.time_remaining > 0) {
                timeLeft = Math.min(timeLeft, data.progress.time_remaining);
            }
        }
    })
    .catch(error => {
        console.error('Error loading progress:', error);
    });
}

function hideLoading() {
    const loadingContainer = document.querySelector('#questionContainer .text-center');
    if (loadingContainer) {
        loadingContainer.style.display = 'none';
    }
}

function showQuestion(index) {
    if (index < 0 || index >= questions.length) return;
    
    currentQuestion = index;
    const q = questions[index];
    const container = document.getElementById('questionContainer');
    
    let html = `
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Question ${index + 1} of ${questions.length}</h2>
                <div class="text-sm text-gray-600">
                    ${getAnsweredCount()} of ${questions.length} answered
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <p class="text-gray-800 font-medium">${q.question_text}</p>
            </div>
            <div class="space-y-3">
    `;
    
       q.choices.forEach((choice, i) => {
        const isSelected = answers[index] == choice.choice_id;
        html += `
            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 ${isSelected ? 'bg-blue-50 border-blue-300' : 'border-gray-200'}">
                <input type="radio" 
                       name="answer" 
                       value="${choice.choice_id}" 
                       ${isSelected ? 'checked' : ''} 
                       class="mr-3 text-blue-600 focus:ring-blue-500">
                <span class="text-gray-800">${choice.choice_text}</span>
            </label>
        `;
    });
    
    html += `
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Add event listeners for answer selection
    document.querySelectorAll('input[name="answer"]').forEach(radio => {
        radio.addEventListener('change', function() {
            saveAnswer(index, parseInt(this.value));
        });
    });
    
    // Update navigation buttons
    updateNavigationButtons();
}

function saveAnswer(questionIndex, choiceId) {
    answers[questionIndex] = choiceId;
    
    // Save to backend
    fetch('/api/student/saveExamAnswer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            registration_id: registrationId,
            question_id: questions[questionIndex].question_id,
            choice_id: choiceId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to save answer:', data.message);
        }
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
    
    // Update question navigation
    updateQuestionNavigation();
}

function showPrevQuestion() {
    if (currentQuestion > 0) {
        showQuestion(currentQuestion - 1);
    }
}

function showNextQuestion() {
    if (currentQuestion < questions.length - 1) {
        showQuestion(currentQuestion + 1);
    }
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (prevBtn) {
        prevBtn.disabled = currentQuestion === 0;
        prevBtn.className = currentQuestion === 0 
            ? 'px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed'
            : 'px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors';
    }
    
    if (nextBtn) {
        nextBtn.disabled = currentQuestion === questions.length - 1;
        nextBtn.className = currentQuestion === questions.length - 1
            ? 'px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed'
            : 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors';
    }
    
    // Show submit button on last question
    if (submitBtn) {
        if (currentQuestion === questions.length - 1) {
            submitBtn.style.display = 'inline-block';
        } else {
            submitBtn.style.display = 'none';
        }
    }
}

function updateQuestionNavigation() {
    const navContainer = document.getElementById('questionNavigation');
    if (!navContainer) return;
    
    let html = '<div class="flex flex-wrap gap-2 mb-4">';
    
    questions.forEach((q, index) => {
        const isAnswered = answers.hasOwnProperty(index);
        const isCurrent = index === currentQuestion;
        
        let buttonClass = 'w-10 h-10 rounded-lg border-2 flex items-center justify-center text-sm font-medium transition-colors cursor-pointer ';
        
        if (isCurrent) {
            buttonClass += 'bg-blue-600 text-white border-blue-600';
        } else if (isAnswered) {
            buttonClass += 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200';
        } else {
            buttonClass += 'bg-gray-100 text-gray-600 border-gray-300 hover:bg-gray-200';
        }
        
        html += `
            <button class="${buttonClass}" onclick="showQuestion(${index})">
                ${index + 1}
            </button>
        `;
    });
    
    html += '</div>';
    navContainer.innerHTML = html;
}

function getAnsweredCount() {
    return Object.keys(answers).length;
}

function startTimer() {
    updateTimerDisplay();
    timerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        
        // Auto-save progress every minute
        if (timeLeft % 60 === 0) {
            autoSaveProgress();
        }
        
        // Warning when 5 minutes left
        if (timeLeft === 300) {
            showNotification('5 minutes remaining!', 'warning');
        }
        
        // Warning when 1 minute left
        if (timeLeft === 60) {
            showNotification('1 minute remaining!', 'warning');
        }
        
        // Auto-submit when time is up
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            showNotification('Time is up! Submitting exam...', 'info');
            setTimeout(() => {
                submitExam();
            }, 2000);
        }
    }, 1000);
}

function updateTimerDisplay() {
    const hours = Math.floor(timeLeft / 3600);
    const minutes = Math.floor((timeLeft % 3600) / 60);
    const seconds = timeLeft % 60;
    
    const timerElement = document.getElementById('timer');
    if (timerElement) {
        let timeString = '';
        if (hours > 0) {
            timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        } else {
            timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        timerElement.textContent = timeString;
        
        // Change color when time is running low
        if (timeLeft <= 300) { // 5 minutes
            timerElement.className = 'text-red-600 font-bold text-lg';
        } else if (timeLeft <= 600) { // 10 minutes
            timerElement.className = 'text-yellow-600 font-bold text-lg';
        } else {
            timerElement.className = 'text-green-600 font-bold text-lg';
        }
    }
}

function updateExamInfo() {
    const examTitleElement = document.getElementById('examTitle');
    const examInfoElement = document.getElementById('examInfo');
    
    if (examTitleElement) {
        examTitleElement.textContent = examData.title;
    }
    
    if (examInfoElement) {
        examInfoElement.innerHTML = `
            <div class="text-sm text-gray-600">
                <span class="mr-4">Duration: ${examData.duration_minutes} minutes</span>
                <span class="mr-4">Questions: ${questions.length}</span>
                <span>Pass Mark: ${examData.pass_mark}%</span>
            </div>
        `;
    }
}

function autoSaveProgress() {
    if (registrationId > 0) {
        fetch('/api/student/saveExamProgress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                registration_id: registrationId,
                current_question: currentQuestion,
                time_remaining: timeLeft,
                answers: answers
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSaveIndicator();
            }
        })
        .catch(error => {
            console.error('Auto-save failed:', error);
        });
    }
}

function showSaveIndicator() {
    const indicator = document.getElementById('saveIndicator');
    if (indicator) {
        indicator.textContent = 'Saved';
        indicator.className = 'text-green-600 text-sm';
        setTimeout(() => {
            indicator.textContent = '';
        }, 2000);
    }
}

function submitExam() {
    // Confirm submission
    const unansweredCount = questions.length - getAnsweredCount();
    let confirmMessage = 'Are you sure you want to submit your exam?';
    
    if (unansweredCount > 0) {
        confirmMessage += `\n\nYou have ${unansweredCount} unanswered question(s).`;
    }
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    // Disable all buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (prevBtn) prevBtn.disabled = true;
    if (nextBtn) nextBtn.disabled = true;
    if (submitBtn) submitBtn.disabled = true;
    
    // Clear intervals
    clearInterval(timerInterval);
    clearInterval(autoSaveInterval);
    
    // Show loading overlay
    showLoadingOverlay();
    
    // Submit to backend
    fetch('/api/student/submitExam.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            registration_id: registrationId,
            answers: answers,
            time_taken: (examData.duration_minutes * 60) - timeLeft
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingOverlay();
        
        if (data.success) {
            showNotification('Exam submitted successfully!', 'success');
            
            // Redirect to results page after 2 seconds
            setTimeout(() => {
                window.location.href = `/student/results/view.php?result_id=${data.result.result_id}`;
            }, 2000);
        } else {
            showNotification(data.message, 'error');
            // Re-enable buttons if submission failed
            if (prevBtn) prevBtn.disabled = false;
            if (nextBtn) nextBtn.disabled = false;
            if (submitBtn) submitBtn.disabled = false;
        }
    })
    .catch(error => {
        hideLoadingOverlay();
        console.error('Submission error:', error);
        showNotification('Failed to submit exam. Please try again.', 'error');
        // Re-enable buttons
        if (prevBtn) prevBtn.disabled = false;
        if (nextBtn) nextBtn.disabled = false;
        if (submitBtn) submitBtn.disabled = false;
    });
}

function showLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
    }
}

function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Prevent F5 refresh
        if (e.key === 'F5') {
            e.preventDefault();
            showNotification('Page refresh is disabled during exam', 'warning');
        }
        
        // Arrow key navigation
        if (e.key === 'ArrowLeft' && currentQuestion > 0) {
            showPrevQuestion();
        } else if (e.key === 'ArrowRight' && currentQuestion < questions.length - 1) {
            showNextQuestion();
        }
        
        // Number key navigation (1-9)
        if (e.key >= '1' && e.key <= '9') {
            const questionIndex = parseInt(e.key) - 1;
            if (questionIndex < questions.length) {
                showQuestion(questionIndex);
            }
        }
        
        // Ctrl+Enter to submit
        if (e.ctrlKey && e.key === 'Enter') {
            submitExam();
        }
    });
}

function setupNavigationPrevention() {
    // Prevent accidental navigation
    window.addEventListener('beforeunload', function(e) {
        if (registrationId > 0) {
            e.preventDefault();
            e.returnValue = 'Are you sure you want to leave? Your progress will be saved but you may lose time.';
            
            // Save progress before leaving
            autoSaveProgress();
        }
    });
    
    // Prevent right-click context menu
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        showNotification('Right-click is disabled during exam', 'warning');
    });
    
    // Prevent text selection (optional anti-cheating measure)
    if (examData.anti_cheating) {
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
        });
        
        // Disable copy/paste
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'a')) {
                e.preventDefault();
                showNotification('Copy/paste is disabled during exam', 'warning');
            }
        });
    }
}

function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500',
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type] || colors.info} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Export functions for global access
window.showQuestion = showQuestion;
window.showNotification = showNotification;
window.submitExam = submitExam;


