<?php
// Check if student is already logged in
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Signup - EMS</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(209, 213, 219, 0.3);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .step-indicator {
            transition: all 0.3s ease;
        }
        .step-indicator.active {
            background-color: #10b981;
            color: white;
        }
        .step-indicator.completed {
            background-color: #059669;
            color: white;
        }
    </style>
</head>
<body class="min-h-screen gradient-bg py-8 px-4">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white opacity-10 rounded-full animate-pulse-slow"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white opacity-5 rounded-full animate-float"></div>
        <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white opacity-10 rounded-full animate-pulse-slow"></div>
    </div>

    <!-- Signup Container -->
    <div class="relative max-w-2xl mx-auto">
        <!-- Logo/Brand Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4 animate-float">
                <i class="fas fa-user-plus text-2xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Create Account</h1>
            <p class="text-white text-opacity-80">Join our student community today</p>
        </div>

        <!-- Progress Steps -->
        <div class="flex justify-center mb-8">
                            <div class="flex items-center space-x-4">
                    <div class="step-indicator active flex items-center justify-center w-10 h-10 rounded-full bg-white bg-opacity-20 text-white font-semibold">
                        1
                    </div>
                    <div class="w-16 h-1 bg-white bg-opacity-20 rounded"></div>
                    <div class="step-indicator flex items-center justify-center w-10 h-10 rounded-full bg-white bg-opacity-20 text-white font-semibold">
                        2
                    </div>
                    <div class="w-16 h-1 bg-white bg-opacity-20 rounded"></div>
                    <div class="step-indicator flex items-center justify-center w-10 h-10 rounded-full bg-white bg-opacity-20 text-white font-semibold">
                        3
                    </div>
                </div>
            </div>
        </div>

        <!-- Signup Form -->
        <div class="glass-effect rounded-2xl shadow-xl p-8">
            <form id="signupForm" class="space-y-6">
                <!-- Step 1: Personal Information -->
                <div id="step1" class="step-content">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-user mr-2 text-emerald-600"></i>
                        Personal Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                First Name *
                            </label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                                placeholder="Enter your first name"
                                autocomplete="given-name"
                            >
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name *
                            </label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                                placeholder="Enter your last name"
                                autocomplete="family-name"
                            >
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Username *
                            </label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                                placeholder="Choose a username"
                                autocomplete="username"
                            >
                            <p class="text-xs text-gray-500 mt-1">Only letters, numbers, and underscores allowed</p>
                        </div>

                        <!-- Index Number -->
                        <div>
                            <label for="index_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Index Number *
                            </label>
                            <input 
                                type="text" 
                                id="index_number" 
                                name="index_number" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                                placeholder="Enter your index number"
                            >
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address *
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                                placeholder="Enter your email address"
                                autocomplete="email"
                            >
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input 
                                type="tel" 
                                id="phone_number" 
                                name="phone_number" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                                placeholder="Enter your phone number"
                                autocomplete="tel"
                            >
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                Date of Birth *
                            </label>
                            <input 
                                type="date" 
                                id="date_of_birth" 
                                name="date_of_birth" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                                autocomplete="bday"
                            >
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                Gender *
                            </label>
                            <select 
                                id="gender" 
                                name="gender" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                            >
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
                        <!-- Department -->
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Department *
                            </label>
                            <select 
                                id="department_id" 
                                name="department_id" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                            >
                                <option value="">Select Department</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>

                        <!-- Program -->
                        <div>
                            <label for="program_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Program *
                            </label>
                            <select 
                                id="program_id" 
                                name="program_id" 
                                required 
                                disabled
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50 disabled:opacity-50"
                            >
                                <option value="">Select Program</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Select a department first</p>
                        </div>

                        <!-- Add this to Step 2: Academic Information -->
                        <div>
                            <label for="level_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Level *
                            </label>
                            <select 
                                id="level_id" 
                                name="level_id" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                            >
                                <option value="">Select Level</option>
                                <!-- Options will be loaded dynamically -->
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
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password *
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                                    placeholder="Create a password"
                                    autocomplete="new-password"
                                >
                                <button 
                                    type="button" 
                                    id="togglePassword" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors"
                                >
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

                        <!-- Confirm Password -->
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password *
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    required 
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 input-focus transition-all duration-200 bg-white bg-opacity-50"
                                    placeholder="Confirm your password"
                                    autocomplete="new-password"
                                >
                                <button 
                                    type="button" 
                                    id="toggleConfirmPassword" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition-colors"
                                >
                                    <i class="fas fa-eye" id="eyeIconConfirm"></i>
                                </button>
                            </div>
                            <div id="password-match" class="mt-2 text-xs text-gray-400 hidden">
                                <i class="fas fa-circle mr-1"></i>
                                <span>Passwords match</span>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mt-6">
                        <div class="flex items-start">
                            <input 
                                id="terms" 
                                name="terms" 
                                type="checkbox" 
                                required
                                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded mt-1"
                            >
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
                    <button 
                        type="button" 
                        id="prevBtn"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-200 hidden"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Previous
                    </button>
                    
                    <div class="flex space-x-4">
                        <button 
                            type="button" 
                            id="nextBtn"
                            class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all duration-200 transform hover:scale-105"
                        >
                            Next<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        
                        <button 
                            type="submit" 
                            id="submitBtn"
                                                       class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all duration-200 transform hover:scale-105 hidden"
                        >
                            <span id="submitBtnText">Create Account</span>
                            <i id="submitSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Login Link -->
        <div class="text-center mt-8">
            <p class="text-white text-opacity-80">
                Already have an account? 
                <a href="../login/" class="font-medium text-white hover:text-gray-200 transition-colors underline">
                    Sign in here
                </a>
            </p>
        </div>

        <!-- Footer Links -->
        <div class="text-center mt-6 space-y-2">
            <div class="flex justify-center space-x-6 text-sm text-white text-opacity-80">
                <a href="#" class="hover:text-white transition-colors">Help</a>
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
            </div>
            <p class="text-xs text-white text-opacity-60">
                © 2024 EMS. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <i class="fas fa-spinner fa-spin text-emerald-600 text-xl"></i>
            <span class="text-gray-700">Creating your account...</span>
        </div>
    </div>

    <script src="signup.js"></script>
</body>
</html>


