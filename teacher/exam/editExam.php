<?php include_once '../components/Sidebar.php'; ?>
<?php include_once '../components/Header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Exam - EMS Teacher</title>
  <link rel="stylesheet" href="/src/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
  <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-2xl mx-auto">
      <div class="sticky top-16 z-30 bg-gray-50 pb-4 flex items-center gap-4 border-b mb-6">
        <a href="index.php"
          class="inline-flex items-center px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-700"><i
            class="fas fa-arrow-left mr-2"></i>Back</a>
        <h1 class="text-2xl font-bold text-gray-900 flex-1">Edit Exam</h1>
      </div>
      <form class="bg-white shadow rounded-xl p-8 space-y-6">
        <div class="text-lg font-semibold text-emerald-700 mb-4">Exam Information</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-gray-700 font-medium mb-1">Title</label>
            <input type="text"
              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400"
              value="Algebra Basics">
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-1">Subject</label>
            <select class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400">
              <option>Mathematics</option>
              <option>Physics</option>
              <option>Chemistry</option>
              <option>Biology</option>
              <option>English</option>
              <option>History</option>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-1">Date</label>
            <input type="date"
              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400"
              value="2024-04-20">
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-1">Status</label>
            <select class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400">
              <option>Draft</option>
              <option>Active</option>
              <option>Completed</option>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-1">Duration (minutes)</label>
            <input type="number"
              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" value="60">
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-1">Pass Mark (%)</label>
            <input type="number"
              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" value="50">
          </div>
        </div>
        <div>
          <label class="block text-gray-700 font-medium mb-1">Description</label>
          <textarea class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400"
            rows="4">This is a sample exam description for Algebra Basics. It covers fundamental algebraic concepts and problem-solving skills.</textarea>
        </div>
        <div class="flex justify-end gap-2">
          <button type="submit"
            class="inline-flex items-center px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-medium"><i
              class="fas fa-save mr-2"></i>Save Changes</button>
        </div>
      </form>
    </div>
  </main>
</body>

</html>