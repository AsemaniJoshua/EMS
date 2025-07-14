<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Teacher Login - EMS</title>
  <link rel="stylesheet" href="/src/output.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
  <!-- Axios -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="/teacher/login/login.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
  <div class="min-h-screen grid lg:grid-cols-2">
    <!-- Left Panel -->
    <div class="hidden lg:flex relative bg-gradient-to-br from-emerald-600 to-emerald-800">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="absolute inset-0"
        style="background-image: url('https://images.unsplash.com/photo-1606761568499-6d2451b23c66?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
      </div>
      <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/80 to-emerald-800/60"></div>
      <div class="relative z-10 flex flex-col justify-center px-12 text-white">
        <div class="max-w-md">
          <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mb-8">
            <i class="fa-solid fa-chalkboard-teacher text-2xl text-white"></i>
          </div>
          <h1 class="text-4xl font-bold mb-4">EMS Teacher Portal</h1>
          <p class="text-lg text-emerald-100 mb-8">Empower your teaching journey with our comprehensive portal.</p>
          <div class="flex items-center gap-3 text-emerald-100">
            <i class="fa-solid fa-shield-halved text-xl"></i>
            <span>Secure & Reliable Platform</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="flex items-center justify-center p-8 lg:p-12">
      <div class="w-full max-w-md">
        <div class="lg:hidden text-center mb-8">
          <div class="w-12 h-12 rounded-full bg-emerald-100 mx-auto mb-4 flex items-center justify-center">
            <i class="fa-solid fa-chalkboard-teacher text-xl text-emerald-600"></i>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">EMS Teacher</h1>
        </div>
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
          <p class="text-gray-600">Please sign in to your account</p>
        </div>
        <form id="teacherLoginForm" class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors" />
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors" />
          </div>
          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input type="checkbox" id="remember" name="remember"
                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mr-2">
              <span class="text-sm text-gray-600">Remember me</span>
            </label>
            <button type="button" id="forgotBtn"
              class="text-sm text-emerald-600 hover:text-emerald-500 font-medium">Forgot password?</button>
          </div>
          <button type="submit"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
            <i class="fa-solid fa-sign-in-alt"></i>
            Sign In
          </button>
        </form>
        <div class="mt-8 text-center">
          <p class="text-sm text-gray-600">
            Don't have an account?
            <a href="#" class="text-emerald-600 hover:text-emerald-500 font-medium">Contact administrator</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Forgot Password Modal -->
  <div id="forgotModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-sm relative animate-pop">
      <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
        onclick="closeModal('forgotModal')"><i class="fas fa-times"></i></button>
      <h2 class="text-xl font-bold mb-4 text-emerald-700">Forgot Password</h2>
      <form id="forgotForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email or Phone Number</label>
          <input type="text" id="contactInput" name="contact" required
            placeholder="e.g. user@example.com or +233501234567"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" />

        </div>
        <button type="submit"
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-medium">Send OTP</button>
      </form>
    </div>
  </div>

  <!-- OTP Modal -->
  <div id="otpModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-sm relative animate-pop">
      <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
        onclick="closeModal('otpModal')"><i class="fas fa-times"></i></button>
      <h2 class="text-xl font-bold mb-4 text-emerald-700">Verify OTP</h2>
      <form id="otpForm" class="space-y-4">
        <div class="flex justify-between gap-2">
          <input type="text" maxlength="1"
            class="otp-input w-10 h-12 text-center border rounded text-xl focus:ring-2 focus:ring-emerald-400" />
          <input type="text" maxlength="1"
            class="otp-input w-10 h-12 text-center border rounded text-xl focus:ring-2 focus:ring-emerald-400" />
          <input type="text" maxlength="1"
            class="otp-input w-10 h-12 text-center border rounded text-xl focus:ring-2 focus:ring-emerald-400" />
          <input type="text" maxlength="1"
            class="otp-input w-10 h-12 text-center border rounded text-xl focus:ring-2 focus:ring-emerald-400" />
          <input type="text" maxlength="1"
            class="otp-input w-10 h-12 text-center border rounded text-xl focus:ring-2 focus:ring-emerald-400" />
          <input type="text" maxlength="1"
            class="otp-input w-10 h-12 text-center border rounded text-xl focus:ring-2 focus:ring-emerald-400" />
        </div>
        <button type="submit"
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-medium">Verify</button>
      </form>
    </div>
  </div>

  <!-- Reset Password Modal -->
  <div id="resetModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-sm relative animate-pop">
      <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
        onclick="closeModal('resetModal')"><i class="fas fa-times"></i></button>
      <h2 class="text-xl font-bold mb-4 text-emerald-700">Reset Password</h2>
      <form id="resetForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
          <input type="password" name="new_password" id="new_password" required
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
          <input type="password" id="confirmPassword" name="confirmPassword" required
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-emerald-400" />
        </div>
        <button type="submit"
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-medium">Reset Password</button>
      </form>
    </div>
  </div>

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


</body>

</html>