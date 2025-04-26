<?php
session_start();
require_once 'config/config.php';
require_once 'includes/auth_check.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

// Get user preferences
$stmt = $conn->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// If user has no preferences yet, insert default values
if ($result->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO user_preferences (user_id, theme, search_per_page, safe_search) VALUES (?, 'light', 10, 1)");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    // Get the default preferences
    $stmt = $conn->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
}

$preferences = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // Validate and sanitize inputs
    $theme = trim($_POST['theme']);
    $resultsPerPage = intval($_POST['results_per_page']);
    $safeSearch = isset($_POST['safe_search']) ? 1 : 0;
    
    // Validate theme
    if (!in_array($theme, ['light', 'dark', 'auto'])) {
        $theme = 'light';
    }
    
    // Validate results per page
    if ($resultsPerPage < 5 || $resultsPerPage > 50) {
        $resultsPerPage = 10;
    }
    
    // Update preferences
    $stmt = $conn->prepare("UPDATE user_preferences SET theme = ?, search_per_page = ?, safe_search = ? WHERE user_id = ?");
    $stmt->bind_param("siii", $theme, $resultsPerPage, $safeSearch, $userId);
    
    if ($stmt->execute()) {
        $success = 'Settings updated successfully';
        
        // Update preferences in local variable
        $preferences['theme'] = $theme;
        $preferences['search_per_page'] = $resultsPerPage;
        $preferences['safe_search'] = $safeSearch;
    } else {
        $error = 'Failed to update settings';
    }
}

// Set page title
$pageTitle = 'Settings';

// Include header
include_once 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Settings</h1>
        <p>Customize your search experience</p>
    </div>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="settings-container">
        <form action="settings.php" method="POST" class="settings-form">
            <div class="settings-section">
                <h2>Appearance</h2>
                
                <div class="form-group">
                    <label for="theme">Theme</label>
                    <select id="theme" name="theme" class="form-control">
                        <option value="light" <?php echo $preferences['theme'] === 'light' ? 'selected' : ''; ?>>Light</option>
                        <option value="dark" <?php echo $preferences['theme'] === 'dark' ? 'selected' : ''; ?>>Dark</option>
                        <option value="auto" <?php echo $preferences['theme'] === 'auto' ? 'selected' : ''; ?>>Auto (System default)</option>
                    </select>
                    <small class="form-text">Choose how Filega appears to you</small>
                </div>
                
                <div class="theme-preview">
                    <div class="theme-card light <?php echo $preferences['theme'] === 'light' ? 'active' : ''; ?>" data-theme="light">
                        <div class="theme-header"></div>
                        <div class="theme-body">
                            <div class="theme-search"></div>
                            <div class="theme-content">
                                <div class="theme-result"></div>
                                <div class="theme-result"></div>
                                <div class="theme-result"></div>
                            </div>
                        </div>
                        <div class="theme-name">Light</div>
                    </div>
                    
                    <div class="theme-card dark <?php echo $preferences['theme'] === 'dark' ? 'active' : ''; ?>" data-theme="dark">
                        <div class="theme-header"></div>
                        <div class="theme-body">
                            <div class="theme-search"></div>
                            <div class="theme-content">
                                <div class="theme-result"></div>
                                <div class="theme-result"></div>
                                <div class="theme-result"></div>
                            </div>
                        </div>
                        <div class="theme-name">Dark</div>
                    </div>
                    
                    <div class="theme-card auto <?php echo $preferences['theme'] === 'auto' ? 'active' : ''; ?>" data-theme="auto">
                        <div class="theme-header"></div>
                        <div class="theme-body">
                            <div class="theme-search"></div>
                            <div class="theme-content">
                                <div class="theme-result"></div>
                                <div class="theme-result"></div>
                                <div class="theme-result"></div>
                            </div>
                        </div>
                        <div class="theme-name">Auto</div>
                    </div>
                </div>
            </div>
            
            <div class="settings-section">
                <h2>Search Preferences</h2>
                
                <div class="form-group">
                    <label for="results_per_page">Results Per Page</label>
                    <select id="results_per_page" name="results_per_page" class="form-control">
                        <option value="5" <?php echo $preferences['search_per_page'] == 5 ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?php echo $preferences['search_per_page'] == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="20" <?php echo $preferences['search_per_page'] == 20 ? 'selected' : ''; ?>>20</option>
                        <option value="30" <?php echo $preferences['search_per_page'] == 30 ? 'selected' : ''; ?>>30</option>
                        <option value="50" <?php echo $preferences['search_per_page'] == 50 ? 'selected' : ''; ?>>50</option>
                    </select>
                    <small class="form-text">Number of search results to display per page</small>
                </div>
                
                <div class="form-group checkbox-group">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="safe_search" name="safe_search" <?php echo $preferences['safe_search'] ? 'checked' : ''; ?>>
                        <label for="safe_search">
                            <span class="checkbox-indicator"></span>
                            Enable SafeSearch
                        </label>
                    </div>
                    <small class="form-text">Filter out explicit content from search results</small>
                </div>
            </div>
            
            <div class="settings-section">
                <h2>Privacy Settings</h2>
                
                <div class="privacy-option">
                    <div class="privacy-info">
                        <h3>Search History</h3>
                        <p>Filega saves your search history to help you quickly access your previous searches.</p>
                    </div>
                    <a href="history.php" class="btn btn-outline">Manage History</a>
                </div>
                
                <div class="privacy-option">
                    <div class="privacy-info">
                        <h3>Bookmark Data</h3>
                        <p>Your bookmarked search results are stored to help you quickly access important information.</p>
                    </div>
                    <a href="bookmarks.php" class="btn btn-outline">Manage Bookmarks</a>
                </div>
            </div>
            
            <div class="settings-section form-actions">
                <button type="submit" name="update_settings" class="btn btn-primary">Save Settings</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Theme preview selection
    const themeCards = document.querySelectorAll('.theme-card');
    const themeSelect = document.getElementById('theme');
    
    themeCards.forEach(card => {
        card.addEventListener('click', function() {
            // Update select value
            themeSelect.value = this.getAttribute('data-theme');
            
            // Update active class
            themeCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>

<style>
.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    margin-bottom: 10px;
}

.page-header p {
    color: var(--text-secondary);
}

.settings-container {
    max-width: 800px;
    margin: 0 auto 60px;
}

.settings-section {
    background-color: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 8px var(--shadow-color);
    padding: 30px;
    margin-bottom: 30px;
}

.settings-section h2 {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.theme-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}

.theme-card {
    width: 200px;
    cursor: pointer;
    border-radius: 8px;
    border: 2px solid transparent;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.theme-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px var(--shadow-color);
}

.theme-card.active {
    border-color: var(--primary-color);
}

.theme-header {
    height: 30px;
}

.theme-body {
    padding: 10px;
}

.theme-search {
    height: 20px;
    border-radius: 10px;
    margin-bottom: 15px;
}

.theme-content {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.theme-result {
    height: 15px;
    border-radius: 3px;
}

.theme-name {
    text-align: center;
    padding: 10px;
    font-weight: 500;
}

/* Light theme card styles */
.theme-card.light {
    background-color: #ffffff;
    color: #202124;
}

.theme-card.light .theme-header {
    background-color: #f8f9fa;
}

.theme-card.light .theme-search {
    background-color: #e8eaed;
}

.theme-card.light .theme-result {
    background-color: #e8eaed;
}

/* Dark theme card styles */
.theme-card.dark {
    background-color: #202124;
    color: #e8eaed;
}

.theme-card.dark .theme-header {
    background-color: #303134;
}

.theme-card.dark .theme-search {
    background-color: #303134;
}

.theme-card.dark .theme-result {
    background-color: #303134;
}

/* Auto theme card styles */
.theme-card.auto {
    background: linear-gradient(to right, #ffffff 50%, #202124 50%);
}

.theme-card.auto .theme-header {
    background: linear-gradient(to right, #f8f9fa 50%, #303134 50%);
}

.theme-card.auto .theme-search {
    background: linear-gradient(to right, #e8eaed 50%, #303134 50%);
}

.theme-card.auto .theme-result {
    background: linear-gradient(to right, #e8eaed 50%, #303134 50%);
}

.theme-card.auto .theme-name {
    background: linear-gradient(to right, #ffffff 50%, #202124 50%);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    text-shadow: 0 0 2px rgba(255, 255, 255, 0.5);
}

.checkbox-group {
    margin-top: 25px;
}

.custom-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.custom-checkbox input[type="checkbox"] {
    display: none;
}

.checkbox-indicator {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid var(--border-color);
    border-radius: 4px;
    margin-right: 10px;
    position: relative;
    transition: all 0.2s ease;
}

.custom-checkbox input[type="checkbox"]:checked + label .checkbox-indicator {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.custom-checkbox input[type="checkbox"]:checked + label .checkbox-indicator::after {
    content: '';
    position: absolute;
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.privacy-option {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background-color: var(--hover-bg);
    border-radius: 8px;
    margin-bottom: 15px;
}

.privacy-option:last-child {
    margin-bottom: 0;
}

.privacy-info h3 {
    font-size: 1.125rem;
    margin-bottom: 5px;
}

.privacy-info p {
    color: var(--text-secondary);
    margin-bottom: 0;
    font-size: 0.875rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
}

@media (max-width: 768px) {
    .theme-preview {
        justify-content: center;
    }
    
    .privacy-option {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .privacy-info {
        margin-bottom: 15px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions button,
    .form-actions a {
        width: 100%;
    }
}
</style>

<?php include_once 'includes/footer.php'; ?> 