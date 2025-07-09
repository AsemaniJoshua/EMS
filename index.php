<?php
include_once __DIR__ . '/components/Navbar.php';
include_once __DIR__ . '/components/HeroSection.php';
include_once __DIR__ . '/components/FeaturesBenefitsSection.php';
include_once __DIR__ . '/components/CallToActionSection.php';
include_once __DIR__ . '/components/FooterSection.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examplify - Redefine Your Learning Journey</title>
<<<<<<< HEAD

    <link href="src/output.css" rel="stylesheet">
    <style>
        /* Custom styles for Inter font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Keyframes for subtle animations */
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes floatEffect {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Apply animations */
        .animate-slide-in-up { animation: slideInUp 0.8s ease-out forwards; opacity: 0; }
        .animate-zoom-in { animation: zoomIn 0.7s ease-out forwards; opacity: 0; }
        .animate-float { animation: floatEffect 3s infinite ease-in-out; }

        /* Animation delays */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; }
        .delay-800 { animation-delay: 0.8s; }
        .delay-900 { animation-delay: 0.9s; }
        .delay-1000 { animation-delay: 1s; }

        /* Specific styles for mobile menu toggle */
        #mobile-menu {
            transition: transform 0.3s ease-out;
            transform: translateX(-100%); /* Start off-screen to the left */
        }
        #mobile-menu.open {
            transform: translateX(0); /* Slide in from the left */
        }
    </style>

</head>
<body class="max-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-green-100 py-6 px-4 sm:px-6 lg:px-6">
    <div class="min-w-md w-full space-y-8 p-6 sm:p-10 bg-white rounded-3xl shadow-2xl border border-gray-200">
        <div class="text-center">
            <!-- Examplify Logo/Icon - More prominent and refined -->
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-emerald-600 mb-4">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <path d="M14 2v6h6"></path>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <line x1="10" y1="9" x2="8" y2="9"></line>
            </svg>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Welcome Back to <span class="text-emerald-600">Examplify</span>
            </h2>
            <p class="mt-2 text-base text-gray-600">
                Your secure portal for academic excellence
            </p>
        </div>
        <form class="mt-4 space-y-6" method="POST" id="login-form">
            <div class="space-y-4">
                <div>
                    <label for="email-address" class="sr-only">Email address</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required
                           class="appearance-none relative block w-full px-4 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-base transition-colors duration-200 shadow-sm"
                           placeholder="Enter your email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="appearance-none relative block w-full px-4 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-base transition-colors duration-200 shadow-sm"
                           placeholder="Enter your password">
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox"
                           class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-gray-900">
                        Remember me
                    </label>
                </div>

                <div>
                    <a href="#" class="font-medium text-emerald-600 hover:text-emerald-700 transition-colors duration-200">
                        Forgot password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-lg text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300 shadow-lg transform hover:-translate-y-1">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <!-- Lock icon -->
                        <svg class="h-6 w-6 text-white opacity-80 group-hover:opacity-100 transition-opacity duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Sign In
                </button>
            </div>
        </form>
        <p class="mt-6 text-center text-base text-gray-600">
            Don't have an account?
            <a href="#" class="font-medium text-emerald-600 hover:text-emerald-700 transition-colors duration-200">
                Register here
            </a>
        </p>
    </div>

    <!-- Login Modal Overlay -->
    <div id="login-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm hidden">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 animate-zoom-in max-h-screen overflow-y-auto flex flex-col justify-center">
            <button id="close-login-modal" class="absolute top-3 right-3 text-gray-400 hover:text-emerald-600 text-2xl font-bold focus:outline-none">&times;</button>
            <div class="text-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-emerald-600 mb-3">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <path d="M14 2v6h6"></path>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <line x1="10" y1="9" x2="8" y2="9"></line>
                </svg>
                <h2 class="mt-2 text-2xl font-extrabold text-gray-900">Welcome Back!</h2>
                <p class="mt-1 text-base text-gray-600">Sign in to your Examplify account</p>
            </div>
            <form class="space-y-5" method="POST" id="modal-login-form">
                <div>
                    <label for="modal-email" class="sr-only">Email address</label>
                    <input id="modal-email" name="email" type="email" autocomplete="email" required
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-base transition-colors duration-200 shadow-sm"
                        placeholder="Enter your email address">
                </div>
                <div>
                    <label for="modal-password" class="sr-only">Password</label>
                    <input id="modal-password" name="password" type="password" autocomplete="current-password" required
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-base transition-colors duration-200 shadow-sm"
                        placeholder="Enter your password">
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <input id="modal-remember-me" name="remember-me" type="checkbox"
                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                        <label for="modal-remember-me" class="ml-2 block text-gray-900">Remember me</label>
                    </div>
                    <div>
                        <a href="#" class="font-medium text-emerald-600 hover:text-emerald-700 transition-colors duration-200">Forgot password?</a>
                    </div>
                </div>
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-lg text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300 shadow-lg transform hover:-translate-y-1">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-6 w-6 text-white opacity-80 group-hover:opacity-100 transition-opacity duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Sign In
                </button>
            </form>
            <p class="mt-6 text-center text-base text-gray-600">
                Don't have an account?
                <a href="/student/signup/index.php" class="font-medium text-emerald-600 hover:text-emerald-700 transition-colors duration-200">Register here</a>
            </p>
        </div>
    </div>

    <script>
        // Basic JavaScript for form submission (for demonstration)
        document.getElementById('login-form').addEventListener('submit', function(event) {
            console.log('Form submission detected.');
            event.preventDefault(); // Prevent default form submission

            const email = document.getElementById('email-address').value;
            const password = document.getElementById('password').value;

            console.log('Login attempt with:');
            console.log('Email:', email);
            console.log('Password:', password);

            // For now, just a simple message box for demonstration
            // In a real application, you would send this data to a backend server
            // using fetch() or XMLHttpRequest and handle the response.
            alert('Login form submitted! (Check console for details)');
        });
    </script>
=======
    <link href="./src/output.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
    <!-- Navbar -->
   <?php Navbar();?>

   <!-- Hero Section -->
   <?php HeroSection();?>

   <!-- Features & Benefits Section -->
   <?php FeaturesBenefitsSection();?>

    <!-- Final Call to Action -->
    <?php CallToActionSection();?>

    <?php FooterSection(); ?>

>>>>>>> 475e36c183c6abe50ec194ce1389c2ae2dd47bc4
</body>
</html>
