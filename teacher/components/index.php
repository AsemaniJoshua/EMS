<?php
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}
// teacher/components/index.php

// This file contains the reusable HTML for the Navbar and Sidebar.
// It is designed to be included in other PHP pages within the teacher dashboard.

// Define a variable to indicate the active page for sidebar highlighting
// This variable should be set in the including page (e.g., $active_page = 'dashboard';)
if (!isset($active_page)) {
    $active_page = ''; // Default to no active page if not set
}

// Get teacher name from session
$teacherName = $_SESSION['teacher_name'] ?? 'Teacher';
?>

<!-- Top Navigation Bar -->
<header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center sticky top-0 z-40 border-b border-gray-100">
    <div class="flex items-center space-x-2">
        <!-- Hamburger menu for mobile -->
        <button id="sidebar-toggle" class="md:hidden text-gray-600 hover:text-emerald-600 focus:outline-none mr-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <!-- Examplify Logo/Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <path d="M14 2v6h6"></path>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <line x1="10" y1="9" x2="8" y2="9"></line>
        </svg>
        <span class="text-xl font-bold text-gray-900">Examplify</span>
    </div>
    <div class="flex items-center space-x-4">
        <span class="text-gray-700 text-sm md:text-base">Welcome, <?php echo htmlspecialchars($teacherName); ?>!</span>
        <a href="/teacher/login/" onclick="logout()" class="px-4 py-2 rounded-full bg-emerald-600 text-white text-sm hover:bg-emerald-700 transition-colors duration-200 shadow-md">Logout</a>
    </div>
</header>

<!-- Main Layout: Sidebar and Content Wrapper -->
<div class="flex flex-1">
    <!-- Sidebar for Desktop -->
    <aside class="hidden md:flex flex-col w-60 bg-white shadow-lg border-r border-gray-100 py-8 px-4 space-y-2 sticky top-0 h-screen z-30">
        <h3 class="text-lg font-semibold text-gray-900 mb-6 px-2 tracking-wide uppercase">Navigation</h3>
        <a href="index.php?page=dashboard" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-colors duration-200
            <?php echo ($active_page == 'dashboard') ? 'text-emerald-600 bg-emerald-50 font-medium' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
            </svg>
            Dashboard
        </a>
        <a href="index.php?page=exams" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-colors duration-200
            <?php echo ($active_page == 'exams') ? 'text-emerald-600 bg-emerald-50 font-medium' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <path d="M14 2v6h6"></path>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <line x1="10" y1="9" x2="8" y2="9"></line>
            </svg>
            Exams
        </a>
        <a href="index.php?page=results" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-colors duration-200
            <?php echo ($active_page == 'results') ? 'text-emerald-600 bg-emerald-50 font-medium' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                <path d="M2 13V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v7"></path>
                <path d="M2 13h20"></path>
                <path d="M12 20v-7"></path>
                <path d="M8 20v-7"></path>
                <path d="M16 20v-7"></path>
            </svg>
            Results
        </a>
        <a href="index.php?page=profile" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-colors duration-200
            <?php echo ($active_page == 'profile') ? 'text-emerald-600 bg-emerald-50 font-medium' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            Profile
        </a>
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-60 bg-white shadow-xl z-50 md:hidden flex flex-col py-8 px-4 space-y-2 border-r border-gray-100">
        <button id="close-sidebar" class="self-end text-gray-600 hover:text-emerald-600 focus:outline-none mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <h3 class="text-lg font-semibold text-gray-900 mb-6 px-2 tracking-wide uppercase">Navigation</h3>
        <a href="index.php?page=dashboard" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-colors duration-200
            <?php echo ($active_page == 'dashboard') ? 'text-emerald-600 bg-emerald-50 font-medium' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
            </svg>
            Dashboard
        </a>
        <a href="index.php?page=exams" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-colors duration-200
            <?php echo ($active_page == 'exams') ? 'text-emerald-600 bg-emerald-50 font-medium' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <path d="M14 2v6h6"></path>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <line x1="10" y1="9" x2="8" y2="9"></line>
            </svg>
            Exams
        </a>
        <a href="index.php?page=results" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-colors duration-200
            <?php echo ($active_page == 'results') ? 'text-emerald-600 bg-emerald-50 font-medium' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                <path d="M2 13V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v7"></path>
                <path d="M2 13h20"></path>
                <path d="M12 20v-7"></path>
                <path d="M8 20v-7"></path>
                <path d="M16 20v-7"></path>
            </svg>
            Results
        </a>
        <a href="index.php?page=profile" class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 transition-colors duration-200
            <?php echo ($active_page == 'profile') ? 'text-emerald-600 bg-emerald-50 font-medium' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            Profile
        </a>
    </div>

<script>
// Logout function
function logout() {
    // Clear session data
    fetch('/api/login/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(() => {
        // Redirect to login page
        window.location.href = '/teacher/login/';
    })
    .catch(error => {
        console.error('Logout error:', error);
        // Still redirect even if logout API fails
        window.location.href = '/teacher/login/';
    });
}
</script>
