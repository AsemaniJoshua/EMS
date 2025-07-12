<?php
include_once __DIR__ . '/../../api/login/admin/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';
$currentPage = 'teachers';

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch stats
$totalTeachers = 0;
$activeTeachers = 0;
$totalDepartments = 0;
$pendingReviews = 0;
$newTeachersThisMonth = 0;

$stmt = $conn->query("SELECT COUNT(*) as count FROM teachers");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $totalTeachers = $row['count'];
}

// Calculate new teachers added this month
$currentYear = date('Y');
$currentMonth = date('m');
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM teachers WHERE YEAR(created_at) = :year AND MONTH(created_at) = :month");
$stmt->execute(['year' => $currentYear, 'month' => $currentMonth]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $newTeachersThisMonth = $row['count'];
}
$stmt = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE status = 'active'");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $activeTeachers = $row['count'];
}
$stmt = $conn->query("SELECT COUNT(*) as count FROM departments");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $totalDepartments = $row['count'];
}
// Pending reviews: count of teachers with status 'inactive' (example logic)
$stmt = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE status = 'inactive'");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pendingReviews = $row['count'];
}

// Fetch departments for filter
$departments = [];
$stmt = $conn->query("SELECT name FROM departments ORDER BY name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $departments[] = $row['name'];
}

// Pagination setup
$recordsPerPage = 100;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Count total records for pagination
$countStmt = $conn->query("SELECT COUNT(*) FROM teachers t JOIN departments d ON t.department_id = d.department_id");
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// Fetch teachers with pagination
$teachers = [];
$stmt = $conn->prepare("SELECT t.teacher_id, t.staff_id, t.email, t.phone_number, t.username, t.first_name, t.last_name, t.status, d.name as department_name FROM teachers t JOIN departments d ON t.department_id = d.department_id ORDER BY t.first_name, t.last_name LIMIT :offset, :recordsPerPage");
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':recordsPerPage', (int)$recordsPerPage, PDO::PARAM_INT);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $teachers[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

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
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($totalTeachers); ?></div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">
                                                <?php echo $newTeachersThisMonth > 0 ? '+' : '';
                                                echo $newTeachersThisMonth; ?>
                                            </span>
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
                        <?php
                        $activeRate = $totalTeachers > 0 ? round(($activeTeachers / $totalTeachers) * 100, 1) : 0;
                        ?>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-user-check text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Teachers</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($activeTeachers); ?></div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium"><?php echo $activeRate; ?>%</span>
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
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($totalDepartments); ?></div>
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
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($pendingReviews); ?></div>
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
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                            <?php endforeach; ?>
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
                            <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=<?php echo urlencode($teacher['first_name'] . ' ' . $teacher['last_name']); ?>&background=4ade80&color=fff" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900" data-teacher-name="<?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>"><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></div>
                                                <div class="text-sm text-gray-500">Staff ID: <?php echo htmlspecialchars($teacher['staff_id']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($teacher['email']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($teacher['phone_number']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo htmlspecialchars($teacher['department_name']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $teacher['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600'; ?>">
                                            <span class="w-1.5 h-1.5 <?php echo $teacher['status'] === 'active' ? 'bg-emerald-400' : 'bg-gray-400'; ?> rounded-full mr-1.5"></span>
                                            <?php echo ucfirst($teacher['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="view.php?id=<?php echo $teacher['teacher_id']; ?>" class="text-emerald-600 hover:text-emerald-900 transition-colors" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $teacher['teacher_id']; ?>" class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="confirmDelete(<?php echo $teacher['teacher_id']; ?>, '<?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>')" class="text-red-600 hover:text-red-900 transition-colors border-0 bg-transparent p-0" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            <?php
                            $start = min($offset + 1, $totalRecords);
                            $end = min($offset + $recordsPerPage, $totalRecords);
                            ?>
                            Showing <span class="font-medium"><?php echo $start; ?></span> to <span class="font-medium"><?php echo $end; ?></span> of <span class="font-medium"><?php echo $totalRecords; ?></span> teachers
                        </div>
                        <div class="flex items-center space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-1 text-sm text-gray-300 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                                    Previous
                                </span>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);

                            for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" class="px-3 py-1 text-sm border rounded-md <?php echo ($i == $page) ? 'text-white bg-emerald-600 border-emerald-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 text-sm text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    Next
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-1 text-sm text-gray-300 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                                    Next
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize SweetAlert Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        /**
         * Displays a notification toast using SweetAlert.
         * @param {string} message - The message to display in the notification.
         * @param {string} [type='info'] - The type of notification ('success', 'error', 'info', 'warning').
         */
        function showNotification(message, type = 'info') {
            Toast.fire({
                icon: type,
                title: message
            });
        }

        /**
         * Filters the teachers table rows based on the search input (teacher name),
         * selected department, and selected status.
         * Only rows matching all active filters will be displayed; others are hidden.
         * This function is triggered by the Filter button and also by real-time search and dropdown changes.
         */
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
        }

        /**
         * Exports the currently visible teachers in the table to a CSV file.
         * Only rows that are currently displayed (not filtered out) will be included.
         * The CSV will contain columns: Name, Email, Phone, Department, Status.
         * Triggers a download of the generated CSV file and shows notifications for export status.
         */
        function exportTeachers() {
            showNotification('Exporting teachers data...', 'info');

            // Create a CSV export of teachers
            const table = document.getElementById('teachersTable');
            if (!table) return;

            let csv = 'Name,Email,Phone,Department,Status\n';

            for (let i = 0; i < table.rows.length; i++) {
                const row = table.rows[i];
                if (row.style.display !== 'none') {
                    const name = row.cells[0].textContent.trim().split('Staff ID:')[0].trim();
                    // Robust extraction for email and phone
                    const emailElem = row.cells[1].querySelector('.text-gray-900');
                    const phoneElem = row.cells[1].querySelector('.text-gray-500');
                    const email = emailElem ? emailElem.textContent.trim() : '';
                    const phone = phoneElem ? phoneElem.textContent.trim() : '';
                    const department = row.cells[2].textContent.trim();
                    const status = row.cells[3].textContent.trim();

                    csv += `"${name}","${email}","${phone}","${department}","${status}"\n`;
                }
            }

            // Create and trigger download
            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'teachers_export.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showNotification('Export complete!', 'success');
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

        /**
         * Handles the deletion of a teacher via AJAX.
         * @param {number} teacherId - The ID of the teacher to delete.
         * @param {string} teacherName - The name of the teacher (for the confirmation message).
         */
        function confirmDelete(teacherId, teacherName) {
            Swal.fire({
                title: 'Delete Teacher',
                html: `Are you sure you want to delete <strong>${teacherName}</strong>?<br><br>This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show processing state
                    Toast.fire({
                        icon: 'info',
                        title: 'Processing deletion...'
                    });

                    // Send delete request
                    axios.post('/api/teachers/deleteTeacher.php', {
                            teacherId: teacherId
                        })
                        .then(function(response) {
                            if (response.data.status === 'success') {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.data.message
                                });

                                // Remove the row from the table
                                const rows = document.getElementById('teachersTable').getElementsByTagName('tr');
                                for (let i = 0; i < rows.length; i++) {
                                    const deleteBtn = rows[i].querySelector(`button[onclick*="${teacherId}"]`);
                                    if (deleteBtn) {
                                        // Fade out and remove the row
                                        rows[i].style.transition = 'opacity 0.5s';
                                        rows[i].style.opacity = '0';
                                        setTimeout(() => {
                                            rows[i].remove();

                                            // Update the "Showing X to Y of Z teachers" text
                                            updatePaginationCounters();
                                        }, 500);
                                        break;
                                    }
                                }
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: response.data.message
                                });
                            }
                        })
                        .catch(function(error) {
                            Toast.fire({
                                icon: 'error',
                                title: 'Server error. Please try again.'
                            });
                            console.error(error);
                        });
                }
            });
        }

        // Add this helper function to update the pagination counters after deletion
        function updatePaginationCounters() {
            const paginationText = document.querySelector('.text-sm.text-gray-700');
            if (paginationText) {
                const countText = paginationText.textContent;
                const matches = countText.match(/Showing (\d+) to (\d+) of (\d+) teachers/);

                if (matches && matches.length === 4) {
                    const start = parseInt(matches[1]);
                    const end = parseInt(matches[2]);
                    const total = parseInt(matches[3]) - 1;

                    const newEnd = Math.min(end, total);
                    paginationText.textContent = `Showing ${start} to ${newEnd} of ${total} teachers`;
                }
            }
        }

        // Real-time search
        const searchNameInput = document.getElementById('searchName');
        if (searchNameInput) {
            searchNameInput.addEventListener('input', function() {
                if (this.value.length > 2 || this.value.length === 0) {
                    filterTeachers();
                }
            });
        }

        // Auto-filter on dropdown change
        const filterDepartmentElem = document.getElementById('filterDepartment');
        if (filterDepartmentElem) {
            filterDepartmentElem.addEventListener('change', filterTeachers);
        }

        const filterStatusElem = document.getElementById('filterStatus');
        if (filterStatusElem) {
            filterStatusElem.addEventListener('change', filterTeachers);
        }
    </script>
</body>

</html>