<?php
// Include session check and admin components
require_once '../../api/login/admin/sessionCheck.php';
require_once '../components/adminSidebar.php';
require_once '../components/adminHeader.php';

$pageTitle = "System Settings";
$currentPage = "settings";

// Database connection
require_once '../../api/config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Fetch system statistics
try {
    // Get departments count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM departments");
    $stmt->execute();
    $departments_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get programs count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM programs");
    $stmt->execute();
    $programs_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get courses count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM courses");
    $stmt->execute();
    $courses_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get levels count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM levels");
    $stmt->execute();
    $levels_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get semesters count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM semesters");
    $stmt->execute();
    $semesters_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Get active teachers count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM teachers WHERE status = 'active'");
    $stmt->execute();
    $active_teachers_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    $departments_count = 0;
    $programs_count = 0;
    $courses_count = 0;
    $levels_count = 0;
    $semesters_count = 0;
    $active_teachers_count = 0;
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <?php renderAdminSidebar($currentPage); ?>
    <?php renderAdminHeader(); ?>
    <!-- Main content area -->
    <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
        <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl"><?php echo $pageTitle; ?></h1>
                        <p class="mt-1 text-sm text-gray-500">Manage system configurations and academic structure</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="exportSystemData()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <i class="fas fa-download mr-2 -ml-1"></i>
                            Export Data
                        </button>
                        <button onclick="backupDatabase()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <i class="fas fa-database mr-2 -ml-1"></i>
                            Backup System
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Departments</p>
                            <p class="text-2xl font-bold"><?php echo $departments_count; ?></p>
                        </div>
                        <div class="text-blue-200">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Programs</p>
                            <p class="text-2xl font-bold"><?php echo $programs_count; ?></p>
                        </div>
                        <div class="text-green-200">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-lg shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Courses</p>
                            <p class="text-2xl font-bold"><?php echo $courses_count; ?></p>
                        </div>
                        <div class="text-purple-200">
                            <i class="fas fa-book text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 rounded-lg shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Levels</p>
                            <p class="text-2xl font-bold"><?php echo $levels_count; ?></p>
                        </div>
                        <div class="text-orange-200">
                            <i class="fas fa-layer-group text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-teal-500 to-teal-600 p-6 rounded-lg shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-teal-100 text-sm font-medium">Semesters</p>
                            <p class="text-2xl font-bold"><?php echo $semesters_count; ?></p>
                        </div>
                        <div class="text-teal-200">
                            <i class="fas fa-calendar-alt text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-6 rounded-lg shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-100 text-sm font-medium">Active Teachers</p>
                            <p class="text-2xl font-bold"><?php echo $active_teachers_count; ?></p>
                        </div>
                        <div class="text-indigo-200">
                            <i class="fas fa-chalkboard-teacher text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Management Tabs -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="showTab('departments')" id="tab-departments" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button active">
                            <i class="fas fa-building mr-2"></i>Departments
                        </button>
                        <button onclick="showTab('programs')" id="tab-programs" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                            <i class="fas fa-graduation-cap mr-2"></i>Programs
                        </button>
                        <button onclick="showTab('courses')" id="tab-courses" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                            <i class="fas fa-book mr-2"></i>Courses
                        </button>
                        <button onclick="showTab('levels')" id="tab-levels" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                            <i class="fas fa-layer-group mr-2"></i>Levels
                        </button>
                        <button onclick="showTab('semesters')" id="tab-semesters" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                            <i class="fas fa-calendar-alt mr-2"></i>Semesters
                        </button>
                        <button onclick="showTab('system')" id="tab-system" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm tab-button">
                            <i class="fas fa-cog mr-2"></i>System
                        </button>
                    </nav>
                </div>

                <!-- Departments Tab -->
                <div id="content-departments" class="tab-content active p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Department Management</h2>
                        <button onclick="showAddDepartmentModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                            <i class="fas fa-plus mr-2"></i>Add Department
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div id="departments-list" class="space-y-3">
                            <!-- Departments will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Programs Tab -->
                <div id="content-programs" class="tab-content hidden p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Program Management</h2>
                        <button onclick="showAddProgramModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                            <i class="fas fa-plus mr-2"></i>Add Program
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div id="programs-list" class="space-y-3">
                            <!-- Programs will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Courses Tab -->
                <div id="content-courses" class="tab-content hidden p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Course Management</h2>
                        <button onclick="showAddCourseModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                            <i class="fas fa-plus mr-2"></i>Add Course
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div id="courses-list" class="space-y-3">
                            <!-- Courses will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Levels Tab -->
                <div id="content-levels" class="tab-content hidden p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Level Management</h2>
                        <button onclick="showAddLevelModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                            <i class="fas fa-plus mr-2"></i>Add Level
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div id="levels-list" class="space-y-3">
                            <!-- Levels will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Semesters Tab -->
                <div id="content-semesters" class="tab-content hidden p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Semester Management</h2>
                        <button onclick="showAddSemesterModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                            <i class="fas fa-plus mr-2"></i>Add Semester
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div id="semesters-list" class="space-y-3">
                            <!-- Semesters will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- System Configuration Tab -->
                <div id="content-system" class="tab-content hidden p-6">
                    <div class="space-y-8">
                        <!-- System Configuration -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">General Settings</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">System Name</label>
                                        <input type="text" id="systemNameInput" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Exam Management System">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">System Description</label>
                                        <textarea id="systemDescInput" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="System description..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">System Timezone</label>
                                        <select id="systemTimezone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            <option value="UTC">UTC</option>
                                            <option value="Africa/Accra">Africa/Accra</option>
                                            <option value="America/New_York">America/New_York</option>
                                            <option value="Europe/London">Europe/London</option>
                                            <option value="Asia/Tokyo">Asia/Tokyo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Settings</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Session Timeout (seconds)</label>
                                        <input type="number" id="sessionTimeout" min="300" max="86400" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="3600">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Login Attempts</label>
                                        <input type="number" id="maxLoginAttempts" min="3" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="5">
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Maintenance Mode</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="maintenanceMode" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Notification Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Enable Notifications</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="enableNotifications" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Email Notifications</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="emailNotifications" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- System Actions -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Maintenance</h3>
                                <div class="space-y-3">
                                    <button onclick="performBackup()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                                        <i class="fas fa-download mr-2"></i>Create Database Backup
                                    </button>
                                    <button onclick="optimizeDatabase()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                                        <i class="fas fa-cogs mr-2"></i>Optimize Database
                                    </button>
                                    <button onclick="cleanupData()" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md transition-colors">
                                        <i class="fas fa-broom mr-2"></i>Cleanup System Data
                                    </button>
                                    <button onclick="exportSystemData()" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition-colors">
                                        <i class="fas fa-file-export mr-2"></i>Export System Data
                                    </button>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Logs</h3>
                                <div id="logsTypeStats" class="mb-4 text-sm text-gray-600">
                                    <!-- Log type statistics will be loaded here -->
                                </div>
                                <div class="space-y-3">
                                    <button onclick="loadSystemLogs(1, 25); document.getElementById('systemLogsSection').classList.remove('hidden');" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors">
                                        <i class="fas fa-list mr-2"></i>View System Logs
                                    </button>
                                    <button onclick="clearOldLogs()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors">
                                        <i class="fas fa-trash mr-2"></i>Clear Old Logs
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- System Logs Table -->
                        <div id="systemLogsSection" class="bg-white rounded-lg shadow hidden">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                                    <h3 class="text-lg font-semibold text-gray-900">System Logs</h3>
                                    <div class="flex space-x-3">
                                        <select id="logsTypeFilter" onchange="filterLogs()" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                            <option value="">All Types</option>
                                            <option value="info">Info</option>
                                            <option value="warning">Warning</option>
                                            <option value="error">Error</option>
                                            <option value="success">Success</option>
                                            <option value="system">System</option>
                                            <option value="export">Export</option>
                                        </select>
                                        <input type="text" id="logsSearchInput" placeholder="Search logs..." onkeyup="if(event.key==='Enter') filterLogs()" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                                        <button onclick="filterLogs()" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 text-sm">
                                            Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="logsTableBody" class="bg-white divide-y divide-gray-200">
                                        <!-- Logs will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-6 py-3 border-t border-gray-200 flex justify-center">
                                <div id="logsPagination" class="flex space-x-1">
                                    <!-- Pagination will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="flex justify-end">
                            <button onclick="saveSystemSettings()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-md transition-colors">
                                <i class="fas fa-save mr-2"></i>Save System Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals will be inserted here by JavaScript -->
    <script src="settings.js"></script>
</body>

</html>