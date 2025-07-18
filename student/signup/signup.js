// Student Signup JavaScript

let currentStep = 1;
let formData = {};
let departments = [];
let programs = [];

document.addEventListener('DOMContentLoaded', function() {
    initializeSignupForm();
    loadFormData();
    initializePasswordToggle();
    initializePasswordValidation();
    initializeDepartmentProgramLogic();
});

function initializeSignupForm() {
    const signupForm = document.getElementById('signupForm');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    // Form submission
    signupForm.addEventListener('submit', handleSignup);
    
    // Navigation buttons
    nextBtn.addEventListener('click', nextStep);
    prevBtn.addEventListener('click', prevStep);
    
    // Real-time validation
    setupRealTimeValidation();
}

async function loadFormData() {
    try {
        const response = await fetch('/api/students/getFormData.php');
        const data = await response.json();
        
        if (data.success) {
            departments = data.data.departments;
            programs = data.data.programs;
            window.levels = data.data.levels;
            
            populateDepartments();
            populateLevels();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error loading form data:', error);
        showNotification('Failed to load form data. Please refresh the page.', 'error');
    }
}

function populateDepartments() {
    const departmentSelect = document.getElementById('department_id');
    departmentSelect.innerHTML = '<option value="">Select Department</option>';
    
    departments.forEach(dept => {
        const option = document.createElement('option');
        option.value = dept.department_id;
        option.textContent = dept.name;
        departmentSelect.appendChild(option);
    });
}

function populatePrograms(departmentId) {
    const programSelect = document.getElementById('program_id');
    programSelect.innerHTML = '<option value="">Select Program</option>';
    
    if (!departmentId) {
        programSelect.disabled = true;
        return;
    }
    
    const filteredPrograms = programs.filter(prog => prog.department_id == departmentId);
    
    filteredPrograms.forEach(prog => {
        const option = document.createElement('option');
        option.value = prog.program_id;
        option.textContent = prog.name;
        programSelect.appendChild(option);
    });
    
    programSelect.disabled = false;
}

function populateLevels() {
    const levelSelect = document.getElementById('level_id');
    levelSelect.innerHTML = '<option value="">Select Level</option>';
    if (!window.levels) return;
    window.levels.forEach(level => {
        const option = document.createElement('option');
        option.value = level.level_id;
        option.textContent = level.name;
        levelSelect.appendChild(option);
    });
}

function initializeDepartmentProgramLogic() {
    const departmentSelect = document.getElementById('department_id');
    const programSelect = document.getElementById('program_id');
    
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        populatePrograms(departmentId);
        
        // Clear program selection when department changes
        programSelect.value = '';
        clearFieldError('program_id');
    });
}

function initializePasswordToggle() {
    // Password toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });
    
    // Confirm password toggle
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const eyeIconConfirm = document.getElementById('eyeIconConfirm');
    
    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        
        if (type === 'password') {
            eyeIconConfirm.classList.remove('fa-eye-slash');
            eyeIconConfirm.classList.add('fa-eye');
        } else {
            eyeIconConfirm.classList.remove('fa-eye');
            eyeIconConfirm.classList.add('fa-eye-slash');
        }
    });
}

function initializePasswordValidation() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const lengthCheck = document.getElementById('length-check');
    const passwordMatch = document.getElementById('password-match');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Length check
        if (password.length >= 6) {
            lengthCheck.classList.remove('text-gray-400');
            lengthCheck.classList.add('text-green-500');
            lengthCheck.querySelector('i').classList.remove('fa-circle');
            lengthCheck.querySelector('i').classList.add('fa-check-circle');
        } else {
            lengthCheck.classList.remove('text-green-500');
            lengthCheck.classList.add('text-gray-400');
            lengthCheck.querySelector('i').classList.remove('fa-check-circle');
            lengthCheck.querySelector('i').classList.add('fa-circle');
        }
        
        // Check password match if confirm password has value
        if (confirmPasswordInput.value) {
            checkPasswordMatch();
        }
    });
    
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword) {
            if (password === confirmPassword) {
                passwordMatch.classList.remove('hidden', 'text-red-500');
                passwordMatch.classList.add('text-green-500');
                passwordMatch.querySelector('span').textContent = 'Passwords match';
                passwordMatch.querySelector('i').classList.remove('fa-circle', 'fa-times-circle');
                passwordMatch.querySelector('i').classList.add('fa-check-circle');
                clearFieldError('confirm_password');
            } else {
                passwordMatch.classList.remove('hidden', 'text-green-500');
                passwordMatch.classList.add('text-red-500');
                passwordMatch.querySelector('span').textContent = 'Passwords do not match';
                passwordMatch.querySelector('i').classList.remove('fa-circle', 'fa-check-circle');
                passwordMatch.querySelector('i').classList.add('fa-times-circle');
            }
        } else {
            passwordMatch.classList.add('hidden');
        }
    }
}

