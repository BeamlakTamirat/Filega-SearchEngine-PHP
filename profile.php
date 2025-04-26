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

// Fetch user data
$stmt = $conn->prepare("SELECT id, name, email, created_at, last_login FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get user statistics
$stmt = $conn->prepare("SELECT COUNT(*) as search_count FROM search_history WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$searchStats = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("SELECT COUNT(*) as bookmark_count FROM bookmarks WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookmarkStats = $stmt->get_result()->fetch_assoc();

// Handle form submission to update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate name
    if (empty($name)) {
        $error = 'Name cannot be empty';
    } else {
        // Update name
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $userId);
        $stmt->execute();
        
        // If password change is requested
        if (!empty($currentPassword) && !empty($newPassword)) {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            if (password_verify($currentPassword, $result['password'])) {
                // Check if new passwords match
                if ($newPassword === $confirmPassword) {
                    // Validate password strength
                    if (strlen($newPassword) < 8) {
                        $error = 'Password must be at least 8 characters long';
                    } else {
                        // Update password
                        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $stmt->bind_param("si", $hashedPassword, $userId);
                        $stmt->execute();
                        
                        $success = 'Profile updated successfully with new password';
                        // Update session
                        $_SESSION['name'] = $name;
                    }
                } else {
                    $error = 'New passwords do not match';
                }
            } else {
                $error = 'Current password is incorrect';
            }
        } else {
            $success = 'Profile updated successfully';
            // Update session
            $_SESSION['name'] = $name;
        }
    }
    
    // Refresh user data after update
    $stmt = $conn->prepare("SELECT id, name, email, created_at, last_login FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Set page title
$pageTitle = 'My Profile';

// Include header
include_once 'includes/header.php';
?>

<div class="container">
    <div class="profile-header">
        <h1>My Profile</h1>
        <p>Manage your account information and settings</p>
    </div>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $searchStats['search_count']; ?></div>
                    <div class="stat-label">Searches</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $bookmarkStats['bookmark_count']; ?></div>
                    <div class="stat-label">Bookmarks</div>
                </div>
            </div>
            
            <div class="profile-info">
                <div class="info-item">
                    <span class="info-label">Member since:</span>
                    <span class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Last login:</span>
                    <span class="info-value">
                        <?php echo $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'N/A'; ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="profile-content">
            <div class="content-section">
                <h2>Edit Profile</h2>
                <form action="profile.php" method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        <small class="form-text">Email cannot be changed</small>
                    </div>
                    
                    <h3 class="form-section-title">Change Password</h3>
                    <p class="form-section-desc">Leave blank if you don't want to change your password</p>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control">
                        <small class="form-text">Password must be at least 8 characters long</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
            
            <div class="content-section danger-zone">
                <h2>Account Actions</h2>
                <div class="danger-zone-actions">
                    <div class="action-item">
                        <div class="action-info">
                            <h3>Clear Search History</h3>
                            <p>Delete all your search history records. This action cannot be undone.</p>
                        </div>
                        <a href="history.php" class="btn btn-outline">Manage History</a>
                    </div>
                    
                    <div class="action-item">
                        <div class="action-info">
                            <h3>Delete Account</h3>
                            <p>Permanently delete your account and all associated data.</p>
                        </div>
                        <button class="btn btn-danger" onclick="alert('This feature is not implemented in this demo.')">Delete Account</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-header {
    margin-bottom: 30px;
}

.profile-header h1 {
    margin-bottom: 10px;
}

.profile-header p {
    color: var(--text-secondary);
}

.profile-container {
    display: flex;
    gap: 30px;
    margin-bottom: 60px;
}

.profile-sidebar {
    flex: 0 0 300px;
}

.profile-avatar {
    text-align: center;
    background-color: var(--card-bg);
    padding: 30px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px var(--shadow-color);
    margin-bottom: 20px;
}

.profile-avatar i {
    font-size: 5rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.profile-avatar h3 {
    margin-bottom: 5px;
}

.profile-email {
    color: var(--text-secondary);
    margin-bottom: 0;
}

.profile-stats {
    display: flex;
    background-color: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 8px var(--shadow-color);
    margin-bottom: 20px;
}

.stat-item {
    flex: 1;
    text-align: center;
    padding: 15px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.stat-label {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.profile-info {
    background-color: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 8px var(--shadow-color);
    padding: 20px;
}

.info-item {
    margin-bottom: 15px;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-label {
    display: block;
    font-weight: 500;
    margin-bottom: 5px;
}

.info-value {
    color: var(--text-secondary);
}

.profile-content {
    flex: 1;
}

.content-section {
    background-color: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 8px var(--shadow-color);
    padding: 30px;
    margin-bottom: 30px;
}

.content-section h2 {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.profile-form .form-group {
    margin-bottom: 20px;
}

.form-section-title {
    font-size: 1.25rem;
    margin: 30px 0 10px;
}

.form-section-desc {
    color: var(--text-secondary);
    margin-bottom: 20px;
    font-size: 0.875rem;
}

.form-text {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-top: 5px;
}

.form-actions {
    margin-top: 30px;
}

.danger-zone h2 {
    color: var(--accent-color);
}

.danger-zone-actions {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.action-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background-color: var(--hover-bg);
    border-radius: 8px;
}

.action-info h3 {
    font-size: 1.125rem;
    margin-bottom: 5px;
}

.action-info p {
    color: var(--text-secondary);
    margin-bottom: 0;
    font-size: 0.875rem;
}

.btn-danger {
    background-color: var(--accent-color);
    color: white;
}

.btn-danger:hover {
    background-color: #c62828;
}

@media (max-width: 768px) {
    .profile-container {
        flex-direction: column;
    }
    
    .profile-sidebar {
        flex: none;
        width: 100%;
    }
    
    .action-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .action-info {
        margin-bottom: 15px;
    }
}
</style>

<?php include_once 'includes/footer.php'; ?> 