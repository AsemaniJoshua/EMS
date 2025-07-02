<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
function isStudentActive($dir) {
    global $current_dir;
    return $current_dir === $dir;
}
?>
<aside id="sidebar" class="fixed left-0 top-0 min-h-screen lg:h-screen h-[100dvh] w-60 bg-white shadow-lg z-50 flex flex-col overflow-y-auto transform transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full">
    <div class="p-6 text-2xl font-bold text-blue-600 border-b flex items-center justify-between">
        <span>EMS Student</span>
        <button id="closeSidebar" class="lg:hidden text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <nav class="flex-1 p-4 overflow-y-auto">
        <ul class="space-y-6">
            <li>
                <a href="../dashboard/index.php" class="block font-medium transition-colors duration-200 <?php echo isStudentActive('dashboard') ? 'text-blue-600 bg-blue-50 px-3 py-2 rounded-lg' : 'text-gray-700 hover:text-blue-600'; ?>">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                        </svg>
                        Dashboard
                    </div>
                </a>
            </li>
            <li>
                <a href="../exam/index.php" class="block font-medium transition-colors duration-200 <?php echo isStudentActive('exam') ? 'text-blue-600 bg-blue-50 px-3 py-2 rounded-lg' : 'text-gray-700 hover:text-blue-600'; ?>">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                        Exams
                    </div>
                </a>
            </li>
            <li>
                <a href="../results/index.php" class="block font-medium transition-colors duration-200 <?php echo isStudentActive('results') ? 'text-blue-600 bg-blue-50 px-3 py-2 rounded-lg' : 'text-gray-700 hover:text-blue-600'; ?>">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Results
                    </div>
                </a>
            </li>
            <li>
                <a href="../profile/index.php" class="block font-medium transition-colors duration-200 <?php echo isStudentActive('profile') ? 'text-blue-600 bg-blue-50 px-3 py-2 rounded-lg' : 'text-gray-700 hover:text-blue-600'; ?>">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Profile
                    </div>
                </a>
            </li>
        </ul>
    </nav>
    <div class="p-4 border-t">
        <a href="../login/index.php" class="block text-red-500 hover:text-red-700 font-medium transition-colors duration-200 flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
        </a>
    </div>
</aside>

<!-- Mobile overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden"></div>

<script>
// Sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.addEventListener('DOMContentLoaded', function() {
    const closeBtn = document.getElementById('closeSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) closeSidebar();
    });
});
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
</script> 