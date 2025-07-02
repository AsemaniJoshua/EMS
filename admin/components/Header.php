<?php
// Shared Header component for Admin Pages
?>
<header class="fixed top-0 left-0 lg:left-64 right-0 bg-white shadow-md z-40 flex items-center justify-between px-4 lg:px-8 py-4 transition-all duration-300">
    <div class="flex items-center">
        <button id="sidebarToggle" class="lg:hidden mr-4 text-gray-600 hover:text-gray-800 focus:outline-none focus:text-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div class="text-lg lg:text-xl font-bold text-blue-600">Exams Management System</div>
    </div>
    <div class="flex items-center space-x-2 lg:space-x-4">
        <span class="hidden sm:block text-gray-700 font-medium">Welcome, Admin</span>
        <div class="relative">
            <img src="https://ui-avatars.com/api/?name=Admin" alt="Admin Avatar" class="w-8 h-8 lg:w-10 lg:h-10 rounded-full border" />
            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
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