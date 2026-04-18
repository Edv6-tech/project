<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService {
    private $secretKey;
    private $algorithm;
    private $tokenExpiry;
    private $pdo;

    public function __construct() {
        $this->secretKey = JWT_SECRET;
        $this->algorithm = 'HS256';
        $this->tokenExpiry = 3600; // 1 hour

        // ✅ Proper DB connection
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * 🔐 Generate JWT token
     */
    public function generateToken($userId, $username, $email, $name = null) {
        $payload = [
            'iss' => 'zap-ai',
            'sub' => $userId,
            'username' => $username,
            'email' => $email,
            'iat' => time(),
            'exp' => time() + $this->tokenExpiry
        ];

        if ($name !== null) {
            $payload['name'] = $name;
        }

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * 🔍 Validate token + check user still exists
     */
    public function validateToken($token) {
        try {
            // ✅ Decode token
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));

            // ❌ If no DB connection
            if (!$this->pdo) {
                return false;
            }

            // 🔥 Check if user STILL EXISTS in DB
            $stmt = $this->pdo->prepare(
                "SELECT id, username, email, given_name FROM users WHERE id = ?"
            );
            $stmt->execute([$decoded->sub]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return false; // ❌ user deleted → logout
            }

            return $user;

        } catch (\Firebase\JWT\ExpiredException $e) {
            return false; // ⏰ expired
        } catch (Exception $e) {
            return false; // ❌ invalid
        }
    }

    /**
     * 📦 Get token from Authorization header
     */
    public function extractTokenFromHeader() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * 🍪 Get token from cookie
     */
    public function getTokenFromCookie() {
        return $_COOKIE['jwt_token'] ?? null;
    }

    /**
     * 👤 Get current user
     */
    public function getCurrentUser() {
        $token = $this->extractTokenFromHeader();

        if (!$token) {
            $token = $this->getTokenFromCookie();
        }

        if (!$token) {
            return false;
        }

        return $this->validateToken($token);
    }

    /**
     * ✅ Check if authenticated
     */
    public function isAuthenticated() {
        return $this->getCurrentUser() !== false;
    }

    /**
     * 🔄 Refresh token
     */
    public function refreshToken($token) {
        $user = $this->validateToken($token);

        if (!$user) {
            return false;
        }

        return $this->generateToken(
            $user['id'],
            $user['username'],
            $user['email']
        );
    }

    /**
     * 🍪 Set cookie (VERY IMPORTANT SETTINGS)
     */
    public function setTokenCookie($token) {
        setcookie(
            'jwt_token',
            $token,
            [
                'expires' => time() + $this->tokenExpiry,
                'path' => '/',
                'secure' => false, // ⚠️ change to true if using HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }

    /**
     * 🚪 Logout (clear token)
     */
    public function clearToken() {
        setcookie(
            'jwt_token',
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }
}