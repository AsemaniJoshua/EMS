// Admin Settings JS

document.addEventListener('DOMContentLoaded', function () {
    fetchCategories();
    document.getElementById('categoryForm').onsubmit = addCategory;
    document.getElementById('durationForm').onsubmit = setDuration;
});

function fetchCategories() {
    // Placeholder for backend API call
    fetch('/api/admin/categories')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            populateCategories(data.categories || []);
        })
        .catch(error => {
            showNotification('Failed to load categories (placeholder)', 'error');
        });
}

function populateCategories(categories) {
    const list = document.getElementById('categoriesList');
    list.innerHTML = '';
    if (categories.length === 0) {
        list.innerHTML = '<li class="text-gray-500">No categories found.</li>';
        return;
    }
    categories.forEach((cat, idx) => {
        const li = document.createElement('li');
        li.className = 'bg-gray-100 p-3 rounded flex justify-between items-center';
        li.innerHTML = `<span>${cat.name}</span><button onclick="editCategory(${idx})" class="text-blue-500 hover:underline">Edit</button>`;
        list.appendChild(li);
    });
}

function addCategory(e) {
    e.preventDefault();
    const form = e.target;
    const category = form.category.value;
    // Placeholder for backend API call
    fetch('/api/admin/categories', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ category })
    })
    .then(response => {
        if (!response.ok) throw new Error('Add failed');
        return response.json();
    })
    .then(data => {
        showNotification('Category added! (placeholder)', 'success');
        fetchCategories();
        form.reset();
    })
    .catch(error => {
        showNotification('Failed to add category (placeholder)', 'error');
    });
}

function editCategory(idx) {
    // Placeholder for edit logic
    showNotification('Edit category feature coming soon!', 'info');
}

function setDuration(e) {
    e.preventDefault();
    const form = e.target;
    const duration = form.duration.value;
    // Placeholder for backend API call
    fetch('/api/admin/duration', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ duration })
    })
    .then(response => {
        if (!response.ok) throw new Error('Set failed');
        return response.json();
    })
    .then(data => {
        showNotification('Duration set! (placeholder)', 'success');
        form.reset();
    })
    .catch(error => {
        showNotification('Failed to set duration (placeholder)', 'error');
    });
}

function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
    };
    const toast = document.createElement('div');
    toast.className = `fixed top-5 right-5 px-4 py-2 rounded shadow text-white z-50 ${colors[type] || colors.info}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
} 