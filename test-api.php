<?php
// Simulate API request
require_once 'config/db.php';
require_once 'src/JWTService.php';
require_once 'src/AIService.php';

header('Content-Type: application/json');

// Create a test JWT token
$jwt = new JWTService();
$token = $jwt->generateToken(1, 'testuser', 'test@example.com');

// Set the cookie
$_COOKIE['jwt_token'] = $token;

// Now test the API logic
try {
    if (!isset($_COOKIE['jwt_token'])) {
        throw new Exception('No token');
    }

    $user = $jwt->validateToken($_COOKIE['jwt_token']);

    if (!$user) {
        throw new Exception('Invalid token');
    }

    echo json_encode(['success' => true, 'user' => $user, 'message' => 'API test successful']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>