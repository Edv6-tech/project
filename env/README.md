# Chat Application - Clean Project Structure

## 📁 Folder Structure
```
project/
├── public/                 # Web accessible files
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── chat.php
│   ├── history.php
│   ├── google-auth.php
│   ├── google-callback.php
│   ├── css/
│   ├── js/
│   └── test-*.php
├── src/                    # Application logic
│   ├── DatabaseManager.php
│   ├── JWTService.php
│   ├── GoogleAuthService.php
│   ├── SessionManager.php
│   └── AIService.php
├── config/                  # Configuration
│   └── config.php
├── env/                     # Environment files
│   └── .env.example
├── database/                 # Database files
│   └── schema.sql
├── vendor/                   # Composer dependencies
├── composer.json
└── .env                     # Environment variables (local)
```

## 🎯 Clean Structure Benefits
- **public/** - Only web-accessible files
- **src/** - All PHP classes and logic
- **config/** - Configuration files
- **env/** - Environment variables
- **No extra folders** - Clean and organized

## 📋 Files to Remove
- SimpleJWT.php (use Composer JWT)
- test-*.php (move to public/)
- setup-*.php (move to public/)

## 🔧 Environment Setup
1. Copy `.env.example` to `.env`
2. Update `.env` with your values
3. Run `composer install`

## 📦 Dependencies
- firebase/php-jwt
- google/apiclient
