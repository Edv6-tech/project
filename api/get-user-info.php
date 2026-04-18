<?php
require_once __DIR__ . '/../src/GoogleAuthService.php';
require_once __DIR__ . '/../src/DatabaseManager.php';

header('Content-Type: application/json');

try {
    $jwtService = new JWTService();
    
    // Get current user from JWT token
    $user = $jwtService->getCurrentUser();
    
    if ($user) {
        // Get full user info from database
        $dbManager = new DatabaseManager();
        $userInfo = $dbManager->getUserById($user['id']);
        
        if ($userInfo) {
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $userInfo['id'],
                    'name' => $userInfo['name'],
                    'email' => $userInfo['email'],
                    'profile_picture' => $userInfo['profile_picture'],
                    'google_id' => $userInfo['google_id']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'User not found'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Not authenticated'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
