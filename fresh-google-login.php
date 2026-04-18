<?php
require_once 'src/GoogleAuthService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear old session
unset($_SESSION['google_auth_code_used']);

$auth = new GoogleAuthService();
$loginUrl = $auth->getAuthUrl();

echo "<h1>Fresh Google Login Test</h1>";
echo "<p>Click below to start a fresh Google login (this will clear any cached codes):</p>";
echo "<p><a href='" . htmlspecialchars($loginUrl) . "' style='padding: 10px 20px; background: blue; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Login with Google</a></p>";
echo "<p>Session cleared: Yes</p>";
echo "<p>New auth URL generated: " . (strpos($loginUrl, 'code') === false ? "Yes" : "No") . "</p>";
?>
