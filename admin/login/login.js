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

    // Get the login form
    const loginForm = document.getElementById('adminLoginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';

            // Get form data
            const email = this.querySelector('input[type="email"]').value;
            const password = this.querySelector('input[type="password"]').value;
            const remember = this.querySelector('input[type="checkbox"]').checked;

            // Make API request using Axios
            axios.post('/api/login/admin/processLogin.php', {
                email: email,
                password: password,
                remember: remember
            })
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
                            window.location.href = data.redirect || '/admin/dashboard/';
                        }, 1000);
                    } else {
                        // Show error message
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Login failed. Please try again.'
                        });

                        // Reset button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(function (error) {
                    console.error('Login error:', error);

                    // Show error message
                    Toast.fire({
                        icon: 'error',
                        title: 'An error occurred. Please try again.'
                    });

                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
        });
    }
});
