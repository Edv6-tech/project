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

// Get mode from URL parameter
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'general';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - ZAP AI</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* 🌌 GLOBAL RESET & BASE */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(circle at top left, rgba(63, 94, 251, 0.18), transparent 22%),
                        radial-gradient(circle at bottom right, rgba(236, 72, 153, 0.18), transparent 18%),
                        linear-gradient(160deg, #05091f 0%, #0d1a39 45%, #111f45 100%);
            color: #e0e7ff;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* 🌟 Animated Background Effects */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 15% 20%, rgba(59, 130, 246, 0.18), transparent 22%),
                radial-gradient(circle at 85% 10%, rgba(168, 85, 247, 0.14), transparent 18%),
                radial-gradient(circle at 75% 80%, rgba(59, 130, 246, 0.08), transparent 22%),
                radial-gradient(circle at 25% 75%, rgba(236, 72, 153, 0.10), transparent 20%);
            animation: gradientShift 18s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
            opacity: 0.95;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.18) 1px, transparent 1px);
            background-size: 60px 60px;
            opacity: 0.12;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes gradientShift {
            0%, 100% { transform: rotate(0deg) scale(1); }
            33% { transform: rotate(120deg) scale(1.1); }
            66% { transform: rotate(240deg) scale(0.9); }
        }

        /* 🧱 MAIN LAYOUT */
        .app {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 2;
            padding: 32px;
            gap: 24px;
        }

        /* 🌊 SIDEBAR - Glassmorphism */
        .sidebar {
            width: 320px;
            backdrop-filter: blur(30px);
            background: rgba(15, 23, 42, 0.65);
            border-right: 1px solid rgba(255, 255, 255, 0.14);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            box-shadow: 0 40px 90px rgba(0, 0, 0, 0.28);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(59, 130, 246, 0.08) 0%, 
                rgba(168, 85, 247, 0.08) 100%);
            pointer-events: none;
            opacity: 0.9;
        }

        /* HEADER BUTTON */
        .sidebar-header {
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .sidebar-header button {
            width: 100%;
            padding: 16px 24px;
            border-radius: 16px;
            border: none;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
            color: white;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3);
            margin-bottom: 10px;
        }

        .sidebar-header button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .sidebar-header button:hover::before {
            left: 100%;
        }

        .sidebar-header button:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.4);
        }

        /* SUBJECT SWITCHER */
        .subject-switcher-btn {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(139, 92, 246, 0.2);
            background: rgba(255, 255, 255, 0.8);
            color: #7c3aed;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            backdrop-filter: blur(10px);
        }

        .subject-switcher-btn:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.4);
            transform: translateY(-1px);
            text-decoration: none;
        }

        .subject-switcher-btn i {
            font-size: 16px;
        }

        .subject-switcher-btn span {
            flex: 1;
            text-align: left;
        }

        .subject-switcher {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 12px;
            margin-top: 8px;
            z-index: 1000;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.2);
        }

        .subject-switcher.active {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
            padding: 0 20px;
            margin-bottom: 20px;
            z-index: 1;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            z-index: 1;
            background: rgba(15, 23, 42, 0.95);
        }

        .sidebar-footer button {
            width: auto;
            padding: 12px 18px;
            border-radius: 14px;
        }

        .subject-item {
            padding: 14px 18px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1e293b;
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        .subject-item:last-child {
            border-bottom: none;
        }

        .subject-item:hover {
            background: rgba(139, 92, 246, 0.1);
            color: #7c3aed;
            transform: translateX(4px);
        }

        .subject-item i {
            width: 18px;
            text-align: center;
            color: #8b5cf6;
        }

        .chat-item {
            padding: 14px 18px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
        }

        .chat-item:hover {
            background: rgba(139, 92, 246, 0.1);
            transform: translateX(4px);
        }

        .chat-item.active {
            background: rgba(139, 92, 246, 0.15);
            border-left: 3px solid #8b5cf6;
        }

        .chat-item-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .chat-item-title {
            color: #1e293b;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-date {
            color: #64748b;
            font-size: 12px;
            font-weight: 400;
        }

        .chat-item .chat-item-actions {
            display: flex;
            gap: 8px;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .chat-item:hover .chat-item-actions {
            opacity: 1;
        }

        .chat-item-btn {
            padding: 6px 8px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.8);
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-item-btn:hover {
            background: rgba(139, 92, 246, 0.2);
            transform: scale(1.1);
            color: #8b5cf6;
        }

        .chat-item-btn.rename-btn:hover {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .chat-item-btn.delete-btn:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .chat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                rgba(139, 92, 246, 0.05) 0%, 
                rgba(59, 130, 246, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .chat-item:hover::before {
            opacity: 1;
        }

        .chat-item:hover {
            transform: translateX(4px);
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(139, 92, 246, 0.3);
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.1);
        }

        /*  APP LAYOUT */
        .app {
            display: flex;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /*  SIDE BAR */
        .sidebar {
            width: 320px;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.95));
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        /*  CHAT AREA */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /*  MESSAGES */
        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 40px 32px;
            scroll-behavior: smooth;
            position: relative;
            background: radial-gradient(circle at top left, rgba(255,255,255,0.05), transparent 30%),
                        radial-gradient(circle at bottom right, rgba(139,92,246,0.08), transparent 20%);
            min-height: 0; /* Important for flexbox scrolling */
        }

        .messages::-webkit-scrollbar {
            width: 10px;
        }

        .messages::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 999px;
        }

        .messages::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(139, 92, 246, 0.75), rgba(99, 102, 241, 0.75));
            border-radius: 999px;
        }

        /* 💬 MESSAGE BUBBLES */
        .msg {
            max-width: 72%;
            margin-bottom: 22px;
            padding: 18px 22px;
            border-radius: 28px;
            position: relative;
            animation: messageSlideIn 0.35s ease-out;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            line-height: 1.7;
            font-size: 15px;
            box-shadow: 0 18px 35px rgba(17, 24, 39, 0.16);
        }

        .msg::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            background: linear-gradient(135deg, rgba(255,255,255,0.08), transparent 55%);
            opacity: 0.4;
        }

        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .msg.user {
            align-self: flex-end;
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            color: white;
            box-shadow: 0 22px 50px rgba(139, 92, 246, 0.28);
            margin-left: auto;
            margin-right: 0;
        }

        .msg.ai {
            align-self: flex-start;
            background: rgba(255, 255, 255, 0.12);
            color: #e2e8f7;
            box-shadow: 0 22px 50px rgba(15, 23, 42, 0.22);
            margin-right: auto;
            margin-left: 0;
            border-color: rgba(139, 92, 246, 0.12);
        }

        .msg.ai strong {
            color: #d8b4fe;
        }

        .msg.ai em {
            color: #c4b5fd;
        }

        .msg.ai code {
            background: rgba(15, 23, 42, 0.85);
            color: #a78bfa;
        }

        .msg.ai pre {
            background: rgba(15, 23, 42, 0.9);
            color: #e2e8f0;
            border-left: 4px solid rgba(167, 139, 250, 0.8);
        }

        .msg.ai strong {
            color: #7c3aed;
            font-weight: 600;
        }

        .msg.ai em {
            color: #8b5cf6;
            font-style: italic;
        }

        .msg.ai code {
            background: #f1f5f9;
            color: #e11d48;
            padding: 2px 6px;
            border-radius: 6px;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 14px;
        }

        .msg.ai pre {
            background: #f8fafc;
            color: #1e293b;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #8b5cf6;
            margin: 8px 0;
            overflow-x: auto;
            font-size: 14px;
        }

        /* 🎨 INPUT AREA */
        .input-area {
            padding: 20px 30px 30px;
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.15);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            bottom: 0;
            z-index: 10;
            flex-shrink: 0; /* Prevent shrinking */
        }

        .input-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 8px;
            border: 1px solid rgba(139, 92, 246, 0.2);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .input-box:focus-within {
            border-color: #8b5cf6;
            box-shadow: 0 4px 30px rgba(139, 92, 246, 0.2);
            transform: translateY(-2px);
        }

        /* 📷 IMAGE BUTTON */
        .image-upload {
            padding: 12px;
            border-radius: 12px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 18px;
        }

        .image-upload:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
        }

        /* TEXT INPUT */
        #input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 12px 16px;
            font-size: 15px;
            color: #1e293b;
            resize: none;
            outline: none;
            font-family: inherit;
        }

        #input::placeholder {
            color: #94a3b8;
        }

        /* SEND BUTTON */
        .send-btn {
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 25px rgba(139, 92, 246, 0.4);
        }

        .send-btn:active {
            transform: scale(0.98);
        }

        /* 🎯 TYPING INDICATOR */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            max-width: 70%;
            margin-bottom: 20px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #8b5cf6;
            animation: typingPulse 1.4s ease-in-out infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingPulse {
            0%, 60%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            30% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        /* 💫 FADE ANIMATIONS */
        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .messages.fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }

        .messages.fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        /* 🎯 SUBJECT MODAL */
        .subject-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 16px;
            padding: 30px;
            max-width: 600px;
            max-height: 90vh;
            width: 100%;
            overflow-y: auto;
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.3);
            position: relative;
            animation: modalSlideIn 0.3s ease-out;
            box-sizing: border-box;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-content h3 {
            color: #7c3aed;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        .subject-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .subject-option {
            padding: 20px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(139, 92, 246, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .subject-option:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
        }

        .subject-option i {
            font-size: 24px;
            color: #8b5cf6;
        }

        .subject-option span {
            font-size: 16px;
            font-weight: 500;
            color: #1e293b;
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.8);
            border: none;
            color: #7c3aed;
            font-size: 24px;
            cursor: pointer;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: rgba(139, 92, 246, 0.2);
            transform: scale(1.1);
        }

        .subject-modal.active {
            display: flex;
        }

        /* ACTION MODALS (DELETE/RENAME) */
        .action-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .action-modal.active {
            display: flex;
        }

        .action-modal .modal-content {
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            position: relative;
            animation: modalSlideIn 0.2s ease-out;
        }

        .action-modal h3 {
            color: #1e293b;
            margin: 0 0 12px 0;
            font-size: 18px;
            font-weight: 600;
        }

        .action-modal p {
            color: #64748b;
            margin: 0 0 24px 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .rename-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .rename-input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .cancel-btn {
            background: #f1f5f9;
            color: #64748b;
        }

        .cancel-btn:hover {
            background: #e2e8f0;
        }

        .delete-btn {
            background: #dc2626;
            color: white;
        }

        .delete-btn:hover {
            background: #b91c1c;
        }

        .save-btn {
            background: #8b5cf6;
            color: white;
        }

        .save-btn:hover {
            background: #7c3aed;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .modal-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .cancel-btn {
            background: #f1f5f9;
            color: #475569;
        }

        .cancel-btn:hover {
            background: #e2e8f0;
        }

        .delete-btn {
            background: #dc2626;
            color: white;
        }

        .delete-btn:hover {
            background: #b91c1c;
        }

        .save-btn {
            background: #8b5cf6;
            color: white;
        }

        .save-btn:hover {
            background: #7c3aed;
        }

        .action-modal.active {
            display: flex;
        }

        /* 🔄 TOGGLE BUTTON */
        .subject-toggle-btn {
            width: 100%;
            margin-top: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(139, 92, 246, 0.2);
            background: rgba(255, 255, 255, 0.8);
            color: #7c3aed;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(10px);
        }

        .subject-toggle-btn:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.4);
            transform: translateY(-1px);
        }

        /* 📱 RESPONSIVE DESIGN */
        @media (max-width: 1200px) {
            .sidebar {
                width: 280px;
            }
            
            .chat-item {
                padding: 12px 16px;
            }
            
            .chat-item-title {
                font-size: 13px;
            }
            
            .chat-item-date {
                font-size: 11px;
            }
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 260px;
            }
            
            .chat-item {
                padding: 10px 14px;
            }
            
            .chat-item-title {
                font-size: 13px;
            }
            
            .msg {
                max-width: 75%;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -320px;
                top: 0;
                height: 100vh;
                z-index: 2000;
                transition: left 0.3s ease;
                width: 320px;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 2001;
                background: rgba(139, 92, 246, 0.9);
                color: white;
                border: none;
                padding: 12px 16px;
                border-radius: 12px;
                cursor: pointer;
                font-size: 16px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }
            
            .chat-area {
                margin-left: 0;
            }
            
            .messages {
                padding: 20px 15px;
                padding-bottom: 100px;
            }
            
            .input-area {
                padding: 15px;
                padding-bottom: 30px;
            }
            
            .msg {
                max-width: 85%;
                padding: 14px 18px;
                margin-bottom: 16px;
            }
            
            .input-box {
                flex-direction: column;
                gap: 8px;
                padding: 12px;
            }
            
            .input-box textarea {
                width: 100%;
                font-size: 16px;
                padding: 12px;
            }
            
            .send-btn {
                width: 100%;
                padding: 14px;
                font-size: 16px;
            }
            
            .chat-item-btn {
                padding: 8px 12px;
                font-size: 12px;
            }
            
            .modal-content {
                margin: 20px;
                padding: 20px;
            }
            
            .action-modal {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
                left: -100%;
            }
            
            .messages {
                padding: 15px 12px;
                padding-bottom: 80px;
            }
            
            .input-area {
                padding: 12px;
                padding-bottom: 20px;
            }
            
            .msg {
                max-width: 90%;
                padding: 12px 16px;
                margin-bottom: 12px;
                font-size: 14px;
            }
            
            .chat-item {
                padding: 10px 12px;
            }
            
            .chat-item-title {
                font-size: 12px;
                line-height: 1.3;
            }
            
            .chat-item-date {
                font-size: 10px;
            }
            
            .chat-item-btn {
                padding: 6px 10px;
                font-size: 11px;
            }
            
            .mobile-menu-toggle {
                top: 15px;
                left: 15px;
                padding: 10px 14px;
                font-size: 14px;
            }
            
            .modal-content {
                margin: 10px;
                padding: 16px;
                max-width: 95%;
            }
            
            .modal-actions {
                flex-direction: column;
                gap: 8px;
            }
            
            .modal-btn {
                width: 100%;
                padding: 12px;
                font-size: 14px;
            }
        }

        @media (max-width: 360px) {
            .messages {
                padding: 12px 8px;
                padding-bottom: 70px;
            }
            
            .input-area {
                padding: 8px;
                padding-bottom: 15px;
            }
            
            .msg {
                max-width: 95%;
                padding: 10px 14px;
                font-size: 13px;
            }
            
            .chat-item {
                padding: 8px 10px;
            }
            
            .mobile-menu-toggle {
                padding: 8px 12px;
                font-size: 13px;
            }
            
            .modal-content {
                margin: 5px;
                padding: 12px;
            }
        }

        @media (orientation: landscape) and (max-height: 500px) {
            .messages {
                padding: 10px 15px;
                padding-bottom: 60px;
            }
            
            .input-area {
                padding: 8px;
                padding-bottom: 15px;
            }
        }

        /* ✨ ACCESSIBILITY */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>
    <div class="app">
        <!-- 🌊 SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-header">
                <button onclick="newChat()">+ New Chat</button>
                <a href="/project/public/subject.php" class="subject-switcher-btn">
                    <i class="fas fa-book"></i> <span id="current-subject">General</span>
                </a>
                <button id="subject-toggle-btn" onclick="toggleSubjectSwitcher()" class="subject-toggle-btn" style="display: none;">
                    <i class="fas fa-exchange-alt"></i> Toggle
                </button>
            </div>
            
            <!-- SUBJECT MODAL -->
        <div id="subject-modal" class="subject-modal">
            <div class="modal-content">
                <h3>Choose Subject</h3>
                <div class="subject-grid">
                    <div class="subject-option" onclick="selectSubject('mathematics')">
                        <i class="fas fa-calculator"></i>
                        <span>Mathematics</span>
                    </div>
                    <div class="subject-option" onclick="selectSubject('chemistry')">
                        <i class="fas fa-flask"></i>
                        <span>Chemistry</span>
                    </div>
                    <div class="subject-option" onclick="selectSubject('physics')">
                        <i class="fas fa-atom"></i>
                        <span>Physics</span>
                    </div>
                    <div class="subject-option" onclick="selectSubject('programming')">
                        <i class="fas fa-code"></i>
                        <span>Programming</span>
                    </div>
                    <div class="subject-option" onclick="selectSubject('biology')">
                        <i class="fas fa-dna"></i>
                        <span>Biology</span>
                    </div>
                    <div class="subject-option" onclick="selectSubject('literature')">
                        <i class="fas fa-book-open"></i>
                        <span>Literature</span>
                    </div>
                </div>
                <button class="modal-close" onclick="closeSubjectModal()">×</button>
            </div>
        </div>
        
        <!-- DELETE CHAT MODAL -->
        <div id="delete-modal" class="action-modal">
            <div class="modal-content">
                <button class="modal-close" onclick="closeDeleteModal()">×</button>
                <h3>Delete chat?</h3>
                <p>This will permanently delete the chat and its messages. This action cannot be undone.</p>
                <div class="modal-actions">
                    <button class="modal-btn cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                    <button class="modal-btn delete-btn" onclick="confirmDelete()">Delete</button>
                </div>
            </div>
        </div>

        <!-- RENAME CHAT MODAL -->
        <div id="rename-modal" class="action-modal">
            <div class="modal-content">
                <button class="modal-close" onclick="closeRenameModal()">×</button>
                <h3>Rename chat</h3>
                <input type="text" id="rename-input" class="rename-input" placeholder="Enter new name..." maxlength="50">
                <div class="modal-actions">
                    <button class="modal-btn cancel-btn" onclick="closeRenameModal()">Cancel</button>
                    <button class="modal-btn save-btn" onclick="confirmRename()">Save</button>
                </div>
            </div>
        </div>
        
        <div class="chat-list" id="chat-list"></div>
            
            <div class="sidebar-footer">
                <div>
                    <strong id="user-name"></strong><br>
                    <small id="user-email"></small>
                </div>
                <button class="send-btn" onclick="logout()" style="margin-top:10px;">Logout</button>
            </div>
        </div>

        <!-- 💬 CHAT AREA -->
        <div class="chat-area">
            <!-- MOBILE MENU TOGGLE -->
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="messages" id="messages"></div>
            
            <!-- 💎 INPUT AREA -->
            <div class="input-area">
                <!-- IMAGE PREVIEW -->
                <div id="preview-container"></div>
                
                <div class="input-box">
                    <!-- 📷 IMAGE BUTTON -->
                    <label class="image-upload">
                        📷
                        <input type="file" id="imageInput" accept="image/*" hidden>
                    </label>
                    
                    <!-- TEXT -->
                    <textarea id="input" placeholder="Message AI..." rows="1"></textarea>
                    
                    <!-- SEND -->
                    <button class="send-btn" onclick="sendMessage()">Send</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const currentUser = <?php echo json_encode($user); ?>;
        const currentMode = <?php echo json_encode($mode); ?>;
        let selectedImage = null;
        let currentChatId = null;

        // Subject-specific welcome messages with emojis
        const subjectWelcomeMessages = {
            mathematics: "📐 Hello! I'm your Mathematics tutor. How can I help you with equations, problems, or mathematical concepts today?",
            chemistry: "⚗️ Welcome! I'm your Chemistry assistant. I can help with reactions, formulas, molecular structures, and chemical concepts. What would you like to explore?",
            physics: "⚡ Hi there! I'm your Physics guide. I can help with forces, motion, energy, and physical phenomena. What physics topic interests you?",
            programming: "💻 Hey! I'm your Programming mentor. I can assist with code, algorithms, debugging, and programming concepts. What coding challenge can I help with?",
            biology: "🧬 Welcome! I'm your Biology expert. I can help with genetics, ecosystems, anatomy, and biological processes. What biological topic would you like to study?",
            literature: "📚 Hello! I'm your Literature assistant. I can help with text analysis, writing, literary concepts, and creative writing. What would you like to explore?",
            general: "👋 Hi! I'm your AI assistant. How can I help you today?"
        };

        /* 👤 USER INFO */
        document.getElementById("user-name").textContent = currentUser.name;
        document.getElementById("user-email").textContent = currentUser.email;

        /* 🚪 LOGOUT */
        function logout() {
            window.location.href = "/project/public/logout.php";
        }

        /* 📷 IMAGE HANDLING */
        document.getElementById("imageInput").addEventListener("change", function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = function() {
                selectedImage = reader.result;

                document.getElementById("preview-container").innerHTML =
                    `<img src="${selectedImage}" style="max-width:180px;border-radius:10px;">`;
            };

            reader.readAsDataURL(file);
        });

        /* ✨ ENHANCED FORMAT RESPONSE */
        function format(text) {
            if (!text) return "";

            return text
                // Bold text
                .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
                // Italic text
                .replace(/\*(.*?)\*/g, "<em>$1</em>")
                // Line breaks
                .replace(/\n/g, "<br>")
                // Code blocks
                .replace(/```([\s\S]*?)```/g, "<pre><code>$1</code></pre>")
                // Inline code
                .replace(/`([^`]+)`/g, "<code>$1</code>")
                // Headers
                .replace(/^### (.*$)/gm, "<h3>$1</h3>")
                .replace(/^## (.*$)/gm, "<h2>$1</h2>")
                .replace(/^# (.*$)/gm, "<h1>$1</h1>")
                // Horizontal rules
                .replace(/^---$/gm, "<hr>")
                // Lists
                .replace(/^\- (.+)$/gm, "<li>$1</li>")
                // Blockquotes
                .replace(/^> (.+)$/gm, "<blockquote>$1</blockquote>");
        }

        /* 💬 ENHANCED ADD MESSAGE */
        function addMessage(type, text, image = null) {
            console.log("Adding message:", type, text.substring(0, 50) + "...");
            const container = document.getElementById("messages");

            const div = document.createElement("div");
            div.className = "msg " + type;

            if (image) {
                div.innerHTML = `
                    <img src="${image}" style="max-width:200px;border-radius:12px;margin-bottom:8px;">
                    <br>${format(text)}
                `;
            } else {
                div.innerHTML = format(text);
            }

            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
            console.log("Message added to DOM");
        }

        /* 🎯 TYPING INDICATOR */
        function showTyping() {
            const container = document.getElementById("messages");

            const div = document.createElement("div");
            div.className = "typing-indicator";
            div.id = "typing";
            div.innerHTML = `
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            `;

            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }

        function removeTyping() {
            const typing = document.getElementById("typing");
            if (typing) typing.remove();
        }

        /* 🧹 CLEAR IMAGE */
        function clearImage() {
            selectedImage = null;
            document.getElementById("preview-container").innerHTML = "";
            document.getElementById("imageInput").value = "";
        }

        /* 🎯 SUBJECT MODAL */
        function openSubjectModal() {
            document.getElementById('subject-modal').classList.add('active');
            // Show toggle button
            document.getElementById('subject-toggle-btn').style.display = 'block';
        }

        function closeSubjectModal() {
            document.getElementById('subject-modal').classList.remove('active');
            // Hide toggle button
            document.getElementById('subject-toggle-btn').style.display = 'none';
        }

        function toggleSubjectSwitcher() {
            const modal = document.getElementById('subject-modal');
            if (modal.classList.contains('active')) {
                closeSubjectModal();
            } else {
                openSubjectModal();
            }
        }

        function selectSubject(subject) {
            // Update current mode
            currentMode = subject;
            
            // Update button text
            const subjectNames = {
                mathematics: "Mathematics",
                chemistry: "Chemistry",
                physics: "Physics",
                programming: "Programming",
                biology: "Biology",
                literature: "Literature"
            };
            
            document.getElementById("current-subject").textContent = subjectNames[subject];
            
            // Update page title
            const titles = {
                mathematics: "Mathematics Tutor - ZAP AI",
                chemistry: "Chemistry Assistant - ZAP AI", 
                physics: "Physics Guide - ZAP AI",
                programming: "Programming Mentor - ZAP AI",
                biology: "Biology Expert - ZAP AI",
                literature: "Literature Assistant - ZAP AI"
            };
            
            document.title = titles[subject] || "AI Chat - ZAP AI";
            
            // Close modal
            closeSubjectModal();
            
            // Add transition effect
            document.body.style.opacity = '0.5';
            document.body.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                document.body.style.opacity = '1';
                
                // Add system message about subject change
                const welcomeText = subjectWelcomeMessages[subject] || subjectWelcomeMessages.general;
                addMessage("ai", `🎯 Switched to ${subjectNames[subject]} mode. ${welcomeText}`);
                
                // Scroll to bottom
                const messages = document.getElementById("messages");
                messages.scrollTop = messages.scrollHeight;
            }, 300);
        }

        /* 🚀 ENHANCED SEND MESSAGE */
        function sendMessage() {
            const input = document.getElementById("input");
            const msg = input.value.trim();

            if (!msg && !selectedImage) return;

            addMessage("user", msg, selectedImage);

            input.value = "";
            showTyping();

            fetch("/project/api/chat.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                credentials: "include",
                body: JSON.stringify({
                    message: msg,
                    image: selectedImage,
                    chat_id: currentChatId,
                    mode: currentMode
                })
            })
            .then(async res => {
                const text = await res.text();

                if (!res.ok) {
                    console.error("API Response Error:", res.status, text);
                    throw new Error(`API error ${res.status}: ${text}`);
                }

                try {
                    return JSON.parse(text);
                } catch {
                    console.error("JSON Parse Error - RAW RESPONSE:", text);
                    throw new Error("Invalid JSON response from server");
                }
            })
            .then(data => {
                removeTyping();

                if (data.error) {
                    addMessage("ai", "❌ Error: " + data.error);
                    console.error("API returned error:", data);
                    clearImage();
                    return;
                }

                if (!data.response) {
                    addMessage("ai", "❌ Empty response from AI service");
                    console.error("Empty response:", data);
                    clearImage();
                    return;
                }

                currentChatId = data.chat_id;

                // Add typing effect for AI response
                addTypingMessage(data.response);

                loadChats();
                clearImage();
            })
            .catch(err => {
                console.error("Send message error:", err);
                removeTyping();
                addMessage("ai", "❌ Connection error: " + (err.message || "Please check your internet and try again."));
                clearImage();
            });
        }

        /* ⌨️ TYPING EFFECT FOR AI RESPONSES */
        function addTypingMessage(fullText) {
            const container = document.getElementById("messages");
            
            // Remove typing indicator
            removeTyping();
            
            // Create message element
            const msgDiv = document.createElement("div");
            msgDiv.className = "msg ai";
            
            // Start with empty content
            msgDiv.innerHTML = "";
            
            // Type character by character
            let charIndex = 0;
            const typingSpeed = 20; // milliseconds per character
            
            function typeNextChar() {
                if (charIndex < fullText.length) {
                    // Add next character
                    msgDiv.innerHTML = format(fullText.substring(0, charIndex + 1));
                    charIndex++;
                    
                    // Continue typing
                    setTimeout(typeNextChar, typingSpeed);
                } else {
                    // Typing complete
                    msgDiv.innerHTML = format(fullText);
                    container.appendChild(msgDiv);
                    container.scrollTop = container.scrollHeight;
                }
            }
            
            // Start typing
            typeNextChar();
        }

        /* LOAD CHATS */
     function loadChats() {
    console.log("Loading chats...");

    fetch("/project/api/getChats.php", {
        credentials: "include"
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to load chats');
        return res.json();
    })
    .then(chats => {
        console.log("Chats loaded:", chats);

        const list = document.getElementById("chat-list");
        list.innerHTML = "";

        if (chats.length === 0) {
            list.innerHTML = '<div style="text-align: center; color: #94a3b8; padding: 20px;">No previous chats</div>';
            return;
        }

        chats.forEach(chat => {
            console.log("Processing chat:", chat);
            const chatItem = document.createElement('div');
            chatItem.className = 'chat-item';
            chatItem.dataset.chatId = chat.id;
            
            const chatTitle = chat.title || 'New Chat';
            const chatDate = new Date(chat.last_activity).toLocaleDateString();
            
            chatItem.innerHTML = `
                <div class="chat-item-content">
                    <div class="chat-item-title">${chatTitle}</div>
                    <div class="chat-item-date">${chatDate}</div>
                </div>
                <div class="chat-item-actions">
                    <button class="chat-item-btn rename-btn" title="Rename" data-chat-id="${chat.id}" data-chat-title="${chatTitle}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="chat-item-btn delete-btn" title="Delete" data-chat-id="${chat.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            // Add click handler for chat switching
            chatItem.addEventListener('click', function(e) {
                if (e.target.closest('.chat-item-actions')) return;
                loadChat(chat.id);
            });
            
            // Highlight current chat
            if (chat.id == currentChatId) {
                chatItem.classList.add('active');
            }
            
            list.appendChild(chatItem);
        });

        // Use event delegation for button clicks
        list.addEventListener('click', function(e) {
            const target = e.target.closest('.chat-item-btn');
            if (!target) return;

            e.stopPropagation();

            const chatId = target.dataset.chatId;
            const chatTitle = target.dataset.chatTitle;

            console.log("Button clicked:", target.classList.contains('rename-btn') ? 'rename' : 'delete', "for chat:", chatId);

            if (target.classList.contains('rename-btn')) {
                openRenameModal(chatId, chatTitle);
            } else if (target.classList.contains('delete-btn')) {
                openDeleteModal(chatId);
            }
        });
    })
    .catch(err => console.error("LOAD CHAT ERROR:", err));
}

        /* LOAD CHAT */
        function loadChat(chatId) {
            console.log("Loading chat:", chatId);
            currentChatId = chatId;

            const container = document.getElementById("messages");

            // Fade out effect
            container.classList.remove('fade-in');
            container.classList.add('fade-out');

            // Wait for fade-out animation to complete
            setTimeout(() => {
                fetch("/project/api/getMessages.php?chat_id=" + chatId, {
                    credentials: "include"
                })
                .then(res => {
                    console.log("Response status:", res.status);
                    console.log("Response headers:", res.headers);
                    if (!res.ok) {
                        console.error("Response not ok:", res.statusText);
                        throw new Error("Network response was not ok");
                    }
                    return res.json();
                })
                .then(messages => {
                    console.log("Messages loaded:", messages);
                    container.innerHTML = "";

                    if (messages.length === 0) {
                        console.log("No messages found, showing welcome");
                        addWelcomeMessage();
                    } else {
                        // Fill existing messages with proper typing animation
                        messages.forEach((msg, index) => {
                            setTimeout(() => {
                                console.log("Adding message:", msg.role, msg.content.substring(0, 50) + "...");
                                if (msg.role === 'user') {
                                    // Add user message directly (no typing animation)
                                    addMessage("user", msg.content);
                                } else {
                                    // Add AI message with typing animation
                                    addTypingMessage(msg.content);
                                }
                            }, index * 100); // Stagger typing effect
                        });
                    }

                    // Fade in effect
                    container.classList.remove('fade-out');
                    container.classList.add('fade-in');
                })
                .catch(err => {
                    console.error("LOAD CHAT ERROR:", err);
                    console.error("Full error details:", err.stack);
                    addWelcomeMessage();
                    container.classList.remove('fade-out');
                    container.classList.add('fade-in');
                });
            }, 300); // Match the fadeOut animation duration
        }

        /* ➕ NEW CHAT */
        function newChat() {
            currentChatId = null;
            const container = document.getElementById("messages");

            // Fade out effect
            container.classList.remove('fade-in');
            container.classList.add('fade-out');

            // Wait for fade-out animation to complete
            setTimeout(() => {
                container.innerHTML = "";
                addWelcomeMessage();
                
                // Fade in effect
                container.classList.remove('fade-out');
                container.classList.add('fade-in');
            }, 300); // Match the fadeOut animation duration
        }

        /* ACTION MODAL FUNCTIONS */
        let deleteChatId = null;
        let renameChatId = null;

        function openDeleteModal(chatId) {
            console.log("Opening delete modal for chat:", chatId);
            deleteChatId = chatId;
            document.getElementById('delete-modal').classList.add('active');
        }

        function closeDeleteModal() {
            console.log("Closing delete modal");
            document.getElementById('delete-modal').classList.remove('active');
            deleteChatId = null;
        }

        function confirmDelete() {
            console.log("Confirm delete called for chat:", deleteChatId);
            if (!deleteChatId) return;

            fetch("/project/api/deleteChat.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                credentials: "include",
                body: JSON.stringify({ chat_id: deleteChatId })
            })
            .then(res => {
                console.log("Delete API response:", res.status);
                return res.json();
            })
            .then(data => {
                console.log("Delete successful:", data);
                if (deleteChatId === currentChatId) newChat();
                loadChats();
                closeDeleteModal();
            })
            .catch(err => {
                console.error("DELETE ERROR:", err);
                closeDeleteModal();
            });
        }

        function openRenameModal(chatId, currentTitle) {
            console.log("Opening rename modal for chat:", chatId, "title:", currentTitle);
            renameChatId = chatId;
            document.getElementById('rename-input').value = currentTitle;
            document.getElementById('rename-modal').classList.add('active');
            
            // Focus input and select text
            setTimeout(() => {
                const input = document.getElementById('rename-input');
                input.focus();
                input.select();
            }, 100);
        }

        function closeRenameModal() {
            document.getElementById('rename-modal').classList.remove('active');
            renameChatId = null;
        }

        function confirmRename() {
            console.log("Confirm rename called for chat:", renameChatId);
            if (!renameChatId) return;

            const newTitle = document.getElementById('rename-input').value.trim();
            if (!newTitle) return;

            fetch("/project/api/renameChat.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                credentials: "include",
                body: JSON.stringify({ 
                    chat_id: renameChatId,
                    title: newTitle 
                })
            })
            .then(res => {
                console.log("Rename API response:", res.status);
                return res.json();
            })
            .then(data => {
                console.log("Rename successful:", data);
                loadChats();
                closeRenameModal();
            })
            .catch(err => {
                console.error("RENAME ERROR:", err);
                closeRenameModal();
            });
        }

        /* ❌ DELETE CHAT */
        function deleteChat(chatId) {
            openDeleteModal(chatId);
        }

        /* ✏️ RENAME CHAT */
        function renameChat(chatId) {
            openRenameModal(chatId, "New Chat");
        }

        /* ⌨ ENTER SEND */
        document.getElementById("input").addEventListener("keypress", function(e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        /* Add welcome message on load */
        function addWelcomeMessage() {
            const welcomeText = subjectWelcomeMessages[currentMode] || subjectWelcomeMessages.general;
            addTypingMessage(welcomeText);
        }

        /* 🔄 TOGGLE MOBILE MENU */
        function toggleMobileMenu() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }

        /* 🔄 INIT */
        document.addEventListener('DOMContentLoaded', function() {
            loadChats();
            
            // Initialize subject switcher with current mode
            const subjectNames = {
                mathematics: "Mathematics",
                chemistry: "Chemistry", 
                physics: "Physics",
                programming: "Programming",
                biology: "Biology",
                literature: "Literature"
            };
            
            document.getElementById("current-subject").textContent = subjectNames[currentMode] || "General";
            
            // Add welcome message if no existing messages
            const messagesContainer = document.getElementById("messages");
            if (messagesContainer.children.length === 0) {
                addWelcomeMessage();
            }
        });
    </script>
</body>
</html>