function setupRealTimeValidation() {
    // Username validation
    const usernameInput = document.getElementById('username');
    usernameInput.addEventListener('input', function() {
        const username = this.value;
        if (username && !isValidUsername(username)) {
            showFieldError('username', 'Username can only contain letters, numbers, and underscores');
        } else {
            clearFieldError('username');
        }
    });
    
    // Email validation
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email && !isValidEmail(email)) {
            showFieldError('email', 'Please enter a valid email address');
        } else {
            clearFieldError('email');
        }
    });
    
    // Index number validation
    const indexInput = document.getElementById('index_number');
    indexInput.addEventListener('input', function() {
        const indexNumber = this.value;
        if (indexNumber && indexNumber.length < 5) {
            showFieldError('index_number', 'Index number must be at least 5 characters');
        } else {
            clearFieldError('index_number');
        }
    });
    
    // Date of birth validation
    const dobInput = document.getElementById('date_of_birth');
    dobInput.addEventListener('change', function() {
        const dob = new Date(this.value);
        const today = new Date();
        const age = today.getFullYear() - dob.getFullYear();
        
        if (dob > today) {
            showFieldError('date_of_birth', 'Date of birth cannot be in the future');
        } else if (age < 10 || age > 100) {
            showFieldError('date_of_birth', 'Please enter a valid date of birth');
        } else {
            clearFieldError('date_of_birth');
        }
    });
}

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < 3) {
            currentStep++;
            updateStepDisplay();
        }
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
    }
}

function updateStepDisplay() {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(step => {
        step.classList.add('hidden');
    });
    
    // Show current step
    document.getElementById(`step${currentStep}`).classList.remove('hidden');
    
    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        indicator.classList.remove('active', 'completed');
        
        if (index + 1 < currentStep) {
            indicator.classList.add('completed');
            indicator.innerHTML = '<i class="fas fa-check"></i>';
        } else if (index + 1 === currentStep) {
            indicator.classList.add('active');
            indicator.textContent = index + 1;
        } else {
            indicator.textContent = index + 1;
        }
    });
    
    // Update navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (currentStep === 1) {
        prevBtn.classList.add('hidden');
    } else {
        prevBtn.classList.remove('hidden');
    }
    
    if (currentStep === 3) {
        nextBtn.classList.add('hidden');
        submitBtn.classList.remove('hidden');
    } else {
        nextBtn.classList.remove('hidden');
        submitBtn.classList.add('hidden');
    }
}

function validateCurrentStep() {
    clearAllErrors();
    let isValid = true;
    
    if (currentStep === 1) {
        // Validate personal information
        const requiredFields = ['first_name', 'last_name', 'username', 'index_number', 'email', 'date_of_birth', 'gender'];
        
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            const value = input.value.trim();
            
            if (!value) {
                showFieldError(field, `${field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())} is required`);
                isValid = false;
            }
        });
        
        // Additional validations
        const username = document.getElementById('username').value.trim();
        if (username && !isValidUsername(username)) {
            showFieldError('username', 'Username can only contain letters, numbers, and underscores');
            isValid = false;
        }
        
        const email = document.getElementById('email').value.trim();
        if (email && !isValidEmail(email)) {
            showFieldError('email', 'Please enter a valid email address');
            isValid = false;
        }
        
        const indexNumber = document.getElementById('index_number').value.trim();
        if (indexNumber && indexNumber.length < 5) {
            showFieldError('index_number', 'Index number must be at least 5 characters');
            isValid = false;
        }
        
        const dob = document.getElementById('date_of_birth').value;
        if (dob) {
            const dobDate = new Date(dob);
            const today = new Date();
            const age = today.getFullYear() - dobDate.getFullYear();
            
            if (dobDate > today) {
                showFieldError('date_of_birth', 'Date of birth cannot be in the future');
                isValid = false;
            } else if (age < 10 || age > 100) {
                showFieldError('date_of_birth', 'Please enter a valid date of birth');
                isValid = false;
            }
        }
        
    } else if (currentStep === 2) {
        // Validate academic information
        const departmentId = document.getElementById('department_id').value;
        const programId = document.getElementById('program_id').value;
        
        if (!departmentId) {
            showFieldError('department_id', 'Department is required');
            isValid = false;
        }
        
        if (!programId) {
            showFieldError('program_id', 'Program is required');
            isValid = false;
        }
        
    } else if (currentStep === 3) {
        // Validate account security
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const terms = document.getElementById('terms').checked;
                if (!password) {
            showFieldError('password', 'Password is required');
            isValid = false;
        } else if (password.length < 6) {
            showFieldError('password', 'Password must be at least 6 characters');
            isValid = false;
        }
        
        if (!confirmPassword) {
            showFieldError('confirm_password', 'Please confirm your password');
            isValid = false;
        } else if (password !== confirmPassword) {
            showFieldError('confirm_password', 'Passwords do not match');
            isValid = false;
        }
        
        if (!terms) {
            showFieldError('terms', 'You must agree to the terms and conditions');
            isValid = false;
        }
    }
    
    return isValid;
}

