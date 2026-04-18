<?php
require_once 'config/db.php';

echo "<h2>Adding Google OAuth Support to Database</h2>";

try {
    // Check if google_id column exists
    $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'google_id'");
    if ($result->rowCount() === 0) {
        echo "<p>Adding google_id column...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) UNIQUE NULL AFTER email");
        echo "<p style='color: green;'>✓ Added google_id column</p>";
    } else {
        echo "<p style='color: orange;'>google_id column already exists</p>";
    }

    // Check if auth_method column exists
    $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'auth_method'");
    if ($result->rowCount() === 0) {
        echo "<p>Adding auth_method column...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN auth_method ENUM('email', 'google') DEFAULT 'email' AFTER google_id");
        echo "<p style='color: green;'>✓ Added auth_method column</p>";
    } else {
        echo "<p style='color: orange;'>auth_method column already exists</p>";
    }

    // Verify
    echo "<h3>Updated Table Structure</h3>";
    $result = $pdo->query("DESCRIBE users");
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    foreach ($result as $row) {
        echo "<tr><td>" . htmlspecialchars($row['Field']) . "</td><td>" . htmlspecialchars($row['Type']) . "</td></tr>";
    }
    echo "</table>";

    echo "<p style='color: green;'><strong>✓ Database updated successfully!</strong></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
