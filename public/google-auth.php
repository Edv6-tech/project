<?php
require_once __DIR__ . '/../src/GoogleAuthService.php';
require_once __DIR__ . '/../src/JWTService.php';

$auth = new GoogleAuthService();
$jwtService = new JWTService();

/* =========================
   🔥 GET GOOGLE CODE
========================= */
$code = $_GET['code'] ?? null;

/* =========================
   🚀 START GOOGLE LOGIN
========================= */
if (!$code) {
    try {
        $loginUrl = $auth->getAuthUrl();

        if (!$loginUrl) {
            throw new Exception("Failed to generate Google login URL");
        }

        header("Location: " . $loginUrl);
        exit;

    } catch (Exception $e) {
        die("❌ Google Auth Error: " . $e->getMessage());
    }
}

/* =========================
   ✅ HANDLE CALLBACK
========================= */
try {
    $userData = $auth->handleCallback($code, $_GET['state'] ?? null);

    if (!$userData) {
        throw new Exception("Failed to retrieve user data from Google");
    }

    // 🔥 Extract user data safely
    $userId   = $userData['id'] ?? null;
    $username = $userData['name'] ?? 'User';
    $email    = $userData['email'] ?? null;

    if (!$userId || !$email) {
        throw new Exception("Invalid Google user data");
    }

    /* =========================
       🔐 CREATE JWT
    ========================= */
    $token = $jwtService->generateToken($userId, $username, $email);

    /* =========================
       🍪 SET COOKIE (USE SERVICE)
    ========================= */
    $jwtService->setTokenCookie($token);

    /* =========================
       ✅ REDIRECT
    ========================= */
    header("Location: /project/subject.php");
    exit;

} catch (Exception $e) {
    // 🔥 Debug output (remove later in production)
    echo "<h2>❌ Google Login Failed</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    exit;
}