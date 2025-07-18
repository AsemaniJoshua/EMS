 // Student Profile JavaScript

let profileData = null;
let performanceChart = null;
let gradeChart = null;

document.addEventListener('DOMContentLoaded', function() {
    loadProfile();
    initializeEventListeners();
});

function initializeEventListeners() {
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });

    // Edit profile button
    document.getElementById('editProfileBtn').addEventListener('click', openEditProfileModal);
    
    // Change password button
    document.getElementById('changePasswordBtn').addEventListener('click', openChangePasswordModal);
    
    // Modal close buttons
    document.getElementById('cancelEditBtn').addEventListener('click', closeEditProfileModal);
    document.getElementById('cancelPasswordBtn').addEventListener('click', closeChangePasswordModal);
    
    // Form submissions
    document.getElementById('editProfileForm').addEventListener('submit', handleProfileUpdate);
    document.getElementById('changePasswordForm').addEventListener('submit', handlePasswordChange);
    
    // Close modals when clicking outside
    document.getElementById('editProfileModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditProfileModal();
    });
    
    document.getElementById('changePasswordModal').addEventListener('click', function(e) {
        if (e.target === this) closeChangePasswordModal();
    });
}

async function loadProfile() {
    try {
        showLoading();
        
        const response = await fetch('/api/student/getProfile.php');
        const data = await response.json();
        
        if (data.success) {
            profileData = data.data;
            populateProfile(profileData);
            hideLoading();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error loading profile:', error);
        showNotification('Failed to load profile: ' + error.message, 'error');
        hideLoading();
    }
}

function populateProfile(data) {
    const profile = data.profile;
    const stats = data.statistics;
    
    // Profile header
    document.getElementById('profileName').textContent = profile.full_name;
    document.getElementById('profileIndexNumber').textContent = `Index: ${profile.index_number}`;
    document.getElementById('profileProgram').innerHTML = `<i class="fas fa-graduation-cap mr-1"></i>${profile.program_name}`;
    document.getElementById('profileDepartment').innerHTML = `<i class="fas fa-building mr-1"></i>${profile.department_name}`;
    document.getElementById('profileLevel').innerHTML = `<i class="fas fa-layer-group mr-1"></i>${profile.level_name}`;
    
    // Status badge
    const statusElement = document.getElementById('profileStatus');
    const statusClass = profile.status === 'active' ? 'bg-green-100 text-green-800' : 
                       profile.status === 'inactive' ? 'bg-red-100 text-red-800' : 
                       'bg-purple-100 text-purple-800';
    statusElement.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ${statusClass}`;
    statusElement.innerHTML = `<i class="fas fa-${profile.status === 'active' ? 'check-circle' : profile.status === 'inactive' ? 'times-circle' : 'graduation-cap'} mr-1"></i>${profile.status.charAt(0).toUpperCase() + profile.status.slice(1)}`;
    
    // Statistics
    document.getElementById('totalExams').textContent = stats.total_completed_exams;
    document.getElementById('averageScore').textContent = `${stats.average_score}%`;
    document.getElementById('highestScore').textContent = `${stats.highest_score}%`;
    document.getElementById('passRate').textContent = `${stats.pass_rate}%`;
    
    // Personal information
    document.getElementById('firstName').textContent = profile.first_name || 'Not provided';
    document.getElementById('lastName').textContent = profile.last_name || 'Not provided';
    document.getElementById('email').textContent = profile.email || 'Not provided';
    document.getElementById('phoneNumber').textContent = profile.phone_number || 'Not provided';
    document.getElementById('dateOfBirth').textContent = profile.date_of_birth ? 
        new Date(profile.date_of_birth).toLocaleDateString() : 'Not provided';
    document.getElementById('gender').textContent = profile.gender ? 
        profile.gender.charAt(0).toUpperCase() + profile.gender.slice(1) : 'Not provided';
    document.getElementById('username').textContent = profile.username || 'Not provided';
    document.getElementById('joinedDate').textContent = profile.joined_date || 'Not provided';
    
    // Populate courses table
    populateCoursesTable(data.courses);
    
    // Populate activity timeline
    populateActivityTimeline(data.recent_activity);
}

function populateCoursesTable(courses) {
    const tbody = document.getElementById('coursesTable');
    tbody.innerHTML = '';
    
    if (courses.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    No courses found
                </td>
            </tr>
        `;
        return;
    }
    
    courses.forEach(course => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${course.code}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${course.title}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${course.credits}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${course.department_name}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${course.total_exams}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${course.completed_exams}
            </td>
        `;
        tbody.appendChild(row);
    });
}

function populateActivityTimeline(activities) {
    const timeline = document.getElementById('activityTimeline');
    timeline.innerHTML = '';
    
    if (activities.length === 0) {
        timeline.innerHTML = '<p class="text-gray-500 text-center">No recent activity</p>';
        return;
    }
    
    activities.forEach(activity => {
        const activityElement = document.createElement('div');
        activityElement.className = 'flex items-start space-x-3';
        
        const iconClass = activity.activity_type === 'exam_completed' ? 
            (activity.activity_status === 'passed' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') :
            'bg-blue-100 text-blue-600';
        
        const iconName = activity.activity_type === 'exam_completed' ? 
            (activity.activity_status === 'passed' ? 'check-circle' : 'times-circle') :
            'plus-circle';
        
        activityElement.innerHTML = `
            <div class="flex-shrink-0 w-8 h-8 ${iconClass} rounded-full flex items-center justify-center">
                <i class="fas fa-${iconName} text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">${activity.activity_title}</p>
                <p class="text-sm text-gray-500">${activity.activity_description}</p>
                <p class="text-xs text-gray-400">${new Date(activity.activity_date).toLocaleString()}</p>
            </div>
        `;
        
        timeline.appendChild(activityElement);
    });
}

function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'border-emerald-500', 'text-emerald-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active', 'border-emerald-500', 'text-emerald-600');
    document.querySelector(`[data-tab="${tabName}"]`).classList.remove('border-transparent', 'text-gray-500');
    
    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show selected tab content
    document.getElementById(`${tabName}Tab`).classList.remove('hidden');
    
    // Load specific tab data
    if (tabName === 'academic') {
        loadAcademicPerformance();
    }
}

async function loadAcademicPerformance() {
    try {
        const response = await fetch('/api/student/getAcademicPerformance.php');
        const data = await response.json();
        
        if (data.success) {
            populateSubjectPerformance(data.data.subject_performance);
            createPerformanceChart(data.data.performance_over_time);
            createGradeChart(data.data.grade_distribution);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error loading academic performance:', error);
        showNotification('Failed to load academic performance: ' + error.message, 'error');
    }
}

function populateSubjectPerformance(subjects) {
    const tbody = document.getElementById('subjectPerformanceTable');
    tbody.innerHTML = '';
    
    if (subjects.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    No performance data available
                </td>
            </tr>
        `;
        return;
    }
    
    subjects.forEach(subject => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${subject.course_code}</div>
                <div class="text-sm text-gray-500">${subject.course_title}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${subject.exam_count}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${parseFloat(subject.average_score).toFixed(1)}%
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${parseFloat(subject.highest_score).toFixed(1)}%
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${parseFloat(subject.lowest_score).toFixed(1)}%
            </td>
        `;
        tbody.appendChild(row);
    });
}

function createPerformanceChart(performanceData) {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (performanceChart) {
        performanceChart.destroy();
    }
    
    const labels = performanceData.map(item => {
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    });
    
    const scores = performanceData.map(item => parseFloat(item.average_score));
    
    performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Average Score',
                data: scores,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function createGradeChart(gradeData) {
    const ctx = document.getElementById('gradeChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (gradeChart) {
        gradeChart.destroy();
    }
    
    const labels = gradeData.map(item => `Grade ${item.grade}`);
    const counts = gradeData.map(item => parseInt(item.count));
    
    const colors = {
        'A': '#10B981',
        'B': '#3B82F6',
        'C': '#F59E0B',
        'D': '#EF4444',
        'F': '#6B7280'
    };
    
    const backgroundColors = gradeData.map(item => colors[item.grade] || '#6B7280');
    
    gradeChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: backgroundColors,
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function openEditProfileModal() {
    if (!profileData) return;
    
    const profile = profileData.profile;
    
    // Populate form fields
    document.getElementById('editFirstName').value = profile.first_name || '';
    document.getElementById('editLastName').value = profile.last_name || '';
    document.getElementById('editEmail').value = profile.email || '';
    document.getElementById('editPhoneNumber').value = profile.phone_number || '';
    document.getElementById('editDateOfBirth').value = profile.date_of_birth || '';
    document.getElementById('editGender').value = profile.gender || '';
    
    document.getElementById('editProfileModal').classList.remove('hidden');
}

function closeEditProfileModal() {
    document.getElementById('editProfileModal').classList.add('hidden');
    document.getElementById('editProfileForm').reset();
}

function openChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('hidden');
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('hidden');
    document.getElementById('changePasswordForm').reset();
}

async function handleProfileUpdate(e) {
    e.preventDefault();
    
    const formData = {
        first_name: document.getElementById('editFirstName').value.trim(),
        last_name: document.getElementById('editLastName').value.trim(),
        email: document.getElementById('editEmail').value.trim(),
        phone_number: document.getElementById('editPhoneNumber').value.trim(),
        date_of_birth: document.getElementById('editDateOfBirth').value || null,
        gender: document.getElementById('editGender').value || null
    };
    
    // Validate required fields
    if (!formData.first_name || !formData.last_name || !formData.email || !formData.phone_number) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    try {
        const response = await fetch('/api/student/updateProfile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Profile updated successfully', 'success');
            closeEditProfileModal();
            loadProfile(); // Reload profile data
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error updating profile:', error);
        showNotification('Failed to update profile: ' + error.message, 'error');
    }
}

async function handlePasswordChange(e) {
    e.preventDefault();
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validate passwords
    if (newPassword !== confirmPassword) {
        showNotification('New password and confirm password do not match', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        showNotification('New password must be at least 6 characters long', 'error');
        return;
    }
    
    try {
        const response = await fetch('/api/student/changePassword.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Password changed successfully', 'success');
            closeChangePasswordModal();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error changing password:', error);
        showNotification('Failed to change password: ' + error.message, 'error');
    }
}

function showLoading() {
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('profileContent').classList.add('hidden');
}

function hideLoading() {
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('profileContent').classList.remove('hidden');
}

function showNotification(message, type = 'info') {
    // Use SweetAlert2 for notifications
    const icon = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
    
    Swal.fire({
        icon: icon,
        title: type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Info',
        text: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

// Utility function to format date
function formatDate(dateString) {
    if (!dateString) return 'Not provided';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Utility function to capitalize first letter
function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

