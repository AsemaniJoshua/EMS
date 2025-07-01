<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto mt-10 p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-blue-600">Student Results</h2>
        <form id="filterForm" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="student" placeholder="Student Name" class="px-3 py-2 border rounded">
            <input type="text" name="exam" placeholder="Exam" class="px-3 py-2 border rounded">
            <input type="text" name="category" placeholder="Category" class="px-3 py-2 border rounded">
            <input type="date" name="date" class="px-3 py-2 border rounded">
            <button type="submit" class="bg-blue-600 text-white py-2 rounded hover:bg-blue-700 col-span-1 md:col-span-4">Filter</button>
        </form>
        <table class="min-w-full bg-white border rounded">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Student</th>
                    <th class="py-2 px-4 border-b">Exam</th>
                    <th class="py-2 px-4 border-b">Category</th>
                    <th class="py-2 px-4 border-b">Date</th>
                    <th class="py-2 px-4 border-b">Score</th>
                    <th class="py-2 px-4 border-b">Result</th>
                </tr>
            </thead>
            <tbody id="resultsTable">
                <!-- Results will be populated here by JS -->
            </tbody>
        </table>
    </div>
    <script src="results.js"></script>
</body>
</html> 