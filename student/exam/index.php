<?php
$pageTitle = "Exams";
$breadcrumb = "Exams";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Student</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    <!-- Main content area -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-4 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Exams</h1>
                    <p class="mt-1 text-sm text-gray-500">Your upcoming, ongoing, and past exams.</p>
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
            <!-- Tabs -->
            <div class="mb-6">
                <div class="flex gap-2 border-b">
                    <button class="tab-btn px-4 py-2 font-semibold text-emerald-600 border-b-2 border-emerald-600 bg-emerald-50 rounded-t" data-tab="upcoming">Upcoming</button>
                    <button class="tab-btn px-4 py-2 font-semibold text-gray-600 hover:text-emerald-600" data-tab="ongoing">Ongoing</button>
                    <button class="tab-btn px-4 py-2 font-semibold text-gray-600 hover:text-emerald-600" data-tab="past">Past</button>
                </div>
            </div>
            <!-- Search & Filter -->
            <div class="flex flex-col md:flex-row gap-4 mb-6 items-center">
                <input type="text" placeholder="Search exams..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent flex-1" />
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">All Subjects</option>
                    <option>Mathematics</option>
                    <option>Science</option>
                    <option>English</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option>Upcoming</option>
                    <option>Ongoing</option>
                    <option>Completed</option>
                </select>
            </div>
            <!-- Tab Content: Upcoming Exams -->
            <div id="tab-upcoming" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Exam Card 1 -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-8 flex flex-col gap-2 relative"> <!-- Increased padding -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-blue-700">Mathematics</span>
                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-semibold">Upcoming</span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Math Final 2024</h2>
                        <div class="text-gray-600 text-sm mb-2">Date: 2024-06-20</div>
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-clock text-blue-500"></i>
                            <span class="text-gray-700 text-sm">Starts in <span class="font-semibold">2 days</span></span>
                        </div>
                        <button class="mt-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200" onclick="openExamModal('Math Final 2024')">View Details</button>
                    </div>
                    <!-- Exam Card 2 -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-8 flex flex-col gap-2 relative"> <!-- Increased padding -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-green-700">Science</span>
                            <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-semibold">Upcoming</span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Science Quiz</h2>
                        <div class="text-gray-600 text-sm mb-2">Date: 2024-06-25</div>
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-clock text-green-500"></i>
                            <span class="text-gray-700 text-sm">Starts in <span class="font-semibold">5 days</span></span>
                        </div>
                        <button class="mt-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200" onclick="openExamModal('Science Quiz')">View Details</button>
                    </div>
                </div>
            </div>
            <!-- Tab Content: Ongoing Exams -->
            <div id="tab-ongoing" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Ongoing Exam Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-8 flex flex-col gap-2 relative"> <!-- Increased padding -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-emerald-700">English</span>
                            <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-semibold">Ongoing</span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">English Midterm</h2>
                        <div class="text-gray-600 text-sm mb-2">Date: 2024-06-18</div>
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-clock text-emerald-500"></i>
                            <span class="text-gray-700 text-sm">Time left: <span class="font-semibold">00:45:12</span></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: 60%"></div>
                        </div>
                        <button class="mt-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">Continue Exam</button>
                    </div>
                </div>
            </div>
            <!-- Tab Content: Past Exams -->
            <div id="tab-past" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Past Exams</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">English Midterm</td>
                                    <td class="px-6 py-4 whitespace-nowrap">2024-05-10</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-semibold">Completed</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">92%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">History Final</td>
                                    <td class="px-6 py-4 whitespace-nowrap">2024-04-15</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs font-semibold">Completed</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">78%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Exam Details Modal -->
            <div id="examModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden">
                <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-8 relative">
                    <button onclick="closeExamModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
                        <i class="fas fa-times w-6 h-6"></i>
                    </button>
                    <h2 id="modalExamTitle" class="text-2xl font-bold text-emerald-700 mb-2">Exam Title</h2>
                    <div class="mb-4 text-gray-700">Full details and instructions for the exam go here. Please read carefully before starting.</div>
                    <ul class="mb-4 text-gray-600 text-sm list-disc pl-5">
                        <li>Duration: 1 hour</li>
                        <li>Number of Questions: 40</li>
                        <li>Allowed Attempts: 1</li>
                    </ul>
                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">Start Exam</button>
                </div>
            </div>
        </div>
    </main>
    <script>
        // Sample exam data
        const exams = [{
                id: 1,
                title: 'Math Final 2024',
                subject: 'Mathematics',
                date: '2024-06-20',
                status: 'Upcoming',
                type: 'upcoming',
                details: 'Math final exam for 2024. Duration: 1 hour. 40 questions.'
            },
            {
                id: 2,
                title: 'Science Quiz',
                subject: 'Science',
                date: '2024-06-25',
                status: 'Upcoming',
                type: 'upcoming',
                details: 'Science quiz. Duration: 45 minutes. 30 questions.'
            },
            {
                id: 3,
                title: 'English Midterm',
                subject: 'English',
                date: '2024-06-18',
                status: 'Ongoing',
                type: 'ongoing',
                timeLeft: '00:45:12',
                progress: 60,
                details: 'English midterm. Duration: 1 hour. 40 questions.'
            },
            {
                id: 4,
                title: 'English Midterm',
                subject: 'English',
                date: '2024-05-10',
                status: 'Completed',
                type: 'past',
                score: '92%',
                details: 'English midterm. Duration: 1 hour. 40 questions.'
            },
            {
                id: 5,
                title: 'History Final',
                subject: 'History',
                date: '2024-04-15',
                status: 'Completed',
                type: 'past',
                score: '78%',
                details: 'History final. Duration: 1 hour. 40 questions.'
            }
        ];

        // Tabs logic
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('text-emerald-600', 'bg-emerald-50', 'border-b-2', 'border-emerald-600', 'rounded-t'));
                this.classList.add('text-emerald-600', 'bg-emerald-50', 'border-b-2', 'border-emerald-600', 'rounded-t');
                const tab = this.getAttribute('data-tab');
                document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
                document.getElementById('tab-' + tab).classList.remove('hidden');
                renderTab(tab);
            });
        });

        // Search & Filter logic
        const searchInput = document.querySelector('input[placeholder="Search exams..."]');
        const subjectFilter = document.querySelectorAll('select')[0];
        const statusFilter = document.querySelectorAll('select')[1];

        [searchInput, subjectFilter, statusFilter].forEach(el => {
            el.addEventListener('input', () => {
                const activeTab = document.querySelector('.tab-btn.text-emerald-600').getAttribute('data-tab');
                renderTab(activeTab);
            });
        });

        function filterExams(tab) {
            let filtered = exams.filter(e => e.type === tab);
            const search = searchInput.value.toLowerCase();
            const subject = subjectFilter.value;
            const status = statusFilter.value;
            if (search) filtered = filtered.filter(e => e.title.toLowerCase().includes(search) || e.subject.toLowerCase().includes(search));
            if (subject) filtered = filtered.filter(e => e.subject === subject);
            if (status) filtered = filtered.filter(e => e.status === status);
            return filtered;
        }

        function renderTab(tab) {
            if (tab === 'upcoming') {
                const container = document.querySelector('#tab-upcoming .grid');
                container.innerHTML = '';
                filterExams('upcoming').forEach(e => {
                    container.innerHTML += `
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-8 flex flex-col gap-2 relative"> <!-- Increased padding -->
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-blue-700">${e.subject}</span>
                        <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-semibold">Upcoming</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">${e.title}</h2>
                    <div class="text-gray-600 text-sm mb-2">Date: ${e.date}</div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-clock text-blue-500"></i>
                        <span class="text-gray-700 text-sm">Starts in <span class="font-semibold">2 days</span></span>
                    </div>
                    <button class="mt-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200" onclick="openExamModal('${e.title}')">View Details</button>
                </div>`;
                });
            } else if (tab === 'ongoing') {
                const container = document.querySelector('#tab-ongoing .grid');
                container.innerHTML = '';
                filterExams('ongoing').forEach(e => {
                    container.innerHTML += `
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-8 flex flex-col gap-2 relative"> <!-- Increased padding -->
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-emerald-700">${e.subject}</span>
                        <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-semibold">Ongoing</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">${e.title}</h2>
                    <div class="text-gray-600 text-sm mb-2">Date: ${e.date}</div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-clock text-emerald-500"></i>
                        <span class="text-gray-700 text-sm">Time left: <span class="font-semibold">${e.timeLeft || '--:--:--'}</span></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: ${e.progress || 0}%"></div>
                    </div>
                    <button class="mt-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">Continue Exam</button>
                </div>`;
                });
            } else if (tab === 'past') {
                const tbody = document.querySelector('#tab-past table tbody');
                tbody.innerHTML = '';
                filterExams('past').forEach(e => {
                    tbody.innerHTML += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">${e.title}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${e.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-semibold">Completed</span></td>
                    <td class="px-6 py-4 whitespace-nowrap">${e.score || '-'}</td>
                </tr>`;
                });
            }
        }

        // Initial render
        renderTab('upcoming');

        // Modal logic
        function openExamModal(title) {
            const exam = exams.find(e => e.title === title);
            document.getElementById('modalExamTitle').textContent = exam.title;
            document.querySelector('#examModal .mb-4.text-gray-700').textContent = exam.details;
            document.getElementById('examModal').classList.remove('hidden');
        }

        function closeExamModal() {
            document.getElementById('examModal').classList.add('hidden');
        }
        window.openExamModal = openExamModal;
        window.closeExamModal = closeExamModal;
    </script>
</body>
</html>