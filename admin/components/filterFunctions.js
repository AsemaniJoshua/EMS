// Filter Functions for Admin Pages

// Teachers Filter Function
function filterTeachers() {
  const searchName = document.getElementById("searchName").value.toLowerCase();
  const filterDepartment = document.getElementById("filterDepartment").value;
  const filterStatus = document.getElementById("filterStatus").value;

  const table = document.getElementById("teachersTable");
  const rows = table.getElementsByTagName("tr");

  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];
    const nameCell = row.cells[0]; // Name column
    const departmentCell = row.cells[2]; // Department column
    const statusCell = row.cells[3]; // Status column

    if (nameCell && departmentCell && statusCell) {
      const name = nameCell.textContent.toLowerCase();
      const department = departmentCell.textContent;
      const status = statusCell.textContent;

      const nameMatch = searchName === "" || name.includes(searchName);
      const departmentMatch =
        filterDepartment === "" || department === filterDepartment;
      const statusMatch = filterStatus === "" || status === filterStatus;

      if (nameMatch && departmentMatch && statusMatch) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    }
  }

  showNotification("Teachers filtered successfully!", "info");
}

// Students Filter Function
function filterStudents() {
  const searchName = document.getElementById("searchName").value.toLowerCase();
  const filterGrade = document.getElementById("filterGrade").value;
  const filterStatus = document.getElementById("filterStatus").value;

  const table = document.getElementById("studentsTable");
  const rows = table.getElementsByTagName("tr");

  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];
    const nameCell = row.cells[0]; // Name column
    const gradeCell = row.cells[2]; // Grade column
    const statusCell = row.cells[3]; // Status column

    if (nameCell && gradeCell && statusCell) {
      const name = nameCell.textContent.toLowerCase();
      const grade = gradeCell.textContent;
      const status = statusCell.textContent;

      const nameMatch = searchName === "" || name.includes(searchName);
      const gradeMatch = filterGrade === "" || grade.includes(filterGrade);
      const statusMatch = filterStatus === "" || status === filterStatus;

      if (nameMatch && gradeMatch && statusMatch) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    }
  }

  showNotification("Students filtered successfully!", "info");
}

// Results Filter Function
function filterResults() {
  const searchStudent = document
    .getElementById("searchStudent")
    .value.toLowerCase();
  const searchExam = document.getElementById("searchExam").value.toLowerCase();
  const filterCategory = document.getElementById("filterCategory").value;
  const filterDate = document.getElementById("filterDate").value;

  const table = document.getElementById("resultsTable");
  const rows = table.getElementsByTagName("tr");

  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];
    const studentCell = row.cells[0]; // Student column
    const examCell = row.cells[1]; // Exam column
    const categoryCell = row.cells[2]; // Category column
    const dateCell = row.cells[3]; // Date column

    if (studentCell && examCell && categoryCell && dateCell) {
      const student = studentCell.textContent.toLowerCase();
      const exam = examCell.textContent.toLowerCase();
      const category = categoryCell.textContent;
      const date = dateCell.textContent;

      const studentMatch =
        searchStudent === "" || student.includes(searchStudent);
      const examMatch = searchExam === "" || exam.includes(searchExam);
      const categoryMatch =
        filterCategory === "" || category === filterCategory;
      const dateMatch = filterDate === "" || date.includes(filterDate);

      if (studentMatch && examMatch && categoryMatch && dateMatch) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    }
  }

  showNotification("Results filtered successfully!", "info");
}

// Approvals Filter Function
function filterApprovals() {
  const filterStatus = document.getElementById("filterStatus").value;
  const filterCategory = document.getElementById("filterCategory").value;
  const filterDate = document.getElementById("filterDate").value;

  const table = document.getElementById("approvalTable");
  const rows = table.getElementsByTagName("tr");

  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];
    const categoryCell = row.cells[2]; // Category column
    const dateCell = row.cells[4]; // Date column
    const statusCell = row.cells[5]; // Status column

    if (categoryCell && dateCell && statusCell) {
      const category = categoryCell.textContent;
      const date = dateCell.textContent;
      const status = statusCell.textContent;

      const categoryMatch =
        filterCategory === "" || category === filterCategory;
      const dateMatch = filterDate === "" || date.includes(filterDate);
      const statusMatch = filterStatus === "" || status === filterStatus;

      if (categoryMatch && dateMatch && statusMatch) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    }
  }

  showNotification("Approvals filtered successfully!", "info");
}

// Clear Filters Function
function clearFilters(pageType) {
  const filterInputs = document.querySelectorAll(
    'input[type="text"], input[type="date"], select'
  );
  filterInputs.forEach((input) => {
    input.value = "";
  });

  // Show all rows
  const tableId = pageType + "Table";
  const table = document.getElementById(tableId);
  if (table) {
    const rows = table.getElementsByTagName("tr");
    for (let i = 0; i < rows.length; i++) {
      rows[i].style.display = "";
    }
  }

  showNotification("Filters cleared!", "info");
}

// Enhanced Notification Function
function showNotification(message, type = "info") {
  const colors = {
    success: "bg-green-500",
    error: "bg-red-500",
    info: "bg-blue-500",
    warning: "bg-yellow-500",
  };

  const toast = document.createElement("div");
  toast.className = `fixed top-5 right-5 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${
    colors[type] || colors.info
  } transform transition-all duration-300 translate-x-full`;
  toast.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${getNotificationIcon(type)}</span>
            <span>${message}</span>
        </div>
    `;

  document.body.appendChild(toast);

  // Animate in
  setTimeout(() => {
    toast.classList.remove("translate-x-full");
  }, 100);

  // Remove after 3 seconds
  setTimeout(() => {
    toast.classList.add("translate-x-full");
    setTimeout(() => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 300);
  }, 3000);
}

// Get notification icon based on type
function getNotificationIcon(type) {
  const icons = {
    success:
      '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
    error:
      '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
    info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
    warning:
      '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
  };

  return icons[type] || icons.info;
}

// Add event listeners for real-time search
document.addEventListener("DOMContentLoaded", function () {
  // Add real-time search for teachers
  const teacherSearch = document.getElementById("searchName");
  if (teacherSearch) {
    teacherSearch.addEventListener("input", function () {
      if (this.value.length >= 2 || this.value.length === 0) {
        filterTeachers();
      }
    });
  }

  // Add real-time search for students
  const studentSearch = document.getElementById("searchName");
  if (studentSearch && document.getElementById("studentsTable")) {
    studentSearch.addEventListener("input", function () {
      if (this.value.length >= 2 || this.value.length === 0) {
        filterStudents();
      }
    });
  }

  // Add real-time search for results
  const resultStudentSearch = document.getElementById("searchStudent");
  const resultExamSearch = document.getElementById("searchExam");
  if (resultStudentSearch || resultExamSearch) {
    [resultStudentSearch, resultExamSearch].forEach((input) => {
      if (input) {
        input.addEventListener("input", function () {
          if (this.value.length >= 2 || this.value.length === 0) {
            filterResults();
          }
        });
      }
    });
  }
});
