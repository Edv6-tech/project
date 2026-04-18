<?php
require_once __DIR__ . '/../src/GoogleAuthService.php';

$jwtService = new JWTService();
$jwtService->clearToken();
setcookie("jwt_token", "", time() - 3600, "/");
header("Location: login.php");
exit;