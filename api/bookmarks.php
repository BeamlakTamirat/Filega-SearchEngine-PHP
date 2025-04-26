<?php
/**
 * Bookmarks API
 * Provides endpoints for managing user bookmarks
 */

header('Content-Type: application/json');
session_start();
require_once '../config/config.php';
require_once '../includes/auth_check.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = getCurrentUserId();
$action = isset($_GET['action']) ? $_GET['action'] : 'get';

switch ($action) {
    case 'get':
        // Get bookmarks
        $stmt = $conn->prepare("
            SELECT id, title, url, description, created_at 
            FROM bookmarks 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookmarks = [];
        while ($row = $result->fetch_assoc()) {
            $bookmarks[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'url' => $row['url'],
                'description' => $row['description'],
                'created_at' => $row['created_at']
            ];
        }
        
        echo json_encode(['success' => true, 'bookmarks' => $bookmarks]);
        break;
        
    case 'add':
        // Add a bookmark
        if (!isset($_POST['title']) || !isset($_POST['url'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        
        $title = trim($_POST['title']);
        $url = trim($_POST['url']);
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        
        if (empty($title) || empty($url)) {
            http_response_code(400);
            echo json_encode(['error' => 'Title and URL are required']);
            exit;
        }
        
        // Check if URL is already bookmarked
        $stmt = $conn->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND url = ?");
        $stmt->bind_param("is", $userId, $url);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing bookmark
            $bookmarkId = $result->fetch_assoc()['id'];
            $stmt = $conn->prepare("UPDATE bookmarks SET title = ?, description = ?, created_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssi", $title, $description, $bookmarkId);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Bookmark updated', 'id' => $bookmarkId]);
        } else {
            // Insert new bookmark
            $stmt = $conn->prepare("INSERT INTO bookmarks (user_id, title, url, description, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("isss", $userId, $title, $url, $description);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Bookmark added', 'id' => $conn->insert_id]);
        }
        break;
        
    case 'delete':
        // Delete a bookmark
        if (!isset($_POST['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing bookmark ID']);
            exit;
        }
        
        $bookmarkId = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM bookmarks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $bookmarkId, $userId);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Bookmark deleted']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Bookmark not found']);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
} 