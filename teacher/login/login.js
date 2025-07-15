document.addEventListener('DOMContentLoaded', function () {
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
        });
    }
    
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
            warning: 'bg-orange-500'
        };
        
        notification.className = `fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg text-white text-base font-semibold z-50 transform transition-all duration-300 ${colors[type] || colors.info}`;
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        notification.style.display = 'block';
        notification.style.transform = 'translateX(0)';
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 300);
        }, 3000);
    }
    
    // Add some basic validation
    const usernameEmailInput = document.getElementById('username-email');
    const passwordInput = document.getElementById('password');
    
    if (usernameEmailInput) {
        usernameEmailInput.addEventListener('blur', function() {
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
        passwordInput.addEventListener('blur', function() {
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
