<?php
$pageTitle = "Results";
$breadcrumb = "Results";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Student</title>
    <link rel="stylesheet" href="../../src/output.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    <main class="pt-20 lg:ml-60 min-h-screen bg-gray-50 transition-all duration-300 px-4">
        <div class="p-4 lg:p-8 max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold text-blue-700 mb-6">Results</h1>
            <!-- Stats Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg shadow-lg text-white flex flex-col items-center">
                    <span class="text-sm font-medium text-blue-100 mb-1">Average Score</span>
                    <span class="text-3xl font-bold">87%</span>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg shadow-lg text-white flex flex-col items-center">
                    <span class="text-sm font-medium text-green-100 mb-1">Pass Rate</span>
                    <span class="text-3xl font-bold">75%</span>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-lg shadow-lg text-white flex flex-col items-center">
                    <span class="text-sm font-medium text-purple-100 mb-1">Exams Taken</span>
                    <span class="text-3xl font-bold">12</span>
                </div>
            </div>
            
            <!-- Search & Filter -->
            <div class="flex flex-col md:flex-row gap-4 mb-6 items-center">
                <input type="text" id="searchInput" placeholder="Search results..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent flex-1" />
                <select id="subjectFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Subjects</option>
                    <option>Mathematics</option>
                    <option>Science</option>
                    <option>English</option>
                    <option>History</option>
                </select>
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option>Passed</option>
                    <option>Failed</option>
                </select>
            </div>
            <!-- Results Table -->
            <div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Exam Results</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Results will be rendered by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script>
    // Sample results data
    const results = [
        { exam: 'English Midterm', subject: 'English', date: '2024-05-10', score: 92, status: 'Passed' },
        { exam: 'History Final', subject: 'History', date: '2024-04-15', score: 78, status: 'Failed' },
        { exam: 'Math Final 2024', subject: 'Mathematics', date: '2024-06-20', score: 88, status: 'Passed' },
        { exam: 'Science Quiz', subject: 'Science', date: '2024-06-25', score: 85, status: 'Passed' },
        { exam: 'Biology Test', subject: 'Science', date: '2024-07-01', score: 70, status: 'Failed' },
        { exam: 'English Final', subject: 'English', date: '2024-07-10', score: 95, status: 'Passed' },
    ];
    
    // Search & Filter logic
    const searchInput = document.getElementById('searchInput');
    const subjectFilter = document.getElementById('subjectFilter');
    const statusFilter = document.getElementById('statusFilter');
    [searchInput, subjectFilter, statusFilter].forEach(el => {
        el.addEventListener('input', renderResults);
    });
    function renderResults() {
        let filtered = results;
        const search = searchInput.value.toLowerCase();
        const subject = subjectFilter.value;
        const status = statusFilter.value;
        if (search) filtered = filtered.filter(r => r.exam.toLowerCase().includes(search) || r.subject.toLowerCase().includes(search));
        if (subject) filtered = filtered.filter(r => r.subject === subject);
        if (status) filtered = filtered.filter(r => r.status === status);
        const table = document.getElementById('resultsTable');
        table.innerHTML = '';
        if (filtered.length === 0) {
            table.innerHTML = '<tr><td colspan="5" class="text-center py-4">No results found.</td></tr>';
            return;
        }
        filtered.forEach(r => {
            table.innerHTML += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">${r.exam}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${r.subject}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${r.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-bold ${r.status === 'Passed' ? 'text-green-600' : 'text-red-600'}">${r.score}%</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 rounded ${r.status === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-xs font-semibold">${r.status}</span>
                    </td>
                </tr>`;
        });
    }
    // Initial render
    renderResults();
    </script>
</body>
</html>
