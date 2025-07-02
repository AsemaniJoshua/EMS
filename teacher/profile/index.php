<?php
// teacher/profile/index.php
// This file contains the main content for the Teacher Profile section.
// It is designed to be included by teacher/index.php.
?>

<h1 class="text-3xl font-extrabold text-gray-900 mb-6">My Profile</h1>

<!-- Profile Information Section -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 mb-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Personal Information</h3>
    <form id="profile-form" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
        <div>
            <label for="staff-id" class="block text-sm font-medium text-gray-700 mb-1">Staff ID</label>
            <input type="text" id="staff-id" name="staff_id" value="TCH001" disabled
                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed shadow-sm sm:text-sm">
        </div>
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" id="username" name="username" value="teacher.name" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
            <input type="text" id="first-name" name="first_name" value="Teacher" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
            <input type="text" id="last-name" name="last_name" value="Name" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="teacher.name@examplify.com" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="phone-number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <input type="tel" id="phone-number" name="phone_number" value="+1234567890"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div class="md:col-span-2">
            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <select id="department" name="department_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                <option value="101">Software Engineering</option>
                <option value="102">Network Systems</option>
                <option value="103">Cybersecurity</option>
                <!-- Dynamically load from backend -->
            </select>
        </div>
        <div class="md:col-span-2 flex justify-end mt-6">
            <button type="submit" class="px-6 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors duration-200 shadow-md">
                Save Changes
            </button>
        </div>
    </form>
</div>

<!-- Change Password Section -->
<div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Change Password</h3>
    <form id="change-password-form" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
        <div>
            <label for="current-password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
            <input type="password" id="current-password" name="current_password" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="new-password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input type="password" id="new-password" name="new_password" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div>
            <label for="confirm-new-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input type="password" id="confirm-new-password" name="confirm_new_password" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>
        <div class="md:col-span-2 flex justify-end mt-6">
            <button type="submit" class="px-6 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors duration-200 shadow-md">
                Update Password
            </button>
        </div>
    </form>
</div>

<script src="profile.js"></script>
</body>
