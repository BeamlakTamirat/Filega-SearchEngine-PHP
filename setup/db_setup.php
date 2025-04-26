<?php
/**
 * Database Setup Script for Filega Search Engine
 * 
 * This script creates the necessary database and tables for the application.
 * Run this script once to set up the database.
 */

// Load configuration
require_once '../config/config.php';

// Create database if not exists
$conn_init = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn_init->connect_error) {
    die("Connection failed: " . $conn_init->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn_init->query($sql) === FALSE) {
    die("Error creating database: " . $conn_init->error);
}
$conn_init->close();

// Create tables
$tables = [
    // Users table
    "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `reset_token` VARCHAR(64) NULL DEFAULT NULL,
        `reset_expiry` DATETIME NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL,
        `last_login` DATETIME NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    // Search history table
    "CREATE TABLE IF NOT EXISTS `search_history` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) UNSIGNED NOT NULL,
        `query` VARCHAR(255) NOT NULL,
        `search_date` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    // Bookmarks table for saving search results
    "CREATE TABLE IF NOT EXISTS `bookmarks` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) UNSIGNED NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `url` VARCHAR(1024) NOT NULL,
        `description` TEXT NULL,
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    // User preferences table
    "CREATE TABLE IF NOT EXISTS `user_preferences` (
        `user_id` INT(11) UNSIGNED NOT NULL,
        `theme` ENUM('light', 'dark', 'auto') NOT NULL DEFAULT 'light',
        `search_per_page` INT(2) NOT NULL DEFAULT 10,
        `safe_search` BOOLEAN NOT NULL DEFAULT 1,
        PRIMARY KEY (`user_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

// Execute each table creation query
$success = true;
$errors = [];

foreach ($tables as $sql) {
    if ($conn->query($sql) === FALSE) {
        $success = false;
        $errors[] = $conn->error;
    }
}

// Create a demo admin user with email 'admin@example.com' and password 'password123'
$password_hash = password_hash('password123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT IGNORE INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
$name = "Admin User";
$email = "admin@example.com";
$stmt->bind_param("sss", $name, $email, $password_hash);
$stmt->execute();

// Output the result
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Filega</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        ul {
            padding-left: 20px;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
        .button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h1>Filega Database Setup</h1>
    
    <?php if ($success && empty($errors)): ?>
        <div class="success">
            <strong>Success!</strong> The database and all required tables have been created successfully.
        </div>
        
        <div class="info">
            <strong>Demo User Created:</strong>
            <ul>
                <li>Email: admin@example.com</li>
                <li>Password: password123</li>
            </ul>
            <p>You can use these credentials to log in to the application.</p>
        </div>
        
        <a href="../index.php" class="button">Go to Homepage</a>
    <?php else: ?>
        <div class="error">
            <strong>Error!</strong> There were problems setting up the database:
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <p>Please check your database configuration and try again.</p>
    <?php endif; ?>
</body>
</html> 