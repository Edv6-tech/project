# AI Chat Application

A modern, mobile-responsive AI chat application with multimodal capabilities, featuring ChatGPT-style interface, subject-specific conversations, and comprehensive chat management.

## 🌟 Features

- **📱 Mobile Responsive Design** - Works perfectly on all screen sizes
- **💬 ChatGPT-Style Interface** - Professional chat experience
- **🎯 Subject-Specific Modes** - Mathematics, Chemistry, Physics, Programming, Biology, Literature
- **📷 Multimodal Support** - Text and image inputs
- **💾 Chat Management** - Create, rename, delete, and switch between conversations
- **🎨 Modern UI** - Smooth animations, hover effects, and intuitive design
- **🔐 Secure Authentication** - JWT-based user authentication
- **📊 Real-time Responses** - Typing animations and smooth transitions

## 🚀 Quick Start

### Prerequisites
- PHP 8.0+
- MySQL/MariaDB
- Apache/Nginx server
- Composer (for dependencies)

### Installation

1. **Clone Repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/ai-chat-application.git
   cd ai-chat-application
   ```

2. **Database Setup**
   ```sql
   CREATE DATABASE ai_chat;
   USE ai_chat;
   
   -- Users table
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       email VARCHAR(255) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   
   -- Sessions table
   CREATE TABLE sessions (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       title VARCHAR(255),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id)
   );
   
   -- Messages table
   CREATE TABLE messages (
       id INT AUTO_INCREMENT PRIMARY KEY,
       session_id INT NOT NULL,
       user_message TEXT,
       ai_response TEXT,
       user_image_data LONGTEXT,
       message_type ENUM('text', 'image') DEFAULT 'text',
       timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (session_id) REFERENCES sessions(id)
   );
   ```

3. **Configuration**
   ```bash
   # Copy config template
   cp config/config.example.php config/config.php
   
   # Edit configuration
   nano config/config.php
   ```

4. **Install Dependencies**
   ```bash
   composer install
   ```

5. **Set Permissions**
   ```bash
   chmod -R 755 public/
   chmod -R 755 api/
   ```

6. **Configure Web Server**
   
   **Apache (.htaccess already included):**
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ public/$1 [L]
   ```

## 📁 Project Structure

```
ai-chat-application/
├── public/                 # Web root
│   ├── chat.php           # Main chat interface
│   ├── login.php          # User login
│   ├── register.php       # User registration
│   └── index.php         # Landing page
├── api/                  # API endpoints
│   ├── login.php         # User authentication
│   ├── register.php      # User registration
│   ├── chat.php         # Chat API
│   ├── getChats.php     # Get user chats
│   ├── getMessages.php  # Load chat messages
│   ├── renameChat.php   # Rename chat
│   └── deleteChat.php   # Delete chat
├── src/                  # Core classes
│   ├── AIService.php     # AI integration
│   ├── ChatHandler.php   # Chat management
│   ├── JWTService.php    # Authentication
│   └── Database.php      # Database connection
├── config/               # Configuration
│   ├── config.php        # Main config
│   └── db.php          # Database config
├── debug/               # Debug tools
└── README.md
```

## 🔧 Configuration

### AI Service Configuration
Edit `config/config.php`:

```php
define('AI_API_KEY', 'your_gemini_api_key');
define('AI_SERVICE_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro-vision:generateContent');
define('AI_MODEL', 'gemini-pro-vision');
```

### Database Configuration
Edit `config/db.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ai_chat');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
```

## 🌐 API Endpoints

### Authentication
- `POST /api/login.php` - User login
- `POST /api/register.php` - User registration

### Chat Management
- `GET /api/getChats.php` - Get user's chat list
- `GET /api/getMessages.php?chat_id={id}` - Load chat messages
- `POST /api/chat.php` - Send message to AI
- `POST /api/renameChat.php` - Rename chat
- `POST /api/deleteChat.php` - Delete chat

## 📱 Mobile Responsiveness

The application is fully responsive with breakpoints for:
- **Desktop (>1200px)** - Full sidebar and chat interface
- **Tablet (768px-1200px)** - Reduced sidebar width
- **Mobile (<768px)** - Slide-out sidebar drawer
- **Small Mobile (<480px)** - Full-screen optimization

## 🎨 Features

### Chat Interface
- **Real-time typing animations**
- **Smooth fade transitions**
- **Message history persistence**
- **Image upload support**
- **Subject-specific AI responses**

### Chat Management
- **Create new chats**
- **Rename existing chats**
- **Delete unwanted chats**
- **Switch between conversations**
- **Automatic chat titles**

### User Experience
- **Mobile-first design**
- **Touch-friendly interface**
- **Keyboard shortcuts**
- **Dark/light mode ready**
- **Accessibility compliant**

## 🛠️ Development

### Local Development Setup
1. Start XAMPP/MAMP/WAMP
2. Create database and tables
3. Configure `config/config.php`
4. Navigate to `http://localhost/project/`
5. Register/login and start chatting

### Debug Tools
- `/debug_chat_interface.php` - Comprehensive chat debugging
- `/force_refresh.php` - Browser cache clearing guide
- `/test_chat_fix.php` - Chat functionality verification

## 🔒 Security

- **JWT-based authentication**
- **SQL injection prevention**
- **XSS protection**
- **CSRF tokens**
- **Input validation**
- **Secure password hashing**

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📞 Support

For support and questions:
- Create an issue in the repository
- Check the debug tools for common issues
- Review the documentation for setup guides

## 🌟 Credits

Built with modern web technologies and AI integration for an exceptional chat experience.