async function handleSignup(e) {
    e.preventDefault();
    
    if (!validateCurrentStep()) {
        return;
    }
    
    // Collect form data
    const formData = new FormData(document.getElementById('signupForm'));
    const data = Object.fromEntries(formData.entries());
    data.status = 'active';
    data.resetOnLogin = 0;
    
    // Show loading state
    setLoadingState(true);
    
    try {
        const response = await fetch('/api/students/createStudent.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(data)
});
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Account created successfully! Please check your email for verification.', 'success');
            
            // Redirect to login page after success
            setTimeout(() => {
                window.location.href = '/student/login/?message=' + encodeURIComponent('Account created successfully! You can now sign in.');
            }, 2000);
        } else {
            throw new Error(result.message);
        }
        
    } catch (error) {
        console.error('Signup error:', error);
        showNotification(error.message || 'Registration failed. Please try again.', 'error');
        
        // Shake the form on error
        shakeForm();
    } finally {
        setLoadingState(false);
    }
}

function isValidUsername(username) {
    const usernameRegex = /^[a-zA-Z0-9_]+$/;
    return usernameRegex.test(username);
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showFieldError(fieldName, message) {
    const field = document.getElementById(fieldName);
    const existingError = field.parentNode.querySelector('.error-message');
    
    // Remove existing error
    if (existingError) {
        existingError.remove();
    }
    
    // Add error styling
    field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    field.classList.remove('border-gray-300', 'focus:border-emerald-500', 'focus:ring-emerald-500');
    
    // Add error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-red-500 text-sm mt-1';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${message}`;
    
    // Handle checkbox fields differently
    if (field.type === 'checkbox') {
        field.parentNode.parentNode.appendChild(errorDiv);
    } else {
        field.parentNode.appendChild(errorDiv);
    }
}

function clearFieldError(fieldName) {
    const field = typeof fieldName === 'string' ? document.getElementById(fieldName) : fieldName.target;
    const errorMessage = field.parentNode.querySelector('.error-message') || 
                        field.parentNode.parentNode.querySelector('.error-message');
    
    if (errorMessage) {
        errorMessage.remove();
    }
    
    // Reset styling
    field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    field.classList.add('border-gray-300', 'focus:border-emerald-500', 'focus:ring-emerald-500');
}

function clearAllErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(error => error.remove());
    
    const fields = document.querySelectorAll('input, select');
    fields.forEach(field => {
        field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.add('border-gray-300', 'focus:border-emerald-500', 'focus:ring-emerald-500');
    });
}

function setLoadingState(loading) {
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitSpinner = document.getElementById('submitSpinner');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    
    if (loading) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        submitBtnText.textContent = 'Creating Account...';
        submitSpinner.classList.remove('hidden');
        loadingOverlay.classList.remove('hidden');
        nextBtn.disabled = true;
        prevBtn.disabled = true;
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        submitBtnText.textContent = 'Create Account';
        submitSpinner.classList.add('hidden');
        loadingOverlay.classList.add('hidden');
        nextBtn.disabled = false;
        prevBtn.disabled = false;
    }
}

function shakeForm() {
    const form = document.getElementById('signupForm');
    form.classList.add('animate-pulse');
    
    setTimeout(() => {
        form.classList.remove('animate-pulse');
    }, 600);
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full transform transition-all duration-300 translate-x-full`;
    
    const bgColor = type === 'success' ? 'bg-green-500' : 
                   type === 'error' ? 'bg-red-500' : 
                   type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                type === 'error' ? 'fa-exclamation-circle' : 
                type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
    
    notification.innerHTML = `
        <div class="${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
            <i class="fas ${icon} text-lg"></i>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Initialize form on page load
window.addEventListener('load', function() {
    // Focus on first input
    document.getElementById('first_name').focus();
    
    // Set max date for date of birth (18 years ago)
    const dobInput = document.getElementById('date_of_birth');
    const today = new Date();
    const maxDate = new Date(today.getFullYear() - 10, today.getMonth(), today.getDate());
    dobInput.max = maxDate.toISOString().split('T')[0];
});

