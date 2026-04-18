<?php
require_once '../src/ChatHandler.php';

$data = json_decode(file_get_contents("php://input"), true);
$sessionId = $data['session_id'];

$chatHandler = new ChatHandler();
$response = $chatHandler->getChatMessages($sessionId);

echo json_encode($response);