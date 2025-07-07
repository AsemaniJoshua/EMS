<?php
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
$currentPage = 'teachers';
$pageTitle = "Teacher Details";

// In a real implementation, you would fetch the teacher data from the database
// This is just mock data for the UI
$teacherId = isset($_GET['id']) ? intval($_GET['id']) : 1;
$teacher = [
    'id' => $teacherId,
    'firstName' => 'John',
    'lastName' => 'Smith',
    'email' => 'john.smith@school.edu',
    'phoneNumber' => '+1 (555) 123-4567',
    'staffId' => 'TCH001',
    'department' => 'Mathematics',
    'status' => 'active',
    'joinDate' => '2021-09-01',
    'lastLogin' => '2023-12-05 14:23:45',
    'title' => 'Dr.',
    'qualifications' => [
        'PhD in Mathematics, Stanford University',
        'MSc in Applied Mathematics, MIT',
        'BSc in Mathematics, University of Michigan'
    ],
    'courses' => [
        ['code' => 'MATH101', 'name' => 'Introduction to Calculus'],
        ['code' => 'MATH203', 'name' => 'Linear Algebra'],
        ['code' => 'MATH305', 'name' => 'Differential Equations']
    ],
    'exams' => [
        ['id' => 1, 'title' => 'Midterm Exam - Calculus', 'date' => '2023-10-15', 'status' => 'Completed'],
        ['id' => 2, 'title' => 'Final Exam - Linear Algebra', 'date' => '2023-11-20', 'status' => 'Completed'],
        ['id' => 3, 'title' => 'Quiz 1 - Differential Equations', 'date' => '2023-12-10', 'status' => 'Upcoming']
    ],
    'profileImage' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>

    <!-- Main content -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

            <!-- Page Header with Navigation -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <button onclick="window.location.href='index.php'" class="mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Teacher Profile</h1>
                        <p class="mt-1 text-sm text-gray-500">Viewing details for <?php echo $teacher['title'] . ' ' . $teacher['firstName'] . ' ' . $teacher['lastName']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex justify-end space-x-3">
                <button onclick="window.location.href='edit.php?id=<?php echo $teacher['id']; ?>'" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
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
                            <img src="<?php echo $teacher['profileImage']; ?>" alt="<?php echo $teacher['firstName'] . ' ' . $teacher['lastName']; ?>" class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-md">
                            
                            <h2 class="mt-4 text-xl font-semibold text-gray-900"><?php echo $teacher['title'] . ' ' . $teacher['firstName'] . ' ' . $teacher['lastName']; ?></h2>
                            
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo $teacher['department']; ?>
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
                                        <p class="text-sm font-medium"><?php echo $teacher['staffId']; ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-calendar-alt text-gray-500 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Joined</p>
                                        <p class="text-sm font-medium"><?php echo date('M d, Y', strtotime($teacher['joinDate'])); ?></p>
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
                                        <p class="text-sm font-medium"><?php echo $teacher['email']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Phone</p>
                                        <p class="text-sm font-medium"><?php echo $teacher['phoneNumber']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-building text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Department</p>
                                        <p class="text-sm font-medium"><?php echo $teacher['department']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Last Login</p>
                                        <p class="text-sm font-medium"><?php echo date('M d, Y H:i', strtotime($teacher['lastLogin'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Qualifications -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>
                                Qualifications
                            </h3>
                            <ul class="list-disc pl-6 space-y-2">
                                <?php foreach ($teacher['qualifications'] as $qualification): ?>
                                <li class="text-sm text-gray-700"><?php echo $qualification; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <!-- Stats -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                                Statistics
                            </h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-blue-700"><?php echo count($teacher['courses']); ?></p>
                                    <p class="text-xs text-blue-700 mt-1">Courses</p>
                                </div>
                                <div class="bg-emerald-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-emerald-700"><?php echo count($teacher['exams']); ?></p>
                                    <p class="text-xs text-emerald-700 mt-1">Exams</p>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-purple-700">96%</p>
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
                        <button id="tab-activity" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            Recent Activity
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
                        <button class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            View All Courses
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($teacher['courses'] as $index => $course): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $course['code']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo $course['name']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo rand(15, 40); ?> students</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-emerald-600 hover:text-emerald-900 transition-colors mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
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
                        <button class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            Create New Exam
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($teacher['exams'] as $exam): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $exam['title']; ?></div>
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
                                            Upcoming
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-emerald-600 hover:text-emerald-900 transition-colors mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div id="content-activity" class="hidden tab-content bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-emerald-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-clipboard-check text-white"></i>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">Graded Final Exam - Linear Algebra</div>
                                                    <p class="mt-0.5 text-sm text-gray-500">December 1, 2023 at 2:30 PM</p>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-700">
                                                    <p>Completed grading for 32 students with an average score of 87%.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-plus text-white"></i>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">Created Quiz - Differential Equations</div>
                                                    <p class="mt-0.5 text-sm text-gray-500">November 25, 2023 at 10:15 AM</p>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-700">
                                                    <p>Added 15 questions to the upcoming quiz scheduled for December 10.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="relative">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-file-alt text-white"></i>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">Updated course materials for Linear Algebra</div>
                                                    <p class="mt-0.5 text-sm text-gray-500">November 20, 2023 at 9:45 AM</p>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-700">
                                                    <p>Added new lecture notes and practice problems for the upcoming exam.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

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
    </script>
</body>

</html>
