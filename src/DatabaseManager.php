<?php
require_once __DIR__ . '/../config/database.php';

class DatabaseManager {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createUser($username, $email, $passwordHash) {
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
        $this->db->query($sql, [$username, $email, $passwordHash]);
        return $this->db->lastInsertId();
    }

    public function getUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->query($sql, [$username]);
        return $stmt->fetch();
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt->fetch();
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function createSession($userId, $sessionId) {
        $sql = "INSERT INTO sessions (user_id, session_id) VALUES (?, ?)";
        $this->db->query($sql, [$userId, $sessionId]);
        return $this->db->lastInsertId();
    }

    public function getSession($sessionId) {
        $sql = "SELECT * FROM sessions WHERE session_id = ?";
        $stmt = $this->db->query($sql, [$sessionId]);
        return $stmt->fetch();
    }

    public function updateSessionActivity($sessionId) {
        $sql = "UPDATE sessions SET last_activity = CURRENT_TIMESTAMP WHERE session_id = ?";
        $this->db->query($sql, [$sessionId]);
    }

    public function resolveSessionDbId($sessionId) {
        if (empty($sessionId)) {
            return false;
        }

        $sql = "SELECT id FROM sessions WHERE session_id = ? OR id = ? LIMIT 1";
        $stmt = $this->db->query($sql, [$sessionId, $sessionId]);
        $row = $stmt->fetch();

        return $row ? $row['id'] : false;
    }

    public function saveMessage($sessionId, $userMessage, $aiResponse) {
        $dbSessionId = $this->resolveSessionDbId($sessionId);
        if (!$dbSessionId) {
            throw new Exception('Invalid session id for saving message');
        }

        $sql = "INSERT INTO messages (session_id, user_message, ai_response) VALUES (?, ?, ?)";
        $this->db->query($sql, [$dbSessionId, $userMessage, $aiResponse]);
        return $this->db->lastInsertId();
    }

    public function getChatHistory($sessionId) {
        $dbSessionId = $this->resolveSessionDbId($sessionId);
        if (!$dbSessionId) {
            return [];
        }

        $sql = "SELECT user_message, ai_response, timestamp FROM messages WHERE session_id = ? ORDER BY id ASC";
        $stmt = $this->db->query($sql, [$dbSessionId]);
        return $stmt->fetchAll();
    }

    public function getUserSessions($userId) {
        $sql = "SELECT id, session_id, title, created_at, last_activity FROM sessions WHERE user_id = ? ORDER BY last_activity DESC";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetchAll();
    }

    public function updateSessionTitle($sessionId, $title) {
        $sql = "UPDATE sessions SET title = ? WHERE session_id = ?";
        $this->db->query($sql, [$title, $sessionId]);
    }

    public function deleteSession($sessionId) {
        $sql = "DELETE FROM sessions WHERE id = ?";
        $this->db->query($sql, [$sessionId]);
    }
    
    public function createGoogleUser($email, $name, $googleId, $picture = null) {
        $sql = "INSERT INTO users (email, name, google_id, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())";
        $this->db->query($sql, [$email, $name, $googleId, $picture]);
        return $this->db->lastInsertId();
    }
    
    public function getUserByGoogleId($googleId) {
        $sql = "SELECT * FROM users WHERE google_id = ?";
        $stmt = $this->db->query($sql, [$googleId]);
        return $stmt->fetch();
    }
    
    public function updateUserGoogleId($userId, $googleId, $picture = null) {
        $sql = "UPDATE users SET google_id = ?, profile_picture = ? WHERE id = ?";
        $this->db->query($sql, [$googleId, $picture, $userId]);
    }
    
    public function updateGoogleUserInfo($userId, $name, $picture = null) {
        $sql = "UPDATE users SET name = ?, profile_picture = ? WHERE id = ?";
        $this->db->query($sql, [$name, $picture, $userId]);
    }

    public function getConnection() {
        return $this->db->getConnection();
    }
}
?>
