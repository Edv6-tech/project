# 🌍 Environment Variables Setup Guide

## 🎯 Why Use .env Files?

**Environment variables** are the industry standard for managing configuration:
- **🔒 Secure** - No API keys in code
- **🔄 Flexible** - Different settings per environment
- **👥 Team-friendly** - Each developer has own config
- **🚀 Production-ready** - Easy deployment

## 📁 Files Created

### 1. `.env.example` - Template for your team
```bash
# Copy this to create your local environment
cp .env.example .env
```

### 2. `src/EnvLoader.php` - Environment loader class
- **Auto-loads** .env file
- **Parses** key=value pairs
- **Provides** helper methods
- **Handles** quotes and comments

### 3. `config/config.env.php` - Uses environment variables
- **Loads** EnvLoader
- **Defines** constants from .env
- **Provides** defaults for missing values
- **Handles** development vs production

## 🚀 Quick Setup

### Step 1: Create Your Environment
```bash
# Copy the template
cp .env.example .env

# Edit with your actual values
nano .env
```

### Step 2: Fill Your .env File
```bash
# Required - Get from Google AI Studio
GEMINI_API_KEY=your_actual_gemini_api_key

# Required - Generate secure random string
JWT_SECRET_KEY=your_32_character_minimum_secret_key

# Required - Database credentials
DB_HOST=localhost
DB_NAME=ai_chat
DB_USER=your_db_user
DB_PASS=your_db_password
```

### Step 3: Update Configuration
```bash
# Switch to environment-based config
# Replace: require_once 'config.php'
# With: require_once 'config.env.php'
```

## 🔧 Configuration Options

### AI Service
```bash
GEMINI_API_KEY=your_gemini_api_key
AI_SERVICE_URL=https://generativelanguage.googleapis.com/v1beta/models/gemini-pro-vision:generateContent
AI_MODEL=gemini-pro-vision
```

### Security
```bash
JWT_SECRET_KEY=minimum_32_characters_random_string
JWT_ALGORITHM=HS256
JWT_EXPIRE_TIME=86400
```

### Database
```bash
DB_HOST=localhost
DB_NAME=ai_chat
DB_USER=root
DB_PASS=your_password
```

### Application
```bash
APP_NAME=AI Chat Application
APP_URL=http://localhost/project
APP_ENV=development
APP_DEBUG=true
```

## 🌐 Environment Types

### Development (.env)
```bash
APP_ENV=development
APP_DEBUG=true
DB_HOST=localhost
APP_URL=http://localhost/project
```

### Production (.env.production)
```bash
APP_ENV=production
APP_DEBUG=false
DB_HOST=your-production-db.com
APP_URL=https://yourdomain.com
```

## 🔒 Security Best Practices

### ✅ Do This:
- Keep `.env` out of version control
- Use different keys per environment
- Rotate API keys regularly
- Use strong JWT secrets
- Set proper file permissions

### ❌ Don't Do This:
- Commit `.env` to git
- Share API keys in public repos
- Use default JWT secrets
- Hard-code credentials
- Ignore file permissions

## 🔄 Usage in Code

### Using EnvLoader
```php
// Load environment variables
require_once 'src/EnvLoader.php';

// Get required variable
$apiKey = EnvLoader::require('GEMINI_API_KEY');

// Get optional variable with default
$debug = EnvLoader::get('APP_DEBUG', false);

// Check if variable exists
if (EnvLoader::get('SOME_VAR')) {
    // Use it
}
```

### Using Constants
```php
// After loading config.env.php
echo AI_API_KEY;        // From environment
echo APP_NAME;          // From environment
echo DB_HOST;           // From environment
```

## 🚀 Deployment

### Local Development
```bash
# Create local .env
cp .env.example .env
# Edit with local values
# Application works automatically
```

### Production Deployment
```bash
# Create production .env
cp .env.example .env.production
# Edit with production values
# Deploy with environment variables
```

### Server Configuration
```bash
# Set environment variables directly
export GEMINI_API_KEY="production_key"
export JWT_SECRET_KEY="production_secret"
export DB_HOST="production_db"
```

## 🔍 Troubleshooting

### Common Issues

**"Environment variable not found"**
```bash
# Check .env file exists
ls -la .env

# Check syntax
cat .env

# Verify permissions
chmod 600 .env
```

**"API key not working"**
```bash
# Verify .env is loaded
var_dump(getenv('GEMINI_API_KEY'));

# Check for quotes
# Remove quotes around values
```

**"Database connection failed"**
```bash
# Check DB credentials
grep DB_ .env

# Test connection manually
mysql -h localhost -u user -p
```

## 📋 Security Checklist

- [ ] `.env` created from `.env.example`
- [ ] `.env` contains real API keys
- [ ] `.env` is NOT in git repository
- [ ] `.env.example` IS in git repository
- [ ] File permissions are set correctly (600)
- [ ] Different keys for each environment
- [ ] JWT secret is 32+ characters
- [ ] API keys are rotated regularly

**Your environment setup is now production-ready!** 🌍
