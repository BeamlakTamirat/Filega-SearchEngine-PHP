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

// Handle bookmark deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $bookmarkId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM bookmarks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $bookmarkId, $userId);
    $stmt->execute();
    
    // Redirect to remove the query parameter
    header('Location: bookmarks.php?deleted=1');
    exit;
}

// Get all bookmarks for the user
$stmt = $conn->prepare("SELECT id, title, url, description, created_at FROM bookmarks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Set page title
$pageTitle = 'My Bookmarks';

// Include header
include_once 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>My Bookmarks</h1>
        <p>Saved search results and websites for quick access</p>
    </div>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Bookmark deleted successfully.</div>
    <?php endif; ?>
    
    <div class="bookmarks-container">
        <?php if ($result->num_rows > 0): ?>
            <div class="bookmarks-grid">
                <?php while ($bookmark = $result->fetch_assoc()): ?>
                    <div class="bookmark-card">
                        <div class="bookmark-actions">
                            <a href="<?php echo htmlspecialchars($bookmark['url']); ?>" target="_blank" class="btn-icon" title="Open link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <a href="bookmarks.php?delete=<?php echo $bookmark['id']; ?>" class="btn-icon delete-btn" title="Delete bookmark" onclick="return confirm('Are you sure you want to delete this bookmark?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                        
                        <h3 class="bookmark-title">
                            <a href="<?php echo htmlspecialchars($bookmark['url']); ?>" target="_blank">
                                <?php echo htmlspecialchars($bookmark['title']); ?>
                            </a>
                        </h3>
                        
                        <div class="bookmark-url"><?php echo htmlspecialchars(parse_url($bookmark['url'], PHP_URL_HOST)); ?></div>
                        
                        <?php if (!empty($bookmark['description'])): ?>
                            <div class="bookmark-description"><?php echo htmlspecialchars($bookmark['description']); ?></div>
                        <?php endif; ?>
                        
                        <div class="bookmark-date">
                            Saved on <?php echo date('M d, Y', strtotime($bookmark['created_at'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-bookmark"></i>
                </div>
                <h2>No Bookmarks Yet</h2>
                <p>When you find useful search results, bookmark them to access them quickly later.</p>
                <a href="index.php" class="btn btn-primary">Start Searching</a>
            </div>
        <?php endif; ?>
    </div>
</div>

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

.bookmarks-container {
    margin-bottom: 60px;
}

.bookmarks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.bookmark-card {
    background-color: var(--card-bg);
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px var(--shadow-color);
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.bookmark-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px var(--shadow-color);
}

.bookmark-actions {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    gap: 10px;
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background-color: var(--hover-bg);
    color: var(--text-color);
    border-radius: 4px;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    text-decoration: none;
    background-color: var(--primary-color);
    color: white;
}

.delete-btn:hover {
    background-color: var(--accent-color);
}

.bookmark-title {
    font-size: 1.25rem;
    margin-bottom: 10px;
    padding-right: 80px; /* Space for action buttons */
}

.bookmark-title a {
    color: var(--primary-color);
}

.bookmark-url {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 15px;
}

.bookmark-description {
    margin-bottom: 15px;
    font-size: 0.9375rem;
    line-height: 1.5;
    color: var(--text-color);
    /* Limit to 3 lines of text */
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.bookmark-date {
    color: var(--text-secondary);
    font-size: 0.8125rem;
    border-top: 1px solid var(--border-color);
    padding-top: 10px;
    margin-top: 10px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background-color: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 8px var(--shadow-color);
}

.empty-state-icon {
    font-size: 3rem;
    color: var(--text-secondary);
    margin-bottom: 20px;
}

.empty-state h2 {
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--text-secondary);
    margin-bottom: 20px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 768px) {
    .bookmarks-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include_once 'includes/footer.php'; ?> 