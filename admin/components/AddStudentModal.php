<?php
// Add Student Modal component
$modalId = 'addStudentModal';
$modalTitle = 'Add New Student';
$modalContent = '
<form id="addStudentForm" class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="fullName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter full name">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter email address">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
        <input type="text" name="studentId" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter student ID">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Grade/Class</label>
        <select name="grade" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Select Grade</option>
            <option value="9">9th Grade</option>
            <option value="10">10th Grade</option>
            <option value="11">11th Grade</option>
            <option value="12">12th Grade</option>
        </select>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
        <select name="section" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Select Section</option>
            <option value="A">Section A</option>
            <option value="B">Section B</option>
            <option value="C">Section C</option>
            <option value="D">Section D</option>
        </select>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
        <input type="tel" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter phone number">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
        <input type="date" name="dateOfBirth" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
        <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter address"></textarea>
    </div>
    
    <div class="flex gap-3 pt-4">
        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors duration-200">
            Add Student
        </button>
        <button type="button" onclick="closeModal(\'addStudentModal\')" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg transition-colors duration-200">
            Cancel
        </button>
    </div>
</form>

<script>
document.getElementById("addStudentForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const studentData = {
        fullName: formData.get("fullName"),
        email: formData.get("email"),
        studentId: formData.get("studentId"),
        grade: formData.get("grade"),
        section: formData.get("section"),
        phone: formData.get("phone"),
        dateOfBirth: formData.get("dateOfBirth"),
        address: formData.get("address")
    };
    
    // Here you would typically send the data to your backend
    console.log("Adding student:", studentData);
    
    // Show success message
    showNotification("Student added successfully!", "success");
    
    // Close modal and reset form
    closeModal("addStudentModal");
    this.reset();
    
    // Optionally refresh the students table
    // loadStudents();
});

function showNotification(message, type = "info") {
    const colors = {
        success: "bg-green-500",
        error: "bg-red-500",
        info: "bg-blue-500",
    };
    const toast = document.createElement("div");
    toast.className = `fixed top-5 right-5 px-4 py-2 rounded shadow text-white z-50 ${colors[type] || colors.info}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
';
?> 