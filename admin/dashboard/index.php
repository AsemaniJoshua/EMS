<?php
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
$currentPage = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Welcome back! Here's your examination overview.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-plus mr-2 -ml-1"></i>
                        New Exam
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Total Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">2,847</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+12%</span>
                                            <span class="ml-1 text-gray-500">from last month</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Exams -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-clipboard text-emerald-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Exams</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">15</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">3 new</span>
                                            <span class="ml-1 text-gray-500">this week</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teachers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-chalkboard-teacher text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Teachers</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">124</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+5</span>
                                            <span class="ml-1 text-gray-500">new this month</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Reviews -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-50 rounded-lg p-3">
                                <i class="fas fa-clock text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Reviews</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">8</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-orange-600 font-medium">Urgent</span>
                                            <span class="ml-1 text-gray-500">requires attention</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Recent Activity -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-900">Recent Activity</h2>
                            <a href="#" class="text-sm text-emerald-600 hover:text-emerald-700">View all</a>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            <li class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-plus text-emerald-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1">New exam "Mathematics Final" created</p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i> 2 hours ago
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        New
                                    </span>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1">15 students registered for Physics exam</p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i> 4 hours ago
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check text-purple-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1">Chemistry exam results published</p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i> 1 day ago
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Upcoming Exams -->
                <div>
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-900">Upcoming Exams</h2>
                            <a href="#" class="text-sm text-emerald-600 hover:text-emerald-700">See all</a>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Mathematics</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i class="far fa-calendar-alt mr-1"></i> Dec 15, 2023
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Ready
                                    </span>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Physics</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i class="far fa-calendar-alt mr-1"></i> Dec 18, 2023
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Chemistry</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i class="far fa-calendar-alt mr-1"></i> Dec 20, 2023
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Draft
                                    </span>
                                </div>
                            </li>
                        </ul>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <a href="#" class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-emerald-700 bg-white border border-emerald-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                <i class="fas fa-plus mr-2"></i> Add Exam
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>