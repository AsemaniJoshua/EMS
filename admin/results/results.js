// Admin Results JS
document.addEventListener('DOMContentLoaded', function () {
    // Initialize variables for pagination
    let currentPage = 1;
    const resultsPerPage = 50;
    let totalResults = 0;

    // Load results on page load
    fetchResults();

    // Set up event listeners
    document.getElementById('filterButton').addEventListener('click', function () {
        currentPage = 1; // Reset to first page when applying new filters
        fetchResults();
    });

    document.getElementById('exportResultsBtn').addEventListener('click', exportResults);
    document.getElementById('generateReportBtn').addEventListener('click', generateReport);
    document.getElementById('prevPage').addEventListener('click', function () {
        if (currentPage > 1) {
            currentPage--;
            fetchResults();
        }
    });
    document.getElementById('nextPage').addEventListener('click', function () {
        currentPage++;
        fetchResults();
    });

    // Close modal when clicking the close button
    document.getElementById('closeModal').addEventListener('click', function () {
        document.getElementById('resultModal').classList.add('hidden');
    });

    // Also close modal when clicking outside of it
    document.getElementById('resultModal').addEventListener('click', function (event) {
        if (event.target === this) {
            this.classList.add('hidden');
        }
    });

    // Set up cascading dropdown filters (department -> program -> course)
    document.getElementById('filterDepartment').addEventListener('change', function () {
        const departmentId = this.value;
        const programSelect = document.getElementById('filterProgram');
        const courseSelect = document.getElementById('filterCourse');

        // Reset program and course dropdowns
        programSelect.innerHTML = '<option value="">All Programs</option>';
        courseSelect.innerHTML = '<option value="">All Courses</option>';

        if (departmentId) {
            // Fetch programs for the selected department
            programSelect.disabled = true;

            axios.get('../../api/exams/getProgramsByDepartment.php', {
                params: { departmentId: departmentId }
            })
                .then(response => {
                    if (response.data.success) {
                        const programs = response.data.programs;
                        programs.forEach(program => {
                            const option = document.createElement('option');
                            option.value = program.program_id;
                            option.textContent = program.name;
                            programSelect.appendChild(option);
                        });
                    }
                    programSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching programs:', error);
                    showNotification('Failed to load programs', 'error');
                    programSelect.disabled = false;
                });
        }
    });

    document.getElementById('filterProgram').addEventListener('change', function () {
        const programId = this.value;
        const departmentId = document.getElementById('filterDepartment').value;
        const courseSelect = document.getElementById('filterCourse');

        // Reset course dropdown
        courseSelect.innerHTML = '<option value="">All Courses</option>';

        if (programId) {
            // Fetch courses for the selected program
            courseSelect.disabled = true;

            axios.get('../../api/exams/getCoursesByProgram.php', {
                params: {
                    programId: programId,
                    departmentId: departmentId
                }
            })
                .then(response => {
                    if (response.data.success) {
                        const courses = response.data.courses;
                        courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.course_id;
                            option.textContent = `${course.code} - ${course.title}`;
                            courseSelect.appendChild(option);
                        });
                    }
                    courseSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching courses:', error);
                    showNotification('Failed to load courses', 'error');
                    courseSelect.disabled = false;
                });
        }
    });
});

/**
 * Fetches results from the server based on current filters and pagination
 */
function fetchResults() {
    const resultsTable = document.getElementById('resultsTable');
    resultsTable.innerHTML = `
        <tr>
            <td colspan="8" class="px-6 py-4 text-center">
                <div class="flex justify-center items-center">
                    <i class="fas fa-spinner fa-spin mr-2 text-emerald-500"></i>
                    <span class="text-gray-500">Loading results...</span>
                </div>
            </td>
        </tr>
    `;

    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    formData.append('page', currentPage);
    formData.append('resultsPerPage', resultsPerPage);

    // Send fetch request
    fetch('../../api/results/getResults.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                populateResults(data.results);
                updatePagination(data.pagination);
            } else {
                showNotification(data.message || 'Failed to load results', 'error');
                resultsTable.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error: ${data.message || 'Failed to load results'}
                    </td>
                </tr>
            `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load results', 'error');
            resultsTable.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Failed to load results. Please try again.
                </td>
            </tr>
        `;
        });
}

