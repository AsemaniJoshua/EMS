<?php
// teacher/dashboard/index.php
// This file contains the main content for the Teacher Dashboard overview.
// It is designed to be included by teacher/index.php.
?>

<!-- Page Header -->
<div class="mb-4 md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">Welcome back, Teacher Name! Here's your teaching overview.</p>
    </div>
    <div class="mt-4 md:mt-0">
        <a href="index.php?page=exams" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            <i class="fas fa-plus mr-2 -ml-1"></i>
            New Exam
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Card 1: Total Exams Created -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
        <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Exams Created</dt>
                    <dd>
                        <div class="text-xl font-semibold text-gray-900">12</div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <!-- Card 2: Students Enrolled -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
        <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Students Enrolled</dt>
                    <dd>
                        <div class="text-xl font-semibold text-gray-900">245</div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <!-- Card 3: Pending Exam Approvals -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
        <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-orange-50 rounded-lg p-3">
                <i class="fas fa-clock text-orange-600 text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Exam Approvals</dt>
                    <dd>
                        <div class="text-xl font-semibold text-gray-900">3</div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <!-- Card 4: Courses Taught -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
        <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                <i class="fas fa-book text-emerald-600 text-xl"></i>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Courses Taught</dt>
                    <dd>
                        <div class="text-xl font-semibold text-gray-900">5</div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Recent Exams & Status -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-900">Recent Exams & Status</h2>
            </div>
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
    </div>

    <!-- Quick Actions -->
    <div>
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-6 grid grid-cols-1 gap-4">
                <a href="index.php?page=exams" class="flex items-center justify-center py-3 px-4 rounded-lg bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition-colors duration-200 shadow-md">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Exam
                </a>
                <a href="index.php?page=results" class="flex items-center justify-center py-3 px-4 rounded-lg bg-blue-500 text-white font-semibold hover:bg-blue-600 transition-colors duration-200 shadow-md">
                    <i class="fas fa-list mr-2"></i>
                    View All Results
                </a>
                <a href="index.php?page=profile" class="flex items-center justify-center py-3 px-4 rounded-lg bg-purple-500 text-white font-semibold hover:bg-purple-600 transition-colors duration-200 shadow-md">
                    <i class="fas fa-user mr-2"></i>
                    Manage Profile
                </a>
            </div>
        </div>
    </div>
</div>

<script src="dashboard.js"></script>
</body>
