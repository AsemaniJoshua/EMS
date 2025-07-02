<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exam Questions</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-blue-600">Manage Exam Questions</h2>
        <form id="questionForm" class="mb-8 space-y-4">
            <div>
                <label class="block text-gray-700">Question</label>
                <input type="text" name="question" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700">Choice 1</label>
                    <input type="text" name="choice1" required class="w-full px-3 py-2 border rounded">
                </div>
                <div>
                    <label class="block text-gray-700">Choice 2</label>
                    <input type="text" name="choice2" required class="w-full px-3 py-2 border rounded">
                </div>
                <div>
                    <label class="block text-gray-700">Choice 3</label>
                    <input type="text" name="choice3" required class="w-full px-3 py-2 border rounded">
                </div>
                <div>
                    <label class="block text-gray-700">Choice 4</label>
                    <input type="text" name="choice4" required class="w-full px-3 py-2 border rounded">
                </div>
            </div>
            <div>
                <label class="block text-gray-700">Correct Answer</label>
                <select name="correct" required class="w-full px-3 py-2 border rounded">
                    <option value="0">Choice 1</option>
                    <option value="1">Choice 2</option>
                    <option value="2">Choice 3</option>
                    <option value="3">Choice 4</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700">Exam Duration (minutes)</label>
                <input type="number" name="duration" min="1" required class="w-full px-3 py-2 border rounded">
            </div>
            <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded hover:bg-blue-700">Add Question</button>
        </form>
        <div class="mb-8">
            <button id="requestApprovalBtn" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Request Admin Approval</button>
            <span id="examStatus" class="ml-4 font-semibold"></span>
        </div>
        <div>
            <h3 class="text-xl font-semibold mb-2">Questions List</h3>
            <ul id="questionsList" class="space-y-2">
                <!-- Questions will be loaded here by JS -->
            </ul>
        </div>
        <div class="mt-8 text-right">
            <a href="../results/index.php" class="text-blue-500 hover:underline">View Student Results</a>
        </div>
    </div>
    <script src="exam.js"></script>
</body>
</html>
