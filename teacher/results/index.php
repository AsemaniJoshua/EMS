<?php include_once '../components/Sidebar.php'; ?>
<?php include_once '../components/Header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - EMS Teacher</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Exam Results</h1>
                    <p class="mt-1 text-sm text-gray-500">View and analyze results for exams you conducted.</p>
                </div>
            </div>

            <!-- Stats Cards (about exams) -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-clipboard-list text-emerald-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Exams Conducted</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">8</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Students Examined</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">120</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-50 rounded-lg p-3">
                                <i class="fas fa-star text-yellow-500 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg. Exam Score</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">78%</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-hourglass-half text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Exams Pending Grading</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">2</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter/Search Bar -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div class="relative col-span-2">
                            <input type="text" placeholder="Search exam..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Your Exams</option>
                            <option value="Algebra Basics">Algebra Basics</option>
                            <option value="Physics Quiz">Physics Quiz</option>
                            <option value="Chemistry Final">Chemistry Final</option>
                        </select>
                        <button
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-medium">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Table: Only exams conducted by this teacher -->
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Students</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Avg. Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pass Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Algebra Basics</td>
                            <td class="px-6 py-4 whitespace-nowrap">2024-04-20</td>
                            <td class="px-6 py-4 whitespace-nowrap">40</td>
                            <td class="px-6 py-4 whitespace-nowrap">82%</td>
                            <td class="px-6 py-4 whitespace-nowrap">90%</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span
                                    class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Graded</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <a href="viewResult.php?id=1" class="text-blue-600 hover:underline" title="View"><i
                                        class="fas fa-eye"></i></a>
                                <a href="editResult.php?id=1" class="text-yellow-500 hover:underline" title="Edit"><i
                                        class="fas fa-edit"></i></a>
                                <button class="text-red-600 hover:underline delete-btn" data-exam="Algebra Basics"
                                    title="Delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Physics Quiz</td>
                            <td class="px-6 py-4 whitespace-nowrap">2024-04-25</td>
                            <td class="px-6 py-4 whitespace-nowrap">38</td>
                            <td class="px-6 py-4 whitespace-nowrap">76%</td>
                            <td class="px-6 py-4 whitespace-nowrap">80%</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span
                                    class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <a href="viewResult.php?id=2" class="text-blue-600 hover:underline" title="View"><i
                                        class="fas fa-eye"></i></a>
                                <a href="editResult.php?id=2" class="text-yellow-500 hover:underline" title="Edit"><i
                                        class="fas fa-edit"></i></a>
                                <button class="text-red-600 hover:underline delete-btn" data-exam="Physics Quiz"
                                    title="Delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Chemistry Final</td>
                            <td class="px-6 py-4 whitespace-nowrap">2024-05-02</td>
                            <td class="px-6 py-4 whitespace-nowrap">42</td>
                            <td class="px-6 py-4 whitespace-nowrap">70%</td>
                            <td class="px-6 py-4 whitespace-nowrap">75%</td>
                            <td class="px-6 py-4 whitespace-nowrap"><span
                                    class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Completed</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <a href="viewResult.php?id=3" class="text-blue-600 hover:underline" title="View"><i
                                        class="fas fa-eye"></i></a>
                                <a href="editResult.php?id=3" class="text-yellow-500 hover:underline" title="Edit"><i
                                        class="fas fa-edit"></i></a>
                                <button class="text-red-600 hover:underline delete-btn" data-exam="Chemistry Final"
                                    title="Delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <!-- More rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <!-- SweetAlert2 CDN and Delete Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const row = this.closest('tr');
                const exam = this.getAttribute('data-exam');
                Swal.fire({
                    title: `Delete result for '${exam}'?`,
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        Swal.fire('Deleted!', 'The result has been deleted.', 'success');
                    }
                });
            });
        });
    </script>
</body>

</html>