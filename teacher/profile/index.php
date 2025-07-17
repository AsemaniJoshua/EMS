<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Profile - EMS</title>
  <link rel="stylesheet" href="/src/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
  <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-2xl mx-auto">
      <div class="sticky top-16 z-30 bg-gray-50 pb-4 flex items-center gap-4 border-b mb-6">
        <h1 class="text-2xl font-bold text-gray-900 flex-1">Teacher Profile</h1>
        <a href="edit.php?id=<?php echo $teacher_id; ?>"
          class="inline-flex items-center px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-medium"><i
            class="fas fa-edit mr-2"></i>Edit Profile</a>
      </div>
      <div class="bg-white shadow rounded-xl p-8">
        <?php if (isset($error)): ?>
          <div class="text-red-600 mb-4"><?php echo $error; ?></div>
        <?php elseif ($teacher): ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <div class="mb-2 text-gray-600">Full Name</div>
              <div class="font-medium text-gray-900">
                <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></div>
            </div>
            <div>
              <div class="mb-2 text-gray-600">Staff ID</div>
              <div class="font-medium text-gray-900"><?php echo htmlspecialchars($teacher['staff_id']); ?></div>
            </div>
            <div>
              <div class="mb-2 text-gray-600">Username</div>
              <div class="font-medium text-gray-900"><?php echo htmlspecialchars($teacher['username']); ?></div>
            </div>
            <div>
              <div class="mb-2 text-gray-600">Email</div>
              <div class="font-medium text-gray-900"><?php echo htmlspecialchars($teacher['email']); ?></div>
            </div>
            <div>
              <div class="mb-2 text-gray-600">Phone Number</div>
              <div class="font-medium text-gray-900"><?php echo htmlspecialchars($teacher['phone_number']); ?></div>
            </div>
            <div>
              <div class="mb-2 text-gray-600">Department</div>
              <div class="font-medium text-gray-900"><?php echo htmlspecialchars($teacher['department_name']); ?></div>
            </div>
            <div>
              <div class="mb-2 text-gray-600">Status</div>
              <span
                class="inline-flex px-2 py-1 rounded-full text-xs font-semibold <?php echo $teacher['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600'; ?>"><?php echo ucfirst($teacher['status']); ?></span>
            </div>
            <div>
              <div class="mb-2 text-gray-600">Created At</div>
              <div class="font-medium text-gray-900"><?php echo htmlspecialchars($teacher['created_at']); ?></div>
            </div>
            <div>
              <div class="mb-2 text-gray-600">Last Updated</div>
              <div class="font-medium text-gray-900"><?php echo htmlspecialchars($teacher['updated_at']); ?></div>
            </div>
          </div>
        <?php else: ?>
          <div class="text-gray-600">Teacher not found.</div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>

</html>