<?php
require_once 'config/config.php';
require_once 'config/db.php';

echo "<h1>System Diagnostic</h1>";

// Test 1: Check Configuration
echo "<h2>1. Configuration Check</h2>";
echo "<table border='1'>";
echo "<tr><th>Setting</th><th>Status</th></tr>";

$settings = [
    'GOOGLE_CLIENT_ID' => defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID ? "✓ SET" : "✗ NOT SET",
    'GOOGLE_CLIENT_SECRET' => defined('GOOGLE_CLIENT_SECRET') && GOOGLE_CLIENT_SECRET ? "✓ SET" : "✗ NOT SET",
    'GOOGLE_REDIRECT_URI' => defined('GOOGLE_REDIRECT_URI') && GOOGLE_REDIRECT_URI ? "✓ SET" : "✗ NOT SET",
    'JWT_SECRET' => defined('JWT_SECRET') && JWT_SECRET ? "✓ SET" : "✗ NOT SET",
    'DB_HOST' => "localhost",
    'DB_NAME' => "chat_app",
];

foreach ($settings as $key => $val) {
    echo "<tr><td>$key</td><td>$val</td></tr>";
}
echo "</table>";

// Test 2: Database
echo "<h2>2. Database Check</h2>";
try {
    $count = $pdo->query("SELECT COUNT(*) as cnt FROM users")->fetch()['cnt'];
    echo "<p style='color: green;'>✓ Database connected. Users: $count</p>";
    
    // Check columns
    $result = $pdo->query("DESCRIBE users");
    $columns = array_map(fn($r) => $r['Field'], $result->fetchAll(PDO::FETCH_ASSOC));
    echo "<p>Columns: " . implode(", ", $columns) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 3: Google Service
echo "<h2>3. Google Auth Service</h2>";
try {
    require_once 'src/GoogleAuthService.php';
    $auth = new GoogleAuthService();
    echo "<p style='color: green;'>✓ GoogleAuthService initialized</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 4: Callback
echo "<h2>4. OAuth Callback Test</h2>";
if (isset($_GET['code'])) {
    echo "<p>Code received: " . htmlspecialchars(substr($_GET['code'], 0, 30)) . "...</p>";
    try {
        $auth = new GoogleAuthService();
        // We can't call private method, but let's note that the service is ready
        echo "<p style='color: green;'>✓ Ready to process callback</p>";
        echo "<p><a href='/project/public/google-callback.php?code=" . urlencode($_GET['code']) . "'>Process callback</a></p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>No code yet. <a href='https://accounts.google.com/o/oauth2/v2/auth?client_id=" . urlencode(GOOGLE_CLIENT_ID) . "&redirect_uri=" . urlencode('http://localhost/project/test-database.php') . "&response_type=code&scope=email+profile'>Start OAuth Flow</a></p>";
}
?>

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$testUser['username'], $testUser['email'], $testUser['password_hash']]);
        $userId = $pdo->lastInsertId();
        echo "<p style='color: green;'>✅ User creation test passed! User ID: $userId</p>";
        
        // Test session creation
        $sessionId = session_id();
        $stmt = $pdo->prepare("INSERT INTO sessions (user_id, session_id) VALUES (?, ?)");
        $stmt->execute([$userId, $sessionId]);
        $sessionIdDb = $pdo->lastInsertId();
        echo "<p style='color: green;'>✅ Session creation test passed! Session ID: $sessionIdDb</p>";
        
        // Test message creation
        $stmt = $pdo->prepare("INSERT INTO messages (session_id, user_message, ai_response) VALUES (?, ?, ?)");
        $stmt->execute([$sessionIdDb, 'Hello test', 'Test response']);
        $messageId = $pdo->lastInsertId();
        echo "<p style='color: green;'>✅ Message creation test passed! Message ID: $messageId</p>";
        
        // Clean up test data
        $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([$messageId]);
        $pdo->prepare("DELETE FROM sessions WHERE id = ?")->execute([$sessionIdDb]);
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
        echo "<p style='color: blue;'>✅ Test data cleaned up</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Operation test failed: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h3>Database Configuration:</h3>";
echo "<p>Host: localhost</p>";
echo "<p>Database: chat_app</p>";
echo "<p>Username: root</p>";
echo "<p>Password: (empty)</p>";

echo "<h3>Next Steps:</h3>";
echo "<p>If any tests failed, run the schema.sql file in phpMyAdmin:</p>";
echo "<p>1. Open phpMyAdmin (http://localhost/phpmyadmin)</p>";
echo "<p>2. Click 'Import' tab</p>";
echo "<p>3. Select 'database/schema.sql' file</p>";
echo "<p>4. Click 'Go' button</p>";
?>
