<?php
// teacher/exam/index.php
// This file contains the main content for the Teacher Exam Management section.
// It is designed to be included by teacher/index.php.
?>

<h1 class="text-3xl font-extrabold text-gray-900 mb-6">Exam Management</h1>

<!-- Create New Exam Section -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Create New Exam</h3>
    <form id="create-exam-form" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
        <div>
            <label for="exam-title" class="block text-sm font-medium text-gray-700 mb-1">Exam Title</label>
            <input type="text" id="exam-title" name="exam_title" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="exam-code" class="block text-sm font-medium text-gray-700 mb-1">Exam Code</label>
            <input type="text" id="exam-code" name="exam_code" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="course-id" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
            <select id="course-id" name="course_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                <option value="">Select Course</option>
                <option value="1">CS101 - Introduction to Algorithms</option>
                <option value="2">CS102 - Data Structures Fundamentals</option>
                <option value="3">MA201 - Linear Algebra Basics</option>
                <!-- Dynamically load from backend -->
            </select>
        </div>
        <div>
            <label for="duration-minutes" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
            <input type="number" id="duration-minutes" name="duration_minutes" required min="1"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="start-datetime" class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
            <input type="datetime-local" id="start-datetime" name="start_datetime" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="end-datetime" class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
            <input type="datetime-local" id="end-datetime" name="end_datetime" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div class="md:col-span-2 flex justify-end">
            <button type="submit" class="px-6 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors duration-200 shadow-md">
                Save Exam Details
            </button>
        </div>
    </form>
</div>

<!-- Question & Choices Management Section -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Add Questions to Exam</h3>
    <p class="text-sm text-gray-600 mb-4">Select an exam first to add questions.</p>
    <div id="questions-container" class="space-y-6">
        <!-- Question blocks will be added here by JavaScript -->
    </div>
    <button id="add-question-btn" class="mt-6 px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors duration-200 shadow-md flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Question
    </button>
    <button id="submit-exam-for-approval-btn" class="mt-6 ml-4 px-6 py-2 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700 transition-colors duration-200 shadow-md flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11"></polyline>
        </svg>
        Submit Exam for Approval
    </button>
</div>

<!-- My Exams Table -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
    <h3 class="text-xl font-bold text-gray-900 mb-4">My Exams</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">Exam Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="my-exams-table-body">
                <!-- Example rows (will be dynamically loaded) -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Intro to Web Dev</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">WD101</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">45 mins</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                            Pending
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="index.php?page=exams" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                        <a href="index.php?page=exams" class="text-emerald-600 hover:text-emerald-900 ml-4">View Questions</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Database Systems</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">DB201</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">60 mins</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Approved
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="index.php?page=exams" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                        <a href="index.php?page=exams" class="text-emerald-600 hover:text-emerald-900 ml-4">View Questions</a>
                    </td>
                </tr>
                <!-- More rows can be added here dynamically -->
            </tbody>
        </table>
    </div>
</div>

<script>
    // --- Dynamic Question and Choice Management ---
    let questionCounter = 0; // This will track the highest ID assigned, not the current count
    const questionsContainer = document.getElementById('questions-container');
    const addQuestionBtn = document.getElementById('add-question-btn');

    // Function to re-number all questions and update their IDs/names
    function updateQuestionNumbers() {
        const questionBlocks = questionsContainer.querySelectorAll('.bg-gray-50.p-4');
        questionBlocks.forEach((qBlock, index) => {
            const newQuestionNumber = index + 1;

            // Update question heading
            qBlock.querySelector('h4').textContent = `Question ${newQuestionNumber}`;

            // Update question textarea IDs and names
            const questionTextarea = qBlock.querySelector('textarea[name^="question_text_"]');
            if (questionTextarea) {
                questionTextarea.id = `question-text-${newQuestionNumber}`;
                questionTextarea.name = `question_text_${newQuestionNumber}`;
            }

            // Update choices IDs and names
            qBlock.querySelectorAll('input[type="radio"][name^="correct_choice_"]').forEach((radioInput, choiceIndex) => {
                radioInput.id = `choice-${newQuestionNumber}-${choiceIndex + 1}`;
                radioInput.name = `correct_choice_${newQuestionNumber}`; // All radios for one question share the same name
                // Update label's for attribute if it's not sr-only
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
        // Use a unique ID for the question block itself, not necessarily sequential
        // The numbering in the UI will be handled by updateQuestionNumbers
        const uniqueId = Date.now(); // A simple way to get a unique ID for the block
        const questionBlock = document.createElement('div');
        questionBlock.id = `question-block-${uniqueId}`; // Use unique ID for the block
        questionBlock.classList.add('bg-gray-50', 'p-4', 'rounded-lg', 'border', 'border-gray-200', 'space-y-4');

        // The inner HTML uses placeholders for numbering, which will be updated by updateQuestionNumbers
        questionBlock.innerHTML = `
            <div class="flex justify-between items-center">
                <h4 class="text-lg font-semibold text-gray-800">Question X</h4> <!-- Placeholder 'X' -->
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
                        <input type="radio" id="choice-${uniqueId}-${i + 1}" name="correct_choice_${uniqueId}" value="${i + 1}" class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        <label for="choice-${uniqueId}-${i + 1}" class="sr-only">Choice ${i + 1}</label>
                        <input type="text" id="choice-text-${uniqueId}-${i + 1}" name="choice_text_${uniqueId}_${i + 1}" required
                               class="ml-2 flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                               placeholder="Choice ${i + 1} text">
                    </div>
                `).join('')}
            </div>
        `;
        questionsContainer.appendChild(questionBlock);

        // Add event listener to the new remove button
        questionBlock.querySelector('.remove-question-btn').addEventListener('click', function() {
            questionBlock.remove();
            updateQuestionNumbers(); // Re-number after removal
        });

        updateQuestionNumbers(); // Re-number after adding
    }

    addQuestionBtn.addEventListener('click', addQuestionBlock);

    // --- Form Submission Logic (for demonstration) ---
    document.getElementById('create-exam-form').addEventListener('submit', function(event) {
        event.preventDefault();
        console.log('Exam details saved!');
        // In a real app, send data to backend via fetch()
        alert('Exam details saved! You can now add questions below.');
        // You might want to disable the exam details form or show a success message
    });

    document.getElementById('submit-exam-for-approval-btn').addEventListener('click', function() {
        // Collect all exam and question data here
        const examData = {
            title: document.getElementById('exam-title').value,
            code: document.getElementById('exam-code').value,
            course_id: document.getElementById('course-id').value,
            duration_minutes: document.getElementById('duration-minutes').value,
            start_datetime: document.getElementById('start-datetime').value,
            end_datetime: document.getElementById('end-datetime').value,
            questions: []
        };

        questionsContainer.querySelectorAll('.bg-gray-50.p-4').forEach((qBlock, index) => {
            const questionTextarea = qBlock.querySelector('textarea[name^="question_text_"]');
            const questionText = questionTextarea ? questionTextarea.value : '';

            const choices = [];
            qBlock.querySelectorAll('input[type="text"][name^="choice_text_"]').forEach((choiceInput) => {
                // Extract the unique ID from the choiceInput's name attribute
                const nameParts = choiceInput.name.split('_');
                const uniqueBlockId = nameParts[nameParts.length - 2]; // e.g., "uniqueId" from "choice_text_uniqueId_1"
                const choiceIndex = nameParts[nameParts.length - 1]; // e.g., "1" from "choice_text_uniqueId_1"


                if (uniqueBlockId && choiceIndex) {
                    const radioInput = qBlock.querySelector(`input[type="radio"][name="correct_choice_${uniqueBlockId}"][value="${choiceIndex}"]`);
                    choices.push({
                        text: choiceInput.value,
                        is_correct: radioInput ? radioInput.checked : false
                    });
                }
            });

            examData.questions.push({
                question_text: questionText,
                choices: choices
            });
        });

        console.log('Exam Data for Approval:', examData);
        // In a real app, send this consolidated data to the backend
        alert('Exam submitted for approval! Admin will review it.');
    });

    // Initial question block on load for immediate use
    addQuestionBlock(); // Add one question block when the page loads
</script>
