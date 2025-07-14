<?php
// teacher/profile/edit.php
include_once '../components/Sidebar.php';
include_once '../components/Header.php';
require_once '../../api/config/database.php'; // Adjust path as needed

$teacher_id = isset($_GET['id']) ? intval($_GET['id']) : 1; // Replace with session logic in production

// Fetch departments for dropdown
$departments = [];
try {
  $pdo = new PDO('mysql:host=localhost;dbname=ems_db', 'root', ''); // Update credentials as needed
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $deptStmt = $pdo->query('SELECT department_id, name FROM departments');
  $departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $error = 'Database error: ' . $e->getMessage();
}

// Fetch teacher data
$teacher = null;
if (!isset($error)) {
  try {
    $stmt = $pdo->prepare('SELECT * FROM teachers WHERE teacher_id = :teacher_id');
    $stmt->execute(['teacher_id' => $teacher_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
  }
}

// Handle form submission
$success = false;
$password_success = false;
$password_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $teacher) {
  // Profile update
  $first_name = $_POST['first_name'] ?? '';
  $last_name = $_POST['last_name'] ?? '';
  $email = $_POST['email'] ?? '';
  $phone_number = $_POST['phone_number'] ?? '';
  $department_id = $_POST['department_id'] ?? '';
  try {
    $updateStmt = $pdo->prepare('UPDATE teachers SET first_name = :first_name, last_name = :last_name, email = :email, phone_number = :phone_number, department_id = :department_id, updated_at = NOW() WHERE teacher_id = :teacher_id');
    $updateStmt->execute([
      'first_name' => $first_name,
      'last_name' => $last_name,
      'email' => $email,
      'phone_number' => $phone_number,
      'department_id' => $department_id,
      'teacher_id' => $teacher_id
    ]);
    $success = true;
    // Refresh teacher data
    $stmt = $pdo->prepare('SELECT * FROM teachers WHERE teacher_id = :teacher_id');
    $stmt->execute(['teacher_id' => $teacher_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
  }
  // Password change
  if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    // Fetch current hash
    $stmt = $pdo->prepare('SELECT password_hash FROM teachers WHERE teacher_id = :teacher_id');
    $stmt->execute(['teacher_id' => $teacher_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && password_verify($current_password, $row['password_hash'])) {
      if ($new_password === $confirm_password) {
        if (strlen($new_password) >= 6) {
          $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
          $updatePassStmt = $pdo->prepare('UPDATE teachers SET password_hash = :password_hash, updated_at = NOW() WHERE teacher_id = :teacher_id');
          $updatePassStmt->execute([
            'password_hash' => $new_hash,
            'teacher_id' => $teacher_id
          ]);
          $password_success = true;
        } else {
          $password_error = 'New password must be at least 6 characters.';
        }
      } else {
        $password_error = 'New passwords do not match.';
      }
    } else {
      $password_error = 'Current password is incorrect.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Teacher Profile - EMS</title>
  <link rel="stylesheet" href="/src/output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
  <main class="pt-16 lg:pt-18 lg:ml-60 min-h-screen transition-all duration-300">
    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-2xl mx-auto">
      <div class="sticky top-16 z-30 bg-gray-50 pb-4 flex items-center gap-4 border-b mb-6">
        <a href="index.php?id=<?php echo $teacher_id; ?>"
          class="inline-flex items-center px-3 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-700"><i
            class="fas fa-arrow-left mr-2"></i>Back</a>
        <h1 class="text-2xl font-bold text-gray-900 flex-1">Edit Profile</h1>
      </div>
      <div class="bg-white shadow rounded-xl p-8">
        <?php if (isset($error)): ?>
          <div class="text-red-600 mb-4"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
          <div class="text-green-600 mb-4">Profile updated successfully.</div>
        <?php endif; ?>
        <?php if ($teacher): ?>
          <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-gray-700 font-medium mb-1">First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($teacher['first_name']); ?>"
                  class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($teacher['last_name']); ?>"
                  class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>"
                  class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Phone Number</label>
                <input type="text" name="phone_number" value="<?php echo htmlspecialchars($teacher['phone_number']); ?>"
                  class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400">
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Department</label>
                <select name="department_id"
                  class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
                  <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo $dept['department_id']; ?>" <?php if ($teacher['department_id'] == $dept['department_id'])
                         echo 'selected'; ?>>
                      <?php echo htmlspecialchars($dept['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="flex justify-end gap-2">
              <button type="submit"
                class="inline-flex items-center px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-medium"><i
                  class="fas fa-save mr-2"></i>Save Changes</button>
            </div>
          </form>
          <!-- Password Change Section -->
          <div class="mt-10 border-t pt-8">
            <h2 class="text-lg font-semibold text-emerald-700 mb-4">Change Password</h2>
            <?php if ($password_success): ?>
              <div class="text-green-600 mb-4">Password updated successfully.</div>
            <?php elseif ($password_error): ?>
              <div class="text-red-600 mb-4"><?php echo $password_error; ?></div>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
              <div>
                <label class="block text-gray-700 font-medium mb-1">Current Password</label>
                <input type="password" name="current_password"
                  class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">New Password</label>
                <input type="password" name="new_password"
                  class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Confirm New Password</label>
                <input type="password" name="confirm_password"
                  class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" required>
              </div>
              <div class="flex justify-end gap-2">
                <button type="submit"
                  class="inline-flex items-center px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-medium"><i
                    class="fas fa-key mr-2"></i>Change Password</button>
              </div>
            </form>
          </div>
        <?php else: ?>
          <div class="text-gray-600">Teacher not found.</div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>

</html>