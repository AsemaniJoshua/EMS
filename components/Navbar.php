<?php

function Navbar()
{
    $navLinks = [
        'features' => 'Features',
        'how-it-works' => 'How It Works',
        'testimonials' => 'Testimonials',
        'contact' => 'Contact'
    ];

    // Generate HTML for desktop navigation items
    $desktopNavItemsHtml = '';
    foreach ($navLinks as $id => $text) {
        $desktopNavItemsHtml .= '<a href="#' . $id . '" class="text-gray-600 hover:text-emerald-600 font-medium transition-colors duration-200">' . $text . '</a>';
    }

    // Generate HTML for mobile navigation items
    $mobileNavItemsHtml = '';
    foreach ($navLinks as $id => $text) {
        $mobileNavItemsHtml .= '<a href="#' . $id . '" class="text-gray-800 hover:text-emerald-600 text-base font-medium transition-colors duration-200">' . $text . '</a>';
    }
    ?>
    <nav class="bg-white bg-opacity-95 backdrop-blur-md shadow-lg py-3 px-6 md:px-12 lg:px-24 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center space-x-2">
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
            <?php echo $desktopNavItemsHtml ;?> 
        </div>
        <div class="hidden md:flex space-x-3">
            <a href="/student/login/index.php" class="px-4 py-2 rounded-full text-emerald-600 border border-emerald-600 hover:bg-emerald-50 transition-all duration-300 text-sm font-medium shadow-sm">Login</a>
            <a href="/student/signup/index.php" class="px-4 py-2 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 transition-all duration-300 text-sm font-medium shadow-md">Register</a>
        </div>
        <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-emerald-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </nav>

    <div id="mobile-menu" class="fixed top-0 left-0 h-full w-64 bg-white shadow-2xl p-6 z-50 md:hidden flex flex-col space-y-6 rounded-r-xl">
        <button id="close-mobile-menu" class="self-end text-gray-700 hover:text-emerald-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <?php echo $mobileNavItemsHtml ;?> 
    
        <div class="pt-4 border-t border-gray-200 flex flex-col space-y-4">
            <a href="/student/login/index.php" class="px-5 py-2 rounded-full text-emerald-600 border border-emerald-600 hover:bg-emerald-50 transition-colors duration-200 text-sm font-medium text-center">Login</a>
            <a href="/student/signup/index.php" class="px-5 py-2 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 transition-colors duration-200 text-sm font-medium shadow-md text-center">Register</a>
        </div>
    </div>
    
    <script>
        // JavaScript for Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMobileMenuButton = document.getElementById('close-mobile-menu');

        // Check if elements exist before adding event listeners
        if (mobileMenuButton && mobileMenu && closeMobileMenuButton) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.add('open');
                // Optional: Add a class to body to prevent scrolling
                document.body.style.overflow = 'hidden'; 
            });

            closeMobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                document.body.style.overflow = ''; // Restore scrolling
            });

            // Close mobile menu when a link is clicked
            mobileMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.remove('open');
                    document.body.style.overflow = ''; // Restore scrolling
                });
            });
        }
    </script>
<?php
}
?>