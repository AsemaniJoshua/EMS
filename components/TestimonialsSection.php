<?php
function TestimonialsSection()
{
?>
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
                            class="w-14 h-14 rounded-full mr-4 border-2 border-blue-300 shadow-sm" />
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
                            class="w-14 h-14 rounded-full mr-4 border-2 border-orange-300 shadow-sm" />
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
<?php
}
?>