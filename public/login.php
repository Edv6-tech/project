<?php
// login.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>ZAP AI</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    height: 100vh;
    font-family: "Inter", sans-serif;
    background: radial-gradient(circle at top left, #0f172a, #020617);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* ✨ Animated background glow */
body::before {
    content: "";
    position: absolute;
    width: 600px;
    height: 600px;
    background: #2563eb;
    filter: blur(200px);
    opacity: 0.3;
    top: -100px;
    left: -100px;
}

body::after {
    content: "";
    position: absolute;
    width: 500px;
    height: 500px;
    background: #7c3aed;
    filter: blur(200px);
    opacity: 0.3;
    bottom: -100px;
    right: -100px;
}

/* 🧊 Glass card */
.login-box {
    position: relative;
    z-index: 2;
    width: 380px;
    padding: 40px;
    border-radius: 20px;

    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.1);

    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    text-align: center;
}

/* ⚡ Logo */
.logo {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
}

.logo span {
    background: linear-gradient(90deg, #3b82f6, #9333ea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* subtitle */
.subtitle {
    font-size: 14px;
    color: #94a3b8;
    margin-bottom: 30px;
}

/* 🚀 Button */
.google-btn {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    
    background: white;
    color: #1f2937;
    
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    text-decoration: none;
}

.google-btn:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    background: #f8fafc;
}

.google-logo {
    width: 20px;
    height: 20px;
    display: inline-block;
}

/* subtle footer */
.footer {
    margin-top: 25px;
    font-size: 12px;
    color: #64748b;
}
</style>
</head>

<body>

<div class="login-box">
    <div class="logo">
        ZAP <span>AI ⚡</span>
    </div>

    <div class="subtitle">
        Your intelligent study assistant
    </div>

    <button class="google-btn" onclick="loginWithGoogle()">
        <svg class="google-logo" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l2.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Continue with Google
    </button>

    <div class="footer">
        Secure login powered by Google
    </div>
</div>

<script>
function loginWithGoogle() {
    console.log("Google login clicked - redirecting to Google OAuth");
    window.location.href = '/project/public/google-callback.php';
}

// Test button on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log("Page loaded, checking button");
    const button = document.querySelector('.google-btn');
    if (button) {
        console.log("Button found:", button);
        button.addEventListener('click', function(e) {
            console.log("Button clicked via event listener");
            e.preventDefault();
            loginWithGoogle();
        });
    } else {
        console.error("Button not found!");
    }
});

// 🔥 AUTO LOGIN CHECK
console.log("Starting auth check...");
fetch('/project/api/check-auth.php')
.then(res => {
    console.log("Auth check response status:", res.status);
    return res.json();
})
.then(data => {
    console.log("Auth check data:", data);
    if (data.authenticated) {
        console.log("User authenticated, redirecting to subject.php");
        window.location.href = "/project/subject.php";
    }
})
.catch(err => {
    console.error("Auth check failed:", err);
});
</script>

</body>
</html>