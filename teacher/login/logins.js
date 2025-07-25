document.addEventListener('DOMContentLoaded', function () {
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

    // Modal helpers
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
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;

            try {
                const response = await fetch('/api/login/teacher/processTeacherLogin.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password, remember })
                });
                const data = await response.json();

                if (data.status === 'success' || data.success) {
                    Toast.fire({ icon: 'success', title: data.message || 'Login successful!' });
                    setTimeout(() => {
                        window.location.href = data.redirect || '/teacher/dashboard/';
                    }, 1000);
                } else {
                    Toast.fire({ icon: 'error', title: data.message || 'Login failed. Please try again.' });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            } catch (err) {
                Toast.fire({ icon: 'error', title: 'An error occurred. Please try again.' });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }

    // Forgot Password Flow
    const forgotBtn = document.getElementById('forgotBtn');
    forgotBtn?.addEventListener('click', () => openModal('forgotModal'));

    document.getElementById('forgotForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const contact = document.getElementById('contactInput').value.trim();

        if (!contact) {
            return Swal.fire({ icon: 'error', title: 'Required', text: 'Please enter email or phone number.' });
        }

        window._resetContact = contact;

        try {
            await fetch('/api/forgotPassword/teacher/requestReset.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ contact })
            });
            openModal('otpModal');
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to send OTP.' });
        }
    });

    document.getElementById('otpForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const contact = window._resetContact;
        const otpInputs = document.querySelectorAll('.otp-input');
        const otp = Array.from(otpInputs).map(input => input.value).join('');

        if (otp.length !== 6) {
            return Swal.fire({ icon: 'error', title: 'Invalid OTP', text: 'Please enter the 6-digit code.' });
        }

        try {
            await fetch('/api/forgotPassword/teacher/verifyOtp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ contact, otp })
            });
            closeModal('otpModal');
            openModal('resetModal');
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'OTP Error', text: 'Failed to verify OTP.' });
        }
    });

    document.getElementById('resetForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const password = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const contact = window._resetContact;

        if (password !== confirmPassword) {
            return Swal.fire({ icon: 'error', title: 'Mismatch', text: 'Passwords do not match.' });
        }

        try {
            await fetch('/api/forgotPassword/teacher/resetPassword.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ contact, password })
            });
            closeModal('resetModal');
            Swal.fire({
                icon: 'success',
                title: 'Password Reset',
                text: 'Your password has been successfully reset!',
                confirmButtonColor: '#10b981',
            });
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Reset Error', text: 'Could not reset password.' });
        }
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