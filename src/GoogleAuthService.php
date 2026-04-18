<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/JWTService.php';

use Google\Client;
use Google\Service\Oauth2;

class GoogleAuthService {
    private $client;
    private $jwtService;
    private $pdo;

    public function __construct() {
        $this->client = new Client();
        $this->jwtService = new JWTService();

        global $pdo;
        $this->pdo = $pdo;

        $this->setupGoogleClient();
    }

    private function setupGoogleClient() {
        $this->client->setClientId(GOOGLE_CLIENT_ID);
        $this->client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $this->client->setRedirectUri(GOOGLE_REDIRECT_URI);

        $this->client->addScope('email');
        $this->client->addScope('profile');
        $this->client->setPrompt('select_account');
    }

    public function getAuthUrl() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // CSRF protection
        $state = bin2hex(random_bytes(32));
        $_SESSION['google_auth_state'] = $state;
        $this->client->setState($state);

        return $this->client->createAuthUrl();
    }

    public function handleCallback($code, $state = null) {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Validate state (IMPORTANT)
            if ($state !== null) {
                if (!isset($_SESSION['google_auth_state']) || $_SESSION['google_auth_state'] !== $state) {
                    error_log("Invalid state");
                    return false;
                }
            }

            // ✅ CORRECT TOKEN EXCHANGE
            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                error_log("Token Error: " . json_encode($token));
                return false;
            }

            $this->client->setAccessToken($token);

            // Get user info
            $oauth = new Oauth2($this->client);
            $googleUser = $oauth->userinfo->get();

            return [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
            ];

        } catch (Exception $e) {
            error_log("handleCallback Error: " . $e->getMessage());
            return false;
        }
    }

    public function authenticate($code, $state = null) {
        try {
            $user = $this->handleCallback($code, $state);

            if (!$user) return false;

            // Check if user exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE google_id = ?");
            $stmt->execute([$user['id']]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingUser) {
                $username = explode('@', $user['email'])[0] . '_' . substr(uniqid(), -6);
                $passwordHash = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);

                $stmt = $this->pdo->prepare("
                    INSERT INTO users (google_id, email, username, password_hash, given_name, auth_method, created_at)
                    VALUES (?, ?, ?, ?, ?, 'google', NOW())
                ");

                $stmt->execute([
                    $user['id'],
                    $user['email'],
                    $username,
                    $passwordHash,
                    $user['name']
                ]);

                $localUserId = $this->pdo->lastInsertId();
            } else {
                $localUserId = $existingUser['id'];
                // Fetch username for existing user
                $stmt = $this->pdo->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->execute([$localUserId]);
                $userRecord = $stmt->fetch(PDO::FETCH_ASSOC);
                $username = $userRecord['username'];
            }

            // Generate JWT with username, email, and name
            $token = $this->jwtService->generateToken(
                $localUserId,
                $username,
                $user['email'],
                $user['name']
            );

            // Store cookie
            $this->jwtService->setTokenCookie($token);

            return $user;

        } catch (Exception $e) {
            error_log("authenticate Error: " . $e->getMessage());
            return false;
        }
    }
}
?>