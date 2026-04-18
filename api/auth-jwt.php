<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../src/SessionManager.php';
require_once __DIR__ . '/../src/DatabaseManager.php';
require_once __DIR__ . '/../src/JWTService.php';

$sessionManager = new SessionManager();
$dbManager = new DatabaseManager();
$jwtService = new JWTService();

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'login':
            handleLogin($dbManager, $jwtService);
            break;
            
        case 'register':
            handleRegister($dbManager, $jwtService);
            break;
            
        case 'logout':
            handleLogout($jwtService);
            break;
            
        case 'check_session':
            handleSessionCheck($jwtService);
            break;
            
        case 'refresh_token':
            handleTokenRefresh($jwtService);
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

function handleLogin($dbManager, $jwtService) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        throw new Exception('Username and password are required');
    }
    
    try {
        $user = $dbManager->getUserByUsername($username);
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception('Invalid username or password');
        }
        
        // Generate JWT token
        $token = $jwtService->generateToken($user['id'], $user['username'], $user['email']);
        
        // Set token in cookie
        $jwtService->setTokenCookie($token);
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ]);
    } catch (Exception $e) {
        throw new Exception('Login failed: ' . $e->getMessage());
    }
}

function handleRegister($dbManager, $jwtService) {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($email) || empty($password)) {
        throw new Exception('All fields are required');
    }
    
    if ($password !== $confirmPassword) {
        throw new Exception('Passwords do not match');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    try {
        // Check if username already exists
        if ($dbManager->getUserByUsername($username)) {
            throw new Exception('Username already exists');
        }
        
        // Check if email already exists
        if ($dbManager->getUserByEmail($email)) {
            throw new Exception('Email already exists');
        }
        
        // Create new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userId = $dbManager->createUser($username, $email, $hashedPassword);
        
        if (!$userId) {
            throw new Exception('Failed to create user');
        }
        
        // Generate JWT token for new user
        $token = $jwtService->generateToken($userId, $username, $email);
        
        // Set token in cookie
        $jwtService->setTokenCookie($token);
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => [
                'id' => $userId,
                'username' => $username,
                'email' => $email
            ]
        ]);
    } catch (Exception $e) {
        throw new Exception('Registration failed: ' . $e->getMessage());
    }
}

function handleLogout($jwtService) {
    $jwtService->clearToken();
    
    echo json_encode([
        'success' => true,
        'message' => 'Logout successful'
    ]);
}

function handleSessionCheck($jwtService) {
    $user = $jwtService->getCurrentUser();
    
    if ($user) {
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
}

function handleTokenRefresh($jwtService) {
    $oldToken = $jwtService->extractTokenFromHeader();
    
    if (!$oldToken) {
        $oldToken = $jwtService->getTokenFromCookie();
    }
    
    if (!$oldToken) {
        throw new Exception('No token provided');
    }
    
    $newToken = $jwtService->refreshToken($oldToken);
    
    if (!$newToken) {
        throw new Exception('Token refresh failed');
    }
    
    $jwtService->setTokenCookie($newToken);
    
    echo json_encode([
        'success' => true,
        'message' => 'Token refreshed',
        'token' => $newToken
    ]);
}
?>
