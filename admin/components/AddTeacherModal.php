<?php
// Add Teacher Modal component
$modalId = 'addTeacherModal';
$modalTitle = 'Add New Teacher';
$modalContent = '
<form id="addTeacherForm" class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="fullName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter full name">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter email address">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
        <input type="tel" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter phone number">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
        <select name="department" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Select Department</option>
            <option value="Mathematics">Mathematics</option>
            <option value="Science">Science</option>
            <option value="English">English</option>
            <option value="History">History</option>
            <option value="Computer Science">Computer Science</option>
            <option value="Physical Education">Physical Education</option>
        </select>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Qualification</label>
        <input type="text" name="qualification" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., M.Sc., Ph.D.">
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Experience (Years)</label>
        <input type="number" name="experience" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0">
    </div>
    
    <div class="flex gap-3 pt-4">
        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors duration-200">
            Add Teacher
        </button>
        <button type="button" onclick="closeModal(\'addTeacherModal\')" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg transition-colors duration-200">
            Cancel
        </button>
    </div>
</form>

<script>
document.getElementById("addTeacherForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const teacherData = {
        fullName: formData.get("fullName"),
        email: formData.get("email"),
        phone: formData.get("phone"),
        department: formData.get("department"),
        qualification: formData.get("qualification"),
        experience: formData.get("experience")
    };
    
    // Here you would typically send the data to your backend
    console.log("Adding teacher:", teacherData);
    
    // Show success message
    showNotification("Teacher added successfully!", "success");
    
    // Close modal and reset form
    closeModal("addTeacherModal");
    this.reset();
    
    // Optionally refresh the teachers table
    // loadTeachers();
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