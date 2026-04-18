<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/JWTService.php';

header('Content-Type: application/json');

// Catch any fatal errors and return JSON
function errorHandler($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error: ' . $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit;
}

set_error_handler('errorHandler');

set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Exception: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
    exit;
});

$jwtService = new JWTService();

/* =========================
   🔐 AUTHENTICATION
========================= */

try {
    if (!isset($_COOKIE['jwt_token'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $user = $jwtService->validateToken($_COOKIE['jwt_token']);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Authentication error: ' . $e->getMessage()]);
    exit;
}

/* =========================
   📥 GET CHAT ID
========================= */

$chatId = $_GET['chat_id'] ?? null;

if (!$chatId) {
    echo json_encode([]);
    exit;
}

/* =========================
   VERIFY CHAT BELONGS TO USER
========================= */

$stmt = $pdo->prepare("SELECT id FROM sessions WHERE id = ? AND user_id = ?");
$stmt->execute([$chatId, $user['id']]);
$chat = $stmt->fetch();

if (!$chat) {
    http_response_code(404);
    echo json_encode(['error' => 'Chat not found']);
    exit;
}

/* =========================
   📜 FETCH MESSAGES
========================= */

$stmt = $pdo->prepare("
    SELECT user_message, ai_response, user_image_data, timestamp, message_type
    FROM messages
    WHERE session_id = ?
    ORDER BY id ASC
");

$stmt->execute([$chatId]);

$rawMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   🔄 FORMAT FOR FRONTEND
========================= */

$messages = [];

foreach ($rawMessages as $msg) {
    // Add user message
    $messages[] = [
        'role' => 'user',
        'content' => $msg['user_message'],
        'image' => $msg['user_image_data'] ?? null,
        'message_type' => $msg['message_type'] ?? 'text',
        'created_at' => $msg['timestamp']
    ];

    // Add AI response if it exists
    if (!empty($msg['ai_response'])) {
        $messages[] = [
            'role' => 'assistant',
            'content' => $msg['ai_response'],
            'image' => null,
            'message_type' => 'text',
            'created_at' => $msg['timestamp']
        ];
    }
}

/* =========================
   📤 RETURN RESPONSE
========================= */

echo json_encode($messages);