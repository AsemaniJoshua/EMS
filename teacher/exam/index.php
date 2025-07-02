<?php
// teacher/exam/index.php
// This file contains the main content for the Teacher Exam Management section.
// It is designed to be included by teacher/index.php.
?>

<h1 class="text-3xl font-extrabold text-gray-900 mb-6">Exam Management</h1>

<!-- Create New Exam Section -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Create New Exam</h3>
    <form id="create-exam-form" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
        <div>
            <label for="exam-title" class="block text-sm font-medium text-gray-700 mb-1">Exam Title</label>
            <input type="text" id="exam-title" name="exam_title" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="exam-code" class="block text-sm font-medium text-gray-700 mb-1">Exam Code</label>
            <input type="text" id="exam-code" name="exam_code" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="course-id" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
            <select id="course-id" name="course_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                <option value="">Select Course</option>
                <option value="1">CS101 - Introduction to Algorithms</option>
                <option value="2">CS102 - Data Structures Fundamentals</option>
                <option value="3">MA201 - Linear Algebra Basics</option>
                <!-- Dynamically load from backend -->
            </select>
        </div>
        <div>
            <label for="duration-minutes" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
            <input type="number" id="duration-minutes" name="duration_minutes" required min="1"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="start-datetime" class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
            <input type="datetime-local" id="start-datetime" name="start_datetime" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="end-datetime" class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
            <input type="datetime-local" id="end-datetime" name="end_datetime" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div class="md:col-span-2 flex justify-end">
            <button type="submit" class="px-6 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors duration-200 shadow-md">
                Save Exam Details
            </button>
        </div>
    </form>
</div>

<!-- Question & Choices Management Section -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Add Questions to Exam</h3>
    <p class="text-sm text-gray-600 mb-4">Select an exam first to add questions.</p>
    <div id="questions-container" class="space-y-6">
        <!-- Question blocks will be added here by JavaScript -->
    </div>
    <button id="add-question-btn" class="mt-6 px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors duration-200 shadow-md flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Question
    </button>
    <button id="submit-exam-for-approval-btn" class="mt-6 ml-4 px-6 py-2 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700 transition-colors duration-200 shadow-md flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11"></polyline>
        </svg>
        Submit Exam for Approval
    </button>
</div>

<!-- My Exams Table -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
    <h3 class="text-xl font-bold text-gray-900 mb-4">My Exams</h3>
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

<script src="exam.js"></script>
</body>
