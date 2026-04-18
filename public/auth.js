let isLoginMode = true;

// 🔐 Toggle password visibility
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId + '-toggle-icon');
    
    if (!passwordField || !toggleIcon) return;

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.textContent = '👁️';
    } else {
        passwordField.type = 'password';
        toggleIcon.textContent = '👁️';
    }
}

// 🔄 Switch between login/register
function toggleForm() {
    isLoginMode = !isLoginMode;
    window.location.href = isLoginMode ? 'login.php' : 'register.php';
}

// ❌ Show error
function showError(message) {
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');

    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    if (successDiv) {
        successDiv.style.display = 'none';
    }
}

// ✅ Show success
function showSuccess(message) {
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');

    if (successDiv) {
        successDiv.textContent = message;
        successDiv.style.display = 'block';
    }

    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

// 🧹 Hide messages
function hideMessages() {
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');

    if (errorDiv) errorDiv.style.display = 'none';
    if (successDiv) successDiv.style.display = 'none';
}

// ⏳ Loading state
function setLoading(loading) {
    const loadingDiv = document.getElementById('loading');
    const submitBtn = document.querySelector('button[type="submit"]'); // ✅ safer

    if (loadingDiv) {
        loadingDiv.style.display = loading ? 'block' : 'none';
    }

    if (submitBtn) {
        submitBtn.disabled = loading;
    }
}

// 🌐 API call handler (FIXED)
async function postData(url = '', data = {}) {
    console.log('API Call URL:', url);
    console.log('API Call Data:', data);

    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        console.log('Status:', response.status);

        const result = await response.json(); // ✅ always read response

        console.log('Response:', result);

        return result; // ✅ return even if 400

    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

// 🔐 LOGIN
if (document.getElementById('login-form')) {
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        hideMessages();
        setLoading(true);

        try {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            const result = await postData('auth-jwt.php', {
                action: 'login',
                username,
                password
            });

            if (result.success) {
                showSuccess('Login successful! Redirecting...');
                setTimeout(() => {
                    window.location.href = 'chat.php';
                }, 1500);
            } else {
                showError(result.error || 'Login failed');
            }

        } catch (error) {
            showError(error.message || 'Something went wrong');
        } finally {
            setLoading(false);
        }
    });
}

// 📝 REGISTER
if (document.getElementById('register-form')) {
    document.getElementById('register-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        hideMessages();
        setLoading(true);

        try {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // 🔥 Frontend validation (NEW)
            if (!username || !email || !password || !confirmPassword) {
                return showError('All fields are required');
            }

            if (password !== confirmPassword) {
                return showError('Passwords do not match');
            }

            const result = await postData('auth-jwt.php', {
                action: 'register',
                username,
                email,
                password,
                confirm_password: confirmPassword
            });

            if (result.success) {
                showSuccess('Registration successful! Redirecting...');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                showError(result.error || 'Registration failed');
            }

        } catch (error) {
            showError(error.message || 'Something went wrong');
        } finally {
            setLoading(false);
        }
    });
}

// 🔍 CHECK SESSION
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const result = await postData('auth-jwt.php', {
            action: 'check_session'
        });

        if (result.success && result.logged_in) {
            window.location.href = 'chat.php';
        }
    } catch (error) {
        console.error('Session check error:', error);
    }
});

// 🚪 LOGOUT
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        postData('../api/auth.php', { action: 'logout' })
            .then(() => window.location.href = 'login.php')
            .catch(() => window.location.href = 'login.php');
    }
}