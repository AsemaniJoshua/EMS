<?php
if (session_status() === PHP_SESSION_NONE) {
  ini_set('session.cookie_path', '/');
  session_start();
}
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
  header('Location: /student/dashboard/');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Login - EMS</title>
  <link rel="stylesheet" href="/src/output.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
  <style>
    .animate-pop {
      animation: popIn .25s cubic-bezier(.4, 2, .6, 1) both;
    }

    @keyframes popIn {
      0% {
        transform: scale(.8);
        opacity: 0;
      }

      100% {
        transform: scale(1);
        opacity: 1;
      }
    }
  </style>
</head>

<body class="bg-gray-50 h-">
  <div class="grid lg:grid-cols-2 h-screen">
    <!-- Left Panel -->
    <div class="hidden lg:flex relative bg-gradient-to-br from-emerald-600 to-emerald-800">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="absolute inset-0"
        style="background-image: url('https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
      </div>
      <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/80 to-emerald-800/60"></div>
      <div class="relative z-10 flex flex-col justify-center px-12 text-white">
        <div class="max-w-md">
          <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mb-8">
            <i class="fa-solid fa-graduation-cap text-2xl text-white"></i>
          </div>
          <h1 class="text-4xl font-bold mb-4">EMS Student Portal</h1>
          <p class="text-lg text-emerald-100 mb-8">Access your courses, exams, and results in one place.</p>
          <div class="flex items-center gap-3 text-emerald-100">
            <i class="fa-solid fa-shield-halved text-xl"></i>
            <span>Secure & Reliable Platform</span>
          </div>
        </div>
      </div>
    </div>
    <!-- Right Panel -->
    <div class="flex items-center justify-center p-8 lg:p-12 overflow-y-auto">
      <div class="w-full max-w-md">
        <div class="lg:hidden text-center mb-8">
          <div class="w-12 h-12 rounded-full bg-emerald-100 mx-auto mb-4 flex items-center justify-center">
            <i class="fa-solid fa-graduation-cap text-xl text-emerald-600"></i>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">EMS Student</h1>
        </div>
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
          <p class="text-gray-600">Please sign in to your account</p>
        </div>
        <form id="loginForm" class="space-y-6" method="POST">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors" />
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <div class="relative">
              <input type="password" id="password" name="password" placeholder="Enter your password" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors" />
              <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-3 flex items-center">
                <i id="eyeIcon" class="fas fa-eye text-gray-500"></i>
              </button>
            </div>
          </div>
          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input type="checkbox" id="remember" name="remember"
                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mr-2">
              <span class="text-sm text-gray-600">Remember me</span>
            </label>
            <a href="#" class="text-sm text-emerald-600 hover:text-emerald-500 font-medium">Forgot password?</a>
          </div>
          <button type="submit" id="loginBtn"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
            <span id="loginBtnText">Sign In</span>
            <span id="loginSpinner" class="hidden">
              <i class="fas fa-spinner fa-spin"></i>
            </span>
          </button>
          <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-30  items-center justify-center z-50">
            <div class="bg-white p-5 rounded-lg shadow-lg animate-pulse">
              <div class="flex items-center space-x-3">
                <i class="fas fa-spinner fa-spin text-emerald-600 text-xl"></i>
                <span>Processing...</span>
              </div>
            </div>
          </div>
        </form>
        <div class="mt-8 text-center">
          <p class="text-sm text-gray-600">
            Don't have an account?
            <a href="/student/signup/" class="text-emerald-600 hover:text-emerald-500 font-medium">Create one here</a>
          </p>
        </div>
      </div>
    </div>
  </div>
  <script src="/student/login/logins.min.js"></script>
</body>

</html>