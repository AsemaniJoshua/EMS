<?php

/**
 * Renders the shared header component for Teacher Pages.
 * Modern, clean header with responsive design.
 */
function renderTeacherHeader()
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Teacher Portal - Exam Management System</title>
        <!-- Tailwind CSS -->
        <link rel="stylesheet" href="/src/output.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <!-- Axios -->
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>

    <body class="bg-gray-50 min-h-screen">
        <div class="flex h-screen overflow-hidden">
            <!-- Content area -->
            <div class="flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
                <!-- Top navigation -->
                <header class="fixed top-0 left-0 lg:left-64 right-0 bg-white/90 backdrop-blur-sm border-b border-gray-200 z-40 transition-all duration-300">
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
                                            <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['teacher_name'] ?? 'Teacher'); ?></p>
                                            <p class="text-xs text-gray-500">Teacher</p>
                                        </div>
                                        <img
                                            src="https://ui-avatars.com/api/?name=Teacher&background=10b981&color=fff&bold=true"
                                            alt="Teacher"
                                            class="h-8 w-8 rounded-full object-cover border-2 border-emerald-200" />
                                    </button>
                                </div>

                                <!-- Profile dropdown menu (hidden by default) -->
                                <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5" id="profileMenu" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                    <div class="py-1" role="none">
                                        <a href="/teacher/profile/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                                        <a href="/teacher/settings/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
                                        <a href="/api/login/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main content -->
                <main class="flex-1 p-4 pt-20 sm:p-6 sm:pt-24 lg:p-8 lg:pt-24">
                    <!-- Content will be injected here -->
                <?php
            }

            /**
             * Renders the document opening part with metadata and CSS/JS resources
             * This allows for custom title and additional resources
             * 
             * @param string $title The page title (defaults to "Teacher Portal")
             * @param array $additionalResources Additional CSS/JS to include
             */
            function renderTeacherDocumentStart($title = "Teacher Portal - Exam Management System", $additionalResources = [])
            {
                ?>
                    <!DOCTYPE html>
                    <html lang="en">

                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title><?php echo htmlspecialchars($title); ?></title>
                        <!-- Tailwind CSS -->
                        <link rel="stylesheet" href="/src/output.css">
                        <!-- Font Awesome -->
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                        <!-- SweetAlert2 -->
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                        <!-- Axios -->
                        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
                        <!-- SweetAlert2 JS -->
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                        <!-- Chart.js -->
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                        <?php
                        // Include additional resources if provided
                        foreach ($additionalResources as $resource) {
                            echo $resource . "\n";
                        }
                        ?>
                    </head>

                    <body class="bg-gray-50 min-h-screen">
                    <?php
                }
                    ?>