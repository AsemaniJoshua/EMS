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
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Delete exam function with SweetAlert confirmation
    window.deleteExam = function (examId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
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

    if (document.getElementById('studentProgressChart')) {
        initStudentProgressChart();
    }
});

/**
 * Initialize Exam Status pie chart
 */
function initExamStatusChart() {
    const ctx = document.getElementById('examStatusChart').getContext('2d');

    // Get chart data from the data attribute
    const chartCanvas = document.getElementById('examStatusChart');
    const chartData = JSON.parse(chartCanvas.getAttribute('data-chart'));

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.data,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',  // Blue
                    'rgba(255, 159, 64, 0.8)',  // Orange
                    'rgba(75, 192, 192, 0.8)',  // Green
                    'rgba(255, 99, 132, 0.8)',  // Red
                    'rgba(153, 102, 255, 0.8)'  // Purple
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Exam Status Distribution'
                }
            }
        }
    });
}

/**
 * Initialize Student Progress bar chart
 */
function initStudentProgressChart() {
    const ctx = document.getElementById('studentProgressChart').getContext('2d');

    // Get chart data from the data attribute
    const chartCanvas = document.getElementById('studentProgressChart');
    const chartData = JSON.parse(chartCanvas.getAttribute('data-chart'));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Average Score (%)',
                data: chartData.data,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Average Student Scores by Exam'
                }
            }
        }
    });
}
