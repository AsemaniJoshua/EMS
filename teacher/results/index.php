<?php
// teacher/results/index.php
// This file contains the main content for the Teacher Results section.
// It is designed to be included by teacher/index.php.
?>

<h1 class="text-3xl font-extrabold text-gray-900 mb-6">Exam Results</h1>

<!-- Filter and Search Section -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Filter Results</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="filter-course" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
            <select id="filter-course" name="filter_course"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                <option value="">All Courses</option>
                <option value="CS101">CS101 - Introduction to Algorithms</option>
                <option value="CS102">CS102 - Data Structures Fundamentals</option>
                <option value="MA201">MA201 - Linear Algebra Basics</option>
                <!-- Dynamically load from backend -->
            </select>
        </div>
        <div>
            <label for="filter-exam" class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
            <select id="filter-exam" name="filter_exam"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                <option value="">All Exams</option>
                <option value="Exam1">Intro to Web Dev Exam</option>
                <option value="Exam2">Database Systems Midterm</option>
                <!-- Dynamically load from backend -->
            </select>
        </div>
        <div>
            <label for="search-student" class="block text-sm font-medium text-gray-700 mb-1">Search Student</label>
            <input type="text" id="search-student" name="search_student" placeholder="Student Name or ID"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
    </div>
    <div class="mt-6 flex justify-end">
        <button class="px-6 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors duration-200 shadow-md">
            Apply Filters
        </button>
        <button class="ml-4 px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-200 shadow-md">
            Reset Filters
        </button>
    </div>
</div>

<!-- Results Table -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
    <h3 class="text-xl font-bold text-gray-900 mb-4">All Exam Results</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">Student Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score (%)</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct/Total</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="results-table-body">
                <!-- Example rows (will be dynamically loaded) -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Alice Johnson</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Intro to Web Dev</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">WD101</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">85.00%</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">17/20</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Bob Williams</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Database Systems</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">DB201</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-semibold">62.50%</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">10/16</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Charlie Brown</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Intro to Web Dev</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">WD101</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">95.00%</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">19/20</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                    </td>
                </tr>
                <!-- More rows can be added here dynamically -->
            </tbody>
        </table>
    </div>
</div>

<script src="results.js"></script>
</body>
