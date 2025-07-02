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
