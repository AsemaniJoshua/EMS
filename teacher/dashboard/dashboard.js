/**
 * Teacher Dashboard JavaScript
 * Provides chart rendering and interactive functionality for the teacher dashboard
 */

document.addEventListener('DOMContentLoaded', function () {
    // Toast notification setup
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Initialize sidebar toggle
    setupSidebar();

    // Initialize profile dropdown
    setupProfileDropdown();

    // Delete exam function with SweetAlert confirmation
    window.deleteExam = function (examId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Call API to delete exam
                axios.post('/api/exam/deleteExam.php', {
                    exam_id: examId
                })
                    .then(function (response) {
                        if (response.data.status === 'success') {
                            Toast.fire({
                                icon: 'success',
                                title: 'Exam deleted successfully!'
                            });

                            // Reload page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.data.message || 'Failed to delete exam'
                            });
                        }
                    })
                    .catch(function (error) {
                        console.error('Delete exam error:', error);
                        Toast.fire({
                            icon: 'error',
                            title: 'Network error. Please try again.'
                        });
                    });
            }
        });
    };

    // Initialize charts if canvas elements exist
    if (document.getElementById('examStatusChart')) {
        initExamStatusChart();
    }

    if (document.getElementById('resultsChart')) {
        initResultsChart();
    }
});

/**
 * Set up sidebar toggle functionality
 */
function setupSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');

    if (mobileSidebarToggle && sidebar) {
        mobileSidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('-translate-x-full');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('opacity-0');
                sidebarOverlay.classList.toggle('pointer-events-none');
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('opacity-0');
            sidebarOverlay.classList.add('pointer-events-none');
        });
    }
}

/**
 * Set up profile dropdown functionality
 */
function setupProfileDropdown() {
    const profileMenuButton = document.getElementById('profileMenuButton');
    const profileMenu = document.getElementById('profileMenu');

    if (profileMenuButton && profileMenu) {
        profileMenuButton.addEventListener('click', function () {
            profileMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (event) {
            if (!profileMenuButton.contains(event.target) && !profileMenu.contains(event.target)) {
                profileMenu.classList.add('hidden');
            }
        });
    }
}

/**
 * Show no data message when chart has no data
 * @param {HTMLElement} element - The chart element
 * @param {string} message - Message to display
 */
function showNoDataMessage(element, message) {
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = 'flex items-center justify-center h-40 text-gray-500';
    messageDiv.innerHTML = `
        <div class="text-center">
            <i class="fas fa-chart-bar text-3xl mb-2 opacity-30"></i>
            <p>${message}</p>
        </div>
    `;

    // Replace chart with message
    element.style.display = 'none';
    element.parentNode.appendChild(messageDiv);
}

/**
 * Initialize Exam Status pie chart
 */
function initExamStatusChart() {
    const ctx = document.getElementById('examStatusChart').getContext('2d');
    const chartCanvas = document.getElementById('examStatusChart');

    try {
        const chartData = JSON.parse(chartCanvas.getAttribute('data-chart') || '{"labels":[],"data":[]}');

        if (chartData.labels.length === 0) {
            showNoDataMessage(chartCanvas, 'No exam status data available');
            return;
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.labels,
                datasets: [{
                    data: chartData.data,
                    backgroundColor: [
                        '#10B981', // Approved - Emerald
                        '#F59E0B', // Pending - Amber 
                        '#EF4444', // Rejected - Red
                        '#6B7280', // Draft - Gray
                        '#3B82F6'  // Completed - Blue
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Exam Status Distribution'
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error initializing exam status chart:', error);
        showNoDataMessage(chartCanvas, 'Error loading chart data');
    }
}
              