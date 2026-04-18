<?php
require_once 'config/db.php';
require_once 'src/JWTService.php';

try {
    $jwt = new JWTService();
    echo "JWT service loaded successfully\n";

    // Test token generation
    $token = $jwt->generateToken(1, 'testuser', 'test@example.com');
    echo "Token generated: " . substr($token, 0, 50) . "...\n";

    // Test token validation
    $user = $jwt->validateToken($token);
    if ($user) {
        echo "Token validation successful\n";
    } else {
        echo "Token validation failed\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>