<?php
// Database Setup Script - Creates database and tables automatically

echo "<h2> Database Setup</h2>";
echo "<p>This script will create the database and tables for the AI Chat Application.</p>";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'chat_app';

try {
    // Connect to MySQL without specifying database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Connected to MySQL server</p>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✅ Database '$dbname' created or already exists</p>";
    
    // Select the database
    $pdo->exec("USE `$dbname`");
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) UNIQUE NOT NULL,
            `email` VARCHAR(100) UNIQUE NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✅ Users table created</p>";
    
    // Create sessions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `sessions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `session_id` VARCHAR(255) UNIQUE NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✅ Sessions table created</p>";
    
    // Create messages table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `session_id` INT NOT NULL,
            `user_message` TEXT NOT NULL,
            `ai_response` TEXT NOT NULL,
            `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✅ Messages table created</p>";
    
    // Create indexes
    $pdo->exec("CREATE INDEX IF NOT EXISTS `idx_sessions_user_id` ON `sessions`(`user_id`)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS `idx_messages_session_id` ON `messages`(`session_id`)");
    echo "<p style='color: green;'>✅ Database indexes created</p>";
    
    // Test the setup
    echo "<h3>🧪 Testing Database Setup</h3>";
    
    // Check if all tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $expectedTables = ['users', 'sessions', 'messages'];
    
    foreach ($expectedTables as $table) {
        if (in_array($table, $tables)) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' missing</p>";
        }
    }
    
    echo "<h3>🎉 Setup Complete!</h3>";
    echo "<p style='color: green; font-weight: bold;'>Database and tables have been created successfully!</p>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li><a href='index.php'>Go to the application</a></li>";
    echo "<li><a href='test-database.php'>Run database tests</a></li>";
    echo "<li><a href='setup-database.php'>Run setup again</a></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Database Error: " . $e->getMessage() . "</p>";
    
    echo "<h3>Troubleshooting:</h3>";
    echo "<p><strong>Common issues:</strong></p>";
    echo "<ul>";
    echo "<li>MySQL server not running - Start MySQL in XAMPP Control Panel</li>";
    echo "<li>Wrong credentials - Check username/password (default: root/empty)</li>";
    echo "<li>Port conflict - MySQL might be on different port</li>";
    echo "</ul>";
    
    echo "<p><strong>Try these commands:</strong></p>";
    echo "<p>1. Open XAMPP Control Panel</p>";
    echo "<p>2. Start Apache and MySQL services</p>";
    echo "<p>3. Run this script again</p>";
}

echo "<hr>";
echo "<p><small>Setup script completed at: " . date('Y-m-d H:i:s') . "</small></p>";
?>
