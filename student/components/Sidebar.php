<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
function isStudentActive($dir) {
    global $current_dir;
    return $current_dir === $dir;
}
?>
<aside id="sidebar" class="fixed left-0 top-0 min-h-screen lg:h-screen h-[100dvh] w-60 bg-white shadow-2xl z-100 flex flex-col overflow-y-auto transform transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full">
    <!-- Header -->
    <div class="p-5 border-b border-emerald-100 flex items-center justify-between bg-white">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center shadow-md">
                <i class="fas fa-user-graduate text-white text-xl"></i>
            </div>
            <span class="text-xl font-bold text-gray-800">EMS Student</span>
        </div>
        <button id="closeSidebar" class="lg:hidden text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close sidebar">
            <i class="fas fa-times w-6 h-6"></i>
        </button>
    </div>
    <!-- Navigation -->
    <nav class="flex-1 p-4 overflow-y-auto">
        <ul class="space-y-1">
            <li>
                <a href="../dashboard/index.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50 <?php echo isStudentActive('dashboard') ? 'bg-emerald-100 text-emerald-800 border-l-4 border-emerald-600 font-semibold' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 hover:border-l-4 hover:border-emerald-300'; ?>">
                    <i class="fas fa-tachometer-alt text-lg mr-3 flex-shrink-0 <?php echo isStudentActive('dashboard') ? 'text-emerald-700' : 'text-gray-500 group-hover:text-emerald-600'; ?>"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="../exam/index.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50 <?php echo isStudentActive('exam') ? 'bg-emerald-100 text-emerald-800 border-l-4 border-emerald-600 font-semibold' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 hover:border-l-4 hover:border-emerald-300'; ?>">
                    <i class="fas fa-clipboard text-lg mr-3 flex-shrink-0 <?php echo isStudentActive('exam') ? 'text-emerald-700' : 'text-gray-500 group-hover:text-emerald-600'; ?>"></i>
                    Exams
                </a>
            </li>
            <li>
                <a href="../results/index.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50 <?php echo isStudentActive('results') ? 'bg-emerald-100 text-emerald-800 border-l-4 border-emerald-600 font-semibold' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 hover:border-l-4 hover:border-emerald-300'; ?>">
                    <i class="fas fa-chart-bar text-lg mr-3 flex-shrink-0 <?php echo isStudentActive('results') ? 'text-emerald-700' : 'text-gray-500 group-hover:text-emerald-600'; ?>"></i>
                    Results
                </a>
            </li>
            <li>
                <a href="../profile/index.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50 <?php echo isStudentActive('profile') ? 'bg-emerald-100 text-emerald-800 border-l-4 border-emerald-600 font-semibold' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 hover:border-l-4 hover:border-emerald-300'; ?>">
                    <i class="fas fa-user text-lg mr-3 flex-shrink-0 <?php echo isStudentActive('profile') ? 'text-emerald-700' : 'text-gray-500 group-hover:text-emerald-600'; ?>"></i>
                    Profile
                </a>
            </li>
        </ul>
    </nav>
    <!-- Logout -->
    <div class="p-4 border-t border-emerald-100 bg-white">
        <a href="../login/index.php" class="group flex items-center px-3 py-2.5 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
            <i class="fas fa-sign-out-alt w-5 h-5 mr-3 flex-shrink-0 text-red-500 group-hover:text-red-600"></i>
            Logout
        </a>
    </div>
</aside>
<!-- Mobile overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 lg:hidden hidden"></div>
<!-- Mobile menu button -->
<button
    id="openSidebar"
    class="lg:hidden fixed top-4 left-4 p-2.5 w-10 h-10 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-lg drop-shadow-lg z-50 focus:outline-none transition-colors duration-200 flex items-center justify-center"
    aria-label="Open sidebar">
    <i class="fas fa-bars w-5 h-5"></i>
</button>
<script>
// Sidebar toggle functionality (same as admin)
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
    const openBtn = document.getElementById('openSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (openBtn) openBtn.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) closeSidebar();
    });
});
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
</script> 