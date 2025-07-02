// Teacher Exam Management JS

document.addEventListener('DOMContentLoaded', function () {
    // --- Dynamic Question and Choice Management ---
    let questionCounter = 0; // This will track the highest ID assigned, not the current count
    const questionsContainer = document.getElementById('questions-container');
    const addQuestionBtn = document.getElementById('add-question-btn');

    function updateQuestionNumbers() {
        const questionBlocks = questionsContainer.querySelectorAll('.bg-gray-50.p-4');
        questionBlocks.forEach((qBlock, index) => {
            const newQuestionNumber = index + 1;
            qBlock.querySelector('h4').textContent = `Question ${newQuestionNumber}`;
            const questionTextarea = qBlock.querySelector('textarea[name^="question_text_"]');
            if (questionTextarea) {
                questionTextarea.id = `question-text-${newQuestionNumber}`;
                questionTextarea.name = `question_text_${newQuestionNumber}`;
            }
            qBlock.querySelectorAll('input[type="radio"][name^="correct_choice_"]').forEach((radioInput, choiceIndex) => {
                radioInput.id = `choice-${newQuestionNumber}-${choiceIndex + 1}`;
                radioInput.name = `correct_choice_${newQuestionNumber}`;
                const label = qBlock.querySelector(`label[for="choice-${newQuestionNumber}-${choiceIndex + 1}"]`);
                if (label) {
                    label.setAttribute('for', `choice-${newQuestionNumber}-${choiceIndex + 1}`);
                }
            });
            qBlock.querySelectorAll('input[type="text"][name^="choice_text_"]').forEach((choiceTextInput, choiceIndex) => {
                choiceTextInput.id = `choice-text-${newQuestionNumber}-${choiceIndex + 1}`;
                choiceTextInput.name = `choice_text_${newQuestionNumber}_${choiceIndex + 1}`;
            });
        });
    }

    function addQuestionBlock() {
        const uniqueId = Date.now();
        const questionBlock = document.createElement('div');
        questionBlock.id = `question-block-${uniqueId}`;
        questionBlock.classList.add('bg-gray-50', 'p-4', 'rounded-lg', 'border', 'border-gray-200', 'space-y-4');
        questionBlock.innerHTML = `
            <div class="flex justify-between items-center">
                <h4 class="text-lg font-semibold text-gray-800">Question X</h4>
                <button type="button" class="remove-question-btn text-red-500 hover:text-red-700 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div>
                <label for="question-text-${uniqueId}" class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                <textarea id="question-text-${uniqueId}" name="question_text_${uniqueId}" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                          placeholder="Enter your question here"></textarea>
            </div>
            <div class="space-y-2">
                <p class="text-sm font-medium text-gray-700">Choices (Select one correct answer):</p>
                ${Array.from({ length: 4 }).map((_, i) => `
                    <div class="flex items-center">
                        <input type="radio" name="correct_choice_${uniqueId}" id="choice-${uniqueId}-${i + 1}" value="${i + 1}" class="mr-2">
                        <input type="text" name="choice_text_${uniqueId}_${i + 1}" id="choice-text-${uniqueId}-${i + 1}" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" placeholder="Choice ${i + 1}">
                    </div>
                `).join('')}
            </div>
        `;
        questionsContainer.appendChild(questionBlock);
        updateQuestionNumbers();
        questionBlock.querySelector('.remove-question-btn').addEventListener('click', function () {
            questionBlock.remove();
            updateQuestionNumbers();
        });
    }

    if (addQuestionBtn) {
        addQuestionBtn.addEventListener('click', addQuestionBlock);
    }

    // --- Form Submission for Exam Creation ---
    const createExamForm = document.getElementById('create-exam-form');
    if (createExamForm) {
        createExamForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(createExamForm);
            // Collect questions and choices
            const questions = [];
            questionsContainer.querySelectorAll('.bg-gray-50.p-4').forEach((qBlock, index) => {
                const questionText = qBlock.querySelector('textarea').value;
                const choices = Array.from(qBlock.querySelectorAll('input[type="text"]')).map(input => input.value);
                const correctChoice = Array.from(qBlock.querySelectorAll('input[type="radio"]')).find(radio => radio.checked)?.value;
                questions.push({ questionText, choices, correctChoice });
            });
            // Prepare payload
            const payload = Object.fromEntries(formData.entries());
            payload.questions = questions;
            fetch('/api/teacher/exam/create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(response => response.json())
                .then(data => {
                    showNotification('Exam created successfully!', 'success');
                })
                .catch(error => {
                    showNotification('Failed to create exam.', 'error');
                });
        });
    }

    // --- Submit Exam for Approval Button ---
    const submitExamForApprovalBtn = document.getElementById('submit-exam-for-approval-btn');
    if (submitExamForApprovalBtn) {
        submitExamForApprovalBtn.addEventListener('click', function () {
            // Implement API call for submitting exam for approval
            showNotification('Exam submitted for approval (placeholder)', 'info');
        });
    }

    // --- Load My Exams Table from Backend ---
    const myExamsTableBody = document.getElementById('my-exams-table-body');
    if (myExamsTableBody) {
        fetch('/api/teacher/exam/list')
            .then(response => response.json())
            .then(data => {
                myExamsTableBody.innerHTML = '';
                data.forEach(exam => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${exam.title}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${exam.course}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${exam.duration} mins</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${exam.status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'}">
                                ${exam.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="index.php?page=exams" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                            <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                            <a href="index.php?page=exams" class="text-emerald-600 hover:text-emerald-900 ml-4">View Questions</a>
                        </td>
                    `;
                    myExamsTableBody.appendChild(row);
                });
            })
            .catch(error => {
                showNotification('Failed to load exams.', 'error');
            });
    }

    // --- Notification Toast ---
    function showNotification(message, type = 'info') {
        let notification = document.getElementById('notification-toast');
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'notification-toast';
            document.body.appendChild(notification);
        }
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
        };
        notification.className = `fixed top-5 right-5 px-6 py-3 rounded shadow-lg text-white text-base font-semibold z-50 ${colors[type] || colors.info}`;
        notification.textContent = message;
        notification.style.display = 'block';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }
}); 