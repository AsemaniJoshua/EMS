// Admin Settings JS - Comprehensive System Management

document.addEventListener('DOMContentLoaded', function () {
    initializeSettings();
    loadAllData();

    // NOTE: Edit and delete functionality has been implemented for all entity types:
    // - Departments: editDepartment, deleteDepartment
    // - Programs: editProgram, deleteProgram 
    // - Courses: editCourse, deleteCourse
    // - Levels: editLevel, deleteLevel
    // - Semesters: editSemester, deleteSemester
});

// Global variables
let currentEditId = null;
let currentEditType = null;

// Initialize settings page
function initializeSettings() {
    // Set up SweetAlert defaults
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    window.Toast = Toast;

    // Set initial tab
    showTab('departments');
}

// Tab Management
function showTab(tabName) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.add('hidden'));

    // Show selected tab
    const selectedTab = document.getElementById(`content-${tabName}`);
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }

    // Update tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active', 'border-emerald-500', 'text-emerald-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    const activeButton = document.getElementById(`tab-${tabName}`);
    if (activeButton) {
        activeButton.classList.add('active', 'border-emerald-500', 'text-emerald-600');
        activeButton.classList.remove('border-transparent', 'text-gray-500');
    }

    // Load data for the selected tab
    switch (tabName) {
        case 'departments':
            loadDepartments();
            break;
        case 'programs':
            loadPrograms();
            break;
        case 'courses':
            loadCourses();
            break;
        case 'levels':
            loadLevels();
            break;
        case 'semesters':
            loadSemesters();
            break;
    }
}

// Load all data
function loadAllData() {
    loadDepartments();
    loadPrograms();
    loadCourses();
    loadLevels();
    loadSemesters();
}

// DEPARTMENTS MANAGEMENT
function loadDepartments() {
    const container = document.getElementById('departments-list');
    if (container) {
        container.innerHTML = '<div class="flex justify-center items-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i><span class="ml-2 text-gray-500">Loading departments...</span></div>';
    }

    fetch('../../api/admin/settings/departments.php?action=get')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayDepartments(data.data);
            } else {
                showNotification(data.message || 'Failed to load departments', 'error');
                if (container) {
                    container.innerHTML = '<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle mb-2"></i><br>Failed to load departments</div>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading departments:', error);
            showNotification('Error loading departments', 'error');
            if (container) {
                container.innerHTML = '<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle mb-2"></i><br>Error loading departments</div>';
            }
        });
}

function displayDepartments(departments) {
    const container = document.getElementById('departments-list');
    if (!container) return;

    if (departments.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No departments found.</p>';
        return;
    }

    container.innerHTML = departments.map(dept => `
        <div class="bg-white p-4 rounded-lg border border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-900">${escapeHtml(dept.name)}</h3>
                <p class="text-sm text-gray-500">${escapeHtml(dept.description || 'No description')}</p>
            </div>
            <div class="flex space-x-2">
                <button onclick="editDepartment(${dept.department_id})" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteDepartment(${dept.department_id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function showAddDepartmentModal() {
    const modalHtml = `
        <div id="departmentModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50 transition-all duration-300">
            <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl border border-gray-200 transform transition-all duration-300 scale-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Add New Department</h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="departmentForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department Name</label>
                        <input type="text" id="deptName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="deptDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">Cancel</button>
                        <button type="submit" id="departmentSubmitBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors flex items-center justify-center">
                            <span>Add Department</span>
                            <span class="ml-2 hidden spinner"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    document.getElementById('departmentForm').addEventListener('submit', function (e) {
        e.preventDefault();
        saveDepartment();
    });

    setupModalCloseEvents();
}

function saveDepartment() {
    // Reset error states
    document.querySelectorAll('.error-feedback').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });

    const nameInput = document.getElementById('deptName');
    const name = nameInput.value.trim();
    const description = document.getElementById('deptDescription').value.trim();

    // Enhanced validation with field-specific error messages
    let isValid = true;

    if (!name) {
        showInputError(nameInput, 'Department name is required');
        isValid = false;
    } else if (name.length < 2) {
        showInputError(nameInput, 'Department name must be at least 2 characters');
        isValid = false;
    }

    if (!isValid) {
        return;
    }

    const data = {
        action: currentEditId ? 'update' : 'create',
        name: name,
        description: description
    };

    if (currentEditId) {
        data.department_id = currentEditId;
    }

    // Show loading state
    const submitBtn = document.getElementById('departmentSubmitBtn');
    const btnText = submitBtn.querySelector('span:not(.spinner)');
    const spinner = submitBtn.querySelector('.spinner');
    const originalText = btnText.textContent;

    submitBtn.disabled = true;
    btnText.textContent = currentEditId ? 'Updating...' : 'Saving...';
    spinner.classList.remove('hidden');

    fetch('../../api/admin/settings/departments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Department saved successfully', 'success');
                closeModal();
                loadDepartments();
                currentEditId = null;
            } else {
                showNotification(data.message || 'Failed to save department', 'error');
                // Show API validation errors if available
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById(`dept${field.charAt(0).toUpperCase() + field.slice(1)}`);
                        if (input) {
                            showInputError(input, data.errors[field]);
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error saving department:', error);
            showNotification('Error saving department', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            btnText.textContent = originalText;
            spinner.classList.add('hidden');
        });
}

