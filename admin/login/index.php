<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EMS</title>
    <link rel="stylesheet" href="../../src/output.css">
</head>
<body class="bg-gradient-to-br from-blue-100 via-white to-green-100 min-h-screen flex items-center justify-center">
    <div class="bg-white/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl max-w-md w-full flex flex-col items-center">
        <div class="mb-4">
            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-2">
                <i class="fa-solid fa-file-alt text-3xl text-green-600"></i>
            </div>
        </div>
        <h2 class="text-2xl font-extrabold text-center text-gray-900 mb-1">Welcome Back to <span class="text-green-600">EMS Admin</span></h2>
        <p class="text-gray-500 text-center mb-6">Your secure portal for administration</p>
        <form class="space-y-4 w-full">
            <div>
                <input type="email" placeholder="Enter your email address" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
                <input type="password" placeholder="Enter your password" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500" />
                    <span class="text-gray-600">Remember me</span>
                </label>
                <a href="#" class="text-green-600 hover:underline font-medium">Forgot password?</a>
            </div>
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-2 rounded-lg font-bold shadow transition-all duration-200 mt-2">
                <i class="fa-solid fa-lock"></i> Sign In
            </button>
        </form>
        <div class="mt-6 text-center text-sm text-gray-500">
            Don't have an account? <a href="signup.php" class="text-green-600 hover:underline font-medium">Register here</a>
        </div>
    </div>
</body>
</html>
