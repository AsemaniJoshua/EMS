<?php
include_once __DIR__ . '/../../api/login/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';
$currentPage = 'students';

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch stats
$totalStudents = 0;
$activeStudents = 0;
$totalPrograms = 0;
$totalDepartments = 0;
$newStudentsThisMonth = 0;
$examPassRate = 0.0;

// Total students
$stmt = $conn->query("SELECT COUNT(*) as count FROM students");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $totalStudents = $row['count'];
}

// New students this month
$currentYear = date('Y');
$currentMonth = date('m');
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM students WHERE YEAR(created_at) = :year AND MONTH(created_at) = :month");
$stmt->execute(['year' => $currentYear, 'month' => $currentMonth]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $newStudentsThisMonth = $row['count'];
}

// Active students
$stmt = $conn->query("SELECT COUNT(*) as count FROM students WHERE status = 'active'");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $activeStudents = $row['count'];
}

// Programs
$stmt = $conn->query("SELECT COUNT(*) as count FROM programs");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $totalPrograms = $row['count'];
}

// Departments
$stmt = $conn->query("SELECT COUNT(*) as count FROM departments");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $totalDepartments = $row['count'];
}

// Exam pass rate (average score_percentage from results table)
$stmt = $conn->query("SELECT AVG(score_percentage) as avg_score FROM results");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $examPassRate = $row['avg_score'] ? round($row['avg_score'], 1) : 0.0;
}

// Fetch programs, departments, levels for filters
$programs = [];
$stmt = $conn->query("SELECT name FROM programs ORDER BY name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $programs[] = $row['name'];
}
$departments = [];
$stmt = $conn->query("SELECT name FROM departments ORDER BY name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $departments[] = $row['name'];
}
$levels = [];
$stmt = $conn->query("SELECT name FROM levels ORDER BY name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $levels[] = $row['name'];
}