// Helper function to show input-specific error messages
function showInputError(inputElement, errorMessage) {
    // Add error class to input
    inputElement.classList.add('border-red-500');
    inputElement.classList.remove('border-gray-300');

    // Find and show error message container
    const errorContainer = inputElement.nextElementSibling;
    if (errorContainer && errorContainer.classList.contains('error-feedback')) {
        errorContainer.textContent = errorMessage;
        errorContainer.classList.remove('hidden');
    } else {
        // If no error container exists, create one
        const newErrorContainer = document.createElement('div');
        newErrorContainer.className = 'error-feedback text-red-500 text-sm mt-1';
        newErrorContainer.textContent = errorMessage;
        inputElement.parentNode.insertBefore(newErrorContainer, inputElement.nextSibling);
    }

    // Focus on the first input with error
    inputElement.focus();
}

function editDepartment(id) {
    // Show loading notification
    const loadingToast = showNotification('Loading department data...', 'info', false);

    fetch(`../../api/admin/settings/departments.php?action=get&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Close the loading toast
            if (loadingToast && loadingToast.close) {
                loadingToast.close();
            }

            if (data.success && data.data) {
                currentEditId = id;
                showAddDepartmentModal();

                // Wait for modal to be inserted then populate
                setTimeout(() => {
                    const nameInput = document.getElementById('deptName');
                    const descInput = document.getElementById('deptDescription');
                    const submitBtn = document.getElementById('departmentSubmitBtn');
                    const submitBtnText = submitBtn.querySelector('span:not(.spinner)');
                    const modalTitle = document.querySelector('#departmentModal h3');

                    if (nameInput && descInput && submitBtn && modalTitle) {
                        nameInput.value = data.data.name || '';
                        descInput.value = data.data.description || '';
                        submitBtnText.textContent = 'Update Department';
                        modalTitle.textContent = 'Edit Department';

                        // Set focus to the name field
                        nameInput.focus();
                        // Select all text for easy editing
                        nameInput.select();
                    }
                }, 100);
            } else {
                showNotification(data.message || 'Department not found', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading department:', error);
            showNotification('Error loading department', 'error');
        });
}

function deleteDepartment(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the department and may affect related data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../../api/admin/settings/departments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    department_id: id
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Department deleted successfully', 'success');
                        loadDepartments();
                    } else {
                        showNotification(data.message || 'Failed to delete department', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting department:', error);
                    showNotification('Error deleting department', 'error');
                });
        }
    });
}

// PROGRAMS MANAGEMENT
function loadPrograms() {
    fetch('../../api/admin/settings/programs.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPrograms(data.data);
            } else {
                showNotification(data.message || 'Failed to load programs', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading programs:', error);
            showNotification('Error loading programs', 'error');
        });
}

function displayPrograms(programs) {
    const container = document.getElementById('programs-list');
    if (!container) return;

    if (programs.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No programs found.</p>';
        return;
    }

    container.innerHTML = programs.map(program => `
        <div class="bg-white p-4 rounded-lg border border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-900">${escapeHtml(program.name)}</h3>
                <p class="text-sm text-gray-500">${escapeHtml(program.department_name)} | ${escapeHtml(program.description || 'No description')}</p>
            </div>
            <div class="flex space-x-2">
                <button onclick="editProgram(${program.program_id})" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteProgram(${program.program_id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function showAddProgramModal() {
    // First load departments for the dropdown
    fetch('../../api/admin/settings/departments.php?action=get')
        .then(response => response.json())
        .then(data => {
            const departmentOptions = data.success ?
                data.data.map(dept => `<option value="${dept.department_id}">${escapeHtml(dept.name)}</option>`).join('') :
                '<option value="">No departments available</option>';

            const modalHtml = `
                <div id="programModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Program</h3>
                        <form id="programForm">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program Name</label>
                                <input type="text" id="programName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select id="programDepartment" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="">Select Department</option>
                                    ${departmentOptions}
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea id="programDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Add Program</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            document.getElementById('programForm').addEventListener('submit', function (e) {
                e.preventDefault();
                saveProgram();
            });
        });
}

function saveProgram() {
    // Reset error states
    document.querySelectorAll('.error-feedback').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });

    const nameInput = document.getElementById('programName');
    const name = nameInput.value.trim();
    const departmentSelect = document.getElementById('programDepartment');
    const departmentId = departmentSelect.value;
    const description = document.getElementById('programDescription').value.trim();

    // Enhanced validation with field-specific error messages
    let isValid = true;

    if (!name) {
        showInputError(nameInput, 'Program name is required');
        isValid = false;
    } else if (name.length < 2) {
        showInputError(nameInput, 'Program name must be at least 2 characters');
        isValid = false;
    }

    if (!departmentId) {
        showInputError(departmentSelect, 'Department is required');
        isValid = false;
    }

    if (!isValid) {
        return;
    }

    const data = {
        action: currentEditId ? 'update' : 'create',
        name: name,
        department_id: departmentId,
        description: description
    };

    if (currentEditId) {
        data.program_id = currentEditId;
    }

    // Show loading state
    const submitBtn = document.querySelector('#programForm button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span>${currentEditId ? 'Updating...' : 'Saving...'}</span> <i class="fas fa-spinner fa-spin ml-2"></i>`;

    fetch('../../api/admin/settings/programs.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Program saved successfully', 'success');
                closeModal();
                loadPrograms();
                currentEditId = null;
            } else {
                showNotification(data.message || 'Failed to save program', 'error');
                // Show API validation errors if available
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById(`program${field.charAt(0).toUpperCase() + field.slice(1)}`);
                        if (input) {
                            showInputError(input, data.errors[field]);
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error saving program:', error);
            showNotification('Error saving program', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
}

function editProgram(id) {
    // Debug logs
    console.log('editProgram function called with ID:', id);

    // Show loading notification
    const loadingToast = showNotification('Loading program data...', 'info', false);

    fetch(`../../api/admin/settings/programs.php?action=get&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Close the loading toast
            if (loadingToast && loadingToast.close) {
                loadingToast.close();
            }

            if (data.success && data.data) {
                currentEditId = id;

                // First load departments for the dropdown, then show modal
                fetch('../../api/admin/settings/departments.php?action=get')
                    .then(response => response.json())
                    .then(deptData => {
                        const departmentOptions = deptData.success ?
                            deptData.data.map(dept => `<option value="${dept.department_id}">${escapeHtml(dept.name)}</option>`).join('') :
                            '<option value="">No departments available</option>';

                        const modalHtml = `
                            <div id="programModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                                <div class="bg-white rounded-lg p-6 w-full max-w-md">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">Edit Program</h3>
                                        <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <form id="programForm">
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Program Name</label>
                                            <input type="text" id="programName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                            <select id="programDepartment" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                                <option value="">Select Department</option>
                                                ${departmentOptions}
                                            </select>
                                            <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                            <textarea id="programDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                                        </div>
                                        <div class="flex justify-end space-x-3">
                                            <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">Cancel</button>
                                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors flex items-center justify-center">
                                                <span>Update Program</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        `;

                        document.body.insertAdjacentHTML('beforeend', modalHtml);
                        setupModalCloseEvents();

                        document.getElementById('programForm').addEventListener('submit', function (e) {
                            e.preventDefault();
                            saveProgram();
                        });

                        // Fill form with data
                        const nameInput = document.getElementById('programName');
                        const deptSelect = document.getElementById('programDepartment');
                        const descInput = document.getElementById('programDescription');

                        if (nameInput && deptSelect && descInput) {
                            nameInput.value = data.data.name || '';
                            deptSelect.value = data.data.department_id || '';
                            descInput.value = data.data.description || '';

                            // Set focus to the name field
                            nameInput.focus();
                            // Select all text for easy editing
                            nameInput.select();
                        }
                    });
            } else {
                showNotification(data.message || 'Program not found', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading program:', error);
            showNotification('Error loading program', 'error');
        });
}

function deleteProgram(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the program and may affect related data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../../api/admin/settings/programs.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    program_id: id
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Program deleted successfully', 'success');
                        loadPrograms();
                    } else {
                        showNotification(data.message || 'Failed to delete program', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting program:', error);
                    showNotification('Error deleting program', 'error');
                });
        }
    });
}

// COURSES MANAGEMENT (similar pattern)
function loadCourses() {
    fetch('../../api/admin/settings/courses.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCourses(data.data);
            } else {
                showNotification(data.message || 'Failed to load courses', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            showNotification('Error loading courses', 'error');
        });
}

function displayCourses(courses) {
    const container = document.getElementById('courses-list');
    if (!container) return;

    if (courses.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No courses found.</p>';
        return;
    }

    container.innerHTML = courses.map(course => `
        <div class="bg-white p-4 rounded-lg border border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-900">${escapeHtml(course.code)} - ${escapeHtml(course.title)}</h3>
                <p class="text-sm text-gray-500">${escapeHtml(course.department_name)} | ${escapeHtml(course.program_name)} | Credits: ${course.credits}</p>
            </div>
            <div class="flex space-x-2">
                <button onclick="editCourse(${course.course_id})" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteCourse(${course.course_id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function showAddCourseModal() {
    // Load departments, programs, levels, and semesters for dropdowns
    Promise.all([
        fetch('../../api/admin/settings/departments.php?action=get').then(r => r.json()),
        fetch('../../api/admin/settings/programs.php?action=get').then(r => r.json()),
        fetch('../../api/admin/settings/levels.php?action=get').then(r => r.json()),
        fetch('../../api/admin/settings/semesters.php?action=get').then(r => r.json())
    ]).then(([deptData, progData, levelData, semData]) => {
        const departmentOptions = deptData.success ?
            deptData.data.map(dept => `<option value="${dept.department_id}">${escapeHtml(dept.name)}</option>`).join('') : '';
        const programOptions = progData.success ?
            progData.data.map(prog => `<option value="${prog.program_id}">${escapeHtml(prog.name)}</option>`).join('') : '';
        const levelOptions = levelData.success ?
            levelData.data.map(level => `<option value="${level.level_id}">${escapeHtml(level.name)}</option>`).join('') : '';
        const semesterOptions = semData.success ?
            semData.data.map(sem => `<option value="${sem.semester_id}">${escapeHtml(sem.name)}</option>`).join('') : '';

        const modalHtml = `
            <div id="courseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Course</h3>
                    <form id="courseForm">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course Code</label>
                                <input type="text" id="courseCode" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Credits</label>
                                <input type="number" id="courseCredits" min="1" max="10" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Course Title</label>
                            <input type="text" id="courseTitle" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select id="courseDepartment" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="">Select Department</option>
                                    ${departmentOptions}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                                <select id="courseProgram" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="">Select Program</option>
                                    ${programOptions}
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                                <select id="courseLevel" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="">Select Level</option>
                                    ${levelOptions}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                                <select id="courseSemester" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="">Select Semester</option>
                                    ${semesterOptions}
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Add Course</button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        document.getElementById('courseForm').addEventListener('submit', function (e) {
            e.preventDefault();
            saveCourse();
        });
    });
}

function saveCourse() {
    const data = {
        action: currentEditId ? 'update' : 'create',
        code: document.getElementById('courseCode').value,
        title: document.getElementById('courseTitle').value,
        department_id: document.getElementById('courseDepartment').value,
        program_id: document.getElementById('courseProgram').value,
        level_id: document.getElementById('courseLevel').value,
        semester_id: document.getElementById('courseSemester').value,
        credits: document.getElementById('courseCredits').value
    };

    if (currentEditId) {
        data.course_id = currentEditId;
    }

    fetch('../../api/admin/settings/courses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Course saved successfully', 'success');
                closeModal();
                loadCourses();
                currentEditId = null;
            } else {
                showNotification(data.message || 'Failed to save course', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving course:', error);
            showNotification('Error saving course', 'error');
        });
}

function editCourse(id) {
    // Debug logs
    console.log('editCourse function called with ID:', id);

    // Show loading notification
    const loadingToast = showNotification('Loading course data...', 'info', false);

    // First get the course data
    fetch(`../../api/admin/settings/courses.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Close loading notification
            if (loadingToast && loadingToast.close) {
                loadingToast.close();
            }

            if (!data.success || !data.data) {
                showNotification(data.message || 'Failed to load course', 'error');
                return;
            }

            currentEditId = id;
            const course = data.data;

            // Now get required data for dropdowns
            Promise.all([
                fetch('../../api/admin/settings/departments.php?action=get').then(r => r.json()),
                fetch('../../api/admin/settings/programs.php?action=get').then(r => r.json()),
                fetch('../../api/admin/settings/levels.php?action=get').then(r => r.json()),
                fetch('../../api/admin/settings/semesters.php?action=get').then(r => r.json())
            ]).then(([deptData, progData, levelData, semData]) => {
                const departmentOptions = deptData.success ?
                    deptData.data.map(dept => `<option value="${dept.department_id}">${escapeHtml(dept.name)}</option>`).join('') : '';
                const programOptions = progData.success ?
                    progData.data.map(prog => `<option value="${prog.program_id}">${escapeHtml(prog.name)}</option>`).join('') : '';
                const levelOptions = levelData.success ?
                    levelData.data.map(level => `<option value="${level.level_id}">${escapeHtml(level.name)}</option>`).join('') : '';
                const semesterOptions = semData.success ?
                    semData.data.map(sem => `<option value="${sem.semester_id}">${escapeHtml(sem.name)}</option>`).join('') : '';

                const modalHtml = `
                    <div id="courseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Edit Course</h3>
                                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form id="courseForm">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Course Code</label>
                                        <input type="text" id="courseCode" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Credits</label>
                                        <input type="number" id="courseCredits" min="1" max="10" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Course Title</label>
                                    <input type="text" id="courseTitle" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                        <select id="courseDepartment" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            <option value="">Select Department</option>
                                            ${departmentOptions}
                                        </select>
                                        <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                                        <select id="courseProgram" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            <option value="">Select Program</option>
                                            ${programOptions}
                                        </select>
                                        <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                                        <select id="courseLevel" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            <option value="">Select Level</option>
                                            ${levelOptions}
                                        </select>
                                        <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                                        <select id="courseSemester" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            <option value="">Select Semester</option>
                                            ${semesterOptions}
                                        </select>
                                        <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                    </div>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors flex items-center justify-center">
                                        <span>Update Course</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                setupModalCloseEvents();

                document.getElementById('courseForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    saveCourse();
                });

                // Populate the form with data
                document.getElementById('courseCode').value = course.code || '';
                document.getElementById('courseTitle').value = course.name || '';
                document.getElementById('courseDepartment').value = course.department_id || '';
                document.getElementById('courseProgram').value = course.program_id || '';
                document.getElementById('courseLevel').value = course.level_id || '';
                document.getElementById('courseSemester').value = course.semester_id || '';
                document.getElementById('courseCredits').value = course.credit_hours || '';

                // Set focus to the code field
                document.getElementById('courseCode').focus();
            });
        })
        .catch(error => {
            console.error('Error loading course:', error);
            showNotification('Error loading course', 'error');
        });
}

function deleteCourse(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the course and may affect related data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../../api/admin/settings/courses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    course_id: id
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Course deleted successfully', 'success');
                        loadCourses();
                    } else {
                        showNotification(data.message || 'Failed to delete course', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting course:', error);
                    showNotification('Error deleting course', 'error');
                });
        }
    });
}

// LEVELS MANAGEMENT
function loadLevels() {
    fetch('../../api/admin/settings/levels.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayLevels(data.data);
            } else {
                showNotification(data.message || 'Failed to load levels', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading levels:', error);
            showNotification('Error loading levels', 'error');
        });
}

function displayLevels(levels) {
    const container = document.getElementById('levels-list');
    if (!container) return;

    if (levels.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No levels found.</p>';
        return;
    }

    container.innerHTML = levels.map(level => `
        <div class="bg-white p-4 rounded-lg border border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-900">${escapeHtml(level.name)}</h3>
                <p class="text-sm text-gray-500">Level ID: ${level.level_id}</p>
            </div>
            <div class="flex space-x-2">
                <button onclick="editLevel(${level.level_id})" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteLevel(${level.level_id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function showAddLevelModal() {
    const modalHtml = `
        <div id="levelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Level</h3>
                <form id="levelForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level ID</label>
                        <input type="number" id="levelId" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="e.g., 100, 200, 300">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level Name</label>
                        <input type="text" id="levelName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="e.g., Level 100">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Add Level</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    document.getElementById('levelForm').addEventListener('submit', function (e) {
        e.preventDefault();
        saveLevel();
    });
}

function saveLevel() {
    const data = {
        action: currentEditId ? 'update' : 'create',
        level_id: document.getElementById('levelId').value,
        name: document.getElementById('levelName').value
    };

    if (currentEditId) {
        data.old_level_id = currentEditId;
    }

    fetch('../../api/admin/settings/levels.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Level saved successfully', 'success');
                closeModal();
                loadLevels();
                currentEditId = null;
            } else {
                showNotification(data.message || 'Failed to save level', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving level:', error);
            showNotification('Error saving level', 'error');
        });
}

function editLevel(id) {
    // Debug logs
    console.log('editLevel function called with ID:', id);

    // Show loading notification
    const loadingToast = showNotification('Loading level data...', 'info', false);

    fetch(`../../api/admin/settings/levels.php?action=get&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Close loading notification
            if (loadingToast && loadingToast.close) {
                loadingToast.close();
            }

            if (data.success && data.data) {
                currentEditId = id;

                const modalHtml = `
                    <div id="levelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Edit Level</h3>
                                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form id="levelForm">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Level ID</label>
                                    <input type="number" id="levelId" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="e.g., 100, 200, 300">
                                    <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Level Name</label>
                                    <input type="text" id="levelName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="e.g., Level 100">
                                    <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors flex items-center justify-center">
                                        <span>Update Level</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                setupModalCloseEvents();

                document.getElementById('levelForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    saveLevel();
                });

                // Populate form with data
                document.getElementById('levelId').value = data.data.level_id || '';
                document.getElementById('levelName').value = data.data.name || '';

                // Set focus to the level id field
                document.getElementById('levelId').focus();
            } else {
                showNotification(data.message || 'Level not found', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading level:', error);
            showNotification('Error loading level', 'error');
        });
}

function deleteLevel(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the level and may affect related data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../../api/admin/settings/levels.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    level_id: id
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Level deleted successfully', 'success');
                        loadLevels();
                    } else {
                        showNotification(data.message || 'Failed to delete level', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting level:', error);
                    showNotification('Error deleting level', 'error');
                });
        }
    });
}

// SEMESTERS MANAGEMENT
function loadSemesters() {
    fetch('../../api/admin/settings/semesters.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySemesters(data.data);
            } else {
                showNotification(data.message || 'Failed to load semesters', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading semesters:', error);
            showNotification('Error loading semesters', 'error');
        });
}

function displaySemesters(semesters) {
    const container = document.getElementById('semesters-list');
    if (!container) return;

    if (semesters.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4">No semesters found.</p>';
        return;
    }

    container.innerHTML = semesters.map(semester => `
        <div class="bg-white p-4 rounded-lg border border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-900">${escapeHtml(semester.name)}</h3>
                <p class="text-sm text-gray-500">
                    ${semester.start_date ? `Start: ${semester.start_date}` : ''} 
                    ${semester.end_date ? `| End: ${semester.end_date}` : ''}
                </p>
            </div>
            <div class="flex space-x-2">
                <button onclick="editSemester(${semester.semester_id})" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteSemester(${semester.semester_id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function showAddSemesterModal() {
    const modalHtml = `
        <div id="semesterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Semester</h3>
                <form id="semesterForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Semester Name</label>
                        <input type="text" id="semesterName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="e.g., Fall 2024">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" id="semesterStartDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" id="semesterEndDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Add Semester</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    document.getElementById('semesterForm').addEventListener('submit', function (e) {
        e.preventDefault();
        saveSemester();
    });
}

function saveSemester() {
    const data = {
        action: currentEditId ? 'update' : 'create',
        name: document.getElementById('semesterName').value,
        start_date: document.getElementById('semesterStartDate').value || null,
        end_date: document.getElementById('semesterEndDate').value || null
    };

    if (currentEditId) {
        data.semester_id = currentEditId;
    }

    fetch('../../api/admin/settings/semesters.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Semester saved successfully', 'success');
                closeModal();
                loadSemesters();
                currentEditId = null;
            } else {
                showNotification(data.message || 'Failed to save semester', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving semester:', error);
            showNotification('Error saving semester', 'error');
        });
}

function editSemester(id) {
    // Show loading notification
    const loadingToast = showNotification('Loading semester data...', 'info', false);

    fetch(`../../api/admin/settings/semesters.php?action=get&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Close loading notification
            if (loadingToast && loadingToast.close) {
                loadingToast.close();
            }

            if (data.success && data.data) {
                currentEditId = id;

                const modalHtml = `
                    <div id="semesterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Edit Semester</h3>
                                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form id="semesterForm">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Semester Name</label>
                                    <input type="text" id="semesterName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="e.g., Fall 2024">
                                    <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                    <input type="date" id="semesterStartDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                    <input type="date" id="semesterEndDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <div class="error-feedback text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors flex items-center justify-center">
                                        <span>Update Semester</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                setupModalCloseEvents();

                document.getElementById('semesterForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    saveSemester();
                });

                // Populate form with data
                const nameInput = document.getElementById('semesterName');
                const startDateInput = document.getElementById('semesterStartDate');
                const endDateInput = document.getElementById('semesterEndDate');

                nameInput.value = data.data.name || '';
                startDateInput.value = data.data.start_date || '';
                endDateInput.value = data.data.end_date || '';

                // Set focus to the name field
                nameInput.focus();
            } else {
                showNotification(data.message || 'Semester not found', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading semester:', error);
            showNotification('Error loading semester', 'error');
        });
}

function deleteSemester(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the semester and may affect related data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../../api/admin/settings/semesters.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    semester_id: id
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Semester deleted successfully', 'success');
                        loadSemesters();
                    } else {
                        showNotification(data.message || 'Failed to delete semester', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting semester:', error);
                    showNotification('Error deleting semester', 'error');
                });
        }
    });
}

// UTILITY FUNCTIONS
function closeModal() {
    const modals = document.querySelectorAll('[id$="Modal"]');
    modals.forEach(modal => modal.remove());
    currentEditId = null;

    // Remove event listeners
    document.removeEventListener('keydown', handleEscKeyForModal);
}

// Add ESC key support for modals
function handleEscKeyForModal(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
}

// Setup modal close events (ESC key)
function setupModalCloseEvents() {
    document.addEventListener('keydown', handleEscKeyForModal);
}

// Add click outside support for modals
function setupModalCloseEvents() {
    document.addEventListener('keydown', handleEscKeyForModal);

    // Add click event for clicking outside modal
    const modalElements = document.querySelectorAll('[id$="Modal"]');
    modalElements.forEach(modal => {
        modal.addEventListener('click', function (e) {
            // Only close if the click is on the background, not the modal content
            if (e.target === modal) {
                closeModal();
            }
        });
    });
}

// Show notification using SweetAlert Toast
function showNotification(message, type = 'info', autoClose = true) {
    if (window.Toast) {
        const toast = window.Toast.fire({
            icon: type,
            title: message,
            timer: autoClose ? 3000 : undefined,
            showConfirmButton: !autoClose
        });
        return toast;
    } else {
        // Fallback to regular alert if Toast is not available
        alert(message);
        return null;
    }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
}

// Database backup function
async function performBackup() {
    try {
        Swal.fire({
            title: 'Creating Backup...',
            text: 'Please wait while we create a backup of your database',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await axios.post('../../api/admin/settings/backup_simple.php');

        if (response.data.success) {
            Swal.fire({
                title: 'Success!',
                text: `Database backup created successfully: ${response.data.filename}`,
                icon: 'success',
                confirmButtonColor: '#10b981'
            });
        } else {
            throw new Error(response.data.message);
        }
    } catch (error) {
        console.error('Error creating backup:', error);
        Swal.fire({
            title: 'Error',
            text: error.response?.data?.message || 'Failed to create backup',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    }
}

// Make functions globally available
window.performBackup = performBackup;
window.showTab = showTab;
window.showAddDepartmentModal = showAddDepartmentModal;
window.showAddProgramModal = showAddProgramModal;
window.showAddCourseModal = showAddCourseModal;
window.showAddLevelModal = showAddLevelModal;
window.showAddSemesterModal = showAddSemesterModal;
window.editDepartment = editDepartment;
window.deleteDepartment = deleteDepartment;
window.editProgram = editProgram;
window.deleteProgram = deleteProgram;
window.editCourse = editCourse;
window.deleteCourse = deleteCourse;
window.editLevel = editLevel;
window.deleteLevel = deleteLevel;
window.editSemester = editSemester;
window.deleteSemester = deleteSemester;
window.closeModal = closeModal;