<?php
require_once __DIR__ . '/src/JWTService.php';

$jwtService = new JWTService();

if (isset($_COOKIE['jwt_token'])) {
    $user = $jwtService->validateToken($_COOKIE['jwt_token']);

    if ($user) {
        header("Location: /project/subject.php");
        exit;
    }
}

// Not logged in
header("Location: /project/public/login.php");
exit;