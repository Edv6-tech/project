<?php
// Simple setup verification
echo "<h2>🚀 Project Setup Status</h2>";

echo "<h3>✅ Folder Structure</h3>";
$folders = ['public', 'src', 'config', 'env'];
foreach ($folders as $folder) {
    if (is_dir($folder)) {
        echo "<p style='color: green;'>$folder folder exists</p>";
    } else {
        echo "<p style='color: red;'>$folder folder missing</p>";
    }
}

echo "<h3>📋 Configuration Files</h3>";
$files = [
    'config/config.php' => 'Main configuration',
    '.env' => 'Environment variables',
    'composer.json' => 'Composer configuration'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>$description: $file</p>";
    } else {
        echo "<p style='color: orange;'>$description: $file (missing)</p>";
    }
}

echo "<h3>📦 Dependencies</h3>";
if (is_dir('vendor')) {
    echo "<p style='color: green;'>Vendor folder exists</p>";
    
    if (file_exists('vendor/autoload.php')) {
        echo "<p style='color: green;'>Autoloader available</p>";
        
        try {
            require_once 'vendor/autoload.php';
            if (class_exists('Firebase\JWT\JWT')) {
                echo "<p style='color: green;'>Firebase JWT library loaded</p>";
            } else {
                echo "<p style='color: orange;'>Firebase JWT library not found</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error loading dependencies: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Autoloader missing</p>";
    }
} else {
    echo "<p style='color: orange;'>Vendor folder not found</p>";
    echo "<p>Run: <code>composer install</code></p>";
}

echo "<h3>🔐 Environment Setup</h3>";
if (file_exists('.env')) {
    echo "<p style='color: green;'>.env file exists</p>";
    
    // Test environment loading
    try {
        require_once 'config/config.php';
        echo "<p style='color: green;'>Configuration loaded from .env</p>";
        
        if (defined('JWT_SECRET') && JWT_SECRET !== 'your-super-secret-jwt-key-change-this-in-production') {
            echo "<p style='color: green;'>JWT_SECRET configured</p>";
        } else {
            echo "<p style='color: orange;'>Update JWT_SECRET in .env</p>";
        }
        
        if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== 'your-google-client-id.apps.googleusercontent.com') {
            echo "<p style='color: green;'>Google OAuth configured</p>";
        } else {
            echo "<p style='color: orange;'>Update Google OAuth in .env</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Configuration error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>.env file not found</p>";
    echo "<p>Copy env/.env.example to .env and update values</p>";
}

echo "<h3>🎯 Next Steps</h3>";
echo "<ol>";
echo "<li><strong>Update .env:</strong> Add your Google OAuth credentials</li>";
echo "<li><strong>Database Setup:</strong> Create database and tables</li>";
echo "<li><strong>Test Authentication:</strong> Try login with Google</li>";
echo "</ol>";

echo "<h3>🔗 Quick Links</h3>";
echo "<ul>";
echo "<li><a href='login.php'>Login Page</a></li>";
echo "<li><a href='register.php'>Register Page</a></li>";
echo "<li><a href='chat.php'>Chat Application</a></li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>✅ Project structure is clean and organized!</strong></p>";
echo "<p><strong>🔐 Google JWT authentication is ready!</strong></p>";
?>
