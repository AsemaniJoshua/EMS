<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get student information from session
$studentName = $_SESSION['student_name'] ?? 'Student';
$studentEmail = $_SESSION['student_email'] ?? '';
$studentProgram = $_SESSION['student_program'] ?? '';
?>

<!-- Header -->
<header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-30 lg:left-60">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Mobile menu button -->
            <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500">
                <i class="fas fa-bars h-6 w-6"></i>
            </button>
            
            <!-- Page title (will be updated by individual pages) -->
            <div class="flex-1 lg:flex-none">
                <h1 id="page-title" class="text-lg font-semibold text-gray-900 lg:hidden">
                    <?php echo $pageTitle ?? 'EMS Student'; ?>
                </h1>
            </div>
            
            <!-- Right side items -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button onclick="toggleNotifications()" class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <i class="fas fa-bell h-5 w-5"></i>
                        <!-- Notification badge -->
                        <span id="notification-badge" class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden">
                            0
                        </span>
                    </button>
                    
                    <!-- Notifications dropdown -->
                    <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <div class="px-4 py-2 text-sm font-medium text-gray-900 border-b border-gray-200">
                                Notifications
                            </div>
                            <div id="notifications-list" class="max-h-64 overflow-y-auto">
                                <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                    Loading notifications...
                                </div>
                            </div>
                            <!-- <div class="border-t border-gray-200">
                                <a href="/student/notifications/" class="block px-4 py-2 text-sm text-emerald-600 hover:bg-gray-50 text-center">
                                    View all notifications
                                </a>
                            </div> -->
                        </div>
                    </div>
                </div>
                
                <!-- Profile dropdown -->
                <div class="relative">
                    <button onclick="toggleProfileMenu()" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 bg-emerald-500 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-white">
                                    <?php echo strtoupper(substr($studentName, 0, 1)); ?>
                                </span>
                            </div>
                        </div>
                        <div class="hidden md:block text-left">
                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($studentName); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($studentProgram); ?></div>
                        </div>
                        <i class="fas fa-chevron-down h-4 w-4 text-gray-400"></i>
                    </button>
                    
                    <!-- Profile dropdown menu -->
                    <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <div class="px-4 py-2 text-sm text-gray-900 border-b border-gray-200">
                                <div class="font-medium"><?php echo htmlspecialchars($studentName); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($studentEmail); ?></div>
                            </div>
                            <a href="/student/profile/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>
                                Your Profile
                            </a>
                            <a href="/student/exam/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-clipboard-list mr-2"></i>
                                Exams
                            </a>
                            <a href="/student/results/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-chart-bar mr-2"></i>
                               Results
                            </a>
                            <div class="border-t border-gray-200"></div>
                            <button onclick="logoutStudent()" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Sign out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Profile dropdown toggle
function toggleProfileMenu() {
    const dropdown = document.getElementById('profile-dropdown');
    dropdown.classList.toggle('hidden');
    
    // Close notifications dropdown if open
    document.getElementById('notifications-dropdown').classList.add('hidden');
}

// Notifications dropdown toggle
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    dropdown.classList.toggle('hidden');
    
    // Close profile dropdown if open
    document.getElementById('profile-dropdown').classList.add('hidden');
    
    // Load notifications if opening
    if (!dropdown.classList.contains('hidden')) {
        loadNotifications();
    }
}

// Load notifications
function loadNotifications() {
    fetch('/api/students/getNotifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.getElementById('notifications-list');
            const notificationBadge = document.getElementById('notification-badge');
            
            if (data.success && data.notifications.length > 0) {
                notificationsList.innerHTML = data.notifications.map(notification => `
                    <div class="px-4 py-3 hover:bg-gray-50 ${!notification.seen ? 'bg-blue-50' : ''}">
                        <div class="text-sm text-gray-900">${escapeHtml(notification.message)}</div>
                        <div class="text-xs text-gray-500 mt-1">${formatNotificationDate(notification.created_at)}</div>
                    </div>
                `).join('');
                
                // Update badge
                const unreadCount = data.notifications.filter(n => !n.seen).length;
                if (unreadCount > 0) {
                    notificationBadge.textContent = unreadCount;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
            } else {
                notificationsList.innerHTML = `
                    <div class="px-4 py-3 text-sm text-gray-500 text-center">
                        No notifications
                    </div>
                `;
                notificationBadge.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Failed to load notifications:', error);
            document.getElementById('notifications-list').innerHTML = `
                <div class="px-4 py-3 text-sm text-red-500 text-center">
                    Failed to load notifications
                </div>
            `;
        });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const profileDropdown = document.getElementById('profile-dropdown');
    const notificationsDropdown = document.getElementById('notifications-dropdown');
    
    if (!event.target.closest('.relative')) {
        profileDropdown.classList.add('hidden');
        notificationsDropdown.classList.add('hidden');
    }
});

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatNotificationDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = Math.floor((now - date) / (1000 * 60 * 60));
    
    if (diffInHours < 1) {
        return 'Just now';
    } else if (diffInHours < 24) {
        return `${diffInHours}h ago`;
    } else {
        return date.toLocaleDateString();
    }
}

// Load notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load notification count
    fetch('/api/students/getNotifications.php?count_only=1')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.unread_count > 0) {
                const badge = document.getElementById('notification-badge');
                badge.textContent = data.unread_count;
                badge.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Failed to load notification count:', error);
        });
});
</script>
