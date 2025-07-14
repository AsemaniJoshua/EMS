<?php

/**
 * Renders the footer component for Teacher pages
 */
function renderTeacherFooter()
{
?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 p-4">
        <div class="container mx-auto text-center text-gray-600 text-sm">
            &copy; <?php echo date('Y'); ?> Exam Management System. All rights reserved.
        </div>
    </footer>
    </div>
    </div>

    <!-- Profile dropdown script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('user-menu-button');
            const profileMenu = document.getElementById('profileMenu');

            if (userMenuButton && profileMenu) {
                userMenuButton.addEventListener('click', function() {
                    profileMenu.classList.toggle('hidden');
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !profileMenu.contains(event.target)) {
                        profileMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    </body>

    </html>
<?php
}

/**
 * Renders just the closing tags for document
 */
function renderTeacherDocumentEnd()
{
?>
    </div>
    </div>
    </body>

    </html>
<?php
}
?>