<?php

/**
 * Teacher sidebar component
 * Renders the sidebar navigation for teacher pages
 */

function renderTeacherSidebar($currentPage = '')
{
?>
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-20 w-64 bg-emerald-800 shadow-lg transform transition-transform lg:translate-x-0 -translate-x-full" id="sidebar">
        <div class="flex items-center justify-center h-16 bg-emerald-900">
            <span class="text-white font-bold text-xl">EMS Teacher</span>
        </div>
        <nav class="mt-5">
            <div class="px-2 space-y-1">
                <a href="/teacher/dashboard/" class="group flex items-center px-2 py-3 text-base font-medium rounded-md <?php echo $currentPage === 'dashboard' ? 'bg-emerald-900 text-white' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white'; ?>">
                    <i class="fas fa-tachometer-alt mr-3 text-emerald-300"></i>
                    Dashboard
                </a>

                <a href="/teacher/exam/" class="group flex items-center px-2 py-3 text-base font-medium rounded-md <?php echo $currentPage === 'exams' ? 'bg-emerald-900 text-white' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white'; ?>">
                    <i class="fas fa-file-alt mr-3 text-emerald-300"></i>
                    Exams
                </a>

                <a href="/teacher/results/" class="group flex items-center px-2 py-3 text-base font-medium rounded-md <?php echo $currentPage === 'results' ? 'bg-emerald-900 text-white' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white'; ?>">
                    <i class="fas fa-chart-bar mr-3 text-emerald-300"></i>
                    Results
                </a>

                <a href="/teacher/profile/" class="group flex items-center px-2 py-3 text-base font-medium rounded-md <?php echo $currentPage === 'profile' ? 'bg-emerald-900 text-white' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white'; ?>">
                    <i class="fas fa-user mr-3 text-emerald-300"></i>
                    Profile
                </a>

                <div class="pt-4 mt-4 border-t border-emerald-700">
                    <a href="/api/login/logout.php" class="group flex items-center px-2 py-3 text-base font-medium rounded-md text-emerald-100 hover:bg-emerald-700 hover:text-white">
                        <i class="fas fa-sign-out-alt mr-3 text-emerald-300"></i>
                        Logout
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Mobile sidebar overlay -->
    <div class="fixed inset-0 z-10 bg-gray-600 opacity-0 pointer-events-none transition-opacity lg:hidden" id="sidebarOverlay"></div>
<?php
}

// Add JavaScript for sidebar interactions
function renderSidebarJS()
{
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');

            // Toggle mobile sidebar
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    sidebarOverlay.classList.toggle('opacity-0');
                    sidebarOverlay.classList.toggle('pointer-events-none');
                });
            }

            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('opacity-0');
                    sidebarOverlay.classList.add('pointer-events-none');
                });
            }
        });
    </script>
<?php
}
