<?php include_once '../components/Sidebar.php'; ?>
<?php include_once '../components/Header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Exam - EMS Teacher</title>
  <link rel="stylesheet" href="/src/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
  <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <div class="sticky top-16 z-30 bg-gray-50 pb-4 flex items-center gap-4 border-b mb-6">
        <a href="index.php"
          class="inline-flex items-center px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-700"><i
            class="fas fa-arrow-left mr-2"></i>Back</a>
        <h1 class="text-2xl font-bold text-gray-900 flex-1">Exam Details</h1>
        <a href="editExam.php"
          class="inline-flex items-center px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-medium"><i
            class="fas fa-edit mr-2"></i>Edit Exam</a>
      </div>
      <div class="bg-white shadow rounded-xl p-8">
        <h2 class="text-xl font-semibold mb-6 text-emerald-700">Algebra Basics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <div class="mb-2 text-gray-600">Subject</div>
            <div class="font-medium text-gray-900">Mathematics</div>
          </div>
          <div>
            <div class="mb-2 text-gray-600">Date</div>
            <div class="font-medium text-gray-900">2024-04-20</div>
          </div>
          <div>
            <div class="mb-2 text-gray-600">Status</div>
            <span
              class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Draft</span>
          </div>
          <div>
            <div class="mb-2 text-gray-600">Total Questions</div>
            <div class="font-medium text-gray-900">20</div>
          </div>
          <div>
            <div class="mb-2 text-gray-600">Duration</div>
            <div class="font-medium text-gray-900">60 minutes</div>
          </div>
          <div>
            <div class="mb-2 text-gray-600">Pass Mark</div>
            <div class="font-medium text-gray-900">50%</div>
          </div>
        </div>
        <div class="mb-2 text-gray-600 font-semibold">Description</div>
        <div class="mb-4 text-gray-800 border-l-4 border-emerald-200 pl-4 py-2 bg-emerald-50">This is a sample exam
          description for Algebra Basics. It covers fundamental
          algebraic concepts and problem-solving skills.</div>
      </div>
    </div>
  </main>
</body>

</html>