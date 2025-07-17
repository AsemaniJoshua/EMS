<?php
include_once __DIR__ . '/../../api/login/admin/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';
$currentPage = 'students';
$pageTitle = "Student Details";

// Get student ID from query
$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$student = null;
$courses = [];
$exams = [];

if ($studentId > 0) {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch student main info, department, program, level
    $stmt = $conn->prepare(
        "SELECT s.*, 
                d.name AS department, 
                p.name AS program, 
                l.name AS level, 
                DATE_FORMAT(s.date_of_birth, '%Y-%m-%d') as dob,
                DATE_FORMAT(s.created_at, '%Y-%m-%d') as enrollment_date
         FROM students s
         JOIN departments d ON s.department_id = d.department_id
         JOIN programs p ON s.program_id = p.program_id
         JOIN levels l ON s.level_id = l.level_id
         WHERE s.student_id = ?"
    );
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        // Fetch enrolled courses (current semester if needed)
        $stmt = $conn->prepare(
            "SELECT c.code, c.title, d.name as department, t.first_name as teacher_first, t.last_name as teacher_last
             FROM courses c
             JOIN departments d ON c.department_id = d.department_id
             JOIN programs p ON c.program_id = p.program_id
             JOIN levels l ON c.level_id = l.level_id
             LEFT JOIN teacher_courses tc ON c.course_id = tc.course_id
             LEFT JOIN teachers t ON tc.teacher_id = t.teacher_id
             WHERE c.program_id = ? AND c.level_id = ?"
        );
        $stmt->execute([$student['program_id'], $student['level_id']]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch exams and results
        $stmt = $conn->prepare(
            "SELECT e.exam_id, e.title, e.start_datetime, e.status, r.score_percentage
             FROM exams e
             JOIN exam_registrations er ON e.exam_id = er.exam_id
             LEFT JOIN results r ON er.registration_id = r.registration_id
             WHERE er.student_id = ?
             ORDER BY e.start_datetime DESC"
        );
        $stmt->execute([$studentId]);
        $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

            <?php if (!$student): ?>
                <div class="bg-white p-8 rounded-xl shadow-sm text-center">
                    <div class="mb-4 flex justify-center">
                        <div class="rounded-full bg-red-100 p-4">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold mb-2 text-gray-900">Student Not Found</h2>
                    <p class="mb-6 text-gray-500">The student you are looking for does not exist.</p>
                    <a href="index.php" class="inline-block px-5 py-2.5 bg-emerald-600 text-white rounded-lg font-medium transition-colors hover:bg-emerald-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Students
                    </a>
                </div>
            <?php else: ?>

                <!-- Page Header with Navigation -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <button onclick="window.location.href='index.php'" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Student Profile</h1>
                            <p class="mt-1 text-sm text-gray-500">Viewing details for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mb-6 flex justify-end space-x-3">
                    <button onclick="window.location.href='edit.php?id=<?php echo $student['student_id']; ?>'" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profile
                    </button>
                </div>

                <!-- Profile Overview Card -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 overflow-hidden mb-6">
                    <div class="md:flex md:min-h-[300px]">
                        <div class="md:w-1/3 bg-gray-50 p-6 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['first_name'] . ' ' . $student['last_name']); ?>&background=60a5fa&color=fff"
                                alt="<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>"
                                class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-md">

                            <h2 class="mt-4 text-2xl font-bold text-gray-900 leading-tight text-center">
                                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                            </h2>

                            <div class="mt-3 flex flex-wrap justify-center gap-2 max-w-full">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 truncate max-w-[calc(50%-0.25rem)] md:max-w-full">
                                    <?php echo htmlspecialchars($student['program']); ?>
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 truncate max-w-[calc(50%-0.25rem)] md:max-w-full">
                                    <?php echo htmlspecialchars($student['department']); ?>
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 truncate max-w-[calc(50%-0.25rem)] md:max-w-full">
                                    <?php echo htmlspecialchars($student['level']); ?>
                                </span>
                            </div>

                            <div class="mt-4">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-base font-semibold 
                    <?php echo $student['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : ($student['status'] === 'graduated' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <span class="w-2.5 h-2.5 
                        <?php echo $student['status'] === 'active' ? 'bg-emerald-500' : ($student['status'] === 'graduated' ? 'bg-purple-500' : 'bg-gray-500'); ?> 
                        rounded-full mr-2"></span>
                                    <?php echo ucfirst($student['status']); ?>
                                </span>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-4 w-full px-4 sm:px-0 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-id-badge text-gray-500 text-xl mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Index Number</p>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['index_number']); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-calendar-alt text-gray-500 text-xl mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Enrolled</p>
                                        <p class="text-sm font-medium text-gray-900"><?php echo date('M d, Y', strtotime($student['enrollment_date'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="md:w-2/3 p-6 flex flex-col justify-between">
                            <div>
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <i class="fas fa-address-card text-emerald-600 text-xl mr-2"></i>
                                        Contact Information
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-gray-500 text-lg mr-3 w-5 text-center"></i>
                                            <div>
                                                <p class="text-xs text-gray-500">Email</p>
                                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['email']); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-phone text-gray-500 text-lg mr-3 w-5 text-center"></i>
                                            <div>
                                                <p class="text-xs text-gray-500">Phone</p>
                                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['phone_number']); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-birthday-cake text-gray-500 text-lg mr-3 w-5 text-center"></i>
                                            <div>
                                                <p class="text-xs text-gray-500">Date of Birth</p>
                                                <p class="text-sm font-medium text-gray-900"><?php echo $student['dob'] ? date('M d, Y', strtotime($student['dob'])) : '-'; ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-venus-mars text-gray-500 text-lg mr-3 w-5 text-center"></i>
                                            <div>
                                                <p class="text-xs text-gray-500">Gender</p>
                                                <p class="text-sm font-medium text-gray-900"><?php echo ucfirst($student['gender']); ?></p>
                                            </div>
                                        </div>
                                        <div class="sm:col-span-2 flex items-start">
                                            <i class="fas fa-map-marker-alt text-gray-500 text-lg mr-3 w-5 text-center mt-0.5"></i>
                                            <div>
                                                <p class="text-xs text-gray-500">Address</p>
                                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['address'] ?? '-'); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-gray-500 text-lg mr-3 w-5 text-center"></i>
                                            <div>
                                                <p class="text-xs text-gray-500">Last Login</p>
                                                <p class="text-sm font-medium text-gray-900"><?php echo isset($student['updated_at']) ? date('M d, Y H:i', strtotime($student['updated_at'])) : '-'; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <i class="fas fa-chart-bar text-purple-600 text-xl mr-2"></i>
                                        Academic Statistics
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="bg-blue-50 rounded-lg p-4 text-center shadow-sm border border-blue-100">
                                            <p class="text-3xl font-bold text-blue-700"><?php echo count($courses); ?></p>
                                            <p class="text-xs text-blue-700 mt-1 uppercase tracking-wide">Courses</p>
                                        </div>
                                        <div class="bg-emerald-50 rounded-lg p-4 text-center shadow-sm border border-emerald-100">
                                            <p class="text-3xl font-bold text-emerald-700"><?php echo count($exams); ?></p>
                                            <p class="text-xs text-emerald-700 mt-1 uppercase tracking-wide">Exams</p>
                                        </div>
                                        <div class="bg-purple-50 rounded-lg p-4 text-center shadow-sm border border-purple-100">
                                            <p class="text-3xl font-bold text-purple-700">
                                                <?php
                                                $avgScore = 0;
                                                $scoreCount = 0;
                                                foreach ($exams as $exam) {
                                                    if ($exam['score_percentage'] !== null) {
                                                        $avgScore += $exam['score_percentage'];
                                                        $scoreCount++;
                                                    }
                                                }
                                                echo $scoreCount ? round($avgScore / $scoreCount, 1) . '%' : '-';
                                                ?>
                                            </p>
                                            <p class="text-xs text-purple-700 mt-1 uppercase tracking-wide">Avg. Score</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button id="tab-courses" class="tab-button border-emerald-500 text-emerald-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                                Enrolled Courses
                            </button>
                            <button id="tab-exams" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                                Exams & Results
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div id="tab-content">
                    <!-- Courses Tab -->
                    <div id="content-courses" class="tab-content bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Current Courses</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($course['code']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($course['title']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <?php
                                                    if ($course['teacher_first'] && $course['teacher_last']) {
                                                        echo htmlspecialchars($course['teacher_first'] . ' ' . $course['teacher_last']);
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($course['department']); ?></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($courses)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No courses found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Exams Tab -->
                    <div id="content-exams" class="hidden tab-content bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Exams & Results</h3>
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
                                    <?php foreach ($exams as $exam): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($exam['title']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?php echo $exam['start_datetime'] ? date('M d, Y', strtotime($exam['start_datetime'])) : '-'; ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php
                                            if (strtolower($exam['status']) === 'completed') echo 'bg-emerald-100 text-emerald-800';
                                            elseif (strtolower($exam['status']) === 'pending') echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-gray-100 text-gray-600';
                                            ?>">
                                                    <?php echo htmlspecialchars($exam['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <?php echo $exam['score_percentage'] !== null ? $exam['score_percentage'] . '%' : '-'; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($exams)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No exams found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => {
                        t.classList.remove('border-emerald-500', 'text-emerald-600');
                        t.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    });
                    tab.classList.add('border-emerald-500', 'text-emerald-600');
                    tab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    const contentId = tab.id.replace('tab-', 'content-');
                    document.getElementById(contentId).classList.remove('hidden');
                });
            });
        });
    </script>
</body>

</html>