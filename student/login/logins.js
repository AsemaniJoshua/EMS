// Student Login JavaScript

document.addEventListener('DOMContentLoaded', function () {
    initializeLoginForm();
    initializePasswordToggle();

    // Check for any URL parameters (like error messages)
    checkUrlParameters();
});

function initializeLoginForm() {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Form submission
    loginForm.addEventListener('submit', handleLogin);

    // Input validation on blur
    emailInput.addEventListener('blur', validateEmail);

    // Real-time validation
    emailInput.addEventListener('input', clearFieldError);
    passwordInput.addEventListener('input', clearFieldError);

    // Enter key handling
    emailInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            passwordInput.focus();
        }
    });

    passwordInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            handleLogin(e);
        }
    });
}

function initializePasswordToggle() {
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    if (togglePassword && passwordInput && eyeIcon) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle eye icon
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    }
}

function checkUrlParameters() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const message = urlParams.get('message');

    if (error) {
        showNotification(decodeURIComponent(error), 'error');
    }

    if (message) {
        showNotification(decodeURIComponent(message), 'success');
    }

    // Clean URL
    if (error || message) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

async function handleLogin(e) {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const remember = document.getElementById('remember').checked;

    // Validate inputs
    if (!validateForm(email)) {
        return;
    }

    // Show loading state
    if (document.getElementById('loginBtn') &&
    document.getElementById('loginBtnText') &&
    document.getElementById('loginSpinner') &&
    document.getElementById('loadingOverlay')) {
    setLoadingState(true);
}


    try {
        const response = await fetch('/api/students/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email,
                password: password,
                remember: remember
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Login successful! Redirecting...', 'success');

            // Store user data in localStorage if remember me is checked
            if (remember) {
                localStorage.setItem('student_email', email);
            } else {
                localStorage.removeItem('student_email');
            }

            // Check if password reset is required
            if (data.data.require_password_reset) {
                setTimeout(() => {
                    window.location.href = '/student/profile/?reset_password=1';
                }, 1500);
            } else {
                // Redirect to dashboard
                setTimeout(() => {
                    window.location.href = '/student/dashboard/';
                }, 1500);
            }
        } else {
            throw new Error(data.message);
        }

    } catch (error) {
        console.error('Login error:', error);
        showNotification(error.message || 'Login failed. Please try again.', 'error');

        // Shake the form on error
        shakeForm();
    } finally {
        setLoadingState(false);
    }
}

function validateForm(email) {
    let isValid = true;

    // Clear previous errors
    clearAllErrors();

    // Validate email
    if (!email) {
        showFieldError('email', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showFieldError('email', 'Please enter a valid email address');
        isValid = false;
    }

    return isValid;
}

function validateEmail() {
    const email = document.getElementById('email').value.trim();
    if (email && !isValidEmail(email)) {
        showFieldError('email', 'Please enter a valid email address');
        return false;
    }
    clearFieldError('email');
    return true;
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

    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(fieldName) {
    const field = typeof fieldName === 'string' ? document.getElementById(fieldName) : fieldName.target;
    const errorMessage = field.parentNode.querySelector('.error-message');

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

    const fields = ['email'];
    fields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.add('border-gray-300', 'focus:border-emerald-500', 'focus:ring-emerald-500');
    });
}

function setLoadingState(loading) {
    const loginBtn = document.getElementById('loginBtn');
    const loginBtnText = document.getElementById('loginBtnText');
    const loginSpinner = document.getElementById('loginSpinner');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Check if elements exist before accessing their properties
    if (!loginBtn || !loginBtnText || !loginSpinner || !loadingOverlay) {
        console.error('One or more required elements not found');
        return;
    }

    if (loading) {
        loginBtn.disabled = true;
        loginBtn.classList.add('opacity-75', 'cursor-not-allowed');
        loginBtnText.textContent = 'Signing In...';
        loginSpinner.classList.remove('hidden');
        loadingOverlay.classList.remove('hidden');
    } else {
        loginBtn.disabled = false;
        loginBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        loginBtnText.textContent = 'Sign In';
        loginSpinner.classList.add('hidden');
        loadingOverlay.classList.add('hidden');
    }
}

function shakeForm() {
    const form = document.getElementById('loginForm');
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

// Load remembered email on page load
window.addEventListener('load', function () {
    const rememberedEmail = localStorage.getItem('student_email');
    if (rememberedEmail) {
        document.getElementById('email').value = rememberedEmail;
        document.getElementById('remember').checked = true;
        document.getElementById('password').focus();
    } else {
        document.getElementById('email').focus();
    }
});

// Handle browser back button
window.addEventListener('popstate', function () {
    // Prevent going back to login page if already logged in
    if (sessionStorage.getItem('student_logged_in')) {
        window.location.href = '/student/dashboard/';
    }
});
