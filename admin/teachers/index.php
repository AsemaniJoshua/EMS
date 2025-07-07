<?php
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
$currentPage = 'teachers';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers - EMS Admin</title>
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
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Teachers Management</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage teaching staff and their departments</p>
                </div>
                <div class="mt-4 md:mt-0 flex gap-3">
                    <button onclick="exportTeachers()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-download mr-2 -ml-1"></i>
                        Export
                    </button>
                   
                    <a href="./add.php">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <i class="fas fa-plus mr-2 -ml-1"></i>
                            Add Teacher
                        </button>
                    </a>

                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <!-- Total Teachers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-chalkboard-teacher text-emerald-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Teachers</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">124</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">+5</span>
                                            <span class="ml-1 text-gray-500">new this month</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Teachers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-user-check text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Teachers</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">118</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">95.2%</span>
                                            <span class="ml-1 text-gray-500">active rate</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Departments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-building text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Departments</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">12</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-purple-600 font-medium">All</span>
                                            <span class="ml-1 text-gray-500">covered</span>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Reviews -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-50 rounded-lg p-3">
                                <i class="fas fa-clock text-orange-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Reviews</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900">6</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-orange-600 font-medium">Requires</span>
                                            <span class="ml-1 text-gray-500">attention</span>
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
                            <input type="text" id="searchName" placeholder="Search teachers..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <select id="filterDepartment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Departments</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Science">Science</option>
                            <option value="English">English</option>
                            <option value="History">History</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Physical Education">Physical Education</option>
                        </select>
                        <select id="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <button onclick="filterTeachers()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-medium">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Teachers Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">All Teachers</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="teachersTable" class="bg-white divide-y divide-gray-200">
                            <!-- Sample teacher data -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Dr. John Smith</div>
                                            <div class="text-sm text-gray-500">Staff ID: TCH001</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">john.smith@school.edu</div>
                                    <div class="text-sm text-gray-500">+1 (555) 123-4567</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Mathematics
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
                                        <button onclick="viewTeacher(1)" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editTeacher(1)" class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteTeacher(1)" class="text-red-600 hover:text-red-900 transition-colors">
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
                                            <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1494790108755-2616c6f5e241?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Prof. Sarah Johnson</div>
                                            <div class="text-sm text-gray-500">Staff ID: TCH002</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">sarah.johnson@school.edu</div>
                                    <div class="text-sm text-gray-500">+1 (555) 234-5678</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Science
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
                                        <button onclick="viewTeacher(2)" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editTeacher(2)" class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteTeacher(2)" class="text-red-600 hover:text-red-900 transition-colors">
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
                            Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">124</span> teachers
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
        function filterTeachers() {
            const searchName = document.getElementById('searchName').value.toLowerCase();
            const filterDepartment = document.getElementById('filterDepartment').value;
            const filterStatus = document.getElementById('filterStatus').value;

            const table = document.getElementById('teachersTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const nameCell = row.cells[0];
                const departmentCell = row.cells[2];
                const statusCell = row.cells[3];

                if (nameCell && departmentCell && statusCell) {
                    const name = nameCell.textContent.toLowerCase();
                    const department = departmentCell.textContent.trim();
                    const status = statusCell.textContent.toLowerCase();

                    const nameMatch = searchName === '' || name.includes(searchName);
                    const departmentMatch = filterDepartment === '' || department === filterDepartment;
                    const statusMatch = filterStatus === '' || status.includes(filterStatus.toLowerCase());

                    if (nameMatch && departmentMatch && statusMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }

            showNotification('Teachers filtered successfully!', 'success');
        }

        // Teacher actions
        function viewTeacher(id) {
            showNotification('Opening teacher details...', 'info');
            // Add view functionality here
        }

        function editTeacher(id) {
            showNotification('Opening edit form...', 'info');
            // Add edit functionality here
        }

        function deleteTeacher(id) {
            if (confirm('Are you sure you want to delete this teacher?')) {
                showNotification('Teacher deleted successfully!', 'success');
                // Add delete functionality here
            }
        }

        function openAddTeacherModal() {
            showNotification('Opening add teacher form...', 'info');
            // Add modal functionality here
        }

        function exportTeachers() {
            showNotification('Exporting teachers data...', 'info');
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
                filterTeachers();
            }
        });

        // Auto-filter on dropdown change
        document.getElementById('filterDepartment').addEventListener('change', filterTeachers);
        document.getElementById('filterStatus').addEventListener('change', filterTeachers);
    </script>
</body>

</html>