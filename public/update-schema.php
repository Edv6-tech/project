<?php
// Database Schema Update for Google OAuth
echo "<h2>Database Schema Update</h2>";

try {
    require_once __DIR__ . '/../config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    echo "<h3>Current Database Status</h3>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'chat_app'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>Database 'chat_app' exists</p>";
    } else {
        echo "<p style='color: red;'>Database 'chat_app' not found</p>";
        echo "<p>Creating database...</p>";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS chat_app");
        echo "<p style='color: green;'>Database created</p>";
    }
    
    // Use the database
    $pdo->exec("USE chat_app");
    
    echo "<h3>Updating Users Table</h3>";
    
    // Check if columns exist
    $columns = ['google_id', 'profile_picture', 'auth_method', 'updated_at'];
    $updates = [];
    
    foreach ($columns as $column) {
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            $updates[] = $column;
        } else {
            echo "<p style='color: green;'>Column '$column' already exists</p>";
        }
    }
    
    // Add missing columns
    if (in_array('google_id', $updates)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL");
        echo "<p style='color: green;'>Added google_id column</p>";
    }
    
    if (in_array('profile_picture', $updates)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN profile_picture VARCHAR(500) NULL");
        echo "<p style='color: green;'>Added profile_picture column</p>";
    }
    
    if (in_array('auth_method', $updates)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN auth_method ENUM('email', 'google') DEFAULT 'email'");
        echo "<p style='color: green;'>Added auth_method column</p>";
    }
    
    if (in_array('updated_at', $updates)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "<p style='color: green;'>Added updated_at column</p>";
    }
    
    echo "<h3>Creating Indexes</h3>";
    
    // Check indexes
    $indexes = ['idx_users_google_id', 'idx_users_email'];
    
    foreach ($indexes as $index) {
        $stmt = $pdo->query("SHOW INDEX FROM users WHERE Key_name = '$index'");
        if ($stmt->rowCount() == 0) {
            if ($index === 'idx_users_google_id') {
                $pdo->exec("CREATE INDEX idx_users_google_id ON users(google_id)");
                echo "<p style='color: green;'>Created google_id index</p>";
            } elseif ($index === 'idx_users_email') {
                $pdo->exec("CREATE INDEX idx_users_email ON users(email)");
                echo "<p style='color: green;'>Created email index</p>";
            }
        } else {
            echo "<p style='color: green;'>Index '$index' already exists</p>";
        }
    }
    
    echo "<h3>Final Table Structure</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Test Google User Creation</h3>";
    $testGoogleId = '123456789';
    $testEmail = 'test@gmail.com';
    $testName = 'Test User';
    $testPicture = 'https://example.com/pic.jpg';
    
    // Check if test user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
    $stmt->execute([$testGoogleId]);
    
    if ($stmt->rowCount() == 0) {
        // Create test Google user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, google_id, profile_picture, auth_method) VALUES (?, ?, ?, ?, ?, ?)");
        $username = explode('@', $testEmail)[0] . '_' . substr($testGoogleId, -6);
        $passwordHash = password_hash(uniqid(), PASSWORD_DEFAULT);
        $stmt->execute([$username, $testEmail, $passwordHash, $testGoogleId, $testPicture, 'google']);
        echo "<p style='color: green;'>Test Google user created successfully</p>";
    } else {
        echo "<p style='color: orange;'>Test Google user already exists</p>";
    }
    
    echo "<h3>Verification</h3>";
    $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
    $stmt->execute([$testGoogleId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p style='color: green;'>Google user verification successful!</p>";
        echo "<ul>";
        echo "<li>ID: " . htmlspecialchars($user['id']) . "</li>";
        echo "<li>Username: " . htmlspecialchars($user['username']) . "</li>";
        echo "<li>Email: " . htmlspecialchars($user['email']) . "</li>";
        echo "<li>Google ID: " . htmlspecialchars($user['google_id']) . "</li>";
        echo "<li>Auth Method: " . htmlspecialchars($user['auth_method']) . "</li>";
        echo "</ul>";
    }
    
    echo "<h3>Next Steps</h3>";
    echo "<ol>";
    echo "<li>Update .env with your Google OAuth credentials</li>";
    echo "<li>Test Google authentication at login.php</li>";
    echo "<li>Verify JWT token generation</li>";
    echo "</ol>";
    
    echo "<p style='color: green; font-weight: bold;'>Database schema updated successfully!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
