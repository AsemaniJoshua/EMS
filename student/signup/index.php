<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examplify - Register</title>
    <!-- Tailwind CSS CDN -->
    <link href="../../src/output.css" rel="stylesheet">
    <style>
        /* Custom styles for Inter font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-green-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 p-8 sm:p-10 bg-white rounded-3xl shadow-2xl border border-gray-200">
        <div class="text-center">
            <!-- Examplify Logo/Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-emerald-600 mb-4">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <path d="M14 2v6h6"></path>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <line x1="10" y1="9" x2="8" y2="9"></line>
            </svg>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Join <span class="text-emerald-600">Examplify</span> Today!
            </h2>
            <p class="mt-2 text-base text-gray-600">
                Create your account to unlock academic excellence
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST" id="signup-form">
            <div class="space-y-4">
                <div>
                    <label for="full-name" class="sr-only">Full Name</label>
                    <input id="full-name" name="full_name" type="text" autocomplete="name" required
                           class="appearance-none relative block w-full px-4 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-base transition-colors duration-200 shadow-sm"
                           placeholder="Full Name">
                </div>
                <div>
                    <label for="email-address" class="sr-only">Email address</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required
                           class="appearance-none relative block w-full px-4 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-base transition-colors duration-200 shadow-sm"
                           placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="appearance-none relative block w-full px-4 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-base transition-colors duration-200 shadow-sm"
                           placeholder="Password">
                </div>
                <div>
                    <label for="confirm-password" class="sr-only">Confirm Password</label>
                    <input id="confirm-password" name="confirm_password" type="password" autocomplete="new-password" required
                           class="appearance-none relative block w-full px-4 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-base transition-colors duration-200 shadow-sm"
                           placeholder="Confirm Password">
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-lg text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300 shadow-lg transform hover:-translate-y-1">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <!-- User Plus Icon -->
                        <svg class="h-6 w-6 text-white opacity-80 group-hover:opacity-100 transition-opacity duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                    </span>
                    Register Account
                </button>
            </div>
        </form>
        <p class="mt-6 text-center text-base text-gray-600">
            Already have an account?
            <a href="login.html" class="font-medium text-emerald-600 hover:text-emerald-700 transition-colors duration-200">
                Log in here
            </a>
        </p>
    </div>

    <script>
        // Basic JavaScript for form submission (for demonstration)
        document.getElementById('signup-form').addEventListener('submit', function(event) {
            console.log('Sign-up form submission detected.');
            event.preventDefault(); // Prevent default form submission

            const fullName = document.getElementById('full-name').value;
            const email = document.getElementById('email-address').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            // In a real application, you would send this data to a backend server
            // using fetch() or XMLHttpRequest.
            console.log('Registration attempt with:');
            console.log('Full Name:', fullName);
            console.log('Email:', email);
            console.log('Password:', password); // In production, never log raw password

            // For now, just a simple message box for demonstration
            alert('Registration form submitted! (Check console for details)');
        });
    </script>
</body>
</html>
