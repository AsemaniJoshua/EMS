<?php include_once '../components/Sidebar.php'; ?>
<?php include_once '../components/Header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EMS Teacher</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-4 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Teacher Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Welcome back! Here's your teaching and exam overview.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="../exam/index.php"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-plus mr-2 -ml-1"></i>
                        New Exam
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Exams</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">12</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+2</span>
                                            <span class="ml-1 text-gray-500">this month</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                                <i class="fas fa-user-graduate text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Students Taught</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">320</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+10</span>
                                            <span class="ml-1 text-gray-500">this term</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg. Student Score</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">84%</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+4%</span>
                                            <span class="ml-1 text-gray-500">this term</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-50 rounded-lg p-3">
                                <i class="fas fa-clock text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Grading</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">3</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-orange-600 font-medium">Needs review</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Student Performance Trend</h3>
                    <canvas id="performanceChart" height="180"></canvas>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold mb-4">Exam Participation</h3>
                    <canvas id="participationChart" height="180"></canvas>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Recent Activity -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-900">Recent Activity</h2>
                            <a href="#" class="text-sm text-emerald-600 hover:text-emerald-700">View all</a>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            <li class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-plus text-emerald-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1">Created new exam "Algebra
                                            Basics"</p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i> 1 hour ago
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">New</span>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1">Graded 20 student submissions
                                        </p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i> 3 hours ago
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check text-purple-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1">Published results for
                                            "Geometry Quiz"</p>
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
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
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
                                            <p class="text-sm font-medium text-gray-900">Mathematics Midterm</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i class="far fa-calendar-alt mr-1"></i> Apr 20, 2024 • 10:00 AM
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Ready</span>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Physics Quiz</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i class="far fa-calendar-alt mr-1"></i> Apr 25, 2024 • 1:00 PM
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Chemistry Final</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <i class="far fa-calendar-alt mr-1"></i> May 2, 2024 • 9:00 AM
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Draft</span>
                                </div>
                            </li>
                        </ul>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <a href="../exam/index.php"
                                class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-emerald-700 bg-white border border-emerald-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                <i class="fas fa-plus mr-2"></i> Add Exam
                            </a>
                        </div>
                    </div>
                </div>
            </div>
<?php
// teacher/dashboard/index.php
// This file contains the main content for the Teacher Dashboard overview.
// It is designed to be included by teacher/index.php.
?>

<h1 class="text-3xl font-extrabold text-gray-900 mb-6">Teacher Dashboard</h1>

<!-- Welcome Section -->
<div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white p-6 rounded-xl shadow-lg mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold mb-2">Hello, Teacher Name!</h2>
        <p class="text-emerald-100 text-lg">Welcome to your Examplify control panel.</p>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-white opacity-70">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
    </svg>
</div>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Exams Created -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Total Exams Created</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">12</p>
        </div>
        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <path d="M14 2v6h6"></path>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <line x1="10" y1="9" x2="8" y2="9"></line>
            </svg>
        </div>
    </div>

    <!-- Card 2: Total Students Enrolled -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Students Enrolled (Your Courses)</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">245</p>
        </div>
        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
    </div>

    <!-- Card 3: Pending Exam Approvals -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Pending Exam Approvals</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">3</p>
        </div>
        <div class="p-3 rounded-full bg-orange-100 text-orange-600">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
    </div>

    <!-- Card 4: Courses Taught -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Courses Taught</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">5</p>
        </div>
        <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V6.5A2.5 2.5 0 0 0 17.5 4h-11A2.5 2.5 0 0 0 4 6.5v13z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="index.php?page=exams" class="flex items-center justify-center py-3 px-4 rounded-lg bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition-colors duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Create New Exam
        </a>
        <a href="index.php?page=results" class="flex items-center justify-center py-3 px-4 rounded-lg bg-blue-500 text-white font-semibold hover:bg-blue-600 transition-colors duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path d="M2 13V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v7"></path>
                <path d="M2 13h20"></path>
                <path d="M12 20v-7"></path>
                <path d="M8 20v-7"></path>
                <path d="M16 20v-7"></path>
            </svg>
            View All Results
        </a>
        <a href="index.php?page=profile" class="flex items-center justify-center py-3 px-4 rounded-lg bg-purple-500 text-white font-semibold hover:bg-purple-600 transition-colors duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            Manage Profile
        </a>
    </div>
</div>

<!-- Recent Exams/Activities Table -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Recent Exams & Status</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">Exam Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="my-exams-table-body">
                <!-- Example rows (will be dynamically loaded) -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Intro to Web Dev</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">WD101</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">45 mins</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                            Pending
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="index.php?page=exams" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                        <a href="index.php?page=exams" class="text-emerald-600 hover:text-emerald-900 ml-4">View Questions</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Database Systems</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">DB201</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">60 mins</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Approved
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="index.php?page=exams" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                        <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                        <a href="index.php?page=exams" class="text-emerald-600 hover:text-emerald-900 ml-4">View Questions</a>
                    </td>
                </tr>
                <!-- More rows can be added here dynamically -->
            </tbody>
        </table>
    </div>
</div>
