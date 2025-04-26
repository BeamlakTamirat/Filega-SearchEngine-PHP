    </main> <!-- End main content -->
    
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <p style="text-align:center;">Advanced search engine for better results</p>
                </div>
                
                <div class="footer-links">
                    
                    
                    <div class="footer-column" >
                        <h4>Connect</h4>
                        <div class="social-links">
                        <a href="https://www.linkedin.com/in/beamlak-tamirat-801124317/" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="https://x.com/BeamlakTamirat1" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
                <p>Version <?php echo APP_VERSION; ?></p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?php echo baseUrl(); ?>/assets/js/main.js"></script>
    
    <!-- Theme toggle script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    const currentTheme = localStorage.getItem('theme') || 'light';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    setTheme(newTheme);
                });
                
                // Update button icon based on current theme
                const updateThemeToggleButton = () => {
                    const currentTheme = localStorage.getItem('theme') || 'light';
                    themeToggleBtn.classList.remove('light-mode', 'dark-mode');
                    themeToggleBtn.classList.add(currentTheme === 'light' ? 'dark-mode' : 'light-mode');
                };
                
                // Call once on load and whenever theme changes
                updateThemeToggleButton();
                window.addEventListener('storage', function(e) {
                    if (e.key === 'theme') {
                        updateThemeToggleButton();
                    }
                });
            }
            
            // Initialize dropdowns
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    this.parentElement.classList.toggle('open');
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                dropdownToggles.forEach(toggle => {
                    const dropdown = toggle.parentElement;
                    if (!dropdown.contains(event.target)) {
                        dropdown.classList.remove('open');
                    }
                });
            });
        });
    </script>
</body>
</html> 