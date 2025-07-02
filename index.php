<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examplify - Redefine Your Learning Journey</title>
    <!-- Tailwind CSS CDN
    <script src="https://cdn.tailwindcss.com"></script> -->
    <link href="src/output.css" rel="stylesheet">
    <style>
        /* Custom styles for Inter font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Keyframes for subtle animations */
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes floatEffect {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Apply animations */
        .animate-slide-in-up { animation: slideInUp 0.8s ease-out forwards; opacity: 0; }
        .animate-zoom-in { animation: zoomIn 0.7s ease-out forwards; opacity: 0; }
        .animate-float { animation: floatEffect 3s infinite ease-in-out; }

        /* Animation delays */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; }
        .delay-800 { animation-delay: 0.8s; }
        .delay-900 { animation-delay: 0.9s; }
        .delay-1000 { animation-delay: 1s; }

        /* Specific styles for mobile menu toggle */
        #mobile-menu {
            transition: transform 0.3s ease-out;
            transform: translateX(-100%); /* Start off-screen to the left */
        }
        #mobile-menu.open {
            transform: translateX(0); /* Slide in from the left */
        }
    </style>

</head>
<body class="min-h-screen bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white bg-opacity-95 backdrop-blur-md shadow-lg py-3 px-6 md:px-12 lg:px-24 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center space-x-2">
            <!-- Refined Icon for Exams System -->
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <path d="M14 2v6h6"></path>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <line x1="10" y1="9" x2="8" y2="9"></line>
            </svg>
            <span class="text-xl font-bold text-gray-900">Examplify</span>
        </div>
        <div class="hidden md:flex space-x-6">
            <a href="#features" class="text-gray-600 hover:text-emerald-600 font-medium transition-colors duration-200">Features</a>
            <a href="#how-it-works" class="text-gray-600 hover:text-emerald-600 font-medium transition-colors duration-200">How It Works</a>
            <a href="#testimonials" class="text-gray-600 hover:text-emerald-600 font-medium transition-colors duration-200">Testimonials</a>
            <a href="#contact" class="text-gray-600 hover:text-emerald-600 font-medium transition-colors duration-200">Contact</a>
        </div>
        <div class="hidden md:flex space-x-3">
            <a href="/student/login/index.php" class="px-4 py-2 rounded-full text-emerald-600 border border-emerald-600 hover:bg-emerald-50 transition-all duration-300 text-sm font-medium shadow-sm">Login</a>
            <a href="/student/signup/index.php" class="px-4 py-2 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 transition-all duration-300 text-sm font-medium shadow-md">Register</a>
        </div>
        <!-- Mobile Menu Button (Hamburger Icon) -->
        <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-emerald-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="fixed top-0 left-0 h-full w-64 bg-white shadow-2xl p-6 z-50 md:hidden flex flex-col space-y-6 rounded-r-xl">
        <button id="close-mobile-menu" class="self-end text-gray-700 hover:text-emerald-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <a href="#features" class="text-gray-800 hover:text-emerald-600 text-base font-medium transition-colors duration-200">Features</a>
        <a href="#how-it-works" class="text-gray-800 hover:text-emerald-600 text-base font-medium transition-colors duration-200">How It Works</a>
        <a href="#testimonials" class="text-gray-800 hover:text-emerald-600 text-base font-medium transition-colors duration-200">Testimonials</a>
        <a href="#contact" class="text-gray-800 hover:text-emerald-600 text-base font-medium transition-colors duration-200">Contact</a>
        <div class="pt-4 border-t border-gray-200 flex flex-col space-y-4">
            <a href="/student/login/index.php" class="px-5 py-2 rounded-full text-emerald-600 border border-emerald-600 hover:bg-emerald-50 transition-colors duration-200 text-sm font-medium text-center">Login</a>
            <a href="/student/signup/index.php" class="px-5 py-2 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 transition-colors duration-200 text-sm font-medium shadow-md text-center">Register</a>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-white to-teal-50 py-20 md:py-28 overflow-hidden">
        <div class="container mx-auto px-6 md:px-12 lg:px-24 flex flex-col md:flex-row items-center justify-between z-10 relative">
            <div class="md:w-3/5 text-center md:text-left mb-10 md:mb-0 animate-slide-in-up delay-100">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4">
                    Redefine Your <span class="text-emerald-600">Learning Journey</span>.
                </h1>
                <p class="text-lg text-gray-700 mb-8 max-w-xl mx-auto md:mx-0">
                    Examplify empowers students, teachers, and administrators with an intelligent, seamless, and secure online examination experience.
                </p>
                <div class="flex justify-center md:justify-start space-x-4">
                    <button class="px-6 py-3 bg-emerald-600 text-white text-base font-semibold rounded-full shadow-lg hover:bg-emerald-700 transition-all duration-300 transform hover:-translate-y-1">
                        Start Free Trial
                    </button>
                    <button class="px-6 py-3 bg-white text-emerald-600 text-base font-semibold rounded-full shadow-lg border-2 border-emerald-200 hover:bg-emerald-50 transition-all duration-300 transform hover:-translate-y-1">
                        Explore Features
                    </button>
                </div>
            </div>
            <div class="md:w-2/5 flex justify-center md:justify-end animate-zoom-in delay-300">
                <!-- Dynamic, abstract illustration placeholder -->
                <img
                    src="./2148913219.jpg"
                    alt="African college students studying together"
                    class="w-full max-w-md rounded-2xl shadow-xl border border-teal-100 transform rotate-2 hover:rotate-0 transition-transform duration-500 animate-float"
                />
            </div>
        </div>
        <!-- Subtle background shapes/gradients -->
        <div class="absolute top-1/4 left-0 w-40 h-40 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float delay-500"></div>
        <div class="absolute bottom-1/4 right-0 w-52 h-52 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float delay-1000"></div>
    </section>

    <!-- Features/Benefits Section -->
    <section id="features" class="py-20 md:py-28 bg-gradient-to-br from-white to-gray-50">
        <div class="container mx-auto px-6 md:px-12 lg:px-24">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center text-gray-900 mb-14 leading-tight animate-slide-in-up delay-200">
                Tailored Solutions for Every User
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Student Card -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-xl animate-zoom-in delay-400">
                    <div class="flex items-center justify-center w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full mb-6 shadow-md">
                        <!-- Student Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 18a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2"></path>
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">For Students</h3>
                    <ul class="list-none space-y-2 text-base text-gray-700">
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-1 text-emerald-500">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg> Intuitive Exam Interface
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-1 text-emerald-500">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg> Instant Performance Feedback
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-1 text-emerald-500">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg> Comprehensive Progress Tracking
                        </li>
                    </ul>
                </div>

                <!-- Teacher Card -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-xl animate-zoom-in delay-600">
                    <div class="flex items-center justify-center w-14 h-14 bg-orange-100 text-orange-600 rounded-full mb-6 shadow-md">
                        <!-- Teacher Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V6.5A2.5 2.5 0 0 0 17.5 4h-11A2.5 2.5 0 0 0 4 6.5v13z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">For Teachers</h3>
                    <ul class="list-none space-y-2 text-base text-gray-700">
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-1 text-orange-500">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg> Easy Question & Exam Creation
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-1 text-orange-500">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg> Flexible Scheduling Options
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-1 text-orange-500">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg> Detailed Student Analytics
                        </li>
                    </ul>
                </div>

                <!-- Admin Card -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg border border-gray-100 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-xl animate-zoom-in delay-800">
                    <div class="flex items-center justify-center w-14 h-14 bg-purple-100 text-purple-600 rounded-full mb-6 shadow-md">
                        <!-- Admin Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <path d="M12 16a4 4 0 1 0 0-8a4 4 0 0 0 0 8z"></path>
                            <path d="M12 12v.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">For Administrators</h3>
                    <ul class="list-none space-y-2 text-base text-gray-700">
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-1 text-purple-500">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg> Centralized User Management
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-1 text-purple-500">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg> Full Exam System Control
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-1 text-purple-500">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg> Robust Reporting & Oversight
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 md:py-28 bg-gradient-to-br from-teal-50 to-emerald-100">
        <div class="container mx-auto px-6 md:px-12 lg:px-24">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center text-gray-900 mb-14 leading-tight animate-slide-in-up delay-100">
                Your Path to Proficiency
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-md border border-teal-200 transform transition-transform duration-300 hover:scale-105 hover:shadow-lg animate-zoom-in delay-200">
                    <div class="w-12 h-12 bg-teal-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 shadow-md">
                        1
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sign Up & Log In</h3>
                    <p class="text-gray-700 text-sm">Create your account and securely access your personalized dashboard.</p>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-md border border-emerald-200 transform transition-transform duration-300 hover:scale-105 hover:shadow-lg animate-zoom-in delay-400">
                    <div class="w-12 h-12 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 shadow-md">
                        2
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Select Your Exam</h3>
                    <p class="text-gray-700 text-sm">Choose from a diverse library of subjects tailored to your academic needs.</p>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-md border border-orange-200 transform transition-transform duration-300 hover:scale-105 hover:shadow-lg animate-zoom-in delay-600">
                    <div class="w-12 h-12 bg-orange-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 shadow-md">
                        3
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Engage in Tests</h3>
                    <p class="text-gray-700 text-sm">Experience a fluid, question-by-question interface with real-time timers.</p>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-md border border-purple-200 transform transition-transform duration-300 hover:scale-105 hover:shadow-lg animate-zoom-in delay-800">
                    <div class="w-12 h-12 bg-purple-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 shadow-md">
                        4
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Analyze Progress</h3>
                    <p class="text-gray-700 text-sm">Receive instant results and detailed insights to monitor your growth.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-20 md:py-28 bg-white">
        <div class="container mx-auto px-6 md:px-12 lg:px-24">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center text-gray-900 mb-14 leading-tight animate-slide-in-up delay-100">
                What Our Users Are Saying
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-8 rounded-2xl shadow-lg border border-blue-200 transform transition-transform duration-300 hover:scale-[1.01] hover:shadow-xl animate-zoom-in delay-300">
                    <div class="flex items-center mb-5">
                        <img
                            src="https://placehold.co/56x56/BFDBFE/1F2937?text=JD"
                            alt="John Doe"
                            class="w-14 h-14 rounded-full mr-4 border-2 border-blue-300 shadow-sm"
                        />
                        <div>
                            <p class="font-bold text-base text-gray-900">John D.</p>
                            <p class="text-sm text-gray-700">Student, City College</p>
                        </div>
                    </div>
                    <p class="text-base text-gray-800 leading-relaxed italic">
                        "Examplify's interface is incredibly intuitive and visually appealing. The instant feedback on my exams is a game-changer for my study habits. Truly a fantastic platform!"
                    </p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-yellow-100 p-8 rounded-2xl shadow-lg border border-orange-200 transform transition-transform duration-300 hover:scale-[1.01] hover:shadow-xl animate-zoom-in delay-500">
                    <div class="flex items-center mb-5">
                        <img
                            src="https://placehold.co/56x56/D1FAE5/1F2937?text=AS"
                            alt="Jane Smith"
                            class="w-14 h-14 rounded-full mr-4 border-2 border-orange-300 shadow-sm"
                        />
                        <div>
                            <p class="font-bold text-base text-gray-900">Jane S.</p>
                            <p class="text-sm text-gray-700">Educator, Bright Minds School</p>
                        </div>
                    </div>
                    <p class="text-base text-gray-800 leading-relaxed italic">
                        "Managing exams and tracking student progress with Examplify is a breeze. The clean design makes it a pleasure to use, and the comprehensive analytics are invaluable for tailoring my teaching."
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final Call to Action -->
    <section class="py-20 md:py-28 bg-gradient-to-br from-emerald-600 to-teal-700 text-white text-center">
        <div class="container mx-auto px-6 md:px-12 lg:px-24">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-7 leading-tight animate-slide-in-up delay-100">
                Ready to Experience the Difference?
            </h2>
            <p class="text-lg mb-10 max-w-2xl mx-auto opacity-90 animate-slide-in-up delay-200">
                Join our thriving community and elevate your online examination experience with Examplify today.
            </p>
            <button class="px-8 py-3 bg-white text-emerald-700 text-base font-bold rounded-full shadow-xl hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-1 animate-bounce-effect delay-400">
                Get Started for Free
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-gray-300 py-10 px-6 md:px-12 lg:px-24">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-bold text-white mb-4">Examplify</h3>
                <p class="text-sm leading-relaxed">Innovative online assessment solutions for a smarter future.</p>
            </div>
            <div>
                <h3 class="text-base font-bold text-white mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="#features" class="text-sm hover:text-emerald-400 transition-colors duration-200">Features</a></li>
                    <li><a href="#how-it-works" class="text-sm hover:text-emerald-400 transition-colors duration-200">How It Works</a></li>
                    <li><a href="#testimonials" class="text-sm hover:text-emerald-400 transition-colors duration-200">Testimonials</a></li>
                    <li><a href="#" class="text-sm hover:text-emerald-400 transition-colors duration-200">Privacy Policy</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-base font-bold text-white mb-4">Contact Us</h3>
                <p class="text-sm mb-1">Email: <a href="mailto:info@examplify.com" class="hover:text-emerald-400 transition-colors duration-200">info@examplify.com</a></p>
                <p class="text-sm mb-1">Phone: +1 (555) 987-6543</p>
                <p class="text-sm">Address: 456 Learning Lane, Knowledge City, TX 78901</p>
            </div>
        </div>
        <div class="text-center text-gray-500 text-xs mt-10 border-t border-gray-700 pt-6">
            &copy; <script>document.write(new Date().getFullYear())</script> Examplify. All rights reserved.
        </div>
    </footer>

    <script>
        // JavaScript for Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMobileMenuButton = document.getElementById('close-mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('open');
        });

        closeMobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
        });

        // Close mobile menu when a link is clicked
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
            });
        });
    </script>
</body>
</html>
