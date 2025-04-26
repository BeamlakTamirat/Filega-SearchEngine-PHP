<?php
/**
 * Auth Check
 * 
 * This file is included in pages that require authentication.
 * It checks if the user is logged in and updates the last login time.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user ID
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Get current user name
function getCurrentUserName() {
    return isset($_SESSION['name']) ? $_SESSION['name'] : null;
}

// Get current user email
function getCurrentUserEmail() {
    return isset($_SESSION['email']) ? $_SESSION['email'] : null;
}

// Update last login time for user (called when user logs in)
function updateLastLogin($userId) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
} 