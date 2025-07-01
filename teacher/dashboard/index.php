<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <div class="text-xl font-bold text-blue-600">Exams Management System</div>
        <ul class="flex space-x-6">
            <li><a href="../dashboard/index.php" class="text-gray-700 hover:text-blue-600">Dashboard</a></li>
            <li><a href="../exam/index.php" class="text-gray-700 hover:text-blue-600">Manage Exams</a></li>
            <li><a href="../profile/index.php" class="text-gray-700 hover:text-blue-600">Profile</a></li>
            <li><a href="../login/index.php" class="text-red-500 hover:text-red-700">Logout</a></li>
        </ul>
    </nav>
    <main class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-semibold mb-4">Welcome, Teacher!</h1>
        <p class="text-gray-600">This is your dashboard. Here you can manage exams, questions, and view results.</p>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-100 p-4 rounded shadow text-center">
                <div class="font-bold text-lg">Add/Edit Questions</div>
                <p class="text-blue-700">Manage exam questions and categories.</p>
            </div>
            <div class="bg-green-100 p-4 rounded shadow text-center">
                <div class="font-bold text-lg">View Results</div>
                <p class="text-green-700">Monitor student performance.</p>
            </div>
        </div>
    </main>
    <script src="dashboard.js"></script>
</body>
</html>
