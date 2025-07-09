<?php

/**
 * Renders the shared header component for Admin Pages.
 * Modern, clean header with responsive design.
 */
function renderAdminHeader()
{
?>
    <header class="fixed top-0 left-0 lg:left-60 right-0 bg-white/90 backdrop-blur-sm border-b border-gray-200 z-40 transition-all duration-300">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center">
                <button id="sidebarToggle" class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <div class="hidden lg:flex items-center">
                    <span class="text-sm font-medium text-gray-700">Examination Management System</span>
                    <span class="mx-2 text-gray-300">|</span>
                    <span class="text-xs text-gray-500"><?php echo date('l, F j, Y'); ?></span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Search - Hidden on mobile -->
                <div class="hidden md:block relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input
                        type="search"
                        placeholder="Search..."
                        class="block w-full pl-10 pr-3 py-1.5 text-sm border border-gray-300 rounded-md bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Notifications -->
                <div class="relative">
                    <button class="p-1.5 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                        <span class="sr-only">View notifications</span>
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>
                </div>

                <!-- User Menu -->
                <div class="relative">
                    <div class="flex items-center">
                        <button
                            type="button"
                            class="flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 rounded-full transition-all duration-200"
                            id="user-menu-button"
                            aria-expanded="false"
                            aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <div class="hidden md:block text-right pr-1">
                                <p class="text-sm font-medium text-gray-800"><?php echo $_SESSION['admin_name']?></p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                            <img
                                src="https://ui-avatars.com/api/?name=Admin&background=10b981&color=fff&bold=true"
                                alt="Admin"
                                class="h-8 w-8 rounded-full object-cover border-2 border-emerald-200" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (typeof toggleSidebar === 'function') {
                        toggleSidebar();
                    }
                });
            }
        });
    </script>
<?php
}
?>