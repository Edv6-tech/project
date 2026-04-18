<?php
require_once __DIR__ . '/src/GoogleAuthService.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/src/JWTService.php';

$jwtService = new JWTService();

/* =========================
   🔐 CHECK AUTH
========================= */

if (isset($_COOKIE['jwt_token'])) {
    $user = $jwtService->validateToken($_COOKIE['jwt_token']);

    if ($user) {

        // 🔥 CHECK DATABASE
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$user['email']]);

        $dbUser = $stmt->fetch();

        if ($dbUser) {
            // ✅ CORRECT REDIRECT
            header("Location: /project/public/subject.php");
            exit;
        } else {
            // ❌ USER DELETED → CLEAR COOKIE
            setcookie("jwt_token", "", time() - 3600, "/");
        }
    }
}

/* =========================
   ❌ NOT LOGGED IN
========================= */

header('Location: /project/public/login.php');
exit;
?>