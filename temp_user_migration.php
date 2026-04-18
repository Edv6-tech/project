<?php
$pdo = new PDO("mysql:host=localhost;dbname=chat_app", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$cols = [
    "username varchar(50) NOT NULL UNIQUE",
    "password_hash varchar(255) NOT NULL",
    "given_name varchar(255) NULL",
    "auth_method enum('email','google') NOT NULL DEFAULT 'google'"
];
foreach ($cols as $col) {
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN $col");
        echo "Added $col\n";
    } catch (PDOException $e) {
        echo "Skip $col: " . $e->getMessage() . "\n";
    }
}
echo "Done\n";
?>