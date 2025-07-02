<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Reset Password - Examplify</title>
    <!-- Tailwind  -->
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
                Reset Your <span class="text-emerald-600">Password</span>
            </h2>
            <p class="mt-2 text-base text-gray-600">
                Enter your email to receive a password reset link.
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST" id="reset-password-form">
            <div class="space-y-4">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="appearance-none relative block w-full px-4 py-2.5 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-base transition-colors duration-200 shadow-sm"
                           placeholder="Enter your email address">
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-lg text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300 shadow-lg transform hover:-translate-y-1">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <!-- Mail icon -->
                        <svg class="h-6 w-6 text-white opacity-80 group-hover:opacity-100 transition-opacity duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                    </span>
                    Send Reset Link
                </button>
            </div>
        </form>
        <p class="mt-6 text-center text-base text-gray-600">
            Remember your password?
            <a href="login/index.php" class="font-medium text-emerald-600 hover:text-emerald-700 transition-colors duration-200">
                Login here
            </a>
        </p>
    </div>

    <script>
        document.getElementById('reset-password-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const email = document.getElementById('email').value;

            console.log('Password reset request for:', email);

            // In a real application, you would send this email to your backend.
            // The backend would then send a password reset link to the provided email.
            alert('If an account with that email exists, a password reset link has been sent to your inbox.');

            // Optionally, redirect to a confirmation page or back to login
            // window.location.href = 'login/index.php';
        });
    </script>
</body>
</html>
