<?php
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
$currentPage = 'students';
$pageTitle = "Student Details";

// In a real implementation, you would fetch the student data from the database
// This is just mock data for the UI
$studentId = isset($_GET['id']) ? intval($_GET['id']) : 1;
$student = [
    'id' => $studentId,
    'firstName' => 'Jacob',
    'lastName' => 'Wilson',
    'email' => 'jacob.wilson@example.com',
    'phoneNumber' => '+1 (555) 234-5678',
    'studentId' => 'STD10045',
    'department' => 'Science and Technology',
    'program' => 'Computer Science',
    'status' => 'active',
    'currentSemester' => '3rd Semester',
    'enrollmentDate' => '2022-09-01',
    'dateOfBirth' => '2000-05-15',
    'gender' => 'Male',
    'address' => '123 College St, University Town, UT 12345',
    'lastLogin' => '2023-12-03 10:15:30',
    'emergencyContact' => [
        'name' => 'Robert Wilson',
        'relationship' => 'Father',
        'phone' => '+1 (555) 987-6543',
        'email' => 'robert.wilson@example.com'
    ],
    'courses' => [
        ['code' => 'CS101', 'name' => 'Introduction to Programming'],
        ['code' => 'CS205', 'name' => 'Data Structures and Algorithms'],
        ['code' => 'MATH201', 'name' => 'Calculus II']
    ],
    'exams' => [
        ['id' => 1, 'title' => 'Midterm Exam - Programming', 'date' => '2023-10-10', 'status' => 'Completed', 'score' => '85%'],
        ['id' => 2, 'title' => 'Final Exam - Data Structures', 'date' => '2023-11-25', 'status' => 'Completed', 'score' => '92%'],
        ['id' => 3, 'title' => 'Quiz - Calculus II', 'date' => '2023-12-12', 'status' => 'Upcoming']
    ],
    'profileImage' => 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80'
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
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Student Profile</h1>
                        <p class="mt-1 text-sm text-gray-500">Viewing details for <?php echo $student['firstName'] . ' ' . $student['lastName']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex justify-end space-x-3">
                <button onclick="window.location.href='edit.php?id=<?php echo $student['id']; ?>'" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
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
                            <img src="<?php echo $student['profileImage']; ?>" alt="<?php echo $student['firstName'] . ' ' . $student['lastName']; ?>" class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-md">
                            
                            <h2 class="mt-4 text-xl font-semibold text-gray-900"><?php echo $student['firstName'] . ' ' . $student['lastName']; ?></h2>
                            
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo $student['program']; ?>
                                </span>
                            </div>
                            
                            <div class="mt-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $student['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <span class="w-2 h-2 <?php echo $student['status'] === 'active' ? 'bg-emerald-500' : 'bg-gray-500'; ?> rounded-full mr-2"></span>
                                    <?php echo ucfirst($student['status']); ?>
                                </span>
                            </div>
                            
                            <div class="mt-6 grid grid-cols-1 gap-4 w-full max-w-xs">
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-id-badge text-gray-500 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Student ID</p>
                                        <p class="text-sm font-medium"><?php echo $student['studentId']; ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-calendar-alt text-gray-500 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Enrolled</p>
                                        <p class="text-sm font-medium"><?php echo date('M d, Y', strtotime($student['enrollmentDate'])); ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <i class="fas fa-graduation-cap text-gray-500 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Current Semester</p>
                                        <p class="text-sm font-medium"><?php echo $student['currentSemester']; ?></p>
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
                                        <p class="text-sm font-medium"><?php echo $student['email']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Phone</p>
                                        <p class="text-sm font-medium"><?php echo $student['phoneNumber']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-birthday-cake text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Date of Birth</p>
                                        <p class="text-sm font-medium"><?php echo date('M d, Y', strtotime($student['dateOfBirth'])); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-venus-mars text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Gender</p>
                                        <p class="text-sm font-medium"><?php echo $student['gender']; ?></p>
                                    </div>
                                </div>
                                <div class="md:col-span-2 flex items-start">
                                    <i class="fas fa-map-marker-alt text-gray-500 mr-3 w-5 text-center mt-0.5"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Address</p>
                                        <p class="text-sm font-medium"><?php echo $student['address']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Last Login</p>
                                        <p class="text-sm font-medium"><?php echo date('M d, Y H:i', strtotime($student['lastLogin'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Emergency Contact -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-phone-alt text-blue-600 mr-2"></i>
                                Emergency Contact
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center">
                                    <i class="fas fa-user text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Name</p>
                                        <p class="text-sm font-medium"><?php echo $student['emergencyContact']['name']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-user-friends text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Relationship</p>
                                        <p class="text-sm font-medium"><?php echo $student['emergencyContact']['relationship']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Phone</p>
                                        <p class="text-sm font-medium"><?php echo $student['emergencyContact']['phone']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-500 mr-3 w-5 text-center"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm font-medium"><?php echo $student['emergencyContact']['email']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                                Academic Statistics
                            </h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-blue-700"><?php echo count($student['courses']); ?></p>
                                    <p class="text-xs text-blue-700 mt-1">Courses</p>
                                </div>
                                <div class="bg-emerald-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-emerald-700"><?php echo count($student['exams']); ?></p>
                                    <p class="text-xs text-emerald-700 mt-1">Exams</p>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold text-purple-700">88.5%</p>
                                    <p class="text-xs text-purple-700 mt-1">Avg. Score</p>
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
                        <h3 class="text-lg font-semibold text-gray-900">Current Courses</h3>
                        <button class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            View All Courses
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($student['courses'] as $index => $course): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $course['code']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo $course['name']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo $index === 0 ? 'Dr. John Smith' : ($index === 1 ? 'Prof. Sarah Johnson' : 'Dr. Michael Brown'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-emerald-600 hover:text-emerald-900 transition-colors mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-file-alt"></i>
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
                        <h3 class="text-lg font-semibold text-gray-900">Exams & Results</h3>
                        <button class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            View All Exams
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($student['exams'] as $exam): ?>
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo isset($exam['score']) ? $exam['score'] : '-'; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-emerald-600 hover:text-emerald-900 transition-colors mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($exam['status'] === 'Completed'): ?>
                                        <button class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-file-download"></i>
                                        </button>
                                        <?php endif; ?>
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
                                                    <div class="text-sm font-medium text-gray-900">Completed Final Exam - Data Structures</div>
                                                    <p class="mt-0.5 text-sm text-gray-500">November 25, 2023 at 2:30 PM</p>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-700">
                                                    <p>Scored 92% on the exam. Top score in the class.</p>
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
                                                    <i class="fas fa-book text-white"></i>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">Enrolled in course - Calculus II</div>
                                                    <p class="mt-0.5 text-sm text-gray-500">November 15, 2023 at 10:15 AM</p>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-700">
                                                    <p>Successfully enrolled in MATH201 - Calculus II for the Winter semester.</p>
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
                                                    <div class="text-sm font-medium text-gray-900">Submitted assignment - Programming Project</div>
                                                    <p class="mt-0.5 text-sm text-gray-500">November 10, 2023 at 9:45 AM</p>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-700">
                                                    <p>Submitted the final project for Introduction to Programming course.</p>
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
