<?php
echo "<h2>Fix API Error 401 - Unauthorized</h2>";

// Test 1: Check API Configuration
echo "<h3>1. API Configuration Check</h3>";
try {
    require_once __DIR__ . '/config/config.php';
    
    echo "<p><strong>Current Configuration:</strong></p>";
    echo "<ul>";
    
    if (defined('AI_API_KEY')) {
        echo "<li style='color: green;'>✓ AI_API_KEY: SET</li>";
        echo "<li>Key length: " . strlen(AI_API_KEY) . " characters</li>";
        echo "<li>Key format: " . (substr(AI_API_KEY, 0, 3) . "..." . substr(AI_API_KEY, -3)) . "</li>";
        
        // Check key format
        if (strpos(AI_API_KEY, 'sk-') === 0) {
            echo "<li style='color: green;'>✓ Key format: Valid (starts with sk-)</li>";
        } else {
            echo "<li style='color: red;'>✗ Key format: Invalid (should start with sk-)</li>";
        }
    } else {
        echo "<li style='color: red;'>✗ AI_API_KEY: NOT SET</li>";
    }
    
    if (defined('AI_API_URL')) {
        echo "<li style='color: green;'>✓ AI_API_URL: " . htmlspecialchars(AI_API_URL) . "</li>";
    } else {
        echo "<li style='color: red;'>✗ AI_API_URL: NOT SET</li>";
    }
    
    if (defined('AI_SERVICE_PROVIDER')) {
        echo "<li style='color: green;'>✓ AI_SERVICE_PROVIDER: " . htmlspecialchars(AI_SERVICE_PROVIDER) . "</li>";
    } else {
        echo "<li style='color: red;'>✗ AI_SERVICE_PROVIDER: NOT SET</li>";
    }
    
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Config error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 2: Check API Key Validity
echo "<h3>2. API Key Validation</h3>";
if (defined('AI_API_KEY') && AI_API_KEY) {
    echo "<p>Testing API key validity...</p>";
    
    // Test with OpenAI API
    if (defined('AI_API_URL') && strpos(AI_API_URL, 'openai') !== false) {
        echo "<p>Testing OpenAI API key...</p>";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/models');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . AI_API_KEY,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "<p style='color: red;'>CURL Error: " . htmlspecialchars($error) . "</p>";
        } else {
            echo "<p>HTTP Status: $httpCode</p>";
            
            if ($httpCode === 200) {
                echo "<p style='color: green;'>✓ API Key is VALID</p>";
            } elseif ($httpCode === 401) {
                echo "<p style='color: red;'>✗ API Key is INVALID or EXPIRED</p>";
                echo "<p><strong>Solution:</strong> Check your OpenAI API key</p>";
            } elseif ($httpCode === 403) {
                echo "<p style='color: red;'>✗ API Key lacks permissions</p>";
                echo "<p><strong>Solution:</strong> Check API key permissions</p>";
            } else {
                echo "<p style='color: orange;'>⚠ Unexpected status: $httpCode</p>";
            }
            
            echo "<p>Response: " . substr($response, 0, 200) . "...</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Not OpenAI API - manual verification needed</p>";
    }
} else {
    echo "<p style='color: red;'>No API key to test</p>";
}

// Test 3: Check Request Format
echo "<h3>3. Request Format Check</h3>";
echo "<p>Checking how API requests are being made...</p>";

// Look for AIService files
$aiServiceFiles = [
    __DIR__ . '/src/AIService.php',
    __DIR__ . '/src/OpenAIService.php',
    __DIR__ . '/src/ChatGPTService.php'
];

foreach ($aiServiceFiles as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>Found: " . basename($file) . "</p>";
        
        // Read file to check authentication method
        $content = file_get_contents($file);
        
        if (strpos($content, 'Bearer') !== false) {
            echo "<p style='color: green;'>✓ Uses Bearer token authentication</p>";
        } else {
            echo "<p style='color: red;'>✗ Missing Bearer token authentication</p>";
        }
        
        if (strpos($content, 'Authorization') !== false) {
            echo "<p style='color: green;'>✓ Sets Authorization header</p>";
        } else {
            echo "<p style='color: red;'>✗ Missing Authorization header</p>";
        }
    }
}

// Test 4: Environment Variables
echo "<h3>4. Environment Variables</h3>";
echo "<p>Checking .env file...</p>";

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "<p style='color: green;'>✓ .env file exists</p>";
    
    $envContent = file_get_contents($envFile);
    
    if (strpos($envContent, 'AI_API_KEY') !== false) {
        echo "<p style='color: green;'>✓ AI_API_KEY in .env</p>";
    } else {
        echo "<p style='color: red;'>✗ AI_API_KEY missing from .env</p>";
    }
    
    if (strpos($envContent, 'AI_API_URL') !== false) {
        echo "<p style='color: green;'>✓ AI_API_URL in .env</p>";
    } else {
        echo "<p style='color: red;'>✗ AI_API_URL missing from .env</p>";
    }
} else {
    echo "<p style='color: red;'>✗ .env file missing</p>";
}

echo "<h3>5. Quick Fixes</h3>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "<h4>Common 401 Error Solutions:</h4>";
echo "<ol>";
echo "<li><strong>Check API Key:</strong> Ensure it's a valid OpenAI API key starting with 'sk-'</li>";
echo "<li><strong>Verify Key Active:</strong> Check OpenAI dashboard for active key</li>";
echo "<li><strong>Check Permissions:</strong> Ensure key has API access permissions</li>";
echo "<li><strong>Update .env:</strong> Make sure AI_API_KEY is set correctly</li>";
echo "<li><strong>Check URL:</strong> Verify AI_API_URL is correct</li>";
echo "<li><strong>Clear Cache:</strong> Restart server after changing .env</li>";
echo "</ol>";
echo "</div>";

echo "<h3>6. Test Actions</h3>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "<a href='/project/fix_api_401.php' style='display: inline-block; margin: 5px; padding: 10px; background: #007bff; color: white; text-decoration: none; border-radius: 3px;'>Refresh Test</a>";
echo "<a href='/project/debug_ai_connection.php' style='display: inline-block; margin: 5px; padding: 10px; background: #28a745; color: white; text-decoration: none; border-radius: 3px;'>Full AI Debug</a>";
echo "<a href='/project/public/chat.php' style='display: inline-block; margin: 5px; padding: 10px; background: #17a2b8; color: white; text-decoration: none; border-radius: 3px;'>Test Chat</a>";
echo "</div>";

echo "<h3>7. API Key Setup Guide</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
echo "<h4>How to Fix API Key:</h4>";
echo "<ol>";
echo "<li>Go to <a href='https://platform.openai.com/api-keys' target='_blank'>OpenAI API Keys</a></li>";
echo "<li>Create a new API key or copy existing one</li>";
echo "<li>Update your .env file: <code>AI_API_KEY=sk-your-key-here</code></li>";
echo "<li>Restart your server</li>";
echo "<li>Test again</li>";
echo "</ol>";
echo "</div>";
?>
