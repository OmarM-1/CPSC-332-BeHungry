<?php
$current_year = date('Y');
?>
    </div>
    
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3> BeHungry</h3>
                <p class="footer-description">
                    A community-driven platform for food enthusiasts to share, discover, 
                    and discuss recipes from around the world.
                </p>
                <div class="social-links">
                    <span class="social-label">Follow us:</span>
                    <a href="#" class="social-icon" aria-label="Facebook"></a>
                    <a href="#" class="social-icon" aria-label="Instagram"></a>

                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="search.php">Browse Recipes</a></li>
                    <li><a href="submit_recipe.php">Submit Recipe</a></li>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Categories</h4>
                <ul class="footer-links">
                    <li><a href="search.php?q=breakfast">Breakfast</a></li>
                    <li><a href="search.php?q=lunch">Lunch</a></li>
                    <li><a href="search.php?q=dinner">Dinner</a></li>
                    <li><a href="search.php?q=dessert">Dessert</a></li>
                    <li><a href="search.php?q=vegetarian">Vegetarian</a></li>
                    <li><a href="search.php?q=vegan">Vegan</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>About</h4>
                <ul class="footer-links">
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="admin.php">Admin Panel</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= $current_year ?> BeHungry Recipe Sharing Platform. All rights reserved.</p>
            <p class="tech-stack">
                Built with: <span class="tech">HTML5</span> | <span class="tech">CSS3</span> | 
                <span class="tech">PHP</span> | <span class="tech">MySQL</span>
            </p>
            <p class="project-info">
                Academic Project | Web Development Course | Group: <?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Team' ?>
            </p>
        </div>
    </footer>
    

    <script>

    document.querySelectorAll('.delete-btn, .btn-delete, .delete-comment-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this?')) {
                e.preventDefault();
            }
        });
    });
    
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#ff6b6b';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
    
    if (document.querySelector('.tab-btn')) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                showTab(tabName);
            });
        });
        
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabName).classList.add('active');
            
            event.currentTarget.classList.add('active');
        }
    }
    
    </div>
</body>
</html>