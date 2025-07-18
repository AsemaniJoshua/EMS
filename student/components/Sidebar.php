<?php
// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>

<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-60 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
    <div class="flex items-center justify-center h-16 px-4 bg-emerald-600">
        <h1 class="text-xl font-bold text-white">EMS Student</h1>
    </div>
    
    <nav class="mt-5 px-2">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="/student/dashboard/" 
               class="<?php echo ($currentDir == 'dashboard') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-tachometer-alt <?php echo ($currentDir == 'dashboard') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                Dashboard
            </a>
            
            <!-- Exams -->
            <a href="/student/exam/" 
               class="<?php echo ($currentDir == 'exam') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-clipboard-list <?php echo ($currentDir == 'exam') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                Exams
            </a>
            
            <!-- Results -->
            <a href="/student/results/" 
               class="<?php echo ($currentDir == 'results') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-chart-bar <?php echo ($currentDir == 'results') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                Results
            </a>
            
            <!-- Profile -->
            <a href="/student/profile/" 
               class="<?php echo ($currentDir == 'profile') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-user <?php echo ($currentDir == 'profile') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                Profile
            </a>
            
            <!-- Notifications -->
            <a href="/student/notifications/" 
               class="<?php echo ($currentDir == 'notifications') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-bell <?php echo ($currentDir == 'notifications') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                Notifications
                <?php
                // Get unread notifications count
                if (isset($_SESSION['student_id'])) {
                    try {
                        require_once __DIR__ . '/../../api/config/database.php';
                        $db = new Database();
                        $conn = $db->getConnection();
                        
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND seen = 0");
                        $stmt->bindParam(':user_id', $_SESSION['student_id']);
                        $stmt->execute();
                        $unreadCount = $stmt->fetchColumn();
                        
                        if ($unreadCount > 0) {
                            echo '<span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">' . $unreadCount . '</span>';
                        }
                    } catch (Exception $e) {
                        // Silently fail - don't show notification count if there's an error
                    }
                }
                ?>
            </a>
        </div>
        
        <!-- Divider -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <div class="space-y-1">
                <!-- Help & Support -->
                <a href="/student/help/" 
                   class="<?php echo ($currentDir == 'help') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                    <i class="fas fa-question-circle <?php echo ($currentDir == 'help') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                    Help & Support
                </a>
                
                <!-- Settings -->
                <a href="/student/settings/" 
                   class="<?php echo ($currentDir == 'settings') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                    <i class="fas fa-cog <?php echo ($currentDir == 'settings') ? 'text-emerald-500': 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                    Settings
                </a>
                
                <!-- Logout -->
                <button onclick="logoutStudent()" 
                        class="w-full text-left text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                    <i class="fas fa-sign-out-alt text-gray-400 group-hover:text-gray-500 mr-3 flex-shrink-0 h-5 w-5"></i>
                    Logout
                </button>
            </div>
        </div>
    </nav>
</div>

<!-- Sidebar overlay for mobile -->
<div id="sidebar-overlay" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 hidden lg:hidden"></div>

<script>
// Sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

// Close sidebar when clicking overlay
document.getElementById('sidebar-overlay').addEventListener('click', toggleSidebar);

// Logout function
function logoutStudent() {
    if (confirm('Are you sure you want to logout?')) {
        fetch('/api/student/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url || '/student/login/';
            } else {
                alert('Logout failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Logout error:', error);
            // Force redirect even if API fails
            window.location.href = '/student/login/';
        });
    }
}
</script>

