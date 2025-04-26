<?php
session_start();
require_once 'config/config.php';
require_once 'includes/auth_check.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Include header
include_once 'includes/header.php';
?>

<div class="container">
    <div class="search-container">
        <div class="logo-container">
            <img src="assets/img/filega-logo-small.png" style="height:100px;" alt="Filega" class="logo">
        </div>
        <form action="search.php" method="GET" class="search-form">
            <div class="search-input-container">
                <input type="text" name="q" id="search-input" class="search-input" placeholder="Search anything..." autocomplete="off" required>
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
        <div class="search-suggestions" id="search-suggestions"></div>
    </div>
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="recent-searches">
        <h3>Recent Searches</h3>
        <div class="recent-searches-list" id="recent-searches">
            <!-- Recent searches will be loaded here via AJAX -->
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="assets/js/main.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadRecentSearches();
    });
</script>

<?php include_once 'includes/footer.php'; ?> 