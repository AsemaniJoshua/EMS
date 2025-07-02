<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <div class="text-xl font-bold text-blue-600">Exams Management System</div>
        <ul class="flex space-x-6">
            <li><a href="../dashboard/index.php" class="text-gray-700 hover:text-blue-600">Dashboard</a></li>
            <li><a href="../teachers/index.php" class="text-gray-700 hover:text-blue-600">Manage Teachers</a></li>
            <li><a href="../students/index.php" class="text-gray-700 hover:text-blue-600">Manage Students</a></li>
            <li><a href="../settings/index.php" class="text-gray-700 hover:text-blue-600">Settings</a></li>
            <li><a href="../login/index.php" class="text-red-500 hover:text-red-700">Logout</a></li>
        </ul>
    </nav>
    <main class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-semibold mb-4">Welcome, Admin!</h1>
        <p class="text-gray-600">This is your dashboard. Here you can manage users, exams, and view system statistics.</p>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-100 p-4 rounded shadow text-center">
                <div class="font-bold text-lg">Manage Teachers</div>
                <p class="text-blue-700">Add, edit, or remove teachers.</p>
            </div>
            <div class="bg-green-100 p-4 rounded shadow text-center">
                <div class="font-bold text-lg">Manage Students</div>
                <p class="text-green-700">Add, edit, or remove students.</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded shadow text-center">
                <div class="font-bold text-lg">Settings</div>
                <p class="text-yellow-700">Configure exam categories and durations.</p>
            </div>
        </div>
    </main>
    <script src="dashboard.js"></script>
</body>
</html>
