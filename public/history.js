let sessions = [];
let filteredSessions = [];

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

function formatDate(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function truncateText(text, maxLength = 100) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

function getSessionPreview(sessionId) {
    // This would ideally fetch the first message of the session
    // For now, return a generic preview
    return 'Chat session started...';
}

function renderSessions(sessionsToRender) {
    const container = document.getElementById('sessions-container');
    
    if (!sessionsToRender || sessionsToRender.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <h3>No chat history found</h3>
                <p>Start a new conversation to see it here.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = '<div class="sessions-grid" id="sessions-grid"></div>';
    const grid = document.getElementById('sessions-grid');
    
    sessionsToRender.forEach(session => {
        const card = document.createElement('div');
        card.className = 'session-card';
        
        const preview = getSessionPreview(session.session_id);
        const sessionDate = formatDate(session.last_activity);
        
        card.innerHTML = `
            <div class="session-header">
                <div class="session-title">Session ${session.id}</div>
                <div class="session-date">${sessionDate}</div>
            </div>
            <div class="session-preview">${preview}</div>
            <div class="session-stats">
                <span>📅 Created: ${formatDate(session.created_at)}</span>
                <span>🕒 Last active: ${sessionDate}</span>
            </div>
            <div class="session-actions">
                <button class="btn-small" onclick="openSession('${session.session_id}')">Open Chat</button>
                <button class="btn-small btn-danger" onclick="deleteSession('${session.session_id}')">Delete</button>
            </div>
        `;
        
        grid.appendChild(card);
    });
}

async function loadSessions() {
    try {
        const result = await postData('../api/chatHandler.php', {
            action: 'get_sessions'
        });
        
        if (result.success) {
            sessions = result.sessions;
            filteredSessions = sessions;
            renderSessions(sessions);
        } else {
            showError(result.error || 'Failed to load chat history');
        }
    } catch (error) {
        showError('Network error. Please try again.');
        console.error('Load sessions error:', error);
    }
}

function searchSessions() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    
    if (!searchTerm) {
        filteredSessions = sessions;
    } else {
        filteredSessions = sessions.filter(session => {
            const sessionText = `Session ${session.id} ${session.last_activity}`.toLowerCase();
            return sessionText.includes(searchTerm);
        });
    }
    
    renderSessions(filteredSessions);
}

function openSession(sessionId) {
    // Store the session ID and redirect to chat
    sessionStorage.setItem('selectedSessionId', sessionId);
    window.location.href = 'chat.php';
}

async function deleteSession(sessionId) {
    if (!confirm('Are you sure you want to delete this chat session? This action cannot be undone.')) {
        return;
    }
    
    try {
        // Note: You would need to implement a delete session endpoint in the API
        showError('Delete functionality not yet implemented');
        
        // When implemented, you would call something like:
        // const result = await postData('../api/chatHandler.php', {
        //     action: 'delete_session',
        //     session_id: sessionId
        // });
        
        // Then reload the sessions
        // loadSessions();
        
    } catch (error) {
        showError('Failed to delete session');
        console.error('Delete session error:', error);
    }
}

async function loadUserInfo() {
    try {
        const result = await postData('../api/auth.php', {
            action: 'check_session'
        });
        
        if (result.success && result.logged_in) {
            document.getElementById('user-info').textContent = `Welcome, ${result.user.username}`;
        } else {
            window.location.href = 'login.php';
        }
    } catch (error) {
        console.error('Load user info error:', error);
        window.location.href = 'login.php';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUserInfo();
    loadSessions();
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
