<?php
// Simple table creation script

echo "<h2>🔧 Creating Database Tables</h2>";

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=localhost", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Select database
    $pdo->exec("USE chat_app");
    echo "<p style='color: green;'>✅ Connected to chat_app database</p>";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Users table created</p>";
    
    // Create sessions table
    $sql = "CREATE TABLE IF NOT EXISTS sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        session_id VARCHAR(255) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Sessions table created</p>";
    
    // Create messages table
    $sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id INT NOT NULL,
        user_message TEXT NOT NULL,
        ai_response TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Messages table created</p>";
    
    // Create indexes
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_sessions_user_id ON sessions(user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_messages_session_id ON messages(session_id)");
    echo "<p style='color: green;'>✅ Indexes created</p>";
    
    // Show table list
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>📋 Tables in chat_app:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li style='color: blue;'>$table</li>";
    }
    echo "</ul>";
    
    echo "<h3>🎉 Success!</h3>";
    echo "<p>All database tables have been created successfully!</p>";
    echo "<p><a href='index.php' style='color: green; font-weight: bold;'>Go to Application →</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Error: " . $e->getMessage() . "</p>";
    
    echo "<h3>Troubleshooting:</h3>";
    echo "<p>1. Make sure MySQL is running in XAMPP</p>";
    echo "<p>2. Make sure database 'chat_app' exists</p>";
    echo "<p>3. Check MySQL credentials (root/empty)</p>";
}
?>
