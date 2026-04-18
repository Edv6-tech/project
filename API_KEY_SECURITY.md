# ⚠️ API KEY SECURITY - CRITICAL INFORMATION

## 🚨 IMMEDIATE ACTION REQUIRED

Your Gemini API key is currently exposed in `config/config.php` and will be **PUBLICLY VISIBLE** if you push to GitHub!

## 🔧 Steps to Secure Your API Key

### 1. Create Safe Configuration Template
```bash
# Your config.php currently contains:
define('AI_API_KEY', 'your_actual_api_key_here');  # ⚠️ VISIBLE ON GITHUB!

# We've created config.example.php with:
define('AI_API_KEY', 'YOUR_GEMINI_API_KEY_HERE');  # ✅ SAFE TEMPLATE
```

### 2. Update Your Local Config
```bash
# Keep your actual API key in config.php (for local use)
# But NEVER commit this file to GitHub
```

### 3. Verify .gitignore Protection
Your `.gitignore` already excludes:
```
config/config.php
config/db.php
.env
.env.local
```

### 4. Test Before Pushing
```bash
# Check what will be committed
git status

# Should NOT show config/config.php
# Should show config.example.php
```

## 🛡️ Security Best Practices

### ✅ What to Do:
- Keep `config.php` locally only
- Use `config.example.php` as template
- Add API key to environment variables in production
- Rotate API keys if accidentally exposed

### ❌ What NOT to Do:
- Commit `config.php` to GitHub
- Hard-code API keys in source code
- Share API keys in public repositories
- Use the same API key across multiple projects

## 🔄 If API Key Was Already Exposed

1. **Revoke the exposed key** immediately
2. **Generate new API key** from Google AI Studio
3. **Update local config.php** with new key
4. **Remove exposed commits** from GitHub history
5. **Force push clean history**

## 🌐 Production Deployment

### Environment Variables Method:
```php
// In config.php, use environment variables:
define('AI_API_KEY', $_ENV['GEMINI_API_KEY'] ?? '');
```

### .env File Method:
```bash
# Create .env file (also in .gitignore):
GEMINI_API_KEY=your_actual_api_key_here
```

## 🔍 How to Check if Exposed

1. **Search your repository** on GitHub
2. **Look for API key** in commit history
3. **Check repository settings** for secrets
4. **Use GitHub's secret scanning** if available

## 📞 Emergency Actions

If you accidentally exposed your API key:
1. **Revoke immediately** at [Google AI Studio](https://makersuite.google.com/app/apikey)
2. **Delete repository** if necessary
3. **Create new repository** with proper security
4. **Set up GitHub secrets** for future deployments

## ✅ Security Checklist Before Pushing

- [ ] `config/config.php` is NOT in git status
- [ ] `config.example.php` IS in git status
- [ ] `.gitignore` excludes sensitive files
- [ ] No API keys in commit history
- [ ] Environment variables configured for production

**SECURE YOUR API KEY BEFORE PUSHING TO GITHUB!** 🚨
