<?php
$pageTitle = "Edit Profile";
$breadcrumb = "Edit Profile";
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header('Location: /student/login/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - EMS Student</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Add viewport for responsiveness -->
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-2 py-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto">
                <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Profile</h3>
                </div>
                <form id="editProfileForm" class="p-4 sm:p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" id="editFirstName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" id="editLastName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" id="editEmail" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                        <input type="tel" id="editPhoneNumber" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" id="editDateOfBirth" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select id="editGender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4">
                        <a href="index.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors text-center">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md transition-colors text-center">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
    // Fetch and populate profile data
    document.addEventListener('DOMContentLoaded', async function() {
        const response = await fetch('/api/students/getProfile.php');
        const data = await response.json();
        if (data.success) {
            const profile = data.data.profile;
            document.getElementById('editFirstName').value = profile.first_name || '';
            document.getElementById('editLastName').value = profile.last_name || '';
            document.getElementById('editEmail').value = profile.email || '';
            document.getElementById('editPhoneNumber').value = profile.phone_number || '';
            document.getElementById('editDateOfBirth').value = profile.date_of_birth || '';
            document.getElementById('editGender').value = profile.gender || '';
        }
    });

    // Handle form submission
    document.getElementById('editProfileForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = {
            first_name: document.getElementById('editFirstName').value.trim(),
            last_name: document.getElementById('editLastName').value.trim(),
            email: document.getElementById('editEmail').value.trim(),
            phone_number: document.getElementById('editPhoneNumber').value.trim(),
            date_of_birth: document.getElementById('editDateOfBirth').value || null,
            gender: document.getElementById('editGender').value || null
        };
        const response = await fetch('/api/students/updateProfile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const result = await response.json();
        if (result.success) {
            Swal.fire('Success!', 'Profile updated successfully.', 'success').then(() => {
                window.location.href = 'index.php';
            });
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    });
    </script>
</body>
</html>