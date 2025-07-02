document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/teacher/profile')
        .then(response => {
            if (!response.ok) throw new Error('Failed to load profile data');
            return response.json();
        })
        .then(data => {
            showNotification('Profile page loaded (placeholder)', 'success');
        })
        .catch(error => {
            showNotification('Failed to load profile data', 'error');
        });

    // Profile Form Submission
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(profileForm);
            const data = Object.fromEntries(formData.entries());
            // Replace with real API call to /api/teacher/profile/update
            fetch('/api/teacher/profile/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    showNotification('Profile changes saved!', 'success');
                })
                .catch(error => {
                    showNotification('Failed to save profile changes.', 'error');
                });
        });
    }
    // Change Password Form Submission
    const changePasswordForm = document.getElementById('change-password-form');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const newPassword = document.getElementById('new-password').value;
            const confirmNewPassword = document.getElementById('confirm-new-password').value;
            if (newPassword !== confirmNewPassword) {
                showNotification('New passwords do not match!', 'error');
                return;
            }
            const data = {
                current_password: document.getElementById('current-password').value,
                new_password: newPassword
            };
            // Replace with real API call to /api/teacher/profile/change-password
            fetch('/api/teacher/profile/change-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    showNotification('Password updated successfully!', 'success');
                })
                .catch(error => {
                    showNotification('Failed to update password.', 'error');
                });
        });
    }
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
