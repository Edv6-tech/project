<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AI Chat Application</title>
    <link rel="stylesheet" href="css/sky-blue-theme.css">
    <link rel="stylesheet" href="css/animations.css">
</head>
<body>
    <div class="bg-particles"></div>
    <div class="auth-container animate-slide-in-bottom">
        <div class="header">
            <h1>Join AI Chat</h1>
            <p>Create your account to start chatting with AI.</p>
        </div>

        <div class="alert alert-danger" id="error-message"></div>
        <div class="alert alert-success" id="success-message"></div>

        <form id="register-form" class="auth-form">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                <span class="input-icon">👤</span>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                <span class="input-icon">📧</span>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                <span class="input-icon">🔒</span>
                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                    <span id="password-toggle-icon">👁️</span>
                </button>
                <div class="password-strength">
                    <div class="password-strength-bar"></div>
                </div>
                <div class="password-requirements">Minimum 6 characters</div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                <span class="input-icon">🔒</span>
                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                    <span id="confirm_password-toggle-icon">👁️</span>
                </button>
            </div>

            <button type="submit" class="btn btn-primary" id="register-btn">Create Account</button>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            Creating account...
        </div>

        <div class="social-login">
            <div class="social-divider">
                <span>Or continue with</span>
            </div>
            <div class="social-buttons">
                <button class="social-btn google-btn" type="button">
                    <svg class="google-logo" viewBox="0 0 24 24" width="18" height="18">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.05-1.53-.22-2.25-.55A7.96 7.96 0 0 0 17.5 7c0-2.89.88-5.29 2.23-6.15l3.95-2.76c.84.53 1.81 1.26 2.91 1.26 1.03 0 1.93-.39 2.65-1.15l-3.45 2.49c-.27.2-.58.34-.91.34-.31 0-.9-.06-1.7-.25-2.42-.43-.97-.26-1.83-.58-2.49-.83l1.85-1.34c1.02.73 2.07 1.48 3.12 2.25.3.21.62.41 1.06.41z"/>
                        <path fill="#34A853" d="M12 15.5c3.04 0 5.5-2.46 5.5-5.5V13c0-3.04-2.46-5.5-5.5-5.5H7c-.27 0-.5.22-.5.5v-1c0-.28.22-.5.5-.5h5c2.96 0 5.5 2.46 5.5 5.5v2c0 .28.22.5.5.5h.5c.83 0 1.5-.67 1.5-1.5v-2c0-.83-.67-1.5-1.5-1.5z"/>
                    </svg>
                    <span>Google</span>
                </button>
                <button class="social-btn github-btn" type="button">
                    <svg class="github-logo" viewBox="0 0 24 24" width="18" height="18">
                        <path fill="currentColor" d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387-.599-.111-.793-.291-.793-.291-.564 0-1.782.704-2.366.704-.463 0-.925-.005-1.368.005-.913 0-1.602-.602-1.602-1.602-.315 0-1.789-.196-1.789-.196-.451.04-.919.257-1.188.46-.191-.164-.42-.289-.732-.289-.564 0-1.712.704-2.366.704-.816 0-1.416-.089-1.416-.089-.271 0-.492.154-.492.154-.48 0-.923-.242-1.13-.578-.199-.377-.402-.624-.402-.654 0-1.523.277-1.523.277-.635 0-1.395-.022-1.395-.022-.47 0-.945.213-1.37.623-.653-.187-.727-.392-1.009-.392-.96 0-1.9.002-1.9.002-1.998 0-3.083.589-3.083.589-.003 0-.003 0-.003.003z"/>
                    </svg>
                    <span>GitHub</span>
                </button>
            </div>
        </div>

        <div class="toggle-form">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
    <script src="auth.js"></script>
    <script src="js/enhanced-interactions.js"></script>
</body>
</html>
