<?php
// Google OAuth Setup Guide
echo "<h2>Google OAuth Setup Guide</h2>";

echo "<h3>Step 1: Get Google OAuth Credentials</h3>";
echo "<ol>";
echo "<li>Go to <a href='https://console.cloud.google.com/'>Google Cloud Console</a></li>";
echo "<li>Create a new project or select existing one</li>";
echo "<li>Enable 'Google+ API' and 'Google OAuth2 API'</li>";
echo "<li>Go to 'Credentials' > 'Create Credentials' > 'OAuth 2.0 Client IDs'</li>";
echo "<li>Select 'Web application'</li>";
echo "<li>Add authorized redirect URI: <code>http://localhost/project/public/google-callback.php</code></li>";
echo "<li>Copy Client ID and Client Secret</li>";
echo "</ol>";

echo "<h3>Step 2: Update Environment Variables</h3>";
echo "<p>Edit your <code>.env</code> file and update these values:</p>";
echo "<pre>";
echo "GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost/project/public/google-callback.php";
echo "</pre>";

echo "<h3>Step 3: Update Database Schema</h3>";
echo "<p>Run the schema update script:</p>";
echo "<p><a href='update-schema.php'>Update Database Schema</a></p>";

echo "<h3>Step 4: Test Google Authentication</h3>";
echo "<ol>";
echo "<li>Go to <a href='login.php'>Login Page</a></li>";
echo "<li>Click the 'Google' button</li>";
echo "<li>Sign in with your Google account</li>";
echo "<li>Verify you're redirected to chat.php</li>";
echo "</ol>";

echo "<h3>Current Configuration Status</h3>";

// Check .env file
if (file_exists(__DIR__ . '/../.env')) {
    echo "<p style='color: green;'>.env file exists</p>";
    
    // Read .env content
    $envContent = file_get_contents(__DIR__ . '/../.env');
    
    if (strpos($envContent, 'your-google-client-id') !== false) {
        echo "<p style='color: orange;'>Update GOOGLE_CLIENT_ID in .env</p>";
    } else {
        echo "<p style='color: green;'>GOOGLE_CLIENT_ID configured</p>";
    }
    
    if (strpos($envContent, 'your-google-client-secret') !== false) {
        echo "<p style='color: orange;'>Update GOOGLE_CLIENT_SECRET in .env</p>";
    } else {
        echo "<p style='color: green;'>GOOGLE_CLIENT_SECRET configured</p>";
    }
} else {
    echo "<p style='color: red;'>.env file not found</p>";
}

// Check database schema
try {
    require_once __DIR__ . '/../config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    $pdo->exec("USE chat_app");
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'google_id'");
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>Database schema updated for Google OAuth</p>";
    } else {
        echo "<p style='color: orange;'>Database schema needs update</p>";
        echo "<p><a href='update-schema.php'>Update Schema Now</a></p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h3>Troubleshooting</h3>";
echo "<ul>";
echo "<li><strong>Invalid redirect URI:</strong> Make sure it matches exactly in Google Console</li>";
echo "<li><strong>API not enabled:</strong> Enable Google+ API and OAuth2 API</li>";
echo "<li><strong>Database errors:</strong> Run update-schema.php</li>";
echo "<li><strong>JWT errors:</strong> Check vendor/autoload.php exists</li>";
echo "</ul>";

echo "<h3>Quick Test Links</h3>";
echo "<ul>";
echo "<li><a href='login.php'>Login Page</a></li>";
echo "<li><a href='register.php'>Register Page</a></li>";
echo "<li><a href='update-schema.php'>Update Schema</a></li>";
echo "<li><a href='google-auth.php'>Test Google Auth</a></li>";
echo "</ul>";
?>
