<?php
/**
 * Search Suggestions API
 * Provides search suggestions as the user types
 */

header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../includes/search_api.php';

// Get query parameter
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// If query is empty or too short, return empty result
if (empty($query) || strlen($query) < 2) {
    echo json_encode(['success' => true, 'suggestions' => []]);
    exit;
}

// Get suggestions from API
$suggestions = getSearchSuggestions($query);

// Return suggestions as JSON
echo json_encode([
    'success' => true,
    'query' => $query,
    'suggestions' => $suggestions
]); 