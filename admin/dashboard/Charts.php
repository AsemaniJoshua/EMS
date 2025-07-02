<?php
// Charts component for Admin Dashboard
?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Exam Performance Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold mb-4">Exam Performance Trends</h3>
        <canvas id="performanceChart" width="400" height="200"></canvas>
    </div>
    
    <!-- Student Enrollment Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold mb-4">Student Enrollment by Month</h3>
        <canvas id="enrollmentChart" width="400" height="200"></canvas>
    </div>
    
    <!-- Subject Distribution Chart -->
    <!-- <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold mb-4">Subject Distribution</h3>
        <canvas id="subjectChart" width="400" height="200"></canvas>
    </div> -->
    
    <!-- Teacher Activity Chart -->
    <!-- <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold mb-4">Teacher Activity</h3>
        <canvas id="teacherChart" width="400" height="200"></canvas>
    </div> -->
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Average Score (%)',
                data: [75, 78, 82, 85, 87, 89],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Enrollment Chart
    const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
    new Chart(enrollmentCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'New Students',
                data: [45, 52, 38, 67, 89, 95],
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Subject Distribution Chart
    const subjectCtx = document.getElementById('subjectChart').getContext('2d');
    new Chart(subjectCtx, {
        type: 'doughnut',
        data: {
            labels: ['Mathematics', 'Science', 'English', 'History', 'Computer Science'],
            datasets: [{
                data: [30, 25, 20, 15, 10],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Teacher Activity Chart
    const teacherCtx = document.getElementById('teacherChart').getContext('2d');
    new Chart(teacherCtx, {
        type: 'radar',
        data: {
            labels: ['Exams Created', 'Students Taught', 'Grading Speed', 'Student Feedback', 'Innovation'],
            datasets: [{
                label: 'Teacher Performance',
                data: [85, 90, 75, 88, 82],
                backgroundColor: 'rgba(168, 85, 247, 0.2)',
                borderColor: 'rgb(168, 85, 247)',
                borderWidth: 2,
                pointBackgroundColor: 'rgb(168, 85, 247)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(168, 85, 247)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});
</script> 