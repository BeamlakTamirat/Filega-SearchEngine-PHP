<?php
/**
 * Search History API
 * Provides endpoints for managing user search history
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
        // Get recent search history
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $stmt = $conn->prepare("
            SELECT id, query, search_date 
            FROM search_history 
            WHERE user_id = ? 
            ORDER BY search_date DESC 
            LIMIT ?
        ");
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = [
                'id' => $row['id'],
                'query' => $row['query'],
                'search_date' => $row['search_date']
            ];
        }
        
        echo json_encode(['success' => true, 'history' => $history]);
        break;
        
    case 'delete':
        // Delete a specific search from history
        if (!isset($_POST['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing search ID']);
            exit;
        }
        
        $searchId = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM search_history WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $searchId, $userId);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Search history item deleted']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Search history item not found']);
        }
        break;
        
    case 'clear':
        // Clear all search history for current user
        $stmt = $conn->prepare("DELETE FROM search_history WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Search history cleared']);
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
} 