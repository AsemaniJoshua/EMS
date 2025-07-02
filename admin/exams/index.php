<?php
$pageTitle = "Exam Management";
$breadcrumb = "Exams";
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
            
            <!-- Tabs for Pending and Past Exams -->
            <div class="mb-8">
                <div class="flex space-x-4 border-b mb-4">
                    <button id="pendingTab" class="px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-semibold focus:outline-none">Pending Exams</button>
                    <button id="pastTab" class="px-4 py-2 text-gray-600 hover:text-blue-600 border-b-2 border-transparent font-semibold focus:outline-none">Past Exams</button>
                </div>
            </div>
            
            <!-- Pending Exams Table -->
            <div id="pendingExamsSection">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Pending Exams</h3>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200" onclick="openCreateExamModal()">Create New Exam</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pendingExamsTable" class="bg-white divide-y divide-gray-200">
                                <!-- Pending exams will be populated here by JS -->
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Math Final 2024</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Mathematics</td>
                                    <td class="px-6 py-4 whitespace-nowrap">2024-06-20</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold">Pending</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button class="text-blue-600 hover:underline mr-3" onclick="editExam()">Edit</button>
                                        <button class="text-red-600 hover:underline" onclick="deleteExam()">Delete</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">Science Quiz</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Science</td>
                                    <td class="px-6 py-4 whitespace-nowrap">2024-06-25</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold">Pending</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button class="text-blue-600 hover:underline mr-3" onclick="editExam()">Edit</button>
                                        <button class="text-red-600 hover:underline" onclick="deleteExam()">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Past Exams Table -->
            <div id="pastExamsSection" class="hidden">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Past Exams</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="pastExamsTable" class="bg-white divide-y divide-gray-200">
                                <!-- Past exams will be populated here by JS -->
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">English Midterm</td>
                                    <td class="px-6 py-4 whitespace-nowrap">English</td>
                                    <td class="px-6 py-4 whitespace-nowrap">2024-05-10</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-semibold">Completed</span></td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">History Final</td>
                                    <td class="px-6 py-4 whitespace-nowrap">History</td>
                                    <td class="px-6 py-4 whitespace-nowrap">2024-04-15</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-semibold">Completed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Exam Modal (for create/edit) -->
    <div id="examModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg relative">
            <button onclick="closeExamModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h2 class="text-xl font-bold mb-4" id="examModalTitle">Create/Edit Exam</h2>
            <form id="examForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Exam Name</label>
                    <input type="text" name="examName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter exam name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <input type="text" name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter category">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeExamModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">Save Exam</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    // Tab switching
    const pendingTab = document.getElementById('pendingTab');
    const pastTab = document.getElementById('pastTab');
    const pendingSection = document.getElementById('pendingExamsSection');
    const pastSection = document.getElementById('pastExamsSection');
    
    pendingTab.addEventListener('click', function() {
        pendingTab.classList.add('text-blue-600', 'border-blue-600');
        pendingTab.classList.remove('text-gray-600', 'border-transparent');
        pastTab.classList.remove('text-blue-600', 'border-blue-600');
        pastTab.classList.add('text-gray-600', 'border-transparent');
        pendingSection.classList.remove('hidden');
        pastSection.classList.add('hidden');
    });
    pastTab.addEventListener('click', function() {
        pastTab.classList.add('text-blue-600', 'border-blue-600');
        pastTab.classList.remove('text-gray-600', 'border-transparent');
        pendingTab.classList.remove('text-blue-600', 'border-blue-600');
        pendingTab.classList.add('text-gray-600', 'border-transparent');
        pastSection.classList.remove('hidden');
        pendingSection.classList.add('hidden');
    });
    
    // Exam Modal logic
    function openCreateExamModal() {
        document.getElementById('examModalTitle').textContent = 'Create Exam';
        document.getElementById('examForm').reset();
        document.getElementById('examModal').classList.remove('hidden');
    }
    function editExam() {
        document.getElementById('examModalTitle').textContent = 'Edit Exam';
        document.getElementById('examModal').classList.remove('hidden');
        // Populate form fields with exam data (to be implemented)
    }
    function deleteExam() {
        if (confirm('Are you sure you want to delete this exam?')) {
            // Delete logic (to be implemented)
            alert('Exam deleted!');
        }
    }
    function closeExamModal() {
        document.getElementById('examModal').classList.add('hidden');
    }
    document.getElementById('examForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Save logic (to be implemented)
        closeExamModal();
        alert('Exam saved!');
    });
    </script>
</body>
</html> 