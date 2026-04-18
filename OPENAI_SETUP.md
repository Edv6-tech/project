# OpenAI API Setup Guide

## Getting Your OpenAI API Key

To make your chat app responses exactly like ChatGPT, you need an OpenAI API key:

### Step 1: Create OpenAI Account
1. Go to [https://platform.openai.com/](https://platform.openai.com/)
2. Click "Sign up" (or "Log in" if you already have an account)
3. Complete the registration process

### Step 2: Add Billing
1. Once logged in, click on your profile (top right)
2. Go to "Manage account" → "Billing"
3. Add a payment method (credit card)
4. Add at least $5-10 credits to start

### Step 3: Generate API Key
1. Go to [API Keys section](https://platform.openai.com/account/api-keys)
2. Click "Create new secret key"
3. Give it a name (e.g., "Chat App")
4. **Copy the key immediately** (you won't see it again!)

### Step 4: Update Your .env File
1. Open `c:\xampp\htdocs\project\.env`
2. Find this line:
   ```
   AI_API_KEY=your-openai-api-key-here
   ```
3. Replace `your-openai-api-key-here` with your actual API key
4. Save the file

### Step 5: Test the Integration
1. Open command prompt in your project folder
2. Run: `php test-ai.php`
3. You should see a successful response!

## What You'll Get

With OpenAI integration, your chat app will have:
- **ChatGPT-quality responses** - Natural, helpful, and intelligent
- **Better reasoning** - More accurate and thoughtful answers
- **Conversation memory** - Remembers context throughout chats
- **Subject expertise** - Specialized knowledge for different topics
- **Image analysis** - Can understand and describe images you upload

## Cost Information

- **GPT-4o-mini**: ~$0.15 per 1M input tokens, ~$0.60 per 1M output tokens
- **Free tier**: $5 credit when you sign up
- Typical chat usage: Very low cost (pennies per day for normal usage)

## Troubleshooting

If you get API errors:
1. Check your API key is correct and starts with `sk-`
2. Verify you have credits in your OpenAI account
3. Make sure your internet connection is stable
4. Check the OpenAI status page for any outages

Need help? Check the [OpenAI documentation](https://platform.openai.com/docs) or contact their support.