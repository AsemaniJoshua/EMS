// login.js

document.addEventListener('DOMContentLoaded', function () {
<<<<<<< HEAD
    // Set up SweetAlert Toast Mixin (if SweetAlert is available)
    let Toast;
    if (typeof Swal !== 'undefined') {
        Toast = Swal.mixin({
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
    }

    const loginForm = document.getElementById('teacher-login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Show loading state
            const submitButton = loginForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Signing In...';
            submitButton.disabled = true;
            
            // Get form data
            const usernameEmail = document.getElementById('username-email').value.trim();
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('remember-me').checked;
            
            // Prepare data for API
            const loginData = {
                email: usernameEmail, // Using email field for both username and email
                password: password,
                remember: rememberMe
            };
            
            // Make API call to teacher login endpoint
            fetch('/api/login/processTeacherLogin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                // credentials: 'same-origin', // Ensure cookies are sent
                body: JSON.stringify(loginData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Show success message
                    if (Toast) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message || 'Login successful!'
                        });
                    } else {
                        showNotification(data.message || 'Login successful!', 'success');
                    }
                    
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = data.redirect || '/teacher/';
                    }, 1000);
                } else {
                    // Show error message
                    if (Toast) {
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Login failed. Please try again.'
                        });
                    } else {
                        showNotification(data.message || 'Login failed. Please try again.', 'error');
                    }
                    
                    // Reset button state
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                
                // Show error message
                if (Toast) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Network error. Please check your connection and try again.'
                    });
                } else {
                    showNotification('Network error. Please check your connection and try again.', 'error');
                }
                
                // Reset button state
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
=======
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
    });

    // Make modal functions available globally
    window.openModal = function (id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    };

    window.closeModal = function (id) {
        document.getElementById(id).classList.add('hidden');
    };

    // Login Handler
    const loginForm = document.getElementById('teacherLoginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;

            axios.post('/api/login/teacher/processTeacherLogin.php', {
                email,
                password,
                remember
            })
                .then((res) => {
                    const data = res.data;
                    if (data.status === 'success') {
                        Toast.fire({ icon: 'success', title: data.message || 'Login successful!' });
                        setTimeout(() => {
                            window.location.href = data.redirect || '/teacher/dashboard/';
                        }, 1000);
                    } else {
                        Toast.fire({ icon: 'error', title: data.message || 'Login failed. Please try again.' });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch((err) => {
                    Toast.fire({ icon: 'error', title: 'An error occurred. Please try again.' });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
>>>>>>> 6776f878dc1553b01f99c94a6008787df1c74290
        });
    }

    // Forgot Password Flow
    const forgotBtn = document.getElementById('forgotBtn');
    forgotBtn?.addEventListener('click', () => openModal('forgotModal'));

    document.getElementById('forgotForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const contact = document.getElementById('contactInput').value.trim();

        if (!contact) {
            return Swal.fire({ icon: 'error', title: 'Required', text: 'Please enter email or phone number.' });
        }

        window._resetContact = contact;

        axios.post('/api/forgotPassword/teacher/requestReset.php', { contact })
            .then(() => {
                openModal('otpModal');
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'Failed to send OTP.' });
            });
    });

    document.getElementById('otpForm')?.addEventListener('submit', function (e) {
        e.preventDefault();

        const contact = window._resetContact;
        const otpInputs = document.querySelectorAll('.otp-input');
        const otp = Array.from(otpInputs).map(input => input.value).join('');

        if (otp.length !== 6) {
            return Swal.fire({ icon: 'error', title: 'Invalid OTP', text: 'Please enter the 6-digit code.' });
        }

        axios.post('/api/forgotPassword/teacher/verifyOtp.php', { contact, otp })
            .then(() => {
                closeModal('otpModal');
                openModal('resetModal');
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'OTP Error', text: err.response?.data?.message || 'Failed to verify OTP.' });
            });
    });

    document.getElementById('resetForm')?.addEventListener('submit', function (e) {
        e.preventDefault();

        const password = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const contact = window._resetContact;

        if (password !== confirmPassword) {
            return Swal.fire({ icon: 'error', title: 'Mismatch', text: 'Passwords do not match.' });
        }

        axios.post('/api/forgotPassword/teacher/resetPassword.php', { contact, password })
            .then(() => {
                closeModal('resetModal');
                Swal.fire({
                    icon: 'success',
                    title: 'Password Reset',
                    text: 'Your password has been successfully reset!',
                    confirmButtonColor: '#10b981',
                });
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Reset Error', text: err.response?.data?.message || 'Could not reset password.' });
            });
    });

    // Auto-advance for OTP inputs
    document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    // Input validation borders
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('blur', function () {
            if (!this.value) {
                this.classList.remove('border-gray-300');
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
            }
        });
    });
});
