# AI Chat Application

A complete PHP-based chat application with AI integration, following a clean MVC architecture.

## Features

- User authentication (login/register)
- Real-time chat with AI assistant
- Session management and chat history
- Responsive web interface
- Secure session handling
- Database integration

## Architecture

```
project/
├── public/          # Frontend UI files
│   ├── login.php   # Login page
│   ├── register.php # Registration page
│   ├── chat.php    # Main chat interface
│   ├── history.php # Chat history page
│   ├── auth.js     # Authentication JavaScript
│   ├── chat.js     # Chat functionality JavaScript
│   └── history.js  # History page JavaScript
├── api/            # API endpoints
│   ├── auth.php    # Authentication API
│   └── chatHandler.php # Chat API
├── src/            # Backend logic
│   ├── DatabaseManager.php # Database operations
│   ├── SessionManager.php  # Session management
│   ├── AIService.php       # AI service integration
│   └── ChatHandler.php     # Chat processing logic
├── config/         # Configuration files
│   ├── database.php # Database connection
│   └── config.php   # App configuration
├── database/       # Database files
│   └── schema.sql   # Database schema
└── index.php       # Entry point
```

## Setup Instructions

### 1. Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB
- XAMPP/WAMP/MAMP (or similar web server)
- **Google Gemini API key** (for AI responses)

### 2. Get Gemini API Key

1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy the generated API key
5. Add it to your environment variables

### 3. Environment Configuration

1. Copy `.env.example` to `.env` (if it exists) or create `.env` file
2. Update the following values in `.env`:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=chat_app
DB_USER=root
DB_PASS=

# AI Service Configuration (GEMINI AI)
AI_SERVICE_URL=https://generativelanguage.googleapis.com/v1beta/models/gemini-pro-vision:generateContent
AI_API_KEY=your-gemini-api-key-here  # Replace with your actual Gemini API key
AI_MODEL=gemini-pro-vision
```

### 4. Database Setup

Run the database setup script:
```bash
php setup-database.php
```

### 5. Start the Application

1. Start your web server (XAMPP/WAMP/MAMP)
2. Open your browser and go to: `http://localhost/project/public/`
3. Register a new account or login
4. Start chatting with the AI!

## AI Features

The application now uses **Google's Gemini AI models** for intelligent responses:

- **Gemini Pro Vision**: Advanced AI with text and image understanding
- **Conversation memory**: Maintains context throughout the chat
- **Subject-specific expertise**: Specialized knowledge for different topics
- **Image analysis**: Can analyze uploaded images with vision capabilities
- **Natural conversation flow**: Responses feel conversational and helpful

## API Endpoints

- `POST /api/chat.php` - Send chat messages
- `GET /api/getChats.php` - Get user's chat sessions
- `POST /api/deleteChat.php` - Delete a chat session
- `POST /api/renameChat.php` - Rename a chat session

### 2. Database Setup

1. Create a MySQL database named `chat_app`
2. Import the database schema from `database/schema.sql`
3. Update database credentials in `config/database.php` if needed

### 3. Configuration

1. Open `config/config.env.php` (or use environment variables)
2. Replace `your-gemini-api-key-here` with your actual Gemini API key
3. Update other configuration settings as needed

### 4. Web Server Setup

1. Place the project in your web server's document root (e.g., `htdocs/` for XAMPP)
2. Ensure PHP extensions are enabled:
   - PDO
   - PDO_MySQL
   - cURL
   - JSON

### 5. Access the Application

1. Start your web server
2. Navigate to `http://localhost/project` in your browser
3. Register a new account or login
4. Start chatting with the AI!

## Usage

### Authentication
- Register a new account with username, email, and password
- Login with your credentials
- Sessions are managed automatically

### Chat Features
- Send messages to the AI assistant
- View conversation history
- Start new chat sessions
- Switch between different sessions

### History Management
- View all previous chat sessions
- Search through chat history
- Open specific sessions to continue conversations

## API Endpoints

### Authentication (`/api/auth.php`)
- `POST action=login` - User login
- `POST action=register` - User registration
- `POST action=logout` - User logout
- `POST action=check_session` - Check session validity

### Chat (`/api/chatHandler.php`)
- `POST action=send_message` - Send message to AI
- `POST action=get_history` - Get chat history
- `POST action=new_session` - Create new chat session
- `POST action=get_sessions` - Get user sessions
- `POST action=validate_ai` - Validate AI service configuration

## Security Features

- Password hashing using PHP's `password_hash()`
- Session-based authentication
- SQL injection prevention with prepared statements
- Input validation and sanitization
- CSRF protection considerations

## Customization

### AI Service Configuration
To use a different AI service, modify the `AIService.php` class:

1. Update the API endpoint in `config/config.php`
2. Modify the request/response handling in `AIService.php`
3. Adjust the message formatting as needed

### Database Configuration
Update database settings in `config/database.php`:
- Host, database name, username, password
- Connection parameters and options

### UI Customization
Modify CSS and HTML files in the `public/` directory:
- Update colors and styling
- Add new features to the interface
- Modify layout and responsive design

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check MySQL server is running
   - Verify database credentials in `config/database.php`
   - Ensure database `chat_app` exists

2. **AI Service Not Working**
   - Verify OpenAI API key is set correctly
   - Check internet connection
   - Ensure cURL is enabled in PHP

3. **Session Issues**
   - Check PHP session configuration
   - Verify session storage directory is writable
   - Clear browser cookies if needed

4. **Permission Errors**
   - Ensure web server has read permissions for all files
   - Check write permissions for session storage

### Debug Mode

Enable debug mode in `config/config.php`:
```php
define('DEBUG_MODE', true);
```

This will display PHP errors and warnings for troubleshooting.

## License

This project is open source and available under the MIT License.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review the code comments
3. Check browser console for JavaScript errors
4. Review server error logs
