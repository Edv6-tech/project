let currentSessionId = null;
let sessions = [];
let isLoadingHistory = false;

function showError(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}

function hideError() {
    document.getElementById('error-message').style.display = 'none';
}

function setLoading(loading) {
    const sendBtn = document.getElementById('send-btn');
    const messageInput = document.getElementById('message-input');
    
    if (loading) {
        sendBtn.disabled = true;
        sendBtn.textContent = 'Sending...';
        messageInput.disabled = true;
    } else {
        sendBtn.disabled = false;
        sendBtn.textContent = 'Send';
        messageInput.disabled = false;
    }
}

async function postData(url = '', data = {}) {
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    const response = await fetch(url, {
        method: 'POST',
        credentials: 'include',
        body: formData
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

function formatDate(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function addMessage(message, isUser = false) {
    const messagesContainer = document.getElementById('messages-container');
    
    // Remove empty state if it exists
    const emptyState = messagesContainer.querySelector('.empty-state');
    if (emptyState) {
        emptyState.remove();
    }

    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${isUser ? 'user' : 'ai'}`;
    
    messageDiv.innerHTML = `
        <div class="message-avatar">${isUser ? 'U' : 'AI'}</div>
        <div class="message-content">
            <div>${message}</div>
            <div class="message-time">${formatTime(new Date().toISOString())}</div>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function loadMessages(history) {
    const messagesContainer = document.getElementById('messages-container');
    messagesContainer.innerHTML = '';
    
    if (!history || history.length === 0) {
        messagesContainer.innerHTML = `
            <div class="empty-state">
                <h3>No messages yet</h3>
                <p>Start a conversation by typing a message below.</p>
            </div>
        `;
        return;
    }

    history.forEach(item => {
        addMessage(item.user_message, true);
        addMessage(item.ai_response, false);
    });
}

function loadSessions(sessionsList) {
    const sessionsContainer = document.getElementById('sessions-list');
    sessionsContainer.innerHTML = '';
    
    if (!sessionsList || sessionsList.length === 0) {
        sessionsContainer.innerHTML = `
            <div class="empty-state">
                <p>No previous conversations</p>
            </div>
        `;
        return;
    }

    sessions = sessionsList;
    
    sessionsList.forEach(session => {
        const sessionDiv = document.createElement('div');
        sessionDiv.className = 'session-item';
        sessionDiv.dataset.sessionId = session.session_id;
        if (session.session_id === currentSessionId) {
            sessionDiv.classList.add('active');
        }
        
        const title = session.title || `Session ${session.session_id}`;
        
        sessionDiv.innerHTML = `
            <div class="session-content">
                <div class="session-title">${title}</div>
                <div class="session-date">${formatDate(session.last_activity)}</div>
            </div>
            <div class="session-actions">
                <button class="edit-btn" onclick="event.stopPropagation(); editSessionTitle('${session.session_id}', '${title.replace(/'/g, "\\'")}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="delete-btn" onclick="event.stopPropagation(); deleteSession('${session.session_id}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        sessionDiv.onclick = (evt) => selectSession(session.session_id, evt);
        sessionsContainer.appendChild(sessionDiv);
    });
}

async function selectSession(sessionId, evt) {
    if (isLoadingHistory) return;
    
    isLoadingHistory = true;
    currentSessionId = sessionId;
    
    // Update active state
    document.querySelectorAll('.session-item').forEach(item => {
        item.classList.remove('active');
    });
    const selected = document.querySelector(`.session-item[data-session-id="${sessionId}"]`);
    selected?.classList.add('active');
    
    try {
        const result = await postData('../api/chatHandler.php', {
            action: 'get_history',
            session_id: sessionId
        });
        
        if (result.success) {
            loadMessages(result.history);
        } else {
            showError(result.error || 'Failed to load chat history');
        }
    } catch (error) {
        showError('Network error. Please try again.');
        console.error('Load history error:', error);
    } finally {
        isLoadingHistory = false;
    }
}

async function sendMessage() {
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    hideError();
    setLoading(true);
    
    // Add user message immediately
    addMessage(message, true);
    messageInput.value = '';
    
    // Adjust textarea height
    messageInput.style.height = 'auto';
    
    try {
        const result = await postData('../api/chatHandler.php', {
            action: 'send_message',
            message: message,
            session_id: currentSessionId
        });
        
        if (result.success) {
            addMessage(result.ai_response, false);
        } else {
            showError(result.error || 'Failed to send message');
        }
    } catch (error) {
        showError('Network error. Please try again.');
        console.error('Send message error:', error);
    } finally {
        setLoading(false);
    }
}

async function newChat() {
    try {
        const result = await postData('../api/chatHandler.php', {
            action: 'new_session'
        });
        
        if (result.success) {
            currentSessionId = result.session_id;
            loadMessages([]); // Clear messages
            await loadUserSessions(); // Refresh sessions list from server
        } else {
            showError(result.error || 'Failed to create new session');
        }
    } catch (error) {
        showError('Network error. Please try again.');
        console.error('New chat error:', error);
    }
}

async function loadUserInfo() {
    try {
        const response = await fetch('api/get-user-info.php', {
            credentials: 'include'
        });
        const data = await response.json();
        
        if (data.success) {
            // Update user info display like ChatGPT
            const userAvatar = document.getElementById('user-avatar-img');
            const userName = document.getElementById('user-name');
            const userEmail = document.getElementById('user-email');
            
            if (data.user.profile_picture) {
                userAvatar.src = data.user.profile_picture;
            } else {
                userAvatar.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjIiIHZpZXdCb3g9IjAgMjQgMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2UwZTBmYiIvPjxwYXRoIGQ9Ik0xMCAxMmMwIDUuNCAxMiAxMiA1LjQgMTJTMCA1LjQgMTIgMTIgMTJNMCAxMmMwLTUuNC0xMi0xMi0xMlMCA1LjQgMTIgMTIgMTJ6bTEyIDBjMC02LjYgMC0xMiA2LjYgMTJoMTJjNi42IDAgMTIgNi42IDEyIDEyem0wIDEyYzAtNi42IDAtMTItNi40LTEySDEyYy02LjQgMC0xMiA2LjQtMTJ6IiBmaWxsPSIjOTk5OTk5Ii8+PC9zdmc+';
            }
            
            userName.textContent = data.user.name || 'User';
            userEmail.textContent = data.user.email || '';
            
            // Add hover effect
            const userInfo = document.getElementById('user-info');
            userInfo.title = `${data.user.name}\n${data.user.email}`;
        } else {
            console.error('Failed to load user info');
        }
    } catch (error) {
        console.error('Error loading user info:', error);
    }
}

async function loadUserSessions() {
    try {
        const result = await postData('../api/chatHandler.php', {
            action: 'get_sessions'
        });
        
        if (result.success) {
            loadSessions(result.sessions);
        } else {
            showError(result.error || 'Failed to load sessions');
        }
    } catch (error) {
        showError('Network error. Please try again.');
        console.error('Load sessions error:', error);
    }
}

// Auto-resize textarea
document.getElementById('message-input').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Send message on Ctrl+Enter or Cmd+Enter
document.getElementById('message-input').addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        sendMessage();
    }
});

// Send message on Enter (without Ctrl) for single line
document.getElementById('message-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey && this.style.height === '50px') {
        e.preventDefault();
        sendMessage();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUserInfo();
    loadUserSessions();
    
    // Validate AI service
    postData('../api/chatHandler.php', { action: 'validate_ai' })
        .then(result => {
            if (!result.success) {
                showError(result.error);
            }
        })
        .catch(error => {
            console.error('AI validation error:', error);
        });
});

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        postData('../api/auth.php', { action: 'logout' })
            .then(() => {
                window.location.href = 'login.php';
            })
            .catch(error => {
                console.error('Logout error:', error);
                window.location.href = 'login.php';
            });
    }
}

async function editSessionTitle(sessionId, currentTitle) {
    const newTitle = prompt('Enter new chat title:', currentTitle);
    if (newTitle && newTitle.trim() !== currentTitle) {
        try {
            const result = await fetch('../api/renameChat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'include',
                body: JSON.stringify({
                    chat_id: sessionId,
                    title: newTitle.trim()
                })
            });
            
            const data = await result.json();
            if (data.success) {
                // Reload sessions to show updated title
                loadUserSessions();
            } else {
                showError(data.error || 'Failed to rename chat');
            }
        } catch (error) {
            showError('Network error. Please try again.');
            console.error('Rename error:', error);
        }
    }
}

async function deleteSession(sessionId) {
    if (confirm('Are you sure you want to delete this chat? This action cannot be undone.')) {
        try {
            const result = await fetch('../api/deleteChat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'include',
                body: JSON.stringify({
                    chat_id: sessionId
                })
            });
            
            const data = await result.json();
            if (data.success) {
                if (currentSessionId === sessionId) {
                    currentSessionId = null;
                    loadMessages([]);
                }

                // Reload sessions
                await loadUserSessions();
            } else {
                showError(data.error || 'Failed to delete chat');
            }
        } catch (error) {
            showError('Network error. Please try again.');
            console.error('Delete error:', error);
        }
    }
}
