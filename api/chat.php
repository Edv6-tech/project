```php
<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/JWTService.php';
require_once __DIR__ . '/../src/AIService.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

/* =========================
   ⚠️ ERROR HANDLING
========================= */
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

/* =========================
   🔐 AUTH
========================= */
$jwtService = new JWTService();

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
   📩 INPUT
========================= */
$data = json_decode(file_get_contents("php://input"), true);

$message = $data['message'] ?? '';
$image   = $data['image'] ?? null;
$chatId  = $data['chat_id'] ?? null;

if (!$message && !$image) {
    echo json_encode(['error' => 'Message or image required']);
    exit;
}

/* =========================
   🆔 CREATE OR FETCH SESSION
========================= */
try {
    $ai = new AIService();
    $session_db_id = null;

    if ($chatId) {
        // 🔍 Existing chat → get DB id
        $stmt = $pdo->prepare("SELECT id FROM sessions WHERE session_id = ?");
        $stmt->execute([$chatId]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($session) {
            $session_db_id = $session['id'];
        } else {
            // ❗ chat_id sent but not found → create new
            $chatId = null;
        }
    }

    if (!$chatId) {
        // 🆕 Create new chat
        $chatId = uniqid("chat_");

        try {
            $title = $ai->generateTitle($message);
        } catch (Exception $e) {
            $title = "New Chat";
        }

        if (!$title) $title = "New Chat";

        $stmt = $pdo->prepare("
            INSERT INTO sessions (user_id, session_id, title)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user['id'], $chatId, $title]);

        // ✅ GET REAL DB ID
        $session_db_id = $pdo->lastInsertId();
    }

    if (!$session_db_id) {
        throw new Exception("Failed to resolve session.");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Session error: ' . $e->getMessage()]);
    exit;
}

/* =========================
   💾 SAVE USER MESSAGE
========================= */
try {
    $stmt = $pdo->prepare("
        INSERT INTO messages 
        (session_id, user_message, ai_response, user_image_data, message_type)
        VALUES (?, ?, '', ?, 'text')
    ");
    $stmt->execute([$session_db_id, $message, $image]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save message: ' . $e->getMessage()]);
    exit;
}

/* =========================
   🧠 LOAD HISTORY
========================= */
try {
    $stmt = $pdo->prepare("
        SELECT user_message, ai_response
        FROM messages
        WHERE session_id = ?
        ORDER BY id ASC
        LIMIT 20
    ");
    $stmt->execute([$session_db_id]);

    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load history: ' . $e->getMessage()]);
    exit;
}

/* =========================
   🔄 FORMAT HISTORY
========================= */
$conversationHistory = [];

foreach ($history as $msg) {
    $conversationHistory[] = [
        'user_message' => $msg['user_message'],
        'ai_response'  => $msg['ai_response']
    ];
}

/* =========================
   🤖 CALL AI
========================= */
try {
    $response = $ai->getVisionResponse($message, $image, $conversationHistory);
} catch (Exception $e) {
    error_log("AI Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'AI service error',
        'debug' => $e->getMessage()
    ]);
    exit;
}

/* =========================
   💾 SAVE AI RESPONSE
========================= */
try {
    $stmt = $pdo->prepare("
        UPDATE messages
        SET ai_response = ?
        WHERE session_id = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->execute([$response, $session_db_id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save AI response: ' . $e->getMessage()]);
    exit;
}

/* =========================
   📤 RESPONSE
========================= */
echo json_encode([
    'success' => true,
    'chat_id' => $chatId,
    'response' => $response
]);
