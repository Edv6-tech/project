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

/* 🔐 AUTH */
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

/* 📥 GET CHAT ID */
$chatId = $_GET['chat_id'] ?? null;

if ($chatId) {
    // Return messages for specific chat
    $stmt = $pdo->prepare("
        SELECT user_message, ai_response, user_image_data, timestamp
        FROM messages
        WHERE session_id = ?
        ORDER BY id ASC
    ");

    $stmt->execute([$chatId]);
    $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* 🔥 FIX FORMAT */
    $messages = [];

    foreach ($raw as $msg) {
        // Add user message
        $messages[] = [
            "role" => "user",
            "content" => $msg['user_message'],
            "image" => $msg['user_image_data'] ?? null,
            "created_at" => $msg['timestamp']
        ];

        // Add AI response if it exists
        if (!empty($msg['ai_response'])) {
            $messages[] = [
                "role" => "assistant",
                "content" => $msg['ai_response'],
                "image" => null,
                "created_at" => $msg['timestamp']
            ];
        }
    }

    echo json_encode($messages);
    exit;
}

/* 📜 FETCH CHATS FOR USER */
$stmt = $pdo->prepare("
    SELECT session_id as id, title, created_at, last_activity
    FROM sessions
    WHERE user_id = ?
    ORDER BY last_activity DESC
");

$stmt->execute([$user['id']]);

$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* 📤 RETURN RESPONSE */
echo json_encode($chats);