<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examplify - Login</title>
    <link href="../../src/output.css" rel="stylesheet">
    <style>
        /* Custom styles for Inter font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Keyframes for subtle animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Apply animations */
        .animate-fade-in { animation: fadeIn 0.6s ease-out forwards; opacity: 0; }
        .animate-zoom-in { animation: zoomIn 0.5s ease-out forwards; opacity: 0; }

        /* Animation delays */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 p-10 bg-white rounded-xl shadow-2xl animate-zoom-in">
        <div class="text-center">
            <!-- Examplify Logo/Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-emerald-600 mb-4">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <path d="M14 2v6h6"></path>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <line x1="10" y1="9" x2="8" y2="9"></line>
            </svg>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 animate-fade-in delay-100">
                Welcome Back!
            </h2>
            <p class="mt-2 text-sm text-gray-600 animate-fade-in delay-200">
                Sign in to your Examplify account
            </p>
        </div>
        <form class="mt-8 space-y-6" action="#" method="POST" id="login-form">
            <div class="space-y-4">
                <div class="animate-fade-in delay-300">
                    <label for="email-address" class="sr-only">Email address</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required
                           class="appearance-none rounded-xl relative block w-full px-5 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#06b6d4] focus:border-[#06b6d4] focus:z-10 sm:text-base shadow-sm transition"
                           placeholder="Email address">
                </div>
                <div class="animate-fade-in delay-400">
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="appearance-none rounded-xl relative block w-full px-5 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#06b6d4] focus:border-[#06b6d4] focus:z-10 sm:text-base shadow-sm transition"
                           placeholder="Password">
                </div>
            </div>

            <div class="flex items-center justify-between animate-fade-in delay-500">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox"
                           class="h-4 w-4 text-[#06b6d4] focus:ring-[#06b6d4] border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-[#06b6d4] hover:text-[#0e7490] transition">Forgot your password?</a>
                </div>
            </div>

            <div class="animate-fade-in delay-600">
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-6 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-[#06b6d4] to-[#7c3aed] hover:from-[#0ea5e9] hover:to-[#6366f1] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#06b6d4] transition-colors duration-300 shadow-lg">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <!-- Lock icon -->
                        <svg class="h-5 w-5 text-white group-hover:text-[#fbbf24]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Sign in
                </button>
            </div>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600 animate-fade-in delay-700">
            Don't have an account?
            <a href="#" class="font-medium text-emerald-600 hover:text-emerald-500">
                Register here
            </a>
        </p>
    </div>

    <script>
        // Basic JavaScript for form submission (for demonstration)
        document.getElementById('login-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const email = document.getElementById('email-address').value;
            const password = document.getElementById('password').value;

            // In a real application, you would send this data to a backend server
            // using fetch() or XMLHttpRequest.
            console.log('Login attempt with:');
            console.log('Email:', email);
            console.log('Password:', password);

            // You might show a loading spinner or a success/error message here
            // For now, just a simple alert for demonstration
            alert('Login form submitted! (Check console for details)');
        });
    </script>
</body>
</html>
