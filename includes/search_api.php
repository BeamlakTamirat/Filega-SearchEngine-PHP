<?php
/**
 * Search API Integration
 * Uses Google Custom Search API to fetch search results
 */

/**
 * Get search results from Google API
 * 
 * @param string $query The search query
 * @param int $start The starting index for pagination (default: 1)
 * @return array The search results
 */
function getSearchResults($query, $start = 1) {
    // Load configuration
    require_once __DIR__ . '/../config/config.php';
    
    // Google Custom Search API endpoint
    $endpoint = 'https://www.googleapis.com/customsearch/v1';
    
    // Set API key and search engine ID from config
    $api_key = GOOGLE_API_KEY;
    $search_engine_id = GOOGLE_SEARCH_ENGINE_ID;
    
    // Build query parameters
    $params = [
        'key' => $api_key,
        'cx' => $search_engine_id,
        'q' => $query,
        'start' => $start
    ];
    
    // Build URL
    $url = $endpoint . '?' . http_build_query($params);
    
    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For development only
    
    // Execute cURL request
    $response = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        return [
            'error' => 'Failed to fetch search results. Please try again later.',
            'items' => []
        ];
    }
    
    // Close cURL session
    curl_close($ch);
    
    // Parse JSON response
    $data = json_decode($response, true);
    
    // Handle API errors
    if (isset($data['error'])) {
        error_log('Google API Error: ' . json_encode($data['error']));
        return [
            'error' => 'Failed to fetch search results. Please try again later.',
            'items' => []
        ];
    }
    
    // Return results
    return $data;
}

/**
 * Get trending searches
 * This is a placeholder function - in a real app, you would use a trending data source
 * 
 * @param int $limit The number of trending searches to return
 * @return array The trending searches
 */
function getTrendingSearches($limit = 5) {
    // In a real application, you might fetch this data from:
    // 1. Google Trends API
    // 2. A database of popular searches in your application
    // 3. A third-party trending API
    
    // For demonstration, we'll return static data
    $trending = [
        'Artificial Intelligence',
        'Renewable Energy',
        'Web Development',
        'Machine Learning',
        'Blockchain Technology',
        'Quantum Computing',
        'Sustainable Living',
        'Space Exploration',
        'Cybersecurity',
        'Virtual Reality'
    ];
    
    // Shuffle to get random trending topics
    shuffle($trending);
    
    // Return limited set
    return array_slice($trending, 0, $limit);
}

/**
 * Get search suggestions based on user input
 * This is a simplified version - in a real app, you would use a proper suggestion API
 * 
 * @param string $query The partial search query
 * @param int $limit The number of suggestions to return
 * @return array The search suggestions
 */
function getSearchSuggestions($query, $limit = 5) {
    // Common search prefixes and suffixes for demonstration
    $prefixes = ['how to', 'what is', 'where to', 'why is', 'when was'];
    $suffixes = ['tutorial', 'guide', 'examples', 'definition', 'meaning', 'best practices'];
    
    $suggestions = [];
    
    // Add prefix-based suggestions
    foreach ($prefixes as $prefix) {
        if (stripos($query, $prefix) === 0) {
            // Query starts with this prefix
            continue;
        }
        $suggestions[] = $prefix . ' ' . $query;
    }
    
    // Add suffix-based suggestions
    foreach ($suffixes as $suffix) {
        $suggestions[] = $query . ' ' . $suffix;
    }
    
    // In a real application, you would use:
    // 1. Google Suggestions API
    // 2. Historical user searches
    // 3. Database of common queries
    
    // Shuffle and limit results
    shuffle($suggestions);
    return array_slice($suggestions, 0, $limit);
} 