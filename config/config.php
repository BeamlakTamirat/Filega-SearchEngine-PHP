<?php
/**
 * Filega Search Engine Configuration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'filega_db');

// Google Custom Search API Configuration
define('GOOGLE_API_KEY', ''); // Replace with your actual API key
define('GOOGLE_SEARCH_ENGINE_ID', ''); // Replace with your search engine ID

// Application Configuration
define('APP_NAME', 'Filega');
define('APP_URL', 'http://localhost/Filega');
define('APP_VERSION', '1.0.0');

// Timezone Configuration
date_default_timezone_set('UTC');

// Error Reporting (Turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session Configuration - Only apply if session hasn't started yet
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
}

// Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");

/**
 * Escape output function
 * 
 * @param string $data The data to escape
 * @return string The escaped data
 */
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Get the base URL
 * 
 * @return string The base URL
 */
function baseUrl() {
    return APP_URL;
}

/**
 * Redirect to a given URL
 * 
 * @param string $url The URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
} 