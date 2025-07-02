<?php
$pageTitle = "Profile";
$breadcrumb = "Profile";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Student</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-100 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    <main class="pt-20 lg:ml-60 min-h-screen transition-all duration-300 px-4">
        <div class="p-4 lg:p-8 max-w-6xl mx-auto">
            <h1 class="text-3xl font-extrabold text-blue-700 mb-8 tracking-tight">My Profile</h1>
            <div class="relative">
                <!-- Profile Card -->
                <div class="backdrop-blur-md bg-white/70 border border-blue-100 rounded-md shadow-lg p-8 flex flex-col md:flex-row items-center gap-8">
                    <div class="flex flex-col items-center md:items-start md:w-1/3">
                        <div class="relative mb-4">
                            <img id="profileAvatar" src="https://ui-avatars.com/api/?name=Student" alt="Student Avatar" class="w-32 h-32 rounded-full border-4 border-blue-300 shadow-lg object-cover" />
                            <span class="absolute bottom-2 right-2 w-5 h-5 bg-green-400 border-2 border-white rounded-full"></span>
                        </div>
                        <h2 id="profileName" class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2"><i class="fa-solid fa-user"></i> Student Name</h2>
                        <p id="profileEmail" class="text-gray-600 mb-1 flex items-center gap-2"><i class="fa-solid fa-envelope"></i> student@email.com</p>
                        <p id="profilePhone" class="text-gray-600 mb-2 flex items-center gap-2"><i class="fa-solid fa-phone"></i> +1234567890</p>
                        <span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold mb-4"><i class="fa-solid fa-circle-check mr-1"></i> Active</span>
                        <button id="editBtn" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-2 rounded-full font-semibold shadow transition-all duration-200 w-full mt-2">Edit Profile</button>
                    </div>
                    <div class="flex-1 flex flex-col gap-6 w-full">
                        <div id="viewDetails">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-sm font-semibold text-blue-700 mb-2 flex items-center gap-2"><i class="fa-solid fa-book mr-1"></i> Enrolled Courses</h3>
                                    <ul id="profileCourses" class="list-disc pl-5 text-gray-700 text-base space-y-1">
                                        <li>Mathematics</li>
                                        <li>Science</li>
                                        <li>English</li>
                                    </ul>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-blue-700 mb-2 flex items-center gap-2"><i class="fa-solid fa-id-card mr-1"></i> Account Info</h3>
                                    <ul class="text-gray-700 text-base space-y-1">
                                        <li><span class="font-medium">Joined:</span> Jan 2024</li>
                                        <li><span class="font-medium">Status:</span> <span class="text-green-600 font-semibold">Active</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="profileMsg" class="mt-2 text-center text-sm"></div>
                        <!-- Divider -->
                        <div class="border-t border-blue-100 my-4"></div>
                        <!-- Reset Password Section -->
                        <div class="mt-2">
                            <h3 class="text-lg font-semibold text-purple-700 mb-2 flex items-center gap-2"><i class="fa-solid fa-key"></i> Reset Password</h3>
                            <form id="resetPasswordForm" class="space-y-4 max-w-md">
                                <div>
                                    <label class="block text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" id="resetPhone" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-400" placeholder="Enter your phone number">
                                </div>
                                <button type="button" id="sendOtpBtn" class="w-full bg-gradient-to-r from-purple-500 to-blue-500 text-white py-2 rounded hover:from-purple-600 hover:to-blue-600 font-semibold">Send OTP</button>
                                <div id="otpSection" class="hidden">
                                    <label class="block text-gray-700 mb-1 mt-2">OTP</label>
                                    <input type="text" id="otpInput" maxlength="6" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-400" placeholder="Enter OTP">
                                    <label class="block text-gray-700 mb-1 mt-2">New Password</label>
                                    <input type="password" id="newPassword" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-400" placeholder="New Password">
                                    <button type="submit" class="w-full mt-4 bg-gradient-to-r from-green-500 to-blue-500 text-white py-2 rounded hover:from-green-600 hover:to-blue-600 font-semibold">Reset Password</button>
                                </div>
                            </form>
                            <div id="resetMsg" class="mt-4 text-center text-sm"></div>
                        </div>
                    </div>
                </div>
                <!-- Edit Modal -->
                <div id="editModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden transition-all duration-300">
                    <div class="bg-white rounded-md shadow-2xl p-8 w-full max-w-2xl relative animate-fadeIn">
                        <button id="closeEditModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
                            <i class="fa-solid fa-xmark fa-lg"></i>
                        </button>
                        <h2 class="text-xl font-bold text-blue-700 mb-4 flex items-center gap-2"><i class="fa-solid fa-pen-to-square"></i> Edit Profile</h2>
                        <form id="editForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 mb-1">Name</label>
                                    <input type="text" id="editName" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                </div>
                                <div>
                                    <label class="block text-gray-700 mb-1">Email</label>
                                    <input type="email" id="editEmail" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                </div>
                                <div>
                                    <label class="block text-gray-700 mb-1">Phone</label>
                                    <input type="tel" id="editPhone" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                </div>
                                <div>
                                    <label class="block text-gray-700 mb-1">Enrolled Courses (comma separated)</label>
                                    <input type="text" id="editCourses" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                </div>
                            </div>
                            <div class="flex gap-4 mt-4 justify-end">
                                <button type="submit" class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white px-6 py-2 rounded-lg font-semibold">Save</button>
                                <button type="button" id="cancelBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-semibold">Cancel</button>
                            </div>
                        </form>
                        <div id="editMsg" class="mt-4 text-center text-sm"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
    // Demo profile data
    let profile = {
        name: 'Student Name',
        email: 'student@email.com',
        phone: '+1234567890',
        courses: ['Mathematics', 'Science', 'English']
    };
    // Edit Profile Logic
    const editBtn = document.getElementById('editBtn');
    const editModal = document.getElementById('editModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const editForm = document.getElementById('editForm');
    const viewDetails = document.getElementById('viewDetails');
    const cancelBtn = document.getElementById('cancelBtn');
    const profileMsg = document.getElementById('profileMsg');
    const editMsg = document.getElementById('editMsg');
    function renderProfile() {
        document.getElementById('profileName').textContent = profile.name;
        document.getElementById('profileEmail').textContent = profile.email;
        document.getElementById('profilePhone').textContent = profile.phone;
        document.getElementById('profileAvatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(profile.name)}`;
        document.getElementById('profileCourses').innerHTML = profile.courses.map(c => `<li>${c}</li>`).join('');
    }
    editBtn.onclick = function() {
        editModal.classList.remove('hidden');
        editMsg.textContent = '';
        document.getElementById('editName').value = profile.name;
        document.getElementById('editEmail').value = profile.email;
        document.getElementById('editPhone').value = profile.phone;
        document.getElementById('editCourses').value = profile.courses.join(', ');
    };
    closeEditModal.onclick = cancelBtn.onclick = function() {
        editModal.classList.add('hidden');
        editMsg.textContent = '';
    };
    editForm.onsubmit = function(e) {
        e.preventDefault();
        profile.name = document.getElementById('editName').value;
        profile.email = document.getElementById('editEmail').value;
        profile.phone = document.getElementById('editPhone').value;
        profile.courses = document.getElementById('editCourses').value.split(',').map(s => s.trim()).filter(Boolean);
        renderProfile();
        editModal.classList.add('hidden');
        profileMsg.textContent = 'Profile updated successfully!';
        profileMsg.className = 'mt-2 text-center text-sm text-green-600';
    };
    renderProfile();
    // Demo OTP logic
    document.getElementById('sendOtpBtn').onclick = function() {
        const phone = document.getElementById('resetPhone').value;
        if (!phone) {
            showResetMsg('Please enter your phone number.', 'red');
            return;
        }
        document.getElementById('otpSection').classList.remove('hidden');
        showResetMsg('OTP sent to your phone (demo).', 'green');
    };
    document.getElementById('resetPasswordForm').onsubmit = function(e) {
        e.preventDefault();
        const otp = document.getElementById('otpInput').value;
        const newPassword = document.getElementById('newPassword').value;
        if (!otp || !newPassword) {
            showResetMsg('Please enter OTP and new password.', 'red');
            return;
        }
        if (otp !== '123456') {
            showResetMsg('Invalid OTP (demo: 123456).', 'red');
            return;
        }
        showResetMsg('Password reset successful!', 'green');
        document.getElementById('resetPasswordForm').reset();
        document.getElementById('otpSection').classList.add('hidden');
    };
    function showResetMsg(msg, color) {
        document.getElementById('resetMsg').textContent = msg;
        document.getElementById('resetMsg').className = `mt-4 text-center text-sm text-${color}-600`;
    }
    </script>
    <style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.3s ease; }
    </style>
</body>
</html>
