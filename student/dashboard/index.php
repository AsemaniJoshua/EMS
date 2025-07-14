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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-4 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Student Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">Welcome back! Here’s your student overview.</p>
                </div>
            </div>
            <!-- Enrollment Key Input -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-emerald-700 mb-1">Join a Course or Exam</h2>
                    <p class="text-gray-600 text-sm">Enter your enrollment key to access upcoming exams for your course.</p>
                </div>
                <form id="enrollForm" class="flex gap-2 w-full md:w-auto">
                    <input id="enrollKey" type="text" required placeholder="Enrollment Key" class="px-4 py-2 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent flex-1" />
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">Submit</button>
                </form>
            </div>
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Registered Exams</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900">12</div>
                                    <div class="mt-1 flex items-baseline text-sm">
                                        <span class="text-emerald-600 font-medium">+2</span>
                                        <span class="ml-1 text-gray-500">this month</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                            <i class="fas fa-chart-line text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900">87%</div>
                                    <div class="mt-1 flex items-baseline text-sm">
                                        <span class="text-emerald-600 font-medium">+5%</span>
                                        <span class="ml-1 text-gray-500">from last term</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-yellow-50 rounded-lg p-3">
                            <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Exams</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900">3</div>
                                    <div class="mt-1 flex items-baseline text-sm">
                                        <span class="text-yellow-600 font-medium">Upcoming</span>
                                        <span class="ml-1 text-gray-500">this week</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5 flex items-center">
                        <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                            <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Completed Exams</dt>
                                <dd>
                                    <div class="text-xl font-semibold text-gray-900">9</div>
                                    <div class="mt-1 flex items-baseline text-sm">
                                        <span class="text-purple-600 font-medium">+1</span>
                                        <span class="ml-1 text-gray-500">this week</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Upcoming Exams -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-900">Upcoming Exams</h2>
                            <a href="../exam/index.php" class="text-sm text-emerald-600 hover:text-emerald-700">View All</a>
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
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button onclick="window.location.href='../exam/index.php'" class="text-emerald-600 hover:text-emerald-700 transition-colors duration-200">
                                                <i class="fas fa-play text-lg cursor-pointer"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">Science Quiz</td>
                                        <td class="px-6 py-4 whitespace-nowrap">2024-06-25</td>
                                        <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold">Pending</span></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button onclick="window.location.href='../exam/index.php'" class="text-emerald-600 hover:text-emerald-700 transition-colors duration-200">
                                                <i class="fas fa-play text-lg cursor-pointer"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Recent Results -->
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="font-semibold text-gray-900">Recent Results</h2>
                            <a href="../results/index.php" class="text-sm text-emerald-600 hover:text-emerald-700">View All</a>
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
                </div>
                <!-- Right Column: Activity & Announcements -->
                <div class="flex flex-col gap-6">
                    <!-- Recent Activity Timeline -->
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Recent Activity</h3>
                        <ul class="relative border-l-2 border-emerald-200 pl-6 space-y-4">
                            <li>
                                <span class="absolute -left-3 top-1 w-3 h-3 bg-emerald-500 rounded-full"></span>
                                <span class="font-semibold text-emerald-700">Math Final 2024</span> - Exam scheduled for <span class="text-gray-700">2024-06-20</span>
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
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Announcements</h3>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>Math Final 2024 will be held online. Please check your email for details.</li>
                            <li>Science Quiz rescheduled to June 25th.</li>
                            <li>New study materials available in the Resources section.</li>
                        </ul>
                    </div>
                </div>
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
                        status: 'Pending',
                        action: 'Start'
                    }];
            }
            // ...
        });
    </script>
</body>
</html>