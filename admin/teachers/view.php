<?php
include_once __DIR__ . '/../../api/login/admin/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';
$currentPage = 'teachers';
$pageTitle = "Teacher Details";

// Helper function for status color classes
function getStatusColorClass($status, $type = 'bg')
{
    $status = strtolower($status);
    if ($type === 'bg') {
        if ($status === 'active' || $status === 'approved') return 'bg-emerald-500';
        if ($status === 'pending') return 'bg-yellow-100';
        return 'bg-gray-400';
    } elseif ($type === 'badge') {
        if ($status === 'active' || $status === 'approved') return 'bg-emerald-100 text-emerald-800';
        if ($status === 'pending') return 'bg-yellow-100 text-yellow-800';
        return 'bg-gray-100 text-gray-600';
    } elseif ($type === 'icon') {
        if ($status === 'approved') return 'text-emerald-500';
        if ($status === 'pending') return 'text-yellow-500';
        return 'text-gray-500';
    }
    return '';
}

// Fetch teacher data from DB
$teacherId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$teacher = null;
$courses = [];
$exams = [];

if ($teacherId > 0) {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch teacher main info and department
    $stmt = $conn->prepare(
        "SELECT t.*, d.name AS department 
         FROM teachers t 
         JOIN departments d ON t.department_id = d.department_id 
         WHERE t.teacher_id = ?"
    );
    $stmt->execute([$teacherId]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher) {
        // Fetch courses assigned to this teacher
        $stmt = $conn->prepare(
            "SELECT c.code, c.title 
             FROM courses c 
             JOIN teacher_courses tc ON c.course_id = tc.course_id
             WHERE tc.teacher_id = ?"
        );
        $stmt->execute([$teacherId]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch exams created by this teacher
        $stmt = $conn->prepare(
            "SELECT exam_id, title, start_datetime, status 
             FROM exams 
             WHERE teacher_id = ?"
        );
        $stmt->execute([$teacherId]);
        $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
            <?php if (!$teacher): ?>
                <div class="bg-white p-8 rounded-xl shadow-sm text-center">
                    <div class="mb-4 flex justify-center">
                        <div class="rounded-full bg-red-100 p-4">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold mb-2 text-gray-900">Teacher Not Found</h2>
                    <p class="mb-6 text-gray-500">The teacher you are looking for does not exist.</p>
                    <a href="index.php" class="inline-block px-5 py-2.5 bg-emerald-600 text-white rounded-lg font-medium transition-colors hover:bg-emerald-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Teachers
                    </a>
                </div>
            <?php else: ?>
                <!-- Page header with actions -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center">
                        <a href="index.php" class="mr-4 p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Teacher Profile</h1>
                            <p class="text-gray-500 text-sm">Details for <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="edit.php?id=<?php echo $teacherId; ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                            <i class="fas fa-pen mr-2"></i>Edit
                        </a>
                        <button onclick="confirmDelete(<?php echo $teacherId; ?>)" class="inline-flex items-center px-4 py-2 bg-white border border-red-300 rounded-lg text-sm font-medium text-red-700 shadow-sm hover:bg-red-50 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </div>
                </div>

                <!-- Profile overview -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Profile Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row gap-8 items-start">
                            <div class="flex-shrink-0 flex flex-col items-center">
                                <div class="relative mb-3">
                                    <img class="h-24 w-24 rounded-full object-cover border-2 border-emerald-100"
                                        src="https://ui-avatars.com/api/?name=<?php echo urlencode($teacher['first_name'] . ' ' . $teacher['last_name']); ?>&background=4ade80&color=fff&size=128"
                                        alt="<?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>">
                                    <div class="absolute bottom-0 right-0 h-5 w-5 rounded-full border-2 border-white <?php echo getStatusColorClass($teacher['status'], 'bg'); ?>"></div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo getStatusColorClass($teacher['status'], 'badge'); ?>">
                                    <?php echo ucfirst($teacher['status']); ?>
                                </span>
                            </div>

                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Full Name</label>
                                    <div class="text-gray-900"><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Department</label>
                                    <div class="text-gray-900"><?php echo htmlspecialchars($teacher['department']); ?></div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                                    <div class="text-gray-900"><?php echo htmlspecialchars($teacher['email']); ?></div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Phone Number</label>
                                    <div class="text-gray-900"><?php echo htmlspecialchars($teacher['phone_number']); ?></div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Staff ID</label>
                                    <div class="text-gray-900"><?php echo htmlspecialchars($teacher['staff_id']); ?></div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Username</label>
                                    <div class="text-gray-900"><?php echo htmlspecialchars($teacher['username']); ?></div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Created Date</label>
                                    <div class="text-gray-900"><?php echo date('M d, Y', strtotime($teacher['created_at'])); ?></div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
                                    <div class="text-gray-900"><?php echo date('M d, Y', strtotime($teacher['updated_at'])); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs for Courses and Exams -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="border-b border-gray-100 px-6">
                        <nav class="flex -mb-px" aria-label="Tabs">
                            <button data-tab="courses" class="tab-button active w-1/2 py-4 px-4 mx-2 text-center border-b-2 border-emerald-500 font-medium text-sm text-emerald-600">
                                <i class="fas fa-book-open mr-2"></i>Courses
                            </button>
                            <button data-tab="exams" class="tab-button w-1/2 py-4 px-4 mx-2 text-center border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-file-alt mr-2"></i>Exams
                            </button>
                        </nav>
                    </div>

                    <div id="courses" class="tab-content p-6">
                        <?php if (empty($courses)): ?>
                            <div class="text-center py-8">
                                <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-book text-gray-400"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">No Courses Assigned</h3>
                                <p class="text-sm text-gray-500">This teacher doesn't have any courses assigned yet.</p>
                            </div>
                        <?php else: ?>
                            <ul class="divide-y divide-gray-100">
                                <?php foreach ($courses as $course): ?>
                                    <li class="py-3 flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                            <i class="fas fa-book text-blue-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($course['title']); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($course['code']); ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <div id="exams" class="tab-content p-6 hidden">
                        <?php if (empty($exams)): ?>
                            <div class="text-center py-8">
                                <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-file-alt text-gray-400"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">No Exams Created</h3>
                                <p class="text-sm text-gray-500">This teacher hasn't created any exams yet.</p>
                            </div>
                        <?php else: ?>
                            <ul class="divide-y divide-gray-100">
                                <?php foreach ($exams as $exam): ?>
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-full <?php echo getStatusColorClass($exam['status'], 'bg'); ?> flex items-center justify-center mr-4">
                                                <i class="fas fa-file-alt <?php echo getStatusColorClass($exam['status'], 'icon'); ?>"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 mb-1"><?php echo htmlspecialchars($exam['title']); ?></p>
                                                <p class="text-xs text-gray-500">
                                                    <?php echo date('M d, Y', strtotime($exam['start_datetime'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo getStatusColorClass($exam['status'], 'badge'); ?>">
                                            <?php echo htmlspecialchars(ucfirst($exam['status'])); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <script>
                            // Initialize Toast for notifications using SweetAlert2.
                            // This creates a reusable toast instance for showing quick messages at the top-end of the screen.
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

                            // Tab switching logic for Courses and Exams tabs.
                            // Adds click event listeners to tab buttons to switch visible content and update active styles.
                            document.querySelectorAll('.tab-button').forEach(button => {
                                button.addEventListener('click', function() {
                                    // Remove active class and highlight from all tab buttons
                                    document.querySelectorAll('.tab-button').forEach(btn => {
                                        btn.classList.remove('active', 'border-emerald-500', 'text-emerald-600');
                                        btn.classList.add('border-transparent', 'text-gray-500');
                                    });

                                    // Add active class and highlight to the clicked tab button
                                    this.classList.add('active', 'border-emerald-500', 'text-emerald-600');
                                    this.classList.remove('border-transparent', 'text-gray-500');

                                    // Hide all tab content sections
                                    document.querySelectorAll('.tab-content').forEach(content => {
                                        content.classList.add('hidden');
                                    });

                                    // Show the tab content corresponding to the clicked button
                                    const tabContent = document.getElementById(this.getAttribute('data-tab'));
                                    if (tabContent) {
                                        tabContent.classList.remove('hidden');
                                    }
                                });
                            });

                            // Delete confirmation dialog for deleting a teacher.
                            const confirmDelete = function(teacherId) {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: "You won't be able to revert this!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#ef4444',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Yes, delete it!'
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

                                                    // Redirect to teacher list after successful deletion
                                                    setTimeout(() => {
                                                        window.location.href = 'index.php';
                                                    }, 1000);
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
                        </script>
                    <?php endif; ?>
                    </div>
    </main>
</body>

</html>