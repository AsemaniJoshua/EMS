<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login - EMS</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <!-- Axios for HTTP requests -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Custom login script -->
    <script src="login.js"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen grid lg:grid-cols-2">
        <!-- Left Panel - Image Section -->
        <div class="hidden lg:flex relative h-screen bg-gradient-to-br from-emerald-600 to-emerald-800">

            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute inset-0" style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-green-600/80 to-green-800/60"></div>

            <!-- Content overlay -->
            <div class="relative z-10 flex flex-col justify-center px-12 text-white">
                <div class="max-w-md">
                    <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mb-8">
                        <i class="fa-solid fa-chalkboard-teacher text-2xl text-white"></i>
                    </div>
                    <h1 class="text-4xl font-bold mb-4">Teacher Portal</h1>
                    <p class="text-lg text-emerald-100 mb-8">Access your teaching dashboard and manage examinations with ease.</p>
                    <div class="flex items-center gap-3 text-emerald-100">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                        <span>Secure & Reliable Platform</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Form Section -->
        <div class="flex items-center justify-center p-8 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile header (hidden on lg+) -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 mx-auto mb-4 flex items-center justify-center">
                        <i class="fa-solid fa-chalkboard-teacher text-xl text-emerald-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Teacher Login</h1>
                </div>

                <!-- Form header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
                    <p class="text-gray-600">Please sign in to your teacher account</p>
                </div>

                <!-- Login form -->
                <form id="teacher-login-form" class="space-y-6">
                    <div>
                        <label for="username-email" class="block text-sm font-medium text-gray-700 mb-2">Username or Email</label>
                        <input
                            type="text"
                            id="username-email"
                            name="username_email"
                            placeholder="Enter your username or email"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                id="remember-me"
                                name="remember-me"
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mr-2">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="reset-password/index.php" class="text-sm text-emerald-600 hover:text-emerald-500 font-medium">
                            Forgot password?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Need an account?
                        <a href="#" class="text-emerald-600 hover:text-emerald-500 font-medium">
                            Contact administrator
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>