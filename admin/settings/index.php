<?php
$pageTitle = "System Settings";
$breadcrumb = "Settings";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EMS Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    
    <!-- Main content area -->
    <main class="pt-20 lg:ml-64 min-h-screen bg-gray-50 transition-all duration-300">
        <div class="p-4 lg:p-8 max-w-7xl mx-auto">
            <?php include '../components/PageHeader.php'; ?>
            
            <!-- Settings Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Active Categories</p>
                            <p class="text-3xl font-bold">12</p>
                            <p class="text-blue-100 text-sm">Exam categories</p>
                        </div>
                        <div class="text-blue-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Default Duration</p>
                            <p class="text-3xl font-bold">120</p>
                            <p class="text-green-100 text-sm">Minutes</p>
                        </div>
                        <div class="text-green-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-lg shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">System Status</p>
                            <p class="text-3xl font-bold">Online</p>
                            <p class="text-purple-100 text-sm">All systems operational</p>
                        </div>
                        <div class="text-purple-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Settings Forms -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Category Management -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Management</h3>
                    <form id="categoryForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Category</label>
                            <input type="text" name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter category name">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg transition-colors duration-200">Add Category</button>
                    </form>
                    
                    <div class="mt-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Existing Categories</h4>
                        <ul id="categoriesList" class="space-y-2">
                            <!-- Categories will be loaded here by JS -->
                        </ul>
                    </div>
                </div>
                
                <!-- Exam Duration Settings -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Duration Settings</h3>
                    <form id="durationForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Exam Duration (minutes)</label>
                            <input type="number" name="duration" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="120">
                        </div>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-lg transition-colors duration-200">Set Duration</button>
                    </form>
                    
                    <div class="mt-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">Current Settings</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Default duration: <span class="font-semibold text-gray-900">120 minutes</span></p>
                            <p class="text-sm text-gray-600">Last updated: <span class="font-semibold text-gray-900">Dec 12, 2024</span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- System Configuration -->
            <div class="bg-white p-6 rounded-lg shadow-lg mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Auto-finalize Results</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Enabled</option>
                            <option>Disabled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Result Notification</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Email</option>
                            <option>SMS</option>
                            <option>Both</option>
                            <option>None</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Backup Frequency</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Daily</option>
                            <option>Weekly</option>
                            <option>Monthly</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Mode</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Disabled</option>
                            <option>Enabled</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6">
                    <button class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-6 rounded-lg transition-colors duration-200">Save Configuration</button>
                </div>
            </div>
        </div>
    </main>
    <script src="settings.js"></script>
</body>
</html>
