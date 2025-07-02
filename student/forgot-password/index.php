<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Forgot Password</h2>
        <form id="forgotPasswordForm" class="space-y-4">
            <div>
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Send Reset Link</button>
        </form>
        <div class="mt-4 text-center">
            <a href="../login/index.php" class="text-blue-500 hover:underline">Back to Login</a>
        </div>
    </div>
    <script src="forgot-password.js"></script>
</body>
</html> 