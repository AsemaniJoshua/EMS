<?php
// teacher/index.php

// Determine which page content to load
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; // Default to 'dashboard'

// Set active page for sidebar highlighting
$active_page = $page;

// Define the path to the content file
$content_file = '';
switch ($page) {
    case 'dashboard':
        $content_file = 'dashboard/index.php';
        break;
    case 'exams':
        $content_file = 'exam/index.php'; // Corresponds to the 'exam' folder
        break;
    case 'results':
        $content_file = 'results/index.php';
        break;
    case 'profile':
        $content_file = 'profile/index.php';
        break;
    default:
        // Fallback for invalid page requests, e.g., show dashboard or a 404
        $content_file = 'dashboard/index.php';
        $active_page = 'dashboard'; // Ensure dashboard is highlighted for unknown pages
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Examplify</title>
    <link rel="stylesheet" href="../src/output.css">
    <style>
        /* Custom styles for Inter font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Ensure the main content area is not scrollable independently unless content overflows */
        .main-content-area {
            overflow-y: auto; /* Allows scrolling only if content exceeds height */
            height: calc(100vh - 64px); /* Adjust based on navbar height if fixed */
        }

        /* Custom scrollbar for a cleaner look (optional, for webkit browsers) */
        .main-content-area::-webkit-scrollbar {
            width: 8px;
        }
        .main-content-area::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .main-content-area::-webkit-scrollbar-thumb {
            background: #cbd5e1; /* gray-300 */
            border-radius: 10px;
        }
        .main-content-area::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; /* gray-400 */
        }

        /* Mobile sidebar transition */
        #mobile-sidebar {
            transition: transform 0.3s ease-out;
            transform: translateX(-100%); /* Start off-screen */
        }
        #mobile-sidebar.open {
            transform: translateX(0); /* Slide in */
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-50 text-gray-800">

    <?php
    // Include the reusable navbar and sidebar component
    include 'components/index.php';
    ?>

    <!-- Main Content Area (where dynamic content will be loaded) -->
    <main class="flex-1 p-6 md:p-8 bg-gray-50 main-content-area">
        <?php
        // Include the content for the selected page
        if (file_exists($content_file)) {
            include $content_file;
        } else {
            // Handle case where content file does not exist
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">';
            echo '<strong class="font-bold">Error!</strong>';
            echo '<span class="block sm:inline ml-2">Content for this page could not be loaded.</span>';
            echo '</div>';
        }
        ?>
    </main>
</div>

<script>
    // JavaScript for Mobile Sidebar Toggle (moved here as it controls elements within this main layout)
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const closeSidebar = document.getElementById('close-sidebar');

    if (sidebarToggle && mobileSidebar && closeSidebar) {
        sidebarToggle.addEventListener('click', () => {
            mobileSidebar.classList.add('open');
        });

        closeSidebar.addEventListener('click', () => {
            mobileSidebar.classList.remove('open');
        });

        // Optional: Close sidebar when clicking outside (for full overlay)
        // document.addEventListener('click', (event) => {
        //     if (!mobileSidebar.contains(event.target) && !sidebarToggle.contains(event.target) && mobileSidebar.classList.contains('open')) {
        //         mobileSidebar.classList.remove('open');
        //     }
        // });
    } else {
        console.error("Sidebar elements not found. Mobile sidebar functionality might be impaired.");
    }

    // This script will also handle any specific JS for the loaded content,
    // but for now, it just manages the sidebar.
</script>
</body>
</html>
