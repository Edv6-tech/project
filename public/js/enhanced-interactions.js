// Enhanced Interactive JavaScript Features

class EnhancedInteractions {
    constructor() {
        this.init();
    }

    init() {
        this.createParticles();
        this.initMagneticButtons();
        this.initRippleEffects();
        this.initSmoothScroll();
        this.initParallax();
        this.initTypingEffects();
        this.initMicroInteractions();
        this.initKeyboardShortcuts();
        this.initThemeToggle();
    }

    // Create animated background particles
    createParticles() {
        const particleContainer = document.createElement('div');
        particleContainer.className = 'particle-container';
        document.body.appendChild(particleContainer);

        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = `particle ${['small', 'medium', 'large'][Math.floor(Math.random() * 3)]}`;
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 10 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';
            particleContainer.appendChild(particle);
        }
    }

    // Magnetic button effect
    initMagneticButtons() {
        const buttons = document.querySelectorAll('.magnetic-btn, .btn-primary, .submit-btn');
        
        buttons.forEach(button => {
            button.addEventListener('mousemove', (e) => {
                const rect = button.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                const distance = Math.sqrt(x * x + y * y);
                const maxDistance = 100;
                
                if (distance < maxDistance) {
                    const strength = (maxDistance - distance) / maxDistance;
                    const moveX = x * strength * 0.3;
                    const moveY = y * strength * 0.3;
                    
                    button.style.transform = `translate(${moveX}px, ${moveY}px) scale(${1 + strength * 0.05})`;
                }
            });

            button.addEventListener('mouseleave', () => {
                button.style.transform = '';
            });
        });
    }

    // Ripple effect on clicks
    initRippleEffects() {
        const rippleElements = document.querySelectorAll('.ripple, .btn, .session-card');
        
        rippleElements.forEach(element => {
            element.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.5)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.pointerEvents = 'none';
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });
    }

    // Smooth scroll behavior
    initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Parallax scrolling effects
    initParallax() {
        const parallaxElements = document.querySelectorAll('.parallax');
        
        const handleScroll = () => {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        };

        window.addEventListener('scroll', handleScroll);
    }

    // Typing effect for text
    initTypingEffects() {
        const typingElements = document.querySelectorAll('[data-typing]');
        
        typingElements.forEach(element => {
            const text = element.textContent;
            const speed = parseInt(element.dataset.speed) || 50;
            element.textContent = '';
            
            let i = 0;
            const typeWriter = () => {
                if (i < text.length) {
                    element.textContent += text.charAt(i);
                    i++;
                    setTimeout(typeWriter, speed);
                } else {
                    element.classList.add('typing-complete');
                }
            };
            
            // Start typing when element is in view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        typeWriter();
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            observer.observe(element);
        });
    }

    // Micro-interactions
    initMicroInteractions() {
        // Form input enhancements
        const inputs = document.querySelectorAll('.form-input, input[type="text"], input[type="email"], input[type="password"], textarea');
        
        inputs.forEach(input => {
            // Floating label effect
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.parentElement.classList.remove('focused');
                }
            });

            // Character counter
            if (input.hasAttribute('maxlength')) {
                const counter = document.createElement('div');
                counter.className = 'char-counter';
                counter.textContent = `0/${input.getAttribute('maxlength')}`;
                input.parentElement.appendChild(counter);
                
                input.addEventListener('input', () => {
                    const current = input.value.length;
                    const max = input.getAttribute('maxlength');
                    counter.textContent = `${current}/${max}`;
                    counter.style.color = current > max * 0.9 ? '#e74c3c' : '#999';
                });
            }
        });

        // Card hover effects
        const cards = document.querySelectorAll('.session-card, .message');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-4px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
            });
        });

        // Button click feedback
        const buttons = document.querySelectorAll('.btn, button');
        
        buttons.forEach(button => {
            button.addEventListener('mousedown', () => {
                button.style.transform = 'scale(0.95)';
            });
            
            button.addEventListener('mouseup', () => {
                button.style.transform = '';
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.transform = '';
            });
        });
    }

    // Keyboard shortcuts
    initKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('.search-input');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Escape to close modals
            if (e.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => modal.classList.remove('active'));
            }

            // Ctrl/Cmd + Enter to submit forms
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                const activeElement = document.activeElement;
                if (activeElement && (activeElement.tagName === 'TEXTAREA' || activeElement.type === 'text')) {
                    const form = activeElement.closest('form');
                    if (form) {
                        const submitBtn = form.querySelector('button[type="submit"], .submit-btn');
                        if (submitBtn) {
                            submitBtn.click();
                        }
                    }
                }
            }
        });
    }

    // Theme toggle functionality
    initThemeToggle() {
        const themeToggle = document.querySelector('.theme-toggle');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // Set initial theme
        const savedTheme = localStorage.getItem('theme') || (prefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', savedTheme);
        
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                // Add transition effect
                document.body.style.transition = 'background-color 0.3s ease';
                
                // Update button icon
                themeToggle.innerHTML = newTheme === 'dark' ? '🌙' : '☀️';
            });
        }
    }

    // Loading states
    static setLoading(element, loading) {
        if (loading) {
            element.classList.add('loading');
            element.disabled = true;
            
            if (element.tagName === 'BUTTON') {
                const originalText = element.textContent;
                element.dataset.originalText = originalText;
                element.innerHTML = '<span class="loading-spinner"></span> Loading...';
            }
        } else {
            element.classList.remove('loading');
            element.disabled = false;
            
            if (element.tagName === 'BUTTON' && element.dataset.originalText) {
                element.textContent = element.dataset.originalText;
                delete element.dataset.originalText;
            }
        }
    }

    // Show notifications
    static showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 20px;
            background: ${type === 'error' ? '#e74c3c' : type === 'success' ? '#27ae60' : '#3498db'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            animation: slideInFromRight 0.3s ease-out;
            max-width: 400px;
        `;
        
        document.body.appendChild(notification);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
        
        // Auto remove
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOutToRight 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    }

    // Animate elements on scroll
    static animateOnScroll() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    }
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes slideOutToRight {
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        margin-left: 12px;
    }
    
    .char-counter {
        font-size: 12px;
        color: #999;
        text-align: right;
        margin-top: 4px;
    }
    
    .focused label {
        color: #667eea !important;
    }
    
    [data-theme="dark"] {
        --bg-primary: #1a1a2e;
        --bg-secondary: #16213e;
        --text-primary: white;
        --text-secondary: #ccc;
    }
`;
document.head.appendChild(style);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new EnhancedInteractions();
    EnhancedInteractions.animateOnScroll();
});

// Export for use in other files
window.EnhancedInteractions = EnhancedInteractions;
