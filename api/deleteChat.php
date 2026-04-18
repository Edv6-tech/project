```php
<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/JWTService.php';
require_once __DIR__ . '/../src/DatabaseManager.php';

header('Content-Type: application/json');

/* =========================
   ⚠️ ERROR HANDLING
========================= */
function errorHandler($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit;
}

set_error_handler('errorHandler');

set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Exception',
        'message' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
    exit;
});

/* =========================
   🔐 AUTH
========================= */
$jwtService = new JWTService();
$dbManager  = new DatabaseManager();

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
    echo json_encode([
        'error' => 'Authentication error',
        'message' => $e->getMessage()
    ]);
    exit;
}

/* =========================
    INPUT
========================= */
$data = json_decode(file_get_contents("php://input"), true);

$chatId = $data['chat_id'] ?? $data['session_id'] ?? null;

if (!$chatId) {
    http_response_code(400);
    echo json_encode(['error' => 'chat_id required']);
    exit;
}

/* =========================
   🗑 DELETE CHAT
========================= */
try {
    $pdo = $dbManager->getConnection();

    // 🔍 Step 1: Find session using chat_id (string)
    $stmt = $pdo->prepare("
        SELECT id 
        FROM sessions 
        WHERE session_id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$chatId, $user['id']]);

    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        http_response_code(404);
        echo json_encode(['error' => 'Chat not found']);
        exit;
    }

    $session_db_id = $session['id'];

    // 🗑 Step 2: Delete session (messages auto-delete via CASCADE)
    $stmt = $pdo->prepare("
        DELETE FROM sessions 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$session_db_id, $user['id']]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Delete failed");
    }

    /* =========================
       ✅ SUCCESS RESPONSE
    ========================= */
    echo json_encode([
        'success' => true,
        'chat_id' => $chatId
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Delete error',
        'message' => $e->getMessage()
    ]);
    exit;
}
