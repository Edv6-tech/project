<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/DatabaseManager.php';

class SessionManager {
    private $dbManager;
    private $sessionId;

    public function __construct() {
        $this->dbManager = new DatabaseManager();
        session_name(SESSION_COOKIE_NAME);
        session_start();
        $this->sessionId = session_id();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function login($userId) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['login_time'] = time();
        
        // Create or update session in database
        $existingSession = $this->dbManager->getSession($this->sessionId);
        if (!$existingSession) {
            $this->dbManager->createSession($userId, $this->sessionId);
        } else {
            $this->dbManager->updateSessionActivity($this->sessionId);
        }
        
        return true;
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function getCurrentUserId() {
        return $this->isLoggedIn() ? $_SESSION['user_id'] : null;
    }

    public function getCurrentSessionId() {
        return $this->sessionId;
    }

    public function validateSession() {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $dbSession = $this->dbManager->getSession($this->sessionId);
        if (!$dbSession) {
            $this->logout();
            return false;
        }

        $lastActivity = strtotime($dbSession['last_activity']);
        if (time() - $lastActivity > SESSION_LIFETIME) {
            $this->logout();
            return false;
        }

        $this->dbManager->updateSessionActivity($this->sessionId);
        return true;
    }

    public function requireLogin() {
        if (!$this->validateSession()) {
            header('Location: login.php');
            exit;
        }
    }
}
?>
