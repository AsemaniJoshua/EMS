<?php

/**
 * Renders the EMS Admin sidebar navigation.
 *
 * @param string $currentPage The identifier of the current active page (e.g., 'dashboard', 'teachers').
 */
function renderAdminSidebar($currentPage)
{

    $menuItems = [
        [
            'name' => 'Dashboard',
            'path' => '../dashboard/index.php',
            'page' => 'dashboard',
            'icon_name' => 'fa-tachometer-alt',
        ],
        [
            'name' => 'Teachers',
            'path' => '../teachers/index.php',
            'page' => 'teachers',
            'icon_name' => 'fa-chalkboard-teacher',
        ],
        [
            'name' => 'Students',
            'path' => '../students/index.php',
            'page' => 'students',
            'icon_name' => 'fa-user-graduate',
        ],
        [
            'name' => 'Exams',
            'path' => '../exams/index.php',
            'page' => 'exams',
            'icon_name' => 'fa-clipboard',
        ],
        [
            'name' => 'Results',
            'path' => '../results/index.php',
            'page' => 'results',
            'icon_name' => 'fa-chart-bar',
        ],
        [
            'name' => 'Approvals',
            'path' => '../approval/index.php',
            'page' => 'approval',
            'icon_name' => 'fa-check-circle',
        ],
        [
            'name' => 'Settings',
            'path' => '../settings/index.php',
            'page' => 'settings',
            'icon_name' => 'fa-cog',
        ],
    ];
?>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 min-h-screen lg:h-screen h-[100dvh] w-60 bg-white shadow-2xl z-100 flex flex-col overflow-y-auto transform transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full">
        <!-- Header -->
        <div class="p-5 border-b border-emerald-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center shadow-md">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>
                <span class="text-xl font-bold text-gray-800">EMS Admin</span>
            </div>
            <button id="closeSidebar" class="lg:hidden text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close sidebar">
                <i class="fas fa-times w-6 h-6"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 overflow-y-auto">
            <ul class="space-y-1">
                <?php
                foreach ($menuItems as $item) {
                ?>
                    <li>
                        <a href="<?php echo $item['path']; ?>"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50
                           <?php echo ($currentPage === $item['page'])
                                ? 'bg-emerald-100 text-emerald-800 border-l-4 border-emerald-600 font-semibold'
                                : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 hover:border-l-4 hover:border-emerald-300'; ?>">
                            <i class="fas <?php echo $item['icon_name']; ?> text-lg mr-3 flex-shrink-0 <?php echo ($currentPage === $item['page']) ? 'text-emerald-700' : 'text-gray-500 group-hover:text-emerald-600'; ?>"></i>
                            <?php echo $item['name']; ?>
                        </a>
                    </li>
                <?php } ?>
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
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-50 lg:hidden hidden"></div>

    <!-- Mobile menu button -->
    <button
        id="openSidebar"
        class="lg:hidden fixed top-4 left-4 p-2.5 w-10 h-10 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-lg drop-shadow-lg z-50 focus:outline-none transition-colors duration-200 flex items-center justify-center"
        aria-label="Open sidebar">
        <i class="fas fa-bars w-5 h-5"></i>
    </button>

    <script>
        // Sidebar toggle functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent scrolling when sidebar is open
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = 'auto'; // Allow scrolling when sidebar is closed
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const closeBtn = document.getElementById('closeSidebar');
            const openBtn = document.getElementById('openSidebar'); // Get the open button
            const overlay = document.getElementById('sidebarOverlay');

            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            if (openBtn) { // Add event listener for the open button
                openBtn.addEventListener('click', toggleSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar on window resize if screen becomes large
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) { // Tailwind's 'lg' breakpoint
                    closeSidebar();
                }
            });
        });

        // Make toggleSidebar globally available
        window.toggleSidebar = toggleSidebar;
        window.closeSidebar = closeSidebar;
    </script>
<?php
}
?>