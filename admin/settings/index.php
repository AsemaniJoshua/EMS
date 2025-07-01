<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Settings</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-blue-600">Exam Settings</h2>
        <form id="categoryForm" class="mb-8 space-y-4">
            <div>
                <label class="block text-gray-700">New Category</label>
                <input type="text" name="category" required class="w-full px-3 py-2 border rounded">
            </div>
            <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded hover:bg-blue-700">Add Category</button>
        </form>
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-2">Categories</h3>
            <ul id="categoriesList" class="space-y-2">
                <!-- Categories will be loaded here by JS -->
            </ul>
        </div>
        <form id="durationForm" class="space-y-4">
            <div>
                <label class="block text-gray-700">Set Exam Duration (minutes)</label>
                <input type="number" name="duration" min="1" required class="w-full px-3 py-2 border rounded">
            </div>
            <button type="submit" class="bg-green-600 text-white py-2 px-6 rounded hover:bg-green-700">Set Duration</button>
        </form>
    </div>
    <script src="settings.js"></script>
</body>
</html>
