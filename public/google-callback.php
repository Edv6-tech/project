<?php
require_once __DIR__ . '/../src/GoogleAuthService.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new GoogleAuthService();

// Start login
if (!isset($_GET['code'])) {
    $loginUrl = $auth->getAuthUrl();
    header("Location: " . $loginUrl);
    exit;
}

// Handle callback
$user = $auth->authenticate($_GET['code'], $_GET['state'] ?? null);

if ($user) {
    header("Location: /project/subject.php");
    exit;
} else {
    echo "<h2>❌ Google Login Failed</h2>";
    echo "<p>Please try again.</p>";
    echo "<a href='/project/public/login.php'>← Back to Login</a>";
}
?>