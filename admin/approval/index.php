<?php
include_once __DIR__ . '/../../api/login/sessionCheck.php';
include_once __DIR__ . '/../components/adminSidebar.php';
include_once __DIR__ . '/../components/adminHeader.php';
require_once __DIR__ . '/../../api/config/database.php';

$currentPage = 'approval';
$pageTitle = "Exam Approvals";
$breadcrumb = "Approvals";

// Initialize database connection
$db = new Database();
$conn = $db->getConnection();

// Get pending approvals count
$pendingCount = 0;
$stmt = $conn->query("SELECT COUNT(*) as count FROM exams WHERE status = 'Pending'");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pendingCount = $row['count'];
}

// Get approvals today
$approvedToday = 0;
$stmt = $conn->query("SELECT COUNT(*) as count FROM exams WHERE status = 'Approved' AND DATE(approved_at) = CURDATE()");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $approvedToday = $row['count'];
}

// Get yesterday's approvals for comparison
$approvedYesterday = 0;
$stmt = $conn->query("SELECT COUNT(*) as count FROM exams WHERE status = 'Approved' AND DATE(approved_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $approvedYesterday = $row['count'];
}
$approvedChange = $approvedToday - $approvedYesterday;

// Get rejections today
$rejectedToday = 0;
$stmt = $conn->query("SELECT COUNT(*) as count FROM exams WHERE status = 'Rejected' AND DATE(approved_at) = CURDATE()");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rejectedToday = $row['count'];
}

// Get yesterday's rejections for comparison
$rejectedYesterday = 0;
$stmt = $conn->query("SELECT COUNT(*) as count FROM exams WHERE status = 'Rejected' AND DATE(approved_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rejectedYesterday = $row['count'];
}
$rejectedChange = $rejectedToday - $rejectedYesterday;

// Calculate average response time (in hours)
$avgResponseTime = 0;
$stmt = $conn->query("
    SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours
    FROM exams
    WHERE status IN ('Approved', 'Rejected')
    AND approved_at IS NOT NULL
    AND DATE(approved_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $avgResponseTime = $row['avg_hours'] ? round($row['avg_hours'], 1) : 0;
}

// Calculate change in response time
$prevAvgResponseTime = 0;
$stmt = $conn->query("
    SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours
    FROM exams
    WHERE status IN ('Approved', 'Rejected')
    AND approved_at IS NOT NULL
    AND DATE(approved_at) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
    AND DATE(approved_at) < DATE_SUB(CURDATE(), INTERVAL 7 DAY)
");
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $prevAvgResponseTime = $row['avg_hours'] ? round($row['avg_hours'], 1) : 0;
}
$responseTimeDiff = $prevAvgResponseTime - $avgResponseTime;

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
                        <p class="mt-1 text-sm text-gray-500">Review and manage exam submissions from teachers</p>
                    </div>
                    <div>
                        <button onclick="window.location.href='../dashboard/index.php'" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <i class="fas fa-arrow-left mr-2 -ml-1"></i>
                            Back to Dashboard
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-50 rounded-lg p-3">
                                <i class="fas fa-hourglass-half text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Approvals</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($pendingCount); ?></div>
                                        <div class="text-sm text-gray-500">Awaiting review</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-50 rounded-lg p-3">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Approved Today</dt>
                                    <dd>
                                        <div class="text-xl font-semibold text-gray-900"><?php echo number_format($approvedToday); ?></div>
                                        <div class="text-sm text-gray-500">
                                            <?php if ($approvedChange > 0): ?>
                                                <span class="text-emerald-600">+<?php echo $approvedChange; ?></span>
                                            <?php elseif ($approvedChange < 0): ?>
                                                <span class="text-red-600"><?php echo $approvedChange; ?></span>
                                            <?php else: ?>
                                                <span>No change</span>
                                            <?php endif; ?> 
                                            from yesterday
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-50 rounded-lg p-3">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Rejected Today</dt>
                                    <dd>
                                        <div id="rejectedCount" class="text-xl font-semibold text-gray-900">0</div>
                                        <div id="rejectedChange" class="text-sm text-gray-500">0 from yesterday</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-50 rounded-lg p-3">
                                <i class="fas fa-clock text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg. Response Time</dt>
                                    <dd>
                                        <div id="responseTime" class="text-xl font-semibold text-gray-900">N/A</div>
                                        <div id="responseTimeDiff" class="text-sm text-gray-500">-</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Approval Management</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <p class="text-gray-600">Review and approve pending exam submissions from teachers</p>
                        <div class="flex gap-3">
                            <button id="bulkApproveBtn" onclick="bulkApprove()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center">
                                <i class="fas fa-check-double mr-2"></i>
                                Bulk Approve
                            </button>
                            <button id="exportApprovalsBtn" onclick="exportReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center">
                                <i class="fas fa-file-export mr-2"></i>
                                Export Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Filter Approvals</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <select id="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="all">All Status</option>
                            <option value="Pending" selected>Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Draft">Draft</option>
                        </select>
                        <div></div> <!-- Empty column for spacing -->
                        <div></div> <!-- Empty column for spacing -->
                        <button onclick="filterApprovals()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-filter mr-2"></i>
                            Apply Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Approvals Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Exam Approvals</h3>
                    <span id="approvalCount" class="text-sm text-gray-500">Loading exams...</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course / Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="approvalTable" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center">
                                        <i class="fas fa-spinner fa-spin mr-2 text-emerald-500"></i>
                                        <span class="text-gray-500">Loading exam approvals...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script src="approval.js"></script>
</body>

</html>