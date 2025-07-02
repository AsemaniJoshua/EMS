<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="../../src/output.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-100 min-h-screen flex items-center justify-center">
    <div class="bg-white/80 backdrop-blur-md p-8 rounded-xl shadow-2xl max-w-md w-full">
        <h2 class="text-3xl font-extrabold mb-6 text-center text-blue-700">Student Login</h2>
        <form id="loginForm" class="space-y-4">
            <div>
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
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
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-semibold">Login</button>
        </form>
        <div class="mt-4 text-center">
            <button id="forgotPasswordBtn" class="text-blue-500 hover:underline">Forgot Password?</button>
        </div>
        <div class="mt-4 text-center">
            <a href="../signup/index.php" class="text-purple-500 hover:underline">Don't have an account? Sign Up</a>
        </div>
    </div>
    <!-- Forgot Password Modal -->
    <div id="forgotModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md relative animate-fadeIn">
            <button id="closeForgotModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h2 class="text-xl font-bold text-blue-700 mb-4">Reset Password</h2>
            <form id="forgotForm" class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-1">Index Number</label>
                    <input type="text" id="forgotIndex" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your index number">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-semibold">Send OTP</button>
            </form>
            <div id="forgotMsg" class="mt-4 text-center text-sm"></div>
        </div>
    </div>
    <!-- OTP Modal -->
    <div id="otpModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md relative animate-fadeIn">
            <button id="closeOtpModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h2 class="text-xl font-bold text-blue-700 mb-4">Enter OTP</h2>
            <form id="otpForm" class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-1">6-digit OTP Code</label>
                    <input type="text" id="otpInput" maxlength="6" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter OTP">
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 font-semibold">Verify OTP</button>
            </form>
            <div id="otpMsg" class="mt-4 text-center text-sm"></div>
        </div>
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
    <style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.3s ease; }
    </style>
</body>
</html>
