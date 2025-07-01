<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Approval</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto mt-10 p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-blue-600">Exam Approval</h2>
        <table class="min-w-full bg-white border rounded">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Exam</th>
                    <th class="py-2 px-4 border-b">Teacher</th>
                    <th class="py-2 px-4 border-b">Category</th>
                    <th class="py-2 px-4 border-b">Duration</th>
                    <th class="py-2 px-4 border-b">Start Date/Time</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody id="approvalTable">
                <!-- Exams will be populated here by JS -->
            </tbody>
        </table>
    </div>
    <script src="approval.js"></script>
</body>
</html> 