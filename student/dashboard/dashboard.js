// Dashboard JS for Student - Updated for real backend integration

document.addEventListener('DOMContentLoaded', function () {
    setupEnrollmentForm();
});

function setupEnrollmentForm() {
    const enrollForm = document.getElementById('enrollForm');
    if (enrollForm) {
        enrollForm.addEventListener('submit', handleEnrollment);
    }
}

function handleEnrollment(e) {
    e.preventDefault();
    const key = document.getElementById('enrollKey').value.trim();
    const submitButton = e.target.querySelector('button[type="submit"]');
    
    if (!key) {
        showNotification('Please enter an enrollment key', 'error');
        return;
    }
    
    // Disable button and show loading
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    // Make API call to course enrollment endpoint
    fetch('/api/courseEnrollment/courseEnrollment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
                    },
        body: JSON.stringify({
            enrollment_key: key
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.data.type === 'exam') {
                showNotification(data.message, 'success');
                // Refresh the upcoming exams table
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else if (data.data.type === 'course') {
                showCourseExams(data.data);
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while processing your request', 'error');
    })
    .finally(() => {
        // Re-enable button
        submitButton.disabled = false;
        submitButton.innerHTML = 'Submit';
        // Clear input
        document.getElementById('enrollKey').value = '';
    });
}

// Function to show course exams modal
function showCourseExams(courseData) {
    const course = courseData.course;
    const exams = courseData.available_exams;
    
    let examsList = '';
    if (exams.length === 0) {
        examsList = '<p class="text-gray-500 text-center py-4">No exams available for this course.</p>';
    } else {
        examsList = '<div class="space-y-3">';
        exams.forEach(exam => {
            const statusColor = exam.status === 'Available' ? 'green' : 
                              exam.status === 'Active' ? 'blue' : 
                              exam.status === 'Already Registered' ? 'yellow' : 'gray';
            
            const startDate = new Date(exam.start_datetime);
            const endDate = new Date(exam.end_datetime);
            
            examsList += `
                <div class="border rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">${exam.title}</h4>
                            <p class="text-sm text-gray-600 mt-1">Code: ${exam.exam_code}</p>
                            <p class="text-sm text-gray-600">Start: ${startDate.toLocaleString()}</p>
                            <p class="text-sm text-gray-600">End: ${endDate.toLocaleString()}</p>
                        </div>
                        <div class="text-right ml-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-${statusColor}-100 text-${statusColor}-800">
                                ${exam.status}
                            </span>
                            ${exam.status === 'Available' ? 
                                `<button onclick="registerForExam(${exam.exam_id})" class="block mt-2 bg-emerald-600 text-white px-3 py-1 rounded text-sm hover:bg-emerald-700 transition-colors">
                                    Register
                                </button>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        examsList += '</div>';
    }
    
    // Show modal using SweetAlert2
    Swal.fire({
        title: `Course: ${course.title}`,
        html: `
            <div class="text-left">
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <p class="mb-2"><strong>Code:</strong> ${course.code}</p>
                    <p class="mb-2"><strong>Department:</strong> ${course.department_name}</p>
                    <p class="mb-2"><strong>Program:</strong> ${course.program_name}</p>
                </div>
                <h3 class="font-semibold mb-3 text-gray-900">Available Exams:</h3>
                ${examsList}
            </div>
        `,
        width: '600px',
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
            popup: 'text-left'
        }
    });
}

// Function to register for specific exam
function registerForExam(examId) {
    Swal.fire({
        title: 'Confirm Registration',
        text: 'Do you want to register for this exam?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Register',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Registering...',
                text: 'Please wait while we register you for the exam.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Make API call to register for specific exam
            fetch('/api/courseEnrollment/examsRegistration.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    exam_id: examId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        // Refresh the page to show updated data
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Registration Failed',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while registering for the exam',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            });
        }
    });
}

// Function to view exam details
function viewExamDetails(examId) {
    // You can implement this to show exam details or redirect to exam page
    window.location.href = `../exam/details.php?exam_id=${examId}`;
}

// Notification function
function showNotification(message, type) {
    const bgColor = type === 'success' ? 'bg-green-500' : 
                   type === 'error' ? 'bg-red-500' : 
                   type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
    
    const icon = type === 'success' ? 'check-circle' : 
                type === 'error' ? 'exclamation-circle' : 
                type === 'warning' ? 'exclamation-triangle' : 'info-circle';
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${icon} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Auto-refresh dashboard data every 5 minutes
setInterval(() => {
    // You can implement auto-refresh of specific sections here
    // For now, we'll just reload the page
    // location.reload();
}, 300000); // 5 minutes

