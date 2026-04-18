<?php
require_once 'src/GoogleAuthService.php';

echo "<h2>Google Auth Debug</h2>";

// Check if we have a code
if (!isset($_GET['code'])) {
    echo "<p>No code provided. Starting Google login...</p>";
    $auth = new GoogleAuthService();
    $loginUrl = $auth->getAuthUrl();
    echo "<p><a href='" . htmlspecialchars($loginUrl) . "'>Click here to login with Google</a></p>";
    exit;
}

// We have a code, test the callback
echo "<p>Testing Google callback with code: " . htmlspecialchars(substr($_GET['code'], 0, 20)) . "...</p>";

try {
    $auth = new GoogleAuthService();
    echo "<p>✓ GoogleAuthService initialized</p>";
    
    // Test handleCallback
    echo "<p>Attempting to get user data...</p>";
    $user = $auth->handleCallback($_GET['code']);
    
    if ($user) {
        echo "<p>✓ Got user data:</p>";
        echo "<pre>" . json_encode($user, JSON_PRETTY_PRINT) . "</pre>";
        
        // Test authenticate
        echo "<p>Attempting full authenticate...</p>";
        $result = $auth->authenticate($_GET['code']);
        
        if ($result) {
            echo "<p style='color:green;'>✓ Authentication successful!</p>";
            echo "<p>Check JWT cookie is set: " . (isset($_COOKIE['jwt_token']) ? "Yes" : "No") . "</p>";
        } else {
            echo "<p style='color:red;'>✗ authenticate() returned false</p>";
            echo "<p>Check PHP error logs for details.</p>";
        }
    } else {
        echo "<p style='color:red;'>✗ handleCallback returned false</p>";
        echo "<p>Check PHP error logs for details.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
