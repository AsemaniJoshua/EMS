<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Signup - EMS</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <!-- Axios for HTTP requests -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Custom signup script -->
    <script src="signup.js"></script>
</head>
<!--
    The 'overflow-hidden' on the body prevents the entire page from scrolling.
    The 'h-screen' on the main grid container ensures it takes full viewport height.
-->
<body class="bg-gray-50 min-h-screen">
    <div class="h-screen grid lg:grid-cols-2 overflow-hidden">
        <!-- Left Panel - Image Section -->
        <!-- This panel is set to h-screen to fill its grid column height -->
        <div class="hidden lg:flex relative h-screen w-full col-span-1 bg-gradient-to-br from-emerald-600 to-emerald-800 overflow-hidden">
            <!-- <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute inset-0" style="background-image: url('https://images.unsplash.com/photo-1464983953574-0892a716854b?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/80 to-emerald-800/60"></div> -->

            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute inset-0" style="background-image: url('https://images.unsplash.com/photo-1464983953574-0892a716854b?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-green-600/80 to-green-800/60"></div>


            <!-- Content overlay -->
            <div class="relative z-10 flex flex-col justify-center items-center text-center w-full h-full">
                <div class="max-w-md flex flex-col items-center justify-center h-full text-white">
                    <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mb-8 mx-auto">
                        <i class="fa-solid fa-user-graduate text-2xl text-white"></i>
                    </div>
                    <h1 class="text-4xl font-bold mb-4">Student Registration</h1>
                    <p class="text-lg text-emerald-100 mb-8">Create your Examplify student account and unlock your academic journey.</p>
                    <div class="flex items-center justify-center gap-3 text-emerald-100">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                        <span>Secure & Reliable Platform</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Form Section -->
        <div class="flex flex-col h-screen justify-center items-center overflow-hidden">
            <div class="w-full max-w-3xl flex flex-col items-center my-auto px-4 lg:px-8">
                <!-- Mobile header (hidden on lg+) -->
                <div class="lg:hidden text-center shrink-0 pt-12 pb-6">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 mx-auto mb-4 flex items-center justify-center">
                        <i class="fa-solid fa-user-graduate text-xl text-emerald-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Student Registration</h1>
                </div>

                <!-- Form header (fixed at top) -->
                <div class="shrink-0 pb-10 pt-16 text-center w-full">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2 text-center">Create your account</h2>
                    <p class="text-gray-600 text-center">Fill in your details to register as a student</p>
                </div>

                <!-- Signup form (scrollable only for fields and button) -->
                <div class="flex flex-col gap-12 w-full">
                    <form class="space-y-6" method="POST" id="signup-form">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                                <label for="first-name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input id="first-name" name="first_name" type="text" autocomplete="given-name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                               placeholder="First Name">
                    </div>
                    <div>
                                <label for="last-name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input id="last-name" name="last_name" type="text" autocomplete="family-name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                               placeholder="Last Name">
                    </div>
                    <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input id="username" name="username" type="text" autocomplete="username" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                               placeholder="Choose a Username">
                    </div>
                    <div>
                                <label for="index-number" class="block text-sm font-medium text-gray-700 mb-2">Index Number</label>
                        <input id="index-number" name="index_number" type="text" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                               placeholder="Index Number (e.g., CA/MY3/2660)">
                    </div>
                    <div>
                                <label for="email-address" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                               placeholder="Email address">
                    </div>
                    <div>
                                <label for="phone-number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input id="phone-number" name="phone_number" type="tel" autocomplete="tel"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                               placeholder="Phone Number (Optional)">
                    </div>
                    <div>
                                <label for="date-of-birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input id="date-of-birth" name="date_of_birth" type="date" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">
                    </div>
                    <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select id="gender" name="gender" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">
                            <option value="" disabled selected>Select your Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div>
                                <label for="program" class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                        <select id="program" name="program_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">
                            <option value="" disabled selected>Select your Program</option>
                            <option value="1">Computer Science</option>
                            <option value="2">Electrical Engineering</option>
                            <option value="3">Business Administration</option>
                            <option value="4">Applied Mathematics</option>
                        </select>
                    </div>
                    <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select id="department" name="department_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors">
                            <option value="" disabled selected>Select your Department</option>
                            <option value="101">Software Engineering</option>
                            <option value="102">Network Systems</option>
                            <option value="103">Cybersecurity</option>
                            <option value="104">Data Science</option>
                        </select>
                    </div>
                    <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                               placeholder="Password">
                    </div>
                    <div>
                                <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input id="confirm-password" name="confirm_password" type="password" autocomplete="new-password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-colors"
                               placeholder="Confirm Password">
                    </div>
                </div>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2 mt-12 mb-8">
                            <i class="fa-solid fa-user-plus"></i>
                    Register Account
                </button>
                    </form>
            </div>
                <div class="text-center w-full mt-auto pb-8">
                    <p class="text-sm text-gray-600">
            Already have an account?
                        <a href="../login/index.php" class="text-emerald-600 hover:text-emerald-500 font-medium">
                Log in here
            </a>
        </p>
    </div>
            </div>
        </div>
    </div>
</body>
</html>