// Pagination setup
$recordsPerPage = 500;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Count total records for pagination
$countStmt = $conn->query("SELECT COUNT(*) FROM students s 
    JOIN programs p ON s.program_id = p.program_id
    JOIN departments d ON s.department_id = d.department_id
    JOIN levels l ON s.level_id = l.level_id");
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// Fetch students with pagination (include department and level)
$students = [];
$stmt = $conn->prepare(
    "SELECT s.student_id, s.index_number, s.email, s.phone_number, s.username, s.first_name, s.last_name, s.status, 
            p.name as program_name, d.name as department_name, l.name as level_name
     FROM students s
     JOIN programs p ON s.program_id = p.program_id
     JOIN departments d ON s.department_id = d.department_id
     JOIN levels l ON s.level_id = l.level_id
     ORDER BY s.first_name, s.last_name
     LIMIT :offset, :recordsPerPage"
);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':recordsPerPage', (int)$recordsPerPage, PDO::PARAM_INT);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $students[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - EMS Admin</title>
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
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($totalStudents); ?></div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium">
                                                <?php echo $newStudentsThisMonth > 0 ? '+' : '';
                                                echo $newStudentsThisMonth; ?>
                                            </span>
                                            <span class="ml-1 text-gray-500">new this month</span>
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
                        <?php
                        $activeRate = $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 1) : 0;
                        ?>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                                <i class="fas fa-user-check text-emerald-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Students</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($activeStudents); ?></div>
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
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($totalPrograms); ?></div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-purple-600 font-medium">Across</span>
                                            <span class="ml-1 text-gray-500"><?php echo number_format($totalDepartments); ?> departments</span>
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
                                        <div class="text-xl font-semibold text-gray-900"><?php echo $examPassRate; ?>%</div>
                                        <div class="mt-1 flex items-baseline text-sm">
                                            <span class="text-emerald-600 font-medium"></span>
                                            <span class="ml-1 text-gray-500">average</span>
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
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                        <div class="relative">
                            <input type="text" id="searchName" placeholder="Search students..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <select id="filterProgram" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Programs</option>
                            <?php foreach ($programs as $prog): ?>
                                <option value="<?php echo htmlspecialchars($prog); ?>"><?php echo htmlspecialchars($prog); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filterDepartment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filterLevel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Levels</option>
                            <?php foreach ($levels as $level): ?>
                                <option value="<?php echo htmlspecialchars($level); ?>"><?php echo htmlspecialchars($level); ?></option>
                            <?php endforeach; ?>
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
                <div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTable" class="bg-white divide-y divide-gray-200">
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['first_name'] . ' ' . $student['last_name']); ?>&background=60a5fa&color=fff" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></div>
                                                <div class="text-sm text-gray-500">ID: <?php echo htmlspecialchars($student['index_number']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 truncate w-48">
                                        <div><?php echo htmlspecialchars($student['email']); ?></div>
                                        <div class="text-gray-500"><?php echo htmlspecialchars($student['phone_number']); ?></div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-900 truncate w-32">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo htmlspecialchars($student['program_name']); ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-900 truncate w-32">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <?php echo htmlspecialchars($student['department_name']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <?php echo htmlspecialchars($student['level_name']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                <?php
                                if ($student['status'] === 'active') echo 'bg-emerald-100 text-emerald-800';
                                elseif ($student['status'] === 'graduated') echo 'bg-purple-100 text-purple-800';
                                else echo 'bg-gray-100 text-gray-600';
                ?>">
                                            <span class="w-1.5 h-1.5
                <?php
                                if ($student['status'] === 'active') echo 'bg-emerald-400';
                                elseif ($student['status'] === 'graduated') echo 'bg-purple-400';
                                else echo 'bg-gray-400';
                ?> rounded-full mr-1.5"></span>
                                            <?php echo ucfirst($student['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="view.php?id=<?php echo $student['student_id']; ?>" class="text-emerald-600 hover:text-emerald-900 transition-colors" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $student['student_id']; ?>" class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deleteStudent(<?php echo $student['student_id']; ?>, '<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>')" class="text-red-600 hover:text-red-900 transition-colors border-0 bg-transparent p-0" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            <?php
                            $start = min($offset + 1, $totalRecords);
                            $end = min($offset + $recordsPerPage, $totalRecords);
                            ?>
                            Showing <span class="font-medium"><?php echo $start; ?></span> to <span class="font-medium"><?php echo $end; ?></span> of <span class="font-medium"><?php echo $totalRecords; ?></span> students
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

    <script>
        // Toast notifications system
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

        function showNotification(message, type = 'info') {
            Toast.fire({
                icon: type,
                title: message
            });
        }

        function filterStudents() {
            const searchName = document.getElementById('searchName').value.toLowerCase();
            const filterProgram = document.getElementById('filterProgram').value;
            const filterDepartment = document.getElementById('filterDepartment').value;
            const filterLevel = document.getElementById('filterLevel').value;
            const filterStatus = document.getElementById('filterStatus').value;

            const table = document.getElementById('studentsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const nameCell = row.cells[0];
                const programCell = row.cells[2];
                const departmentCell = row.cells[3];
                const levelCell = row.cells[4];
                const statusCell = row.cells[5];

                if (nameCell && programCell && departmentCell && levelCell && statusCell) {
                    const name = nameCell.textContent.toLowerCase();
                    const program = programCell.textContent.trim();
                    const department = departmentCell.textContent.trim();
                    const level = levelCell.textContent.trim();
                    const status = statusCell.textContent.toLowerCase();

                    const nameMatch = searchName === '' || name.includes(searchName);
                    const programMatch = filterProgram === '' || program === filterProgram;
                    const departmentMatch = filterDepartment === '' || department === filterDepartment;
                    const levelMatch = filterLevel === '' || level === filterLevel;
                    const statusMatch = filterStatus === '' || status.includes(filterStatus.toLowerCase());

                    if (nameMatch && programMatch && departmentMatch && levelMatch && statusMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        }

        function exportStudents() {
            showNotification('Exporting students data...', 'info');
            const table = document.getElementById('studentsTable');
            if (!table) return;

            let csv = 'Name,Email,Phone,Program,Department,Level,Status\n';

            for (let i = 0; i < table.rows.length; i++) {
                const row = table.rows[i];
                if (row.style.display !== 'none') {
                    const name = row.cells[0].textContent.trim().split('ID:')[0].trim();
                    const emailElem = row.cells[1].querySelector('.text-gray-900');
                    const phoneElem = row.cells[1].querySelector('.text-gray-500');
                    const email = emailElem ? emailElem.textContent.trim() : '';
                    const phone = phoneElem ? phoneElem.textContent.trim() : '';
                    const program = row.cells[2].textContent.trim();
                    const department = row.cells[3].textContent.trim();
                    const level = row.cells[4].textContent.trim();
                    const status = row.cells[5].textContent.trim();

                    csv += `"${name}","${email}","${phone}","${program}","${department}","${level}","${status}"\n`;
                }
            }

            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'students_export.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showNotification('Export complete!', 'success');
        }

        function deleteStudent(studentId, studentName) {
            Swal.fire({
                title: 'Delete Student',
                html: `Are you sure you want to delete <strong>${studentName}</strong>?<br><br>This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Processing deletion...'
                    });
                    // TODO: Implement AJAX delete endpoint for students
                    setTimeout(() => {
                        Toast.fire({
                            icon: 'success',
                            title: 'Student deleted successfully!'
                        });
                        // Optionally remove row from table
                        // location.reload();
                    }, 1000);
                }
            });
        }

        // Real-time search
        document.getElementById('searchName').addEventListener('input', function() {
            if (this.value.length > 2 || this.value.length === 0) {
                filterStudents();
            }
        });

        // Auto-filter on dropdown change
        document.getElementById('filterProgram').addEventListener('change', filterStudents);
        document.getElementById('filterDepartment').addEventListener('change', filterStudents);
        document.getElementById('filterLevel').addEventListener('change', filterStudents);
        document.getElementById('filterStatus').addEventListener('change', filterStudents);
    </script>
</body>

</html>