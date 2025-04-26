<?php
session_start();
require_once 'config/config.php';
require_once 'includes/auth_check.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// If query is empty, redirect to home
if (empty($query)) {
    header('Location: index.php');
    exit;
}

// Save search to history
if (isset($_SESSION['user_id']) && !empty($query)) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO search_history (user_id, query, search_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $userId, $query);
    $stmt->execute();
}

// Fetch search results from Google API
require_once 'includes/search_api.php';
$results = getSearchResults($query);

// Include header
include_once 'includes/header.php';
?>

<div class="container search-results-container" >
    <div class="search-header">
        <div class="logo-container-small">
            <a href="index.php">
                <img src="assets/img/filega-logo-small.png" style="width:70px;height:60px;margin-top:7px;" alt="Filega" class="logo-small">
            </a>
        </div>
        <form action="search.php" method="GET" class="search-form-results">
            <div class="search-input-container-results">
                <input type="text" name="q" id="search-input-results" class="search-input-results" 
                    value="<?php echo htmlspecialchars($query); ?>" autocomplete="off" required>
                <button type="submit" class="search-button-results">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="results-stats">
        <?php if (isset($results['searchInformation']['totalResults'])): ?>
            About <?php echo number_format($results['searchInformation']['totalResults']); ?> results 
            (<?php echo $results['searchInformation']['searchTime']; ?> seconds)
        <?php endif; ?>
    </div>

    <div class="search-results">
        <?php if (isset($results['items']) && !empty($results['items'])): ?>
            <?php foreach ($results['items'] as $item): ?>
                <?php 
                    // Extract domain for favicon
                    $domain = parse_url($item['link'], PHP_URL_HOST);
                ?>
                <div class="result-card">
                    <div class="result-url">
                        <img src="https://www.google.com/s2/favicons?domain=<?php echo urlencode($domain); ?>" alt="" class="favicon">
                        <?php echo htmlspecialchars($item['displayLink']); ?>
                    </div>
                    <h3 class="result-title">
                        <a href="<?php echo htmlspecialchars($item['link']); ?>" target="_blank">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </a>
                    </h3>
                    <div class="result-snippet"><?php echo $item['htmlSnippet']; ?></div>
                    <button class="bookmark-btn" onclick="saveBookmark('<?php echo addslashes(htmlspecialchars($item['title'])); ?>', '<?php echo addslashes(htmlspecialchars($item['link'])); ?>', '<?php echo addslashes(htmlspecialchars(strip_tags($item['snippet']))); ?>', this)">
                        <i class="fas fa-bookmark"></i> Save
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <h2>No results found for "<?php echo htmlspecialchars($query); ?>"</h2>
                <p>Try different keywords or check your spelling.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="pagination">
        <?php if (isset($results['queries']['previousPage'])): ?>
            <a href="search.php?q=<?php echo urlencode($query); ?>&start=<?php echo $results['queries']['previousPage'][0]['startIndex']; ?>" class="pagination-btn prev-btn">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
        <?php endif; ?>
        
        <?php if (isset($results['queries']['nextPage'])): ?>
            <a href="search.php?q=<?php echo urlencode($query); ?>&start=<?php echo $results['queries']['nextPage'][0]['startIndex']; ?>" class="pagination-btn next-btn">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/main.js"></script>

<?php include_once 'includes/footer.php'; ?> 