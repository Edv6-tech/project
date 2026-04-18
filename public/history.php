<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - AI Chat Application</title>
    <link rel="stylesheet" href="css/enhanced-common.css">
    <link rel="stylesheet" href="css/sky-blue-theme.css">
    <link rel="stylesheet" href="css/animations.css">
</head>
<body>
    <div class="history-header">
        <h1>AI Chat Application</h1>
        <div class="header-actions">
            <span class="user-info" id="user-info">Loading...</span>
            <a href="chat.php" class="header-btn ripple">Chat</a>
            <button class="header-btn ripple" onclick="logout()">Logout</button>
        </div>
    </div>

    <div class="history-container">
        <h2 class="page-title" data-typing data-speed="50">Chat History</h2>
        
        <div class="error" id="error-message"></div>

        <div class="search-section">
            <div class="search-container">
                <input 
                    type="text" 
                    class="search-input" 
                    id="search-input" 
                    placeholder="Search conversations..."
                    onkeyup="searchSessions()"
                >
                <span class="search-icon">🔍</span>
            </div>
        </div>

        <div id="sessions-container">
            <div class="loading">
                <div class="spinner"></div>
                Loading chat history...
            </div>
        </div>
    </div>

    <script src="../api/history.js"></script>
    <script src="js/enhanced-interactions.js"></script>
</body>
</html>
