document.addEventListener('DOMContentLoaded', function () {
    // Set up SweetAlert Toast Mixin
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    const loginForm = document.getElementById('teacher-login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Get form data
            const usernameEmail = document.getElementById('username-email').value.trim();
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('remember-me').checked;

            // Show loading state
            const submitButton = loginForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Signing In...';
            submitButton.disabled = true;

            // Prepare data for API
            const loginData = {
                email: usernameEmail, // Using email field for both username and email
                password: password,
                remember: rememberMe
            };

            // Make API call to teacher login endpoint using Axios
            axios.post('/api/login/teacher/processTeacherLogin.php', loginData)
                .then(function (response) {
                    const data = response.data;
                    if (data.status === 'success') {
                        // Show success message
                        Toast.fire({
                            icon: 'success',
                            title: data.message || 'Login successful!'
                        });

                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = data.redirect || '/teacher/dashboard/'; // Use backend redirect if present
                        }, 1000);
                    } else {
                        // Show error message
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Login failed. Please try again.'
                        });

                        // Reset button state
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    }
                })
                .catch(function (error) {
                    console.error('Login error:', error);

                    // Show error message
                    Toast.fire({
                        icon: 'error',
                        title: 'Network error. Please check your connection and try again.'
                    });

                    // Reset button state
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
        });
    }

    // Add some basic validation
    const usernameEmailInput = document.getElementById('username-email');
    const passwordInput = document.getElementById('password');

    if (usernameEmailInput) {
        usernameEmailInput.addEventListener('blur', function () {
            if (this.value.trim() === '') {
                this.classList.add('border-red-500');
                this.classList.remove('border-gray-300');
            } else {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
            }
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('blur', function () {
            if (this.value === '') {
                this.classList.add('border-red-500');
                this.classList.remove('border-gray-300');
            } else {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
            }
        });
    }
});
