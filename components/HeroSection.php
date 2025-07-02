<?php
function HeroSection() {
    ?>
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
                <img
                    src="/assets/images/hero_student.jpg"
                    alt="African college students studying together"
                    class="w-full max-w-md rounded-2xl shadow-xl border border-teal-100 transform rotate-2 hover:rotate-0 transition-transform duration-500 animate-float"
                />
            </div>
        </div>
        <div class="absolute top-1/4 left-0 w-40 h-40 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float delay-500"></div>
        <div class="absolute bottom-1/4 right-0 w-52 h-52 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float delay-1000"></div>
    </section>

<?php 
}
?>