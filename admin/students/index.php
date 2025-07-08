<?php
    include_once __DIR__ . '/../../api/login/sessionCheck.php';
    include_once __DIR__ . '/../components/adminSidebar.php'; 
include_once __DIR__ . '/../components/adminHeader.php';
$currentPage = 'students';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

            <!-- Page Header -->
            <div class="mb-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Students Management</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage student profiles and examination access</p>
                </div>
                <div class="mt-4 md:mt-0 flex gap-3">
                    <button onclick="exportStudents()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-download mr-2 -ml-1"></i>
                        Export
                    </button>
                   
                    <a href="./add.php">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <i class="fas fa-plus mr-2 -ml-1"></i>
                            Add Student
                        </button>
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <!-- Total Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">2,847</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+89</span>
                                            <span class="ml-1 text-gray-500">from last month</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-user-check text-emerald-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Students</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">2,695</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">94.7%</span>
                                            <span class="ml-1 text-gray-500">active rate</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Programs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Programs</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">16</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-purple-600 font-medium">Across</span>
                                            <span class="ml-1 text-gray-500">4 departments</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exam Pass Rate -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-50 rounded-lg p-3">
                                <i class="fas fa-chart-bar text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Exam Pass Rate</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">87.2%</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+3.5%</span>
                                            <span class="ml-1 text-gray-500">since last term</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="relative">
                            <input type="text" id="searchName" placeholder="Search students..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <select id="filterProgram" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Programs</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Business">Business</option>
                            <option value="Arts">Arts</option>
                        </select>
                        <select id="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="graduated">Graduated</option>
                        </select>
                        <button onclick="filterStudents()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-medium">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">All Students</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Sample student data -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Jacob Wilson</div>
                                            <div class="text-sm text-gray-500">ID: STD10045</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">jacob.wilson@example.com</div>
                                    <div class="text-sm text-gray-500">+1 (555) 234-5678</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Computer Science
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1.5"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="viewStudent(1)" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editStudent(1)" class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteStudent(1)" class="text-red-600 hover:text-red-900 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Add more sample rows here -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Emma Thompson</div>
                                            <div class="text-sm text-gray-500">ID: STD10046</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">emma.thompson@example.com</div>
                                    <div class="text-sm text-gray-500">+1 (555) 345-6789</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Engineering
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1.5"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="viewStudent(2)" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editStudent(2)" class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteStudent(2)" class="text-red-600 hover:text-red-900 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">2,847</span> students
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </button>
                            <button class="px-3 py-1 text-sm text-white bg-emerald-600 border border-emerald-600 rounded-md">
                                1
                            </button>
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                2
                            </button>
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                3
                            </button>
                            <button class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Filter functionality
        function filterStudents() {
            const searchName = document.getElementById('searchName').value.toLowerCase();
            const filterProgram = document.getElementById('filterProgram').value;
            const filterStatus = document.getElementById('filterStatus').value;

            const table = document.getElementById('studentsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const nameCell = row.cells[0];
                const programCell = row.cells[2];
                const statusCell = row.cells[3];

                if (nameCell && programCell && statusCell) {
                    const name = nameCell.textContent.toLowerCase();
                    const program = programCell.textContent.trim();
                    const status = statusCell.textContent.toLowerCase();

                    const nameMatch = searchName === '' || name.includes(searchName);
                    const programMatch = filterProgram === '' || program === filterProgram;
                    const statusMatch = filterStatus === '' || status.includes(filterStatus.toLowerCase());

                    if (nameMatch && programMatch && statusMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }

            showNotification('Students filtered successfully!', 'success');
        }

        // Student actions
        function viewStudent(id) {
            window.location.href = `view.php?id=${id}`;
        }

        function editStudent(id) {
            window.location.href = `edit.php?id=${id}`;
        }

        function deleteStudent(id) {
            if (confirm('Are you sure you want to delete this student?')) {
                showNotification('Student deleted successfully!', 'success');
                // Add delete functionality here
            }
        }

        function exportStudents() {
            showNotification('Exporting students data...', 'info');
            // Add export functionality here
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-emerald-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                warning: 'bg-orange-500'
            };

            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${colors[type] || colors.info} transform transition-all duration-300 ease-in-out`;
            toast.textContent = message;

            document.body.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        // Real-time search
        document.getElementById('searchName').addEventListener('input', function() {
            if (this.value.length > 2 || this.value.length === 0) {
                filterStudents();
            }
        });

        // Auto-filter on dropdown change
        document.getElementById('filterProgram').addEventListener('change', filterStudents);
        document.getElementById('filterStatus').addEventListener('change', filterStudents);
    </script>
</body>

</html>
