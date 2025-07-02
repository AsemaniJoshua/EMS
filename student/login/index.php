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
            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
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
    <script src="login.js"></script>
    <style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.3s ease; }
    </style>
</body>
</html>
