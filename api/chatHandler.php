<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../src/SessionManager.php';
require_once __DIR__ . '/../src/ChatHandler.php';

$sessionManager = new SessionManager();
$chatHandler = new ChatHandler();

// Validate session
if (!$sessionManager->validateSession()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized - Please login']);
    exit;
}

$userId = $sessionManager->getCurrentUserId();
$sessionId = $sessionManager->getCurrentSessionId();

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'send_message':
            $userMessage = $_POST['message'] ?? '';
            $sessionId = $_POST['session_id'] ?? $sessionId;  // Use passed session_id or fall back to PHP session
            if (empty(trim($userMessage)) && empty($_FILES['image'] ?? null)) {
                throw new Exception('Message and/or image must be provided');
            }
            
            // Handle image upload if present
            $imageUrl = null;
            $imageData = null;
            if (!empty($_FILES['image']['tmp_name'])) {
                $maxSize = 5 * 1024 * 1024; // 5MB limit
                if ($_FILES['image']['size'] > $maxSize) {
                    throw new Exception('Image size exceeds 5MB limit');
                }
                
                $validMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($_FILES['image']['type'], $validMimes)) {
                    throw new Exception('Invalid image format. Allowed: JPEG, PNG, GIF, WebP');
                }
                
                // Store image as base64 or file path
                $imageData = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
            }
            
            $result = $chatHandler->handleMessage($sessionId, $userMessage);
            echo json_encode($result);
            break;

        case 'get_history':
            $requestSessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? $sessionId;
            $result = $chatHandler->getChatMessages($requestSessionId);
            echo json_encode($result);
            break;

        case 'new_session':
            $result = $chatHandler->createNewSession($userId);
            echo json_encode($result);
            break;

        case 'get_sessions':
            $result = $chatHandler->getUserSessions($userId);
            echo json_encode($result);
            break;

        case 'validate_ai':
            $result = $chatHandler->validateAIService();
            echo json_encode($result);
            break;

        default:
            throw new Exception('Invalid action specified');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
