<?php include("includes/header.php"); ?>

<style>
/* Modern Professional Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-attachment: fixed;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background Particles */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cdefs%3E%3Cfilter id='glow'%3E%3CfeGaussianBlur stdDeviation='3' result='coloredBlur'/%3E%3CfeMerge%3E%3CfeMergeNode in='coloredBlur'/%3E%3CfeMergeNode in='SourceGraphic'/%3E%3C/feMerge%3E%3C/filter%3E%3C/defs%3E%3Ccircle cx='20' cy='20' r='2' fill='rgba(255,255,255,0.1)' filter='url(%23glow)'%3E%3Canimate attributeName='cy' values='20;80;20' dur='10s' repeatCount='indefinite'/%3E%3C/circle%3E%3Ccircle cx='60' cy='80' r='1.5' fill='rgba(255,255,255,0.08)' filter='url(%23glow)'%3E%3Canimate attributeName='cy' values='80;20;80' dur='15s' repeatCount='indefinite'/%3E%3C/circle%3E%3Ccircle cx='90' cy='40' r='1' fill='rgba(255,255,255,0.06)' filter='url(%23glow)'%3E%3Canimate attributeName='cy' values='40;90;40' dur='12s' repeatCount='indefinite'/%3E%3C/circle%3E%3C/svg%3E") repeat;
            opacity: 0.6;
            z-index: -1;
            animation: float 20s ease-in-out infinite;
        }

.hero-section {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    opacity: 0.3;
}

.floating-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
}

.shape {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    animation: float 20s infinite linear;
}

.shape:nth-child(1) {
    width: 80px;
    height: 80px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.shape:nth-child(2) {
    width: 120px;
    height: 120px;
    top: 60%;
    right: 15%;
    animation-delay: 5s;
}

.shape:nth-child(3) {
    width: 60px;
    height: 60px;
    bottom: 30%;
    left: 20%;
    animation-delay: 10s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-30px) rotate(120deg); }
    66% { transform: translateY(20px) rotate(240deg); }
}

.container {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    text-align: center;
}

.hero-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 4rem 3rem;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    max-width: 800px;
    margin: 0 auto;
    transform: translateY(20px);
    animation: slideUp 1s ease-out forwards;
}

@keyframes slideUp {
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.hero-content {
    opacity: 0;
}

.logo {
    font-size: 4rem;
    margin-bottom: 1rem;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

.main-title {
    font-size: 2.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.subtitle {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 3rem;
    line-height: 1.6;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.action-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 3rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 16px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    min-width: 160px;
    justify-content: center;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.login-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.login-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
}

.register-btn {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.3);
}

.register-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(17, 153, 142, 0.4);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.feature-card {
    background: rgba(255, 255, 255, 0.8);
    padding: 2rem;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    background: rgba(255, 255, 255, 0.95);
}

.feature-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    display: block;
}

.feature-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
}

.feature-desc {
    color: #666;
    line-height: 1.5;
}

.stats-section {
    display: flex;
    justify-content: space-around;
    margin: 3rem 0;
    flex-wrap: wrap;
    gap: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: block;
}

.stat-label {
    color: #666;
    font-weight: 500;
    margin-top: 0.5rem;
}

/* Enhanced Responsive Design */
@media (min-width: 1200px) {
    .container {
        max-width: 1400px;
    }
    
    .hero-content {
        padding: 5rem 4rem;
        max-width: 900px;
    }
    
    .main-title {
        font-size: 3.2rem;
    }
    
    .features-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
    }
}

@media (min-width: 992px) and (max-width: 1199px) {
    .hero-content {
        padding: 4rem 3rem;
        max-width: 800px;
    }
    
    .features-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }
    
    .stats-section {
        gap: 4rem;
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    .hero-content {
        padding: 3.5rem 2.5rem;
        max-width: 700px;
    }
    
    .main-title {
        font-size: 2.5rem;
    }
    
    .subtitle {
        font-size: 1.1rem;
    }
    
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    .stats-section {
        gap: 3rem;
        margin: 2.5rem 0;
    }
    
    .action-buttons {
        gap: 1.2rem;
    }
    
    .btn {
        padding: 0.9rem 1.8rem;
        font-size: 1rem;
    }
}

@media (min-width: 576px) and (max-width: 767px) {
    .hero-content {
        padding: 3rem 2rem;
        margin: 1.5rem;
    }
    
    .main-title {
        font-size: 2.3rem;
        line-height: 1.3;
    }
    
    .subtitle {
        font-size: 1.05rem;
        margin-bottom: 2.5rem;
    }
    
    .logo {
        font-size: 3.5rem;
    }
    
    .action-buttons {
        flex-direction: row;
        justify-content: center;
        gap: 1rem;
    }
    
    .btn {
        flex: 1;
        max-width: 200px;
        padding: 0.9rem 1.5rem;
        font-size: 0.95rem;
    }
    
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    .feature-card {
        padding: 1.5rem;
    }
    
    .stats-section {
        gap: 2.5rem;
        margin: 2rem 0;
    }
    
    .stat-number {
        font-size: 2.2rem;
    }
}

@media (max-width: 575px) {
    .hero-section {
        padding: 1rem 0;
    }
    
    .container {
        padding: 1rem;
    }
    
    .hero-content {
        padding: 2.5rem 1.5rem;
        margin: 0.5rem;
        border-radius: 20px;
    }
    
    .logo {
        font-size: 3rem;
        margin-bottom: 0.8rem;
    }
    
    .main-title {
        font-size: 1.9rem;
        line-height: 1.4;
        margin-bottom: 0.8rem;
    }
    
    .subtitle {
        font-size: 0.95rem;
        margin-bottom: 2rem;
        line-height: 1.5;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }
    
    .btn {
        width: 100%;
        max-width: 280px;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        border-radius: 12px;
    }
    
    .stats-section {
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
        margin: 2rem 0;
    }
    
    .stat-item {
        width: 100%;
        max-width: 200px;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .stat-label {
        font-size: 0.9rem;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .feature-card {
        padding: 1.5rem;
        border-radius: 12px;
    }
    
    .feature-icon {
        font-size: 2.2rem;
        margin-bottom: 0.8rem;
    }
    
    .feature-title {
        font-size: 1.2rem;
        margin-bottom: 0.4rem;
    }
    
    .feature-desc {
        font-size: 0.9rem;
        line-height: 1.4;
    }
}

@media (max-width: 400px) {
    .hero-content {
        padding: 2rem 1.2rem;
        margin: 0.3rem;
    }
    
    .main-title {
        font-size: 1.7rem;
    }
    
    .subtitle {
        font-size: 0.9rem;
    }
    
    .logo {
        font-size: 2.5rem;
    }
    
    .btn {
        padding: 0.9rem 1.2rem;
        font-size: 0.95rem;
        max-width: 260px;
    }
    
    .feature-card {
        padding: 1.2rem;
    }
    
    .stat-number {
        font-size: 1.8rem;
    }
}

/* Landscape orientation adjustments */
@media (max-height: 600px) and (orientation: landscape) {
    .hero-section {
        min-height: auto;
        padding: 2rem 0;
    }
    
    .hero-content {
        padding: 2rem;
    }
    
    .logo {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }
    
    .main-title {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .subtitle {
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }
    
    .action-buttons {
        margin-bottom: 2rem;
    }
    
    .stats-section {
        margin: 1.5rem 0;
    }
    
    .features-grid {
        margin-top: 1rem;
    }
}

/* High DPI / Retina displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .hero-content {
        border: 0.5px solid rgba(255, 255, 255, 0.3);
    }
    
    .feature-card {
        border: 0.5px solid rgba(255, 255, 255, 0.2);
    }
}

/* Print styles */
@media print {
    .hero-section {
        background: white !important;
        -webkit-print-color-adjust: exact;
    }
    
    .floating-shapes {
        display: none;
    }
    
    .hero-content {
        background: white !important;
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
    
    .btn {
        border: 2px solid #667eea !important;
        background: white !important;
        color: #667eea !important;
    }
}
</style>

<div class="hero-section">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="container">
        <div class="hero-content">
            <div class="logo">📚</div>
            <h1 class="main-title">Library Management System</h1>
            <p class="subtitle">
                Streamline your library operations with our comprehensive digital solution. 
                Manage books, track memberships, and handle transactions with ease and efficiency.
            </p>
            
            <div class="action-buttons">
                <a href="auth/login.php" class="btn login-btn">
                    🔐 Member Login
                </a>
                <a href="auth/register.php" class="btn register-btn">
                    📝 Join Library
                </a>
            </div>
            
            <div class="stats-section">
                <div class="stat-item">
                    <span class="stat-number">10K+</span>
                    <div class="stat-label">Books Available</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <div class="stat-label">Active Members</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <div class="stat-label">Online Access</div>
                </div>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <span class="feature-icon">📖</span>
                    <h3 class="feature-title">Book Management</h3>
                    <p class="feature-desc">Comprehensive catalog system with search, categorization, and availability tracking.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">👥</span>
                    <h3 class="feature-title">Member Portal</h3>
                    <p class="feature-desc">Easy registration, profile management, and personalized reading history.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">📊</span>
                    <h3 class="feature-title">Smart Analytics</h3>
                    <p class="feature-desc">Detailed reports on book circulation, popular titles, and member activity.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Enhanced JavaScript for responsive behavior
document.addEventListener('DOMContentLoaded', function() {
    // Viewport and device detection
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024;
    const isTouch = 'ontouchstart' in window;
    
    // Adjust animations based on device capabilities
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    // Animate stats numbers with performance consideration
    const statNumbers = document.querySelectorAll('.stat-number');
    const animateNumber = (element, target) => {
        if (prefersReducedMotion) {
            element.textContent = target.toString().includes('K') ? target : target + '+';
            return;
        }
        
        const increment = target / (isMobile ? 30 : 50);
        let current = 0;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target.toString().includes('K') ? target : target + '+';
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + (target.toString().includes('K') ? 'K+' : '+');
            }
        }, isMobile ? 50 : 30);
    };
    
    // Enhanced Intersection Observer with threshold adjustment
    const observerOptions = {
        threshold: isMobile ? 0.1 : 0.3,
        rootMargin: isMobile ? '50px' : '100px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target.textContent;
                if (target.includes('10K')) {
                    animateNumber(entry.target, '10K');
                } else if (target.includes('500')) {
                    animateNumber(entry.target, 500);
                } else if (target.includes('24/7')) {
                    entry.target.textContent = '24/7';
                }
            }
        });
    }, observerOptions);
    
    statNumbers.forEach(stat => observer.observe(stat));
    
    // Enhanced button interactions
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        if (isTouch) {
            // Touch-specific interactions
            btn.addEventListener('touchstart', function() {
                this.style.transform = 'translateY(-2px) scale(0.98)';
            });
            
            btn.addEventListener('touchend', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        } else {
            // Mouse interactions
            btn.addEventListener('mouseenter', function() {
                if (!prefersReducedMotion) {
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                }
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        }
        
        // Click ripple effect
        btn.addEventListener('click', function(e) {
            if (prefersReducedMotion) return;
            
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                pointer-events: none;
            `;
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
    
    // Enhanced feature card animations
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach((card, index) => {
        if (!prefersReducedMotion) {
            card.style.animationDelay = `${index * 0.2}s`;
            card.style.animation = 'slideUp 0.8s ease-out forwards';
        }
        
        // Hover effects for non-touch devices
        if (!isTouch) {
            card.addEventListener('mouseenter', function() {
                if (!prefersReducedMotion) {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        }
    });
    
    // Responsive font size adjustment
    function adjustFontSizes() {
        const vw = window.innerWidth;
        const vh = window.innerHeight;
        
        if (vw < 350) {
            document.documentElement.style.fontSize = '14px';
        } else if (vw < 480) {
            document.documentElement.style.fontSize = '15px';
        } else {
            document.documentElement.style.fontSize = '16px';
        }
    }
    
    // Debounced resize handler
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            adjustFontSizes();
            
            // Recalculate animations for new viewport
            const newIsMobile = window.innerWidth < 768;
            if (newIsMobile !== isMobile) {
                location.reload(); // Refresh for major layout changes
            }
        }, 150);
    });
    
    // Initial font size adjustment
    adjustFontSizes();
    
    // Smooth scroll polyfill for older browsers
    if (!('scrollBehavior' in document.documentElement.style)) {
        const links = document.querySelectorAll('a[href^="#"]');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    }
    
    // Performance monitoring
    if ('requestIdleCallback' in window) {
        requestIdleCallback(() => {
            // Lazy load non-critical animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to { transform: scale(4); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        });
    }
});

// Enhanced parallax effect with performance optimization
let ticking = false;
function updateParallax() {
    const scrolled = window.pageYOffset;
    const shapes = document.querySelectorAll('.shape');
    const rate = scrolled * -0.5;
    
    shapes.forEach((shape, index) => {
        const speed = 0.5 + (index * 0.2);
        const yPos = -(scrolled * speed);
        const rotate = scrolled * 0.05;
        
        shape.style.transform = `translate3d(0, ${yPos}px, 0) rotate(${rotate}deg)`;
    });
    
    ticking = false;
}

// Throttled scroll event
window.addEventListener('scroll', () => {
    if (!ticking && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        requestAnimationFrame(updateParallax);
        ticking = true;
    }
});

// Orientation change handler
window.addEventListener('orientationchange', () => {
    setTimeout(() => {
        // Recalculate layout after orientation change
        window.scrollTo(0, 0);
        
        // Trigger resize event
        window.dispatchEvent(new Event('resize'));
    }, 100);
});

// Enhanced accessibility
document.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
        document.body.classList.add('keyboard-navigation');
    }
});

document.addEventListener('mousedown', () => {
    document.body.classList.remove('keyboard-navigation');
});

// Add focus styles for keyboard navigation
const style = document.createElement('style');
style.textContent = `
    .keyboard-navigation *:focus {
        outline: 2px solid #667eea !important;
        outline-offset: 2px !important;
    }
`;
document.head.appendChild(style);
</script>

<?php include("includes/footer.php"); ?>