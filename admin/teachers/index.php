<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-blue-600">Manage Teachers</h2>
        <button id="addTeacherBtn" class="mb-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Teacher</button>
        <table class="min-w-full bg-white border rounded">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Name</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody id="teachersTable">
                <!-- Teachers will be populated here by JS -->
            </tbody>
        </table>
    </div>
    <script src="teachers.js"></script>
</body>
</html>
