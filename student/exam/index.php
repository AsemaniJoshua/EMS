<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow max-w-xl w-full">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Take Exam</h2>
        <div id="timer" class="text-right text-lg font-semibold text-red-500 mb-4">Time: 00:00</div>
        <form id="examForm">
            <div id="questionContainer" class="mb-6">
                <!-- Question and choices will be loaded here by JS -->
            </div>
            <div class="flex justify-between">
                <button type="button" id="prevBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Previous</button>
                <button type="button" id="nextBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Next</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Submit</button>
            </div>
        </form>
    </div>
    <script src="exam.js"></script>
</body>
</html> 