<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../src/output.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../components/Sidebar.php'; ?>
    <?php include '../components/Header.php'; ?>
    
    <!-- Main content area with proper spacing for fixed sidebar and header -->
    <main class="pt-20 lg:ml-64 min-h-screen bg-gray-50 transition-all duration-300">
        <div class="p-4 lg:p-8 max-w-7xl mx-auto">
            <h1 class="text-3xl f
            ont-bold mb-6 text-gray-900">Dashboard Overview</h1>
            
            <!-- Stats Cards -->
            <?php include 'StatsCards.php'; ?>
            
            <!-- Quick Actions -->
            
            <!-- Charts Section -->
            <?php include 'Charts.php'; ?>
            
            <!-- Content Grid -->
            <div class="grid grid-cols-1">
                <!-- Upcoming Exams -->
                <?php include 'UpcomingExams.php'; ?>
                
                <!-- Recent Activity -->
            </div>
        </div>
    </main>
    <script src="dashboard.js"></script>
</body>
</html>