/**
 * Populates the results table with data from the server
 */
function populateResults(results) {
    const resultsTable = document.getElementById('resultsTable');

    // Clear the table
    resultsTable.innerHTML = '';

    if (!results || results.length === 0) {
        resultsTable.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                    <i class="fas fa-search mr-2"></i>
                    No results found matching your criteria.
                </td>
            </tr>
        `;
        return;
    }

    // Populate with results
    results.forEach(result => {
        const row = document.createElement('tr');

        // Determine the status class for coloring
        const statusClass = result.score_percentage >= 50
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800';

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(result.student_name)}</div>
                </div>
                <div class="text-xs text-gray-500">${escapeHtml(result.index_number || '')}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${escapeHtml(result.exam_title)}</div>
                <div class="text-xs text-gray-500">Code: ${escapeHtml(result.exam_code)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${escapeHtml(result.course_code || '')}</div>
                <div class="text-xs text-gray-500">${escapeHtml(result.course_title || '')}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${result.completed_at}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                ${result.score_percentage.toFixed(1)}%
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                ${result.correct_answers} / ${result.total_questions}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                    ${result.score_percentage >= 50 ? 'Passed' : 'Failed'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button 
                    onclick="viewResultDetails(${result.result_id})" 
                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                    <i class="fas fa-eye mr-1"></i> View
                </button>
                <a 
                    href="../../api/results/printResult.php?result_id=${result.result_id}" 
                    target="_blank" 
                    class="text-green-600 hover:text-green-900">
                    <i class="fas fa-print mr-1"></i> Print
                </a>
            </td>
        `;
        resultsTable.appendChild(row);
    });
}

/**
 * Updates pagination controls
 */
function updatePagination(pagination) {
    if (!pagination) return;

    const { currentPage, totalPages, totalResults, firstResult, lastResult } = pagination;

    // Update page info
    document.getElementById('firstResult').textContent = firstResult;
    document.getElementById('lastResult').textContent = lastResult;
    document.getElementById('totalResults').textContent = totalResults;
    document.getElementById('resultCount').textContent = `${totalResults} results`;

    // Update button states
    document.getElementById('prevPage').disabled = currentPage <= 1;
    document.getElementById('nextPage').disabled = currentPage >= totalPages;
}

/**
 * Exports results to CSV based on current filters
 */
function exportResults() {
    // Show loading notification
    showNotification('Preparing export, please wait...', 'info');

    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Send request to export API
    fetch('../../api/results/exportResults.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.blob();
        })
        .then(blob => {
            // Create a URL for the blob
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'exam_results_export.csv';

            // Append to body and click to trigger download
            document.body.appendChild(a);
            a.click();

            // Clean up
            window.URL.revokeObjectURL(url);
            a.remove();
            showNotification('Export complete!', 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to export results', 'error');
        });
}

/**
 * Generates a comprehensive report based on current filters
 */
function generateReport() {
    // Show loading notification
    showNotification('Generating report, please wait...', 'info');

    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Send request to report API
    fetch('../../api/results/generateReport.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.blob();
        })
        .then(blob => {
            // Create a URL for the blob
            const url = window.URL.createObjectURL(blob);

            // Open report in new tab
            window.open(url, '_blank');

            // Clean up
            setTimeout(() => {
                window.URL.revokeObjectURL(url);
            }, 1000);
            showNotification('Report generated successfully!', 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to generate report', 'error');
        });
}

/**
 * Displays detailed information about a specific result
 */
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
    fetch(`../../api/results/getResultDetails.php?result_id=${resultId}`)
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
            const questions = data.questions || [];

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
                    href="../../api/results/printResult.php?result_id=${resultId}" 
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

/**
 * Shows a notification toast
 */
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };

    const toast = document.createElement('div');
    toast.className = `fixed top-5 right-5 px-6 py-3 rounded shadow-lg text-white z-50 ${colors[type] || colors.info}`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);

    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Escapes HTML to prevent XSS
 */
function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}