<?php
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';
$currentPage = 'teachers';
$pageTitle = "Teacher Details";

// Fetch teacher data from DB
$teacherId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$teacher = null;
$courses = [];
$exams = [];
$qualifications = [];

if ($teacherId > 0) {
    $db = new Database();
    $conn = $db->getConnection();
    // Fetch teacher main info
    $stmt = $conn->prepare("SELECT t.*, d.name as department FROM teachers t JOIN departments d ON t.department_id = d.department_id WHERE t.teacher_id = ?");
    $stmt->execute([$teacherId]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($teacher) {
        // Fetch qualifications (if you have a table, otherwise use a field or skip)
        // Example: $qualifications = ...
        // Fetch courses
        $stmt = $conn->prepare("SELECT c.code, c.title FROM courses c WHERE c.department_id = ?");
        $stmt->execute([$teacher['department_id']]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Fetch exams
        $stmt = $conn->prepare("SELECT exam_id, title, start_datetime as date, status FROM exams WHERE teacher_id = ?");
        $stmt->execute([$teacherId]);
        $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

            <?php if (!$teacher): ?>
                <div class="bg-white p-8 rounded-xl shadow text-center">
                    <h2 class="text-2xl font-bold mb-2 text-red-600">Teacher Not Found</h2>
                    <p class="mb-4">The teacher you are looking for does not exist.</p>
                    <a href="index.php" class="inline-block px-6 py-2 bg-emerald-600 text-white rounded-lg font-medium">Back to List</a>
                </div>
            <?php else: ?>
            <!-- Page Header with Navigation -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <button onclick="window.location.href='index.php'" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Teacher Profile</h1>
                        <p class="mt-1 text-sm text-gray-500">Viewing details for <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex justify-end space-x-3">
                <button onclick="window.location.href='edit.php?id=<?php echo $teacherId; ?>'" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Profile
                </button>
                <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                    <i class="fas fa-envelope mr-2"></i>
                    Message
                </button>
            </div>

            <!-- Profile Overview Card -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-6">
                <div class="md:flex">
                    <!-- Left Column - Profile Image & Basic Info -->
                    <div class="md:w-1/3 bg-gray-50 p-6 border-r border-gray-100">
                        <div class="flex flex-col items-center text-center">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($teacher['first_name'] . ' ' . $teacher['last_name']); ?>&background=4ade80&color=fff" alt="<?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>" class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-md">
                            
                            <h2 class="mt-4 text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></h2>
                            
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo htmlspecialchars($teacher['department']); ?>
                                </span>
                            </div>
                            
                            <div class="mt-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $teacher['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <span class="w-2 h-2 <?php echo $teacher['status'] === 'active' ? 'bg-emerald-500' : 'bg-gray-500'; ?> rounded-full mr-2"></span>
                                    <?php echo ucfirst($teacher['status']); ?>
                                </span>
                            </div>
                            
                            <div class="mt-6 grid grid-cols-1 gap-4 w-full max-w-xs">
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-id-badge text-gray-500 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Staff ID</p>
                                        <p class="text-sm font-medium"><?php echo htmlspecialchars($teacher['staff_id']); ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-calendar-alt text-gray-500 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Joined</p>
                                        <p class="text-sm font-medium"><?php echo date('M d, Y', strtotime($teacher['created_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Detailed Information -->
                    <div class="md:w-2/3 p-6">
                        <!-- Contact Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-address-card text-emerald-600 mr-2"></i>
                                Contact Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm font-medium"><?php echo htmlspecialchars($teacher['email']); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Phone</p>
                                        <p class="text-sm font-medium"><?php echo htmlspecialchars($teacher['phone_number']); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-building text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Department</p>
                                        <p class="text-sm font-medium"><?php echo htmlspecialchars($teacher['department']); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Last Login</p>
                                        <p class="text-sm font-medium"><?php echo !empty($teacher['updated_at']) ? date('M d, Y H:i', strtotime($teacher['updated_at'])) : 'N/A'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                                Statistics
                            </h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-blue-700"><?php echo count($courses); ?></p>
                                    <p class="text-xs text-blue-700 mt-1">Courses</p>
                                </div>
                                <div class="bg-emerald-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-emerald-700"><?php echo count($exams); ?></p>
                                    <p class="text-xs text-emerald-700 mt-1">Exams</p>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-purple-700">-</p>
                                    <p class="text-xs text-purple-700 mt-1">Pass Rate</p>
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
                            Courses
                        </button>
                        <button id="tab-exams" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            Exams
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Content -->
            <div id="tab-content">
                <!-- Courses Tab -->
                <div id="content-courses" class="tab-content bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Assigned Courses</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
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
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Exams Tab -->
                <div id="content-exams" class="hidden tab-content bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Examinations</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($exams as $exam): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($exam['title']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo date('M d, Y', strtotime($exam['date'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($exam['status'] === 'Completed'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            Completed
                                        </span>
                                        <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo htmlspecialchars($exam['status']); ?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
                    // Remove active state from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('border-emerald-500', 'text-emerald-600');
                        t.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    });
                    
                    // Add active state to clicked tab
                    tab.classList.add('border-emerald-500', 'text-emerald-600');
                    tab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show the corresponding content
                    const contentId = tab.id.replace('tab-', 'content-');
                    document.getElementById(contentId).classList.remove('hidden');
                });
            });
        });
    </script>
</body>

</html>
