<?php
require_once __DIR__ . '/DatabaseManager.php';
require_once __DIR__ . '/AIService.php';

class ChatHandler {
    private $dbManager;
    private $aiService;

    public function __construct() {
        $this->dbManager = new DatabaseManager();
        $this->aiService = new AIService();
    }

    /* =========================
       💬 HANDLE NEW MESSAGE
    ========================= */
    public function handleMessage($sessionId, $userMessage) {
        try {
            if (empty(trim($userMessage))) {
                throw new Exception("Message cannot be empty");
            }

            // 🧠 Get history
            $history = $this->dbManager->getChatHistory($sessionId);

            // 🤖 FIXED METHOD NAME HERE
            $aiResponse = $this->aiService->getVisionResponse($userMessage, null, $history);

            // 💾 Save
            $messageId = $this->dbManager->saveMessage($sessionId, $userMessage, $aiResponse);

            // 🎯 If first message, auto-generate chat title
            if (count($history) === 0) {
                $title = $this->generateSessionTitle($userMessage, [$userMessage]); // Pass current message as minimal history
                $this->dbManager->updateSessionTitle($sessionId, $title);
            }

            return [
                'success' => true,
                'message_id' => $messageId,
                'ai_response' => $aiResponse,
                'timestamp' => date('Y-m-d H:i:s')
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /* =========================
       📜 GET CHAT HISTORY (USED WHEN CLICKING CHAT)
    ========================= */
    public function getChatMessages($sessionId) {
        try {
            $messages = $this->dbManager->getChatHistory($sessionId);

            return [
                'success' => true,
                'messages' => $messages
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /* =========================
       🆕 CREATE NEW CHAT SESSION
    ========================= */
    public function createNewSession($userId) {
        try {
            $sessionId = uniqid('chat_');
            $dbSessionId = $this->dbManager->createSession($userId, $sessionId);

            return [
                'success' => true,
                'session_id' => $sessionId,
                'db_id' => $dbSessionId
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /* =========================
       📂 GET ALL USER CHATS
    ========================= */
    public function getUserSessions($userId) {
        try {
            $sessions = $this->dbManager->getUserSessions($userId);

            return [
                'success' => true,
                'sessions' => $sessions
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /* =========================
       🔍 CHECK AI CONFIG
    ========================= */
    public function validateAIService() {
        if (!$this->aiService->isConfigured()) {
            return [
                'success' => false,
                'error' => 'AI service is not properly configured.'
            ];
        }
        return ['success' => true];
    }

    /* =========================
       🎯 GENERATE SESSION TITLE
    ========================= */
    private function generateSessionTitle($firstMessage, $history = []) {
        try {
            // Use AI to generate a ChatGPT-style title
            return $this->aiService->generateTitle($firstMessage, $history);
        } catch (Exception $e) {
            // Fallback to basic text processing if AI fails
            $cleanMessage = trim($firstMessage);

            // Remove common prefixes
            $cleanMessage = preg_replace('/^(hi|hello|hey|can you|please|help me|how do i|what is|tell me)(\s|,|\.|\!|\?)+/i', '', $cleanMessage);
            $cleanMessage = trim($cleanMessage);

            // Take first meaningful part
            if (strlen($cleanMessage) > 40) {
                $cleanMessage = substr($cleanMessage, 0, 40);
                $lastSpace = strrpos($cleanMessage, ' ');
                if ($lastSpace !== false) {
                    $cleanMessage = substr($cleanMessage, 0, $lastSpace);
                }
            }

            return ucfirst($cleanMessage) ?: 'New Chat';
        }
    }
        
        // If empty after cleaning, use default
        if (empty($cleanMessage)) {
            $cleanMessage = 'New Chat';
        }
        
        return $cleanMessage;
    }
}
?>