<?php
require_once 'config/db.php';

try {
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "\n";

    // Check if chats table exists
    if (in_array('chats', $tables)) {
        echo "✅ chats table exists\n";
    } else {
        echo "❌ chats table missing\n";
    }

    // Check if messages table exists
    if (in_array('messages', $tables)) {
        echo "✅ messages table exists\n";
    } else {
        echo "❌ messages table missing\n";
    }

    // Check if users table exists
    if (in_array('users', $tables)) {
        echo "✅ users table exists\n";
    } else {
        echo "❌ users table missing\n";
    }

} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>