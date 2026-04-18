<?php
require_once __DIR__ . '/../src/JWTService.php';

header('Content-Type: application/json');

$jwtService = new JWTService();

if (!isset($_COOKIE['jwt_token'])) {
    echo json_encode(['authenticated' => false]);
    exit;
}

$user = $jwtService->validateToken($_COOKIE['jwt_token']);

if ($user) {
    echo json_encode([
        'authenticated' => true,
        'user' => $user
    ]);
} else {
    echo json_encode(['authenticated' => false]);
}