<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup - EMS</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-100 via-white to-green-100 min-h-screen flex items-center justify-center">
    <div class="bg-white/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl max-w-md w-full flex flex-col items-center">
        <div class="mb-4">
            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-2">
                <i class="fa-solid fa-file-alt text-3xl text-green-600"></i>
            </div>
        </div>
        <h2 class="text-2xl font-extrabold text-center text-gray-900 mb-1">Create <span class="text-green-600">Admin Account</span></h2>
        <p class="text-gray-500 text-center mb-6">Sign up to manage the EMS portal</p>
        <form class="space-y-4 w-full">
            <div>
                <input type="text" placeholder="Full Name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
                <input type="email" placeholder="Email Address" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
                <input type="tel" placeholder="Phone Number" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
                <input type="password" placeholder="Password" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
                <input type="password" placeholder="Confirm Password" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-2 rounded-lg font-bold shadow transition-all duration-200 mt-2">
                <i class="fa-solid fa-user-plus"></i> Sign Up
            </button>
        </form>
        <div class="mt-6 text-center text-sm text-gray-500">
            Already have an account? <a href="index.php" class="text-green-600 hover:underline font-medium">Sign In</a>
        </div>
    </div>
</body>
</html> 