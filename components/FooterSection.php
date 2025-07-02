<?php
function FooterSection()
{
?>
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
            &copy; <?php echo date("Y"); ?> Examplify. All rights reserved.
        </div>
    </footer>
<?php
}

?>