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

// Handle clear history action
if (isset($_POST['clear_history'])) {
    $stmt = $conn->prepare("DELETE FROM search_history WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    // Redirect to same page to prevent form resubmission
    header('Location: history.php?cleared=1');
    exit;
}

// Get pagination parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get total count for pagination
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM search_history WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$totalResult = $stmt->get_result()->fetch_assoc();
$totalRecords = $totalResult['total'];
$totalPages = ceil($totalRecords / $perPage);

// Get search history with pagination
$stmt = $conn->prepare("
    SELECT id, query, search_date 
    FROM search_history 
    WHERE user_id = ? 
    ORDER BY search_date DESC 
    LIMIT ?, ?
");
$stmt->bind_param("iii", $userId, $offset, $perPage);
$stmt->execute();
$result = $stmt->get_result();

// Set page title
$pageTitle = 'Search History';

// Include header
include_once 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Search History</h1>
        <p>View and manage your search history</p>
    </div>
    
    <?php if (isset($_GET['cleared'])): ?>
        <div class="alert alert-success">Your search history has been cleared.</div>
    <?php endif; ?>
    
    <div class="history-actions">
        <form action="history.php" method="POST" onsubmit="return confirm('Are you sure you want to clear your entire search history?');">
            <button type="submit" name="clear_history" class="btn btn-danger">
                <i class="fas fa-trash"></i> Clear All History
            </button>
        </form>
    </div>
    
    <div class="history-container">
        <?php if ($result->num_rows > 0): ?>
            <div class="history-list">
                <div class="history-header">
                    <div class="history-col query-col">Search Query</div>
                    <div class="history-col date-col">Date & Time</div>
                    <div class="history-col actions-col">Actions</div>
                </div>
                
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="history-item" data-id="<?php echo $row['id']; ?>">
                        <div class="history-col query-col">
                            <a href="search.php?q=<?php echo urlencode($row['query']); ?>"><?php echo htmlspecialchars($row['query']); ?></a>
                        </div>
                        <div class="history-col date-col">
                            <?php echo date('M d, Y g:i A', strtotime($row['search_date'])); ?>
                        </div>
                        <div class="history-col actions-col">
                            <button class="btn-icon delete-history" data-id="<?php echo $row['id']; ?>" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button class="btn-icon search-again" data-query="<?php echo htmlspecialchars($row['query']); ?>" title="Search Again">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <div class="pagination-container">
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="history.php?page=<?php echo $page - 1; ?>" class="pagination-btn prev-btn">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        // Calculate range of page numbers to show
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        // Always show first page
                        if ($startPage > 1) {
                            echo '<a href="history.php?page=1" class="pagination-number">1</a>';
                            if ($startPage > 2) {
                                echo '<span class="pagination-ellipsis">...</span>';
                            }
                        }
                        
                        // Show page numbers in range
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            if ($i == $page) {
                                echo '<span class="pagination-number active">' . $i . '</span>';
                            } else {
                                echo '<a href="history.php?page=' . $i . '" class="pagination-number">' . $i . '</a>';
                            }
                        }
                        
                        // Always show last page
                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<span class="pagination-ellipsis">...</span>';
                            }
                            echo '<a href="history.php?page=' . $totalPages . '" class="pagination-number">' . $totalPages . '</a>';
                        }
                        ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="history.php?page=<?php echo $page + 1; ?>" class="pagination-btn next-btn">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h2>No Search History</h2>
                <p>Your search history will appear here once you start searching.</p>
                <a href="index.php" class="btn btn-primary">Go to Search</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete history item
    const deleteButtons = document.querySelectorAll('.delete-history');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const historyItem = this.closest('.history-item');
            
            if (confirm('Are you sure you want to delete this search from your history?')) {
                deleteSearchHistoryItem(id, historyItem);
            }
        });
    });
    
    // Search again buttons
    const searchAgainButtons = document.querySelectorAll('.search-again');
    searchAgainButtons.forEach(button => {
        button.addEventListener('click', function() {
            const query = this.getAttribute('data-query');
            window.location.href = `search.php?q=${encodeURIComponent(query)}`;
        });
    });
});

function deleteSearchHistoryItem(id, element) {
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('api/search_history.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            element.remove();
            
            // Check if there are no more items
            const historyList = document.querySelector('.history-list');
            if (historyList && document.querySelectorAll('.history-item').length === 0) {
                location.reload(); // Reload page to show empty state
            }
        } else {
            alert('Failed to delete search history item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the history item');
    });
}
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

.history-actions {
    margin-bottom: 20px;
    text-align: right;
}

.history-container {
    background-color: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 1px 6px var(--shadow-color);
    overflow: hidden;
}

.history-list {
    width: 100%;
}

.history-header {
    display: flex;
    padding: 15px 20px;
    background-color: var(--hover-bg);
    border-bottom: 1px solid var(--border-color);
    font-weight: 500;
    color: var(--text-secondary);
}

.history-item {
    display: flex;
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.2s ease;
}

.history-item:hover {
    background-color: var(--hover-bg);
}

.history-item:last-child {
    border-bottom: none;
}

.history-col {
    padding: 0 10px;
}

.query-col {
    flex: 1;
    min-width: 0; /* Allow text truncation */
}

.query-col a {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.date-col {
    width: 200px;
    color: var(--text-secondary);
}

.actions-col {
    width: 100px;
    text-align: right;
}

.btn-icon {
    background: transparent;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 6px;
    margin-left: 5px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    background-color: var(--hover-bg);
    color: var(--primary-color);
}

.delete-history:hover {
    color: var(--accent-color);
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
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
}

.pagination-container {
    padding: 20px;
    border-top: 1px solid var(--border-color);
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
}

.pagination-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 4px;
    color: var(--text-color);
    text-decoration: none;
    transition: all 0.2s ease;
}

.pagination-number:hover {
    background-color: var(--hover-bg);
    text-decoration: none;
}

.pagination-number.active {
    background-color: var(--primary-color);
    color: white;
}

.pagination-ellipsis {
    padding: 0 5px;
    color: var(--text-secondary);
}

@media (max-width: 768px) {
    .history-header {
        display: none;
    }
    
    .history-item {
        flex-direction: column;
        padding: 15px;
    }
    
    .history-col {
        padding: 5px 0;
    }
    
    .date-col {
        width: 100%;
        font-size: 0.875rem;
    }
    
    .actions-col {
        width: 100%;
        text-align: left;
        margin-top: 10px;
    }
    
    .btn-icon {
        margin-left: 0;
        margin-right: 10px;
    }
}
</style>

<?php include_once 'includes/footer.php'; ?> 