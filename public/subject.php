<?php
require_once __DIR__ . '/../src/JWTService.php';

$jwtService = new JWTService();

if (!isset($_COOKIE['jwt_token'])) {
    header("Location: /project/public/login.php");
    exit;
}

$user = $jwtService->validateToken($_COOKIE['jwt_token']);

if (!$user) {
    header("Location: /project/public/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZAP AI Assistant - Subject Selection</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* 🌌 Global Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            color: #f1f5f9;
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }

        /* 🌟 Animated Background Canvas */
        #bg-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            opacity: 0.6;
        }

        /* ⚡ Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0a0e27 0%, #16213e 50%, #1a1a3e 100%);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        /* Animated Gradient Background */
        .loading-overlay::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
            animation: gradientShift 8s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes gradientShift {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.1);
            }
        }

        .loading-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 40px;
        }

        /* Animated Orb Container */
        .loading-orb {
            position: relative;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .orb-core {
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, rgba(59, 130, 246, 0.8), rgba(139, 92, 246, 0.6));
            animation: orbPulse 2s ease-in-out infinite, orbFloat 3s ease-in-out infinite;
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.6),
                        0 0 60px rgba(139, 92, 246, 0.4),
                        inset -20px -20px 40px rgba(0, 0, 0, 0.4);
        }

        @keyframes orbPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }

        @keyframes orbFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        /* Rotating rings around orb */
        .orb-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid transparent;
            border-top-color: rgba(59, 130, 246, 0.6);
            border-right-color: rgba(139, 92, 246, 0.4);
            animation: orbRotate 3s linear infinite;
        }

        .orb-ring:nth-child(2) {
            width: 140%;
            height: 140%;
            left: -20%;
            top: -20%;
            border-top-color: rgba(139, 92, 246, 0.5);
            border-right-color: rgba(236, 72, 153, 0.3);
            animation: orbRotate 4s linear infinite reverse;
        }

        .orb-ring:nth-child(3) {
            width: 180%;
            height: 180%;
            left: -40%;
            top: -40%;
            border-top-color: rgba(139, 92, 246, 0.3);
            animation: orbRotate 5s linear infinite;
        }

        @keyframes orbRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Loading Text Styling */
        .loading-text {
            font-size: 28px;
            font-weight: 700;
            color: #f1f5f9;
            text-align: center;
            letter-spacing: 0.5px;
            opacity: 0;
            animation: textReveal 0.8s ease-out forwards, textGlow 2s ease-in-out 0.5s infinite;
            animation-delay: 0.3s;
            line-height: 1.4;
            max-width: 500px;
            background: linear-gradient(135deg, #e2e8f0 0%, #bfdbfe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @keyframes textReveal {
            from {
                opacity: 0;
                transform: translateY(20px);
                filter: blur(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }

        @keyframes textGlow {
            0%, 100% {
                text-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 0 40px rgba(139, 92, 246, 0.2);
            }
            50% {
                text-shadow: 0 0 30px rgba(59, 130, 246, 0.5), 0 0 60px rgba(139, 92, 246, 0.3);
            }
        }

        /* Progress Bar */
        .progress-container {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            opacity: 0;
            animation: fadeIn 1s ease-out 1s forwards;
            margin-top: 20px;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
            border-radius: 2px;
            animation: progressFill 2s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.6);
        }

        @keyframes progressFill {
            0% { width: 0%; }
            50% { width: 100%; }
            100% { width: 0%; }
        }

        /* Subtitle text */
        .loading-subtitle {
            font-size: 14px;
            color: #cbd5e1;
            text-align: center;
            margin-top: 15px;
            opacity: 0;
            animation: fadeIn 0.8s ease-out 1.2s forwards;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Floating dots animation */
        .floating-dots {
            display: flex;
            gap: 8px;
            margin-top: 20px;
            opacity: 0;
            animation: fadeIn 0.8s ease-out 1.5s forwards;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            animation: dotBounce 1.4s ease-in-out infinite;
        }

        .dot:nth-child(1) { animation-delay: 0s; }
        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes dotBounce {
            0%, 100% { transform: translateY(0); opacity: 1; }
            50% { transform: translateY(-15px); opacity: 0.5; }
        }

        .lightning-effect {
            position: absolute;
            width: 200%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #3b82f6, transparent);
            opacity: 0;
            animation: lightningStrike 0.3s ease-out;
            top: 50%;
            z-index: 3;
        }

        @keyframes lightningStrike {
            0% { opacity: 0; transform: scaleX(0); }
            50% { opacity: 1; transform: scaleX(1); }
            100% { opacity: 0; transform: scaleX(0); }
        }

        /* 🎯 Main Container */
        .main-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            animation: mainFadeIn 1.2s ease-out 2s forwards;
            overflow-y: auto;
        }

        @keyframes mainFadeIn {
            from { 
                opacity: 0; 
                transform: scale(0.9) translateY(40px); 
            }
            to { 
                opacity: 1; 
                transform: scale(1) translateY(0); 
            }
        }

        /* 📋 Content Wrapper */
        .content-wrapper {
            max-width: 1200px;
            width: 100%;
            text-align: center;
        }

        /* 🎨 Header Section */
        .header-section {
            margin-bottom: 60px;
            animation: slideDown 1s ease-out 0.8s both;
        }

        @keyframes slideDown {
            from { 
                opacity: 0; 
                transform: translateY(-50px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .welcome-title {
            font-size: clamp(32px, 5vw, 48px);
            font-weight: 800;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 40px rgba(59, 130, 246, 0.3);
            position: relative;
        }

        .welcome-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
            border-radius: 2px;
            animation: expandWidth 2s ease-out 1s forwards;
        }

        @keyframes expandWidth {
            from { width: 0; }
            to { width: 100%; }
        }

        .subtitle {
            font-size: 18px;
            color: #94a3b8;
            font-weight: 400;
            line-height: 1.6;
            opacity: 0;
            animation: fadeIn 1s ease-out 1.5s forwards;
        }

        .highlight {
            color: #60a5fa;
            font-weight: 600;
        }

        /* 🎯 Subject Grid */
        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
            opacity: 0;
            animation: gridFadeIn 1s ease-out 2.2s forwards;
        }

        @keyframes gridFadeIn {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        /* 🎨 Subject Cards */
        .subject-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 24px;
            padding: 40px 30px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-align: left;
        }

        .subject-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            transition: left 0.8s ease;
        }

        .subject-card:hover::before {
            left: 100%;
        }

        .subject-card:hover {
            transform: translateY(-12px) scale(1.02);
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 
                0 20px 40px rgba(59, 130, 246, 0.3),
                0 0 0 1px rgba(59, 130, 246, 0.1);
            background: rgba(30, 41, 59, 0.95);
        }

        .card-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 24px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
            border: 1px solid rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
        }

        .card-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: iconShine 3s ease-in-out infinite;
        }

        @keyframes iconShine {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }

        .card-title {
            font-size: 24px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .card-description {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .card-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .card-action:hover::before {
            left: 100%;
        }

        .card-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        /* 🎨 Footer */
        .footer {
            margin-top: 80px;
            padding: 30px;
            text-align: center;
            opacity: 0;
            animation: footerFadeIn 1s ease-out 3s forwards;
        }

        @keyframes footerFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .footer-text {
            color: #64748b;
            font-size: 14px;
            font-weight: 400;
        }

        .footer-highlight {
            color: #60a5fa;
            font-weight: 600;
        }

        /* 📱 Responsive Design */
        @media (max-width: 768px) {
            .subjects-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                margin-top: 30px;
            }

            .subject-card {
                padding: 30px 24px;
            }

            .card-icon {
                width: 60px;
                height: 60px;
                font-size: 28px;
                margin-bottom: 20px;
            }

            .card-title {
                font-size: 20px;
            }

            .card-description {
                font-size: 14px;
            }

            .welcome-title {
                font-size: clamp(28px, 8vw, 36px);
            }

            .subtitle {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 15px;
            }

            .subject-card {
                padding: 24px 20px;
            }

            .card-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }
        }

        /* 🎯 Performance Optimizations */
        .subject-card,
        .card-action {
            will-change: transform;
        }

        /* 🎨 Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .subject-card:hover {
                transform: none;
            }

            .card-action:hover {
                transform: none;
            }

            .loading-overlay,
            .main-container,
            .subjects-grid {
                animation: none;
            }
        }

        /* Focus States */
        .subject-card:focus-within {
            border-color: rgba(59, 130, 246, 0.6);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .card-action:focus {
            outline: 2px solid #60a5fa;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <!-- 🌟 Animated Background -->
    <canvas id="bg-canvas"></canvas>

    <!-- ⚡ Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="lightning-effect"></div>
        <div class="loading-content">
            <div class="loading-orb">
                <div class="orb-ring"></div>
                <div class="orb-ring"></div>
                <div class="orb-ring"></div>
                <div class="orb-core"></div>
            </div>
            <div>
                <div class="loading-text" id="loading-text">Initializing ZAP AI...</div>
                <div class="loading-subtitle">Preparing your intelligent workspace</div>
                <div class="progress-container">
                    <div class="progress-bar"></div>
                </div>
                <div class="floating-dots">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🎯 Main Content -->
    <div class="main-container" id="main-container">
        <div class="content-wrapper">
            <!-- 🎨 Header -->
            <div class="header-section">
                <h1 class="welcome-title">
                    Welcome <span><?php echo htmlspecialchars($user['given_name'] ?? $user['username'] ?? explode('@', $user['email'])[0]); ?></span> 👋
                </h1>
                <p class="subtitle">
                    Step into your <span class="highlight">intelligent workspace</span> powered by <span class="footer-highlight">ZAP AI</span><br>
                    Select a subject to begin your learning journey
                </p>
            </div>

            <!-- 🎯 Subject Grid -->
            <div class="subjects-grid">
                <!-- Mathematics -->
                <div class="subject-card" onclick="navigateToSubject('mathematics')">
                    <div class="card-icon">📐</div>
                    <h3 class="card-title">Mathematics</h3>
                    <p class="card-description">Advanced problem-solving with step-by-step solutions and visual explanations</p>
                    <a href="/project/public/chat.php?mode=mathematics" class="card-action">
                        <i class="fas fa-calculator"></i>
                        Start Learning
                    </a>
                </div>

                <!-- Chemistry -->
                <div class="subject-card" onclick="navigateToSubject('chemistry')">
                    <div class="card-icon">⚗️</div>
                    <h3 class="card-title">Chemistry</h3>
                    <p class="card-description">Molecular modeling, reaction analysis, and chemical equation solving</p>
                    <a href="/project/public/chat.php?mode=chemistry" class="card-action">
                        <i class="fas fa-flask"></i>
                        Explore Molecules
                    </a>
                </div>

                <!-- Physics -->
                <div class="subject-card" onclick="navigateToSubject('physics')">
                    <div class="card-icon">⚡</div>
                    <h3 class="card-title">Physics</h3>
                    <p class="card-description">Interactive simulations, concept visualization, and problem analysis</p>
                    <a href="/project/public/chat.php?mode=physics" class="card-action">
                        <i class="fas fa-atom"></i>
                        Discover Physics
                    </a>
                </div>

                <!-- Programming -->
                <div class="subject-card" onclick="navigateToSubject('programming')">
                    <div class="card-icon">💻</div>
                    <h3 class="card-title">Programming</h3>
                    <p class="card-description">Code optimization, debugging assistance, and algorithm design</p>
                    <a href="/project/public/chat.php?mode=programming" class="card-action">
                        <i class="fas fa-code"></i>
                        Start Coding
                    </a>
                </div>

                <!-- Biology -->
                <div class="subject-card" onclick="navigateToSubject('biology')">
                    <div class="card-icon">🧬</div>
                    <h3 class="card-title">Biology</h3>
                    <p class="card-description">Genetic analysis, ecosystem modeling, and biological concepts</p>
                    <a href="/project/public/chat.php?mode=biology" class="card-action">
                        <i class="fas fa-dna"></i>
                        Study Life
                    </a>
                </div>

                <!-- Literature -->
                <div class="subject-card" onclick="navigateToSubject('literature')">
                    <div class="card-icon">📚</div>
                    <h3 class="card-title">Literature</h3>
                    <p class="card-description">Text analysis, writing assistance, and literary exploration</p>
                    <a href="/project/public/chat.php?mode=literature" class="card-action">
                        <i class="fas fa-book-open"></i>
                        Read & Write
                    </a>
                </div>
            </div>

            <!-- 🎨 Footer -->
            <div class="footer">
                <p class="footer-text">
                    Powered by <span class="footer-highlight">ZAP AI</span> • Built for <span class="footer-highlight">excellence</span> 🚀
                </p>
            </div>
        </div>
    </div>

    <script>
        // 🌟 Optimized Particle System with Error Handling
        try {
            const canvas = document.getElementById('bg-canvas');
            if (!canvas) throw new Error('Canvas element not found');
            
            const ctx = canvas.getContext('2d');
            if (!ctx) throw new Error('Canvas context failed');
            
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            // Particle system
            class Particle {
                constructor() {
                    this.reset();
                }

                reset() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 3 + 1;
                    this.speedX = (Math.random() - 0.5) * 0.5;
                    this.speedY = (Math.random() - 0.5) * 0.5;
                    this.opacity = Math.random() * 0.5 + 0.3;
                    this.hue = Math.random() * 60 + 200;
                    this.pulse = Math.random() * 0.02 + 0.01;
                    this.pulsePhase = Math.random() * Math.PI * 2;
                }

                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;
                    this.pulsePhase += this.pulse;
                    
                    if (this.x < 0) this.x = canvas.width;
                    if (this.x > canvas.width) this.x = 0;
                    if (this.y < 0) this.y = canvas.height;
                    if (this.y > canvas.height) this.y = 0;
                }

                draw() {
                    const pulseFactor = Math.sin(this.pulsePhase) * 0.3 + 1;
                    const currentSize = this.size * pulseFactor;
                    
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, currentSize, 0, Math.PI * 2);
                    ctx.fillStyle = `hsla(${this.hue}, 70%, 60%, ${this.opacity})`;
                    ctx.fill();
                }
            }

            // Reduced particle count for better performance
            const particles = [];
            for (let i = 0; i < 50; i++) {
                particles.push(new Particle());
            }

            // Optimized animation loop (no connection drawing)
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                particles.forEach(particle => {
                    particle.update();
                    particle.draw();
                });
                
                requestAnimationFrame(animate);
            }

            // ⚡ Loading Sequence
            const loadingTexts = [
                "⚡ Initializing ZAP AI...",
                "🎯 Welcome <?php echo addslashes($user['username'] ?? 'Learner'); ?>",
                "🚀 Preparing your workspace...",
                "✨ Ready to explore subjects"
            ];

            const loadingOverlay = document.getElementById('loading-overlay');
            const loadingText = document.getElementById('loading-text');
            const lightningEffect = document.querySelector('.lightning-effect');

            // Smooth typewriter effect
            async function typeWriter(text, element) {
                if (!element) return;
                element.textContent = '';
                element.style.opacity = '1';
                for (let i = 0; i < text.length; i++) {
                    element.textContent += text[i];
                    await new Promise(resolve => setTimeout(resolve, 25));
                }
            }

            async function loadingSequence() {
                try {
                    for (let i = 0; i < loadingTexts.length; i++) {
                        if (loadingText) {
                            loadingText.style.opacity = '0';
                            loadingText.style.transition = 'opacity 0.3s ease-out';
                        }
                        await new Promise(resolve => setTimeout(resolve, 200));
                        
                        await typeWriter(loadingTexts[i], loadingText);
                        
                        // Longer pause on final message
                        await new Promise(resolve => setTimeout(resolve, i === loadingTexts.length - 1 ? 1200 : 900));
                    }
                    
                    if (lightningEffect) {
                        lightningEffect.style.animation = 'lightningStrike 0.4s ease-out';
                    }
                    
                    await new Promise(resolve => setTimeout(resolve, 500));
                    
                    if (loadingOverlay) {
                        loadingOverlay.style.opacity = '0';
                        loadingOverlay.style.transition = 'opacity 0.8s ease-out';
                        await new Promise(resolve => setTimeout(resolve, 800));
                        loadingOverlay.style.display = 'none';
                    }
                } catch (e) {
                    console.error('Loading sequence error:', e);
                    if (loadingOverlay) loadingOverlay.style.display = 'none';
                }
            }

            // 🎯 Navigation
            function navigateToSubject(subject) {
                if (navigator.vibrate) navigator.vibrate(50);
                document.body.style.opacity = '0';
                document.body.style.transform = 'scale(0.95)';
                setTimeout(() => {
                   window.location.href = `/project/public/chat.php?mode=${subject}`;
                }, 300);
            }

            // 🌟 Window resize handler
            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });

            // 🚀 Initialize on load
            window.addEventListener('load', () => {
                animate();
                setTimeout(loadingSequence, 300);
            });
            
            // Fallback: If load doesn't fire within 3 seconds, hide overlay anyway
            setTimeout(() => {
                if (loadingOverlay && loadingOverlay.parentNode) {
                    loadingOverlay.style.display = 'none';
                }
            }, 5000);

        } catch (e) {
            console.error('Page initialization error:', e);
            const overlay = document.getElementById('loading-overlay');
            if (overlay) overlay.style.display = 'none';
        }
    </script>
</body>
</html>