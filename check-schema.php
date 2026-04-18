<?php
require_once 'config/db.php';

echo "<h2>Users Table Structure</h2>";
try {
    $result = $pdo->query("DESCRIBE users");
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

echo "<h2>Test User Count</h2>";
try {
    $count = $pdo->query("SELECT COUNT(*) as cnt FROM users")->fetch()['cnt'];
    echo "<p>Total users: " . $count . "</p>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
