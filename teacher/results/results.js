// Teacher Results JS

document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/teacher/results')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('results-container');
            if (container) {
                container.innerHTML = '';
                data.forEach(result => {
                    const div = document.createElement('div');
                    div.className = 'bg-white rounded-lg shadow p-4 mb-4';
                    div.innerHTML = `<div class='font-semibold text-gray-800'>${result.student_name}</div><div class='text-gray-600'>Score: ${result.score}</div>`;
                    container.appendChild(div);
                });
            }
        })
        .catch(error => {
            showNotification('Failed to load results.', 'error');
        });
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

    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function (event) {
            event.preventDefault();
            fetchResults();
        });
    }
});

function fetchResults() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form)).toString();
    // Placeholder for backend API call
    fetch('/api/teacher/results?' + params)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            populateResults(data.results || []);
        })
        .catch(error => {
            showNotification('Failed to load results (placeholder)', 'error');
        });
}

function populateResults(results) {
    const table = document.getElementById('resultsTable');
    table.innerHTML = '';
    if (results.length === 0) {
        table.innerHTML = '<tr><td colspan="6" class="text-center py-4">No results found.</td></tr>';
        return;
    }
    results.forEach(result => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="py-2 px-4 border-b">${result.student}</td>
            <td class="py-2 px-4 border-b">${result.exam}</td>
            <td class="py-2 px-4 border-b">${result.category}</td>
            <td class="py-2 px-4 border-b">${result.date}</td>
            <td class="py-2 px-4 border-b">${result.score}</td>
            <td class="py-2 px-4 border-b">${result.passed ? 'Passed' : 'Failed'}</td>
        `;
        table.appendChild(row);
    });
}

function viewResultDetails(resultId) {
    const modal = document.getElementById('resultModal');
    const modalContent = document.getElementById('modalContent');

    // Show loading indicator in modal
    modal.classList.remove('hidden');
    modalContent.innerHTML = `
        <div class="flex justify-center items-center py-10">
            <i class="fas fa-spinner fa-spin mr-2 text-emerald-500 text-2xl"></i>
            <span class="text-gray-500">Loading result details...</span>
        </div>
    `;

    // Fetch result details from API
    fetch(`/api/results/getResultDetails.php?result_id=${resultId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to load result details');
            }

            // Get result and questions data
            const result = data.result;
            const questions = result.questions || [];

            // Determine if pass or fail
            const isPassed = result.score_percentage >= 50;
            const statusClass = isPassed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';

            // Build HTML for modal content
            let html = `
            <div class="mb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-1">${escapeHtml(result.exam_title)}</h4>
                <p class="text-sm text-gray-500">Completed on ${result.completed_at}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Student Information</h5>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p><span class="font-medium">Name:</span> ${escapeHtml(result.student_name)}</p>
                        <p><span class="font-medium">ID:</span> ${escapeHtml(result.index_number || '')}</p>
                        <p><span class="font-medium">Program:</span> ${escapeHtml(result.program_name)}</p>
                    </div>
                </div>
                
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Exam Information</h5>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p><span class="font-medium">Course:</span> ${escapeHtml(result.course_code)} - ${escapeHtml(result.course_title)}</p>
                        <p><span class="font-medium">Department:</span> ${escapeHtml(result.department_name)}</p>
                        <p><span class="font-medium">Exam Code:</span> ${escapeHtml(result.exam_code)}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg text-center mb-6">
                <p class="text-2xl font-bold ${isPassed ? 'text-green-700' : 'text-red-700'}">${result.score_percentage.toFixed(1)}%</p>
                <p class="text-gray-600">${result.correct_answers} correct out of ${result.total_questions} questions</p>
                <p class="mt-2">
                    <span class="px-3 py-1 rounded-full ${statusClass}">
                        ${isPassed ? 'PASSED' : 'FAILED'}
                    </span>
                </p>
            </div>
        `;

            // Add questions section if we have questions data
            if (questions.length > 0) {
                html += `
                <h5 class="font-medium text-gray-800 mb-3">Question Analysis</h5>
                <div class="space-y-4 mb-6">
            `;

                // Loop through questions
                questions.forEach((question, index) => {
                    const isCorrect = question.is_correct;
                    html += `
                    <div class="border rounded-lg overflow-hidden">
                        <div class="px-4 py-2 bg-gray-50 border-b">
                            <p class="font-medium">Question ${question.sequence_number || (index + 1)}</p>
                        </div>
                        <div class="p-4">
                            <p class="mb-3">${escapeHtml(question.question_text)}</p>
                            
                            <div class="mb-2 ${isCorrect ? 'bg-green-50 border-l-4 border-green-500' : 'bg-red-50 border-l-4 border-red-500'} p-2">
                                <p class="text-sm">
                                    <span class="font-medium">Your answer:</span> ${escapeHtml(question.student_answer)}
                                    ${isCorrect
                            ? '<span class="text-green-700 ml-2"><i class="fas fa-check"></i> Correct</span>'
                            : '<span class="text-red-700 ml-2"><i class="fas fa-times"></i> Incorrect</span>'
                        }
                                </p>
                            </div>
                            
                            ${!isCorrect ? `
                                <div class="bg-green-50 border-l-4 border-green-500 p-2">
                                    <p class="text-sm">
                                        <span class="font-medium">Correct answer:</span> ${escapeHtml(question.correct_answer)}
                                    </p>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                });

                html += '</div>';
            }

            // Add action buttons
            html += `
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <a 
                    href="/api/results/printResult.php?result_id=${resultId}" 
                    target="_blank" 
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center mr-3">
                    <i class="fas fa-print mr-2"></i>
                    Print Report
                </a>
                <button 
                    onclick="document.getElementById('resultModal').classList.add('hidden')" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition-colors duration-200">
                    Close
                </button>
            </div>
        `;

            // Update modal content
            modalContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = `
            <div class="text-center py-10">
                <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-3"></i>
                <p class="text-red-600 font-medium">Failed to load result details</p>
                <p class="text-gray-500 mt-1">${error.message || 'Please try again'}</p>
                <button 
                    onclick="document.getElementById('resultModal').classList.add('hidden')" 
                    class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition-colors duration-200">
                    Close
                </button>
            </div>
        `;
        });
}

function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\"/g, "&quot;")
        .replace(/'/g, "&#039;");
} 