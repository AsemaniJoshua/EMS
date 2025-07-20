<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    header('Location: /student/dashboard/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Signup - EMS</title>
  <link rel="stylesheet" href="/src/output.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
  <style>
    .animate-pop {
      animation: popIn .25s cubic-bezier(.4, 2, .6, 1) both;
    }
    @keyframes popIn {
      0% { transform: scale(.8); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="min-h-screen grid lg:grid-cols-2">
    <!-- Left Panel -->
    <div class="hidden lg:flex relative bg-gradient-to-br from-emerald-600 to-emerald-800">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="absolute inset-0"
        style="background-image: url('https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
      </div>
      <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/80 to-emerald-800/60"></div>
      <div class="relative z-10 flex flex-col justify-center px-12 text-white">
        <div class="max-w-md">
          <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mb-8">
            <i class="fa-solid fa-user-plus text-2xl text-white"></i>
          </div>
          <h1 class="text-4xl font-bold mb-4">Join EMS Students</h1>
          <p class="text-lg text-emerald-100 mb-8">Create your account to access courses, exams, and more.</p>
          <div class="flex items-center gap-3 text-emerald-100">
            <i class="fa-solid fa-shield-halved text-xl"></i>
            <span>Secure & Reliable Platform</span>
          </div>
        </div>
      </div>
    </div>
    <!-- Right Panel -->
    <div class="flex items-center justify-center p-8 lg:p-12">
      <div class="w-full max-w-lg">
        <div class="lg:hidden text-center mb-8">
          <div class="w-12 h-12 rounded-full bg-emerald-100 mx-auto mb-4 flex items-center justify-center">
            <i class="fa-solid fa-user-plus text-xl text-emerald-600"></i>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">EMS Student Signup</h1>
        </div>
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Create your account</h2>
          <p class="text-gray-600">Fill in your details to get started</p>
        </div>
        <form id="signupForm" class="space-y-6">
          <!-- Step 1: Personal Information -->
          <div id="step1" class="step-content">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
              <i class="fas fa-user mr-2 text-emerald-600"></i>
              Personal Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                <input type="text" id="first_name" name="first_name" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white"
                  placeholder="Enter your first name" autocomplete="given-name">
              </div>
              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                <input type="text" id="last_name" name="last_name" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white"
                  placeholder="Enter your last name" autocomplete="family-name">
              </div>
              <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                <input type="text" id="username" name="username" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white"
                  placeholder="Choose a username" autocomplete="username">
                <p class="text-xs text-gray-500 mt-1">Only letters, numbers, and underscores allowed</p>
              </div>
              <div>
                <label for="index_number" class="block text-sm font-medium text-gray-700 mb-2">Index Number *</label>
                <input type="text" id="index_number" name="index_number" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white"
                  placeholder="Enter your index number">
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                <input type="email" id="email" name="email" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white"
                  placeholder="Enter your email address" autocomplete="email">
              </div>
              <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white"
                  placeholder="Enter your phone number" autocomplete="tel">
              </div>
              <div>
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                <input type="date" id="date_of_birth" name="date_of_birth" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white"
                  autocomplete="bday">
              </div>
              <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                <select id="gender" name="gender" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white">
                  <option value="">Select Gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
              </div>
            </div>
          </div>
          <!-- Step 2: Academic Information -->
          <div id="step2" class="step-content hidden">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
              <i class="fas fa-graduation-cap mr-2 text-emerald-600"></i>
              Academic Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                <select id="department_id" name="department_id" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white">
                  <option value="">Select Department</option>
                </select>
              </div>
              <div>
                <label for="program_id" class="block text-sm font-medium text-gray-700 mb-2">Program *</label>
                <select id="program_id" name="program_id" required disabled
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white disabled:opacity-50">
                  <option value="">Select Program</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Select a department first</p>
              </div>
              <div>
                <label for="level_id" class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                <select id="level_id" name="level_id" required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white">
                  <option value="">Select Level</option>
                </select>
              </div>
            </div>
          </div>
          <!-- Step 3: Account Security -->
          <div id="step3" class="step-content hidden">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
              <i class="fas fa-shield-alt mr-2 text-emerald-600"></i>
              Account Security
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                <div class="relative">
                  <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white"
                    placeholder="Create a password" autocomplete="new-password">
                  <button type="button" id="togglePassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                  </button>
                </div>
                <div class="mt-2">
                  <div class="flex items-center space-x-2 text-xs">
                    <div id="length-check" class="flex items-center text-gray-400">
                      <i class="fas fa-circle mr-1"></i>
                      <span>At least 8 characters</span>
                    </div>
                  </div>
                </div>
              </div>
              <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                <div class="relative">
                  <input type="password" id="confirm_password" name="confirm_password" required
                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors bg-white"
                    placeholder="Confirm your password" autocomplete="new-password">
                  <button type="button" id="toggleConfirmPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-eye" id="eyeIconConfirm"></i>
                  </button>
                </div>
                <div id="password-match" class="mt-2 text-xs text-gray-400 hidden">
                  <i class="fas fa-circle mr-1"></i>
                  <span>Passwords match</span>
                </div>
              </div>
            </div>
            <div class="mt-6">
              <div class="flex items-start">
                <input id="terms" name="terms" type="checkbox" required
                  class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded mt-1">
                <label for="terms" class="ml-3 block text-sm text-gray-700">
                  I agree to the
                  <a href="#" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors">Terms of Service</a>
                  and
                  <a href="#" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors">Privacy Policy</a>
                </label>
              </div>
            </div>
          </div>
          <!-- Navigation Buttons -->
          <div class="flex justify-between pt-6 border-t border-gray-200">
            <button type="button" id="prevBtn"
              class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-200 hidden">
              <i class="fas fa-arrow-left mr-2"></i>Previous
            </button>
            <div class="flex space-x-4">
              <button type="button" id="nextBtn"
                class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all duration-200 transform hover:scale-105">
                Next<i class="fas fa-arrow-right ml-2"></i>
              </button>
              <button type="submit" id="submitBtn"
                class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all duration-200 transform hover:scale-105 hidden">
                <span id="submitBtnText">Create Account</span>
                <i id="submitSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
              </button>
              <!-- Overlay -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black opacity-25 z-50"></div>
            </div>
          </div>
        </form>
        <div class="text-center mt-8">
          <p class="text-gray-600">
            Already have an account?
            <a href="/student/login/" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors underline">
              Sign in here
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
  <script src="signup.js"></script>
</body>
</html>