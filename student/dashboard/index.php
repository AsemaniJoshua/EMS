<?php
$pageTitle = "Student Dashboard";
$breadcrumb = "Dashboard";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Student</title>
    <link rel="stylesheet" href="../../src/output.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    <main class="pt-20 lg:ml-60 min-h-screen bg-gray-50 transition-all duration-300 px-4">
        <div class="p-4 lg:p-8 max-w-7xl mx-auto">
            <!-- Enrollment Key Input -->
            <div class="bg-gradient-to-r from-blue-100 to-blue-50 rounded-lg shadow-lg p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-blue-700 mb-1">Join a Course or Exam</h2>
                    <p class="text-gray-600 text-sm">Enter your enrollment key to access upcoming exams for your course.</p>
                </div>
                <form id="enrollForm" class="flex gap-2 w-full md:w-auto">
                    <input id="enrollKey" type="text" required placeholder="Enrollment Key" class="px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent flex-1" />
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">Submit</button>
                </form>
            </div>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg shadow-lg text-white relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Registered Exams</p>
                            <p class="text-3xl font-bold">12</p>
                            <p class="text-blue-100 text-sm">+2 this month</p>
                        </div>
                        <div class="text-blue-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 7H7v6h6V7z" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3-9a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H8a1 1 0 01-1-1V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg shadow-lg text-white relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Average Score</p>
                            <p class="text-3xl font-bold">87%</p>
                            <p class="text-green-100 text-sm">+5% from last term</p>
                        </div>
                        <div class="text-green-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3-9a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H8a1 1 0 01-1-1V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-6 rounded-lg shadow-lg text-white relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">Pending Exams</p>
                            <p class="text-3xl font-bold">3</p>
                            <p class="text-yellow-100 text-sm">Upcoming this week</p>
                        </div>
                        <div class="text-yellow-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H8V3a1 1 0 10-2 0v1z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-lg shadow-lg text-white relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Completed Exams</p>
                            <p class="text-3xl font-bold">9</p>
                            <p class="text-purple-100 text-sm">+1 this week</p>
                        </div>
                        <div class="text-purple-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3-9a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Exams (dynamic) -->
        <div id="upcomingExamsSection" class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Upcoming Exams</h3>
                <a href="../exam/index.php" class="text-blue-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody id="upcomingExamsTable" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">Math Final 2024</td>
                            <td class="px-6 py-4 whitespace-nowrap">2024-06-20</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold">Pending</span></td>
                            <td class="px-6 py-4 whitespace-nowrap"><a href="../exam/index.php" class="text-blue-600 hover:underline">Start</a></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">Science Quiz</td>
                            <td class="px-6 py-4 whitespace-nowrap">2024-06-25</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold">Pending</span></td>
                            <td class="px-6 py-4 whitespace-nowrap"><a href="../exam/index.php" class="text-blue-600 hover:underline">Start</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Recent Results -->
        <div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Recent Results</h3>
                <a href="../results/index.php" class="text-blue-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">English Midterm</td>
                            <td class="px-6 py-4 whitespace-nowrap">92%</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-semibold">Passed</span></td>
                            <td class="px-6 py-4 whitespace-nowrap">2024-05-10</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">History Final</td>
                            <td class="px-6 py-4 whitespace-nowrap">78%</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs font-semibold">Failed</span></td>
                            <td class="px-6 py-4 whitespace-nowrap">2024-04-15</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Recent Activity Timeline -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Recent Activity</h3>
            <ul class="relative border-l-2 border-blue-200 pl-6 space-y-4">
                <li>
                    <span class="absolute -left-3 top-1 w-3 h-3 bg-blue-500 rounded-full"></span>
                    <span class="font-semibold text-blue-700">Math Final 2024</span> - Exam scheduled for <span class="text-gray-700">2024-06-20</span>
                </li>
                <li>
                    <span class="absolute -left-3 top-1 w-3 h-3 bg-green-500 rounded-full"></span>
                    <span class="font-semibold text-green-700">English Midterm</span> - Scored <span class="text-gray-700">92%</span> on 2024-05-10
                </li>
                <li>
                    <span class="absolute -left-3 top-1 w-3 h-3 bg-yellow-500 rounded-full"></span>
                    <span class="font-semibold text-yellow-700">Science Quiz</span> - Rescheduled to <span class="text-gray-700">2024-06-25</span>
                </li>
            </ul>
        </div>
        <!-- Announcements -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Announcements</h3>
            <ul class="list-disc pl-6 text-gray-700 space-y-2">
                <li>Math Final 2024 will be held online. Please check your email for details.</li>
                <li>Science Quiz rescheduled to June 25th.</li>
                <li>New study materials available in the Resources section.</li>
            </ul>
        </div>
        </div>
    </main>
    <script>
        // Enrollment key demo logic
        document.getElementById('enrollForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const key = document.getElementById('enrollKey').value.trim();
            const examsTable = document.getElementById('upcomingExamsTable');
            // Simulate fetching exams for the key
            let exams = [];
            if (key === 'MATH2024') {
                exams = [{
                        name: 'Math Final 2024',
                        date: '2024-06-20',
                        status: 'Pending'
                    },
                    {
                        name: 'Algebra Quiz',
                        date: '2024-06-22',
                        status: 'Pending'
                    }
                ];
            } else if (key === 'SCI2024') {
                exams = [{
                        name: 'Science Quiz',
                        date: '2024-06-25',
                        status: 'Pending'
                    },
                    {
                        name: 'Biology Test',
                        date: '2024-07-01',
                        status: 'Pending'
                    }
                ];
            } else {
                exams = [];
            }
            let html = '';
            if (exams.length > 0) {
                exams.forEach(exam => {
                    html += `<tr>
                    <td class='px-6 py-4 whitespace-nowrap'>${exam.name}</td>
                    <td class='px-6 py-4 whitespace-nowrap'>${exam.date}</td>
                    <td class='px-6 py-4 whitespace-nowrap'><span class='px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold'>${exam.status}</span></td>
                    <td class='px-6 py-4 whitespace-nowrap'><a href='../exam/index.php' class='text-blue-600 hover:underline'>Start</a></td>
                </tr>`;
                });
            } else {
                html = `<tr><td colspan='4' class='px-6 py-4 text-center text-gray-500'>No upcoming exams found for this key.</td></tr>`;
            }
            examsTable.innerHTML = html;
        });
    </script>
</body>

</html>