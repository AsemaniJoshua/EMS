<?php
// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>

<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-60 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
    <div class="flex items-center justify-center h-16 px-4 bg-emerald-600 mb-3">
        <h1 class="text-xl font-bold text-white">EMS Student</h1>
    </div>
    
    <nav class="mt-[60px] px-2">
        <div class="space-y-5">
            <!-- Dashboard -->
            <a href="/student/dashboard/" 
               class="<?php echo ($currentDir == 'dashboard') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-4 text-sm font-medium rounded-md transition-colors duration-200 mt-[100px]">
                <i class="fas fa-tachometer-alt <?php echo ($currentDir == 'dashboard') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                Dashboard
            </a>
            
            <!-- Exams -->
            <a href="/student/exam/" 
               class="<?php echo ($currentDir == 'exam') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-4 text-sm font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-clipboard-list <?php echo ($currentDir == 'exam') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                Exams
            </a>
            
            <!-- Results -->
            <a href="/student/results/" 
               class="<?php echo ($currentDir == 'results') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-4 text-sm font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-chart-bar <?php echo ($currentDir == 'results') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                Results
            </a>
            
            <!-- Profile -->
            <a href="/student/profile/" 
               class="<?php echo ($currentDir == 'profile') ? 'bg-emerald-100 text-emerald-700 border-r-4 border-emerald-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-2 py-4 text-sm font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-user <?php echo ($currentDir == 'profile') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500'; ?> mr-3 flex-shrink-0 h-5 w-5"></i>
                Profile
            </a>
        </div>
    </nav>
    <!-- Logout button below the sidebar -->
    <div class="absolute bottom-0 left-0 w-full px-2 pb-6">
        <button onclick="logoutStudent()" 
            class="w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
            <i class="fas fa-sign-out-alt text-red-400 group-hover:text-red-700 mr-3 flex-shrink-0 h-5 w-5"></i>
            Logout
        </button>
    </div>
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
        fetch('/api/students/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/student/login/';
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