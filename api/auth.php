<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../src/SessionManager.php';
require_once __DIR__ . '/../src/DatabaseManager.php';

$sessionManager = new SessionManager();
$dbManager = new DatabaseManager();

$action = $_POST['action'] ?? '';

try {

    switch ($action) {

        // 🔐 LOGIN
        case 'login':
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                throw new Exception('Username/Email and password are required');
            }

            // 🔥 Allow login with username OR email
            $user = $dbManager->getUserByUsername($username);
            if (!$user) {
                $user = $dbManager->getUserByEmail($username);
            }

            if (!$user || !password_verify($password, $user['password_hash'])) {
                throw new Exception('Invalid login credentials');
            }

            $sessionManager->login($user['id']);

            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ]);
            break;


        // 📝 REGISTER
        case 'register':
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // 🔍 Validation
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                throw new Exception('All fields are required');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            if ($password !== $confirmPassword) {
                throw new Exception('Passwords do not match');
            }

            if (strlen($password) < 6) {
                throw new Exception('Password must be at least 6 characters');
            }

            // 🔥 Check duplicates
            if ($dbManager->getUserByUsername($username)) {
                throw new Exception('Username already exists');
            }

            if ($dbManager->getUserByEmail($email)) {
                throw new Exception('Email already exists');
            }

            // 🔐 Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // 💾 Create user
            $userId = $dbManager->createUser($username, $email, $passwordHash);

            // 🔥 Auto login after register
            $sessionManager->login($userId);

            echo json_encode([
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => $userId,
                    'username' => $username,
                    'email' => $email
                ]
            ]);
            break;


        // 🚪 LOGOUT
        case 'logout':
            $sessionManager->logout();
            echo json_encode([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
            break;


        // 🔍 CHECK SESSION
        case 'check_session':
            $isValid = $sessionManager->validateSession();

            if ($isValid) {
                $userId = $sessionManager->getCurrentUserId();
                $user = $dbManager->getUserById($userId);

                echo json_encode([
                    'success' => true,
                    'logged_in' => true,
                    'user' => $user
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'logged_in' => false
                ]);
            }
            break;


        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}