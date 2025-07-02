<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Signup</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-100 min-h-screen flex items-center justify-center">
    <div class="bg-white/80 backdrop-blur-md p-8 rounded-xl shadow-2xl max-w-md w-full">
        <h2 class="text-3xl font-extrabold mb-6 text-center text-blue-700">Student Signup</h2>
        <form id="signupForm" class="space-y-4">
            <div>
                <label class="block text-gray-700">Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-gray-700">Phone</label>
                <input type="tel" name="phone" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-gray-700">Confirm Password</label>
                <input type="password" name="confirm_password" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-semibold">Sign Up</button>
        </form>
        <div class="mt-4 text-center">
            <a href="../login/index.php" class="text-purple-500 hover:underline">Already have an account? Login</a>
        </div>
    </div>
    <script src="signup.js"></script>
</body>
</html>
