<?php include("../includes/header.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Book - Library Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            --secondary-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
            --error-gradient: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-primary: #1a202c;
            --text-secondary: #4a5568;
            --text-muted: #718096;
            --surface: rgba(255, 255, 255, 0.95);
            --surface-elevated: rgba(255, 255, 255, 0.98);
            --shadow-sm: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 10px 40px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.15);
            --shadow-xl: 0 30px 80px rgba(0, 0, 0, 0.2);
            --border-radius: 20px;
            --border-radius-lg: 28px;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

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
            display: flex;
            flex-direction: column;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.02)"/><circle cx="10" cy="50" r="0.5" fill="rgba(255,255,255,0.02)"/><circle cx="90" cy="50" r="0.5" fill="rgba(255,255,255,0.02)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
            z-index: 1;
        }

        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 20s infinite linear;
        }

        .shape:nth-child(1) {
            top: 20%;
            left: 10%;
            width: 100px;
            height: 100px;
            background: var(--secondary-gradient);
            border-radius: 50%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 60%;
            right: 15%;
            width: 150px;
            height: 150px;
            background: var(--success-gradient);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation-delay: -5s;
        }

        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            width: 80px;
            height: 80px;
            background: var(--warning-gradient);
            border-radius: 20px;
            transform: rotate(45deg);
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(90deg); }
            50% { transform: translateY(-40px) rotate(180deg); }
            75% { transform: translateY(-20px) rotate(270deg); }
        }

        .main-container {
            flex: 1;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 10;
        }

        .request-container {
            width: 100%;
            max-width: 900px;
            position: relative;
        }

        .request-card {
            background: var(--surface-elevated);
            backdrop-filter: blur(30px);
            border-radius: var(--border-radius-lg);
            padding: 3.5rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .request-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--primary-gradient);
            background-size: 300% 100%;
            animation: shimmer 4s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { background-position: 300% 0; }
            50% { background-position: -100% 0; }
        }

        .card-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .card-icon-container {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }

        .card-icon {
            width: 100px;
            height: 100px;
            background: var(--primary-gradient);
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            animation: iconFloat 4s ease-in-out infinite;
        }

        .card-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            animation: iconShine 3s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-8px) scale(1.05); }
        }

        @keyframes iconShine {
            0%, 100% { transform: translateX(-200%) translateY(-200%) rotate(45deg); }
            50% { transform: translateX(200%) translateY(200%) rotate(45deg); }
        }

        .card-title {
            font-size: 2.75rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .card-subtitle {
            color: var(--text-muted);
            font-size: 1.2rem;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
            font-weight: 400;
        }

        .form-section {
            margin-bottom: 2.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 2rem;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .form-label i {
            font-size: 1.1rem;
            color: #667eea;
        }

        .form-input-container {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 1.25rem 1.5rem;
            border: 2px solid #e2e8f0;
            border-radius: var(--border-radius);
            font-size: 1.05rem;
            font-weight: 500;
            transition: var(--transition);
            background: #fafbfc;
            color: var(--text-primary);
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), var(--shadow-md);
            transform: translateY(-2px);
        }

        .form-input:valid {
            border-color: #48bb78;
        }

        .form-textarea {
            min-height: 140px;
            resize: vertical;
            font-family: inherit;
        }

        .priority-section {
            margin: 2.5rem 0;
        }

        .priority-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .priority-card {
            background: var(--surface);
            border: 2px solid #e2e8f0;
            border-radius: var(--border-radius);
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .priority-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: var(--transition);
        }

        .priority-card:hover::before {
            left: 100%;
        }

        .priority-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .priority-card.selected {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .priority-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .priority-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .priority-low .priority-icon {
            background: var(--success-gradient);
        }

        .priority-medium .priority-icon {
            background: var(--warning-gradient);
        }

        .priority-high .priority-icon {
            background: var(--error-gradient);
        }

        .priority-label {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .priority-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .submit-section {
            margin-top: 3rem;
            text-align: center;
        }

        .submit-btn {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 1.5rem 3rem;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            min-width: 200px;
            box-shadow: var(--shadow-md);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: var(--transition);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .submit-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .submit-btn.success {
            background: var(--success-gradient);
        }

        .features-section {
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            padding: 2rem;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--secondary-gradient);
            transform: scaleX(0);
            transition: var(--transition);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: var(--secondary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.8rem;
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .feature-title {
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .feature-description {
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 1rem;
        }

        .success-message {
            background: var(--success-gradient);
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 600;
            opacity: 0;
            transform: translateY(-20px);
            transition: var(--transition);
        }

        .success-message.show {
            opacity: 1;
            transform: translateY(0);
        }

        .input-validation {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: var(--transition-fast);
        }

        .input-validation.valid {
            opacity: 1;
            color: #48bb78;
        }

        .input-validation.invalid {
            opacity: 1;
            color: #f56565;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
            
            .request-card {
                padding: 2rem;
            }
            
            .card-title {
                font-size: 2.2rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .priority-grid {
                grid-template-columns: 1fr;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .request-card {
                padding: 1.5rem;
            }
            
            .card-title {
                font-size: 2rem;
            }
            
            .card-subtitle {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="animated-bg">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
    </div>

    <main class="main-container">
        <div class="request-container">
            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i> Your book request has been submitted successfully!
            </div>
            
            <div class="request-card">
                <div class="card-header">
                    <div class="card-icon-container">
                        <div class="card-icon">
                            <i class="fas fa-book-medical"></i>
                        </div>
                    </div>
                    <h1 class="card-title">Request a Book</h1>
                    <p class="card-subtitle">
                        Couldn't find what you're looking for? Submit a detailed request and our expert library team will carefully review it for acquisition. We're committed to expanding our collection based on your needs.
                    </p>
                </div>

                <form id="bookRequestForm" action="" method="POST">
                    <div class="form-section">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="bookTitle">
                                    <i class="fas fa-book"></i>
                                    Book Title *
                                </label>
                                <div class="form-input-container">
                                    <input type="text" id="bookTitle" name="book_title" class="form-input" placeholder="Enter the complete book title" required>
                                    <i class="fas fa-check input-validation"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="authorName">
                                    <i class="fas fa-user-edit"></i>
                                    Author Name *
                                </label>
                                <div class="form-input-container">
                                    <input type="text" id="authorName" name="author_name" class="form-input" placeholder="Enter the author's full name" required>
                                    <i class="fas fa-check input-validation"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="isbn">
                                    <i class="fas fa-barcode"></i>
                                    ISBN (Optional)
                                </label>
                                <div class="form-input-container">
                                    <input type="text" id="isbn" name="isbn" class="form-input" placeholder="978-XXXXXXXXXX">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="publisher">
                                    <i class="fas fa-building"></i>
                                    Publisher (Optional)
                                </label>
                                <div class="form-input-container">
                                    <input type="text" id="publisher" name="publisher" class="form-input" placeholder="Publishing house name">
                                </div>
                            </div>
                        </div>

                        <div class="priority-section">
                            <label class="form-label">
                                <i class="fas fa-exclamation-triangle"></i>
                                Request Priority Level
                            </label>
                            <div class="priority-grid">
                                <label class="priority-card priority-low selected" for="priority-low">
                                    <input type="radio" id="priority-low" name="priority" value="low" checked>
                                    <div class="priority-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="priority-label">Standard</div>
                                    <div class="priority-desc">Regular processing timeline</div>
                                </label>
                                <label class="priority-card priority-medium" for="priority-medium">
                                    <input type="radio" id="priority-medium" name="priority" value="medium">
                                    <div class="priority-icon">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div class="priority-label">Important</div>
                                    <div class="priority-desc">Expedited review process</div>
                                </label>
                                <label class="priority-card priority-high" for="priority-high">
                                    <input type="radio" id="priority-high" name="priority" value="high">
                                    <div class="priority-icon">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <div class="priority-label">Urgent</div>
                                    <div class="priority-desc">Immediate attention required</div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reason">
                                <i class="fas fa-comment-alt"></i>
                                Request Justification
                            </label>
                            <div class="form-input-container">
                                <textarea id="reason" name="reason" class="form-input form-textarea" placeholder="Please provide detailed information about why you need this book (academic research, course requirements, professional development, etc.). The more specific you are, the better we can prioritize your request."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="submit-section">
                        <button type="submit" class="submit-btn" id="submitBtn">
                            <span class="btn-text">
                                <i class="fas fa-paper-plane"></i>
                                Submit Request
                            </span>
                        </button>
                    </div>
                </form>

                <div class="features-section">
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div class="feature-title">Lightning Fast Processing</div>
                            <div class="feature-description">Most requests are reviewed and processed within 24-48 hours by our dedicated acquisition team</div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="feature-title">Real-time Notifications</div>
                            <div class="feature-description">Receive instant updates via email and SMS when your request status changes or when the book becomes available</div>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="feature-title">95% Success Rate</div>
                            <div class="feature-description">Our impressive fulfillment rate ensures that most requested books are successfully acquired and added to our collection</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php"); ?>

    <script>
        // Form validation and interactions
        class BookRequestForm {
            constructor() {
                this.form = document.getElementById('bookRequestForm');
                this.submitBtn = document.getElementById('submitBtn');
                this.successMessage = document.getElementById('successMessage');
                this.init();
            }

            init() {
                this.setupPrioritySelection();
                this.setupFormValidation();
                this.setupFormSubmission();
                this.setupAnimations();
            }

            setupPrioritySelection() {
                document.querySelectorAll('.priority-card').forEach(card => {
                    card.addEventListener('click', (e) => {
                        document.querySelectorAll('.priority-card').forEach(c => c.classList.remove('selected'));
                        card.classList.add('selected');
                        card.querySelector('input').checked = true;
                        
                        // Add selection animation
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            card.style.transform = '';
                        }, 150);
                    });
                });
            }

            setupFormValidation() {
                const requiredInputs = document.querySelectorAll('input[required]');
                
                requiredInputs.forEach(input => {
                    const validation = input.parentElement.querySelector('.input-validation');
                    
                    input.addEventListener('input', () => {
                        if (validation) {
                            if (input.value.trim().length > 0) {
                                validation.className = 'fas fa-check input-validation valid';
                            } else {
                                validation.className = 'fas fa-times input-validation invalid';
                            }
                        }
                    });

                    input.addEventListener('blur', () => {
                        if (input.value.trim().length === 0 && validation) {
                            validation.className = 'fas fa-times input-validation invalid';
                        }
                    });
                });
            }

            setupFormSubmission() {
                this.form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleSubmit();
                });
            }

            async handleSubmit() {
                // Validate form
                const bookTitle = document.getElementById('bookTitle').value.trim();
                const authorName = document.getElementById('authorName').value.trim();
                
                if (!bookTitle || !authorName) {
                    this.showError('Please fill in all required fields');
                    return;
                }

                // Show loading state
                this.setLoadingState();

                try {
                    // Simulate API call
                    await this.simulateSubmission();
                    this.showSuccess();
                } catch (error) {
                    this.showError('Something went wrong. Please try again.');
                }
            }

            setLoadingState() {
                this.submitBtn.classList.add('loading');
                this.submitBtn.innerHTML = `
                    <span class="btn-text">
                        <i class="fas fa-spinner fa-spin"></i>
                        Processing Request...
                    </span>
                `;
            }

            async simulateSubmission() {
                return new Promise(resolve => {
                    setTimeout(resolve, 2500);
                });
            }

            showSuccess() {
                this.submitBtn.classList.remove('loading');
                this.submitBtn.classList.add('success');
                this.submitBtn.innerHTML = `
                    <span class="btn-text">
                        <i class="fas fa-check"></i>
                        Request Submitted Successfully!
                    </span>
                `;

                this.successMessage.classList.add('show');
                
                // Scroll to top to show success message
                this.successMessage.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });

                // Reset form after success
                setTimeout(() => {
                    this.resetForm();
                }, 3000);
            }

            showError(message) {
                this.submitBtn.classList.remove('loading');
                this.submitBtn.innerHTML = `
                    <span class="btn-text">
                        <i class="fas fa-exclamation-triangle"></i>
                        ${message}
                    </span>
                `;

                setTimeout(() => {
                    this.resetSubmitButton();
                }, 3000);
            }

            resetSubmitButton() {
                this.submitBtn.classList.remove('loading', 'success');
                this.submitBtn.innerHTML = `
                    <span class="btn-text">
                        <i class="fas fa-paper-plane"></i>
                        Submit Request
                    </span>
                `;
            }

            resetForm() {
                this.form.reset();
                document.querySelectorAll('.priority-card').forEach(c => c.classList.remove('selected'));
                document.querySelector('.priority-card').classList.add('selected');
                document.querySelectorAll('.input-validation').forEach(v => {
                    v.className = 'fas fa-check input-validation';
                });
                this.successMessage.classList.remove('show');
                this.resetSubmitButton();
            }

            setupAnimations() {
                // Intersection Observer for animations
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.animationDelay = `${Math.random() * 0.5}s`;
                            entry.target.classList.add('animate-in');
                        }
                    });
                }, { threshold: 0.1 });

                // Observe form elements
                document.querySelectorAll('.form-group, .feature-card').forEach(el => {
                    observer.observe(el);
                });

                // Parallax effect for floating shapes
                this.setupParallax();

                // Smooth focus animations
                this.setupFocusAnimations();
            }

            setupParallax() {
                let ticking = false;

                function updateParallax() {
                    const scrolled = window.pageYOffset;
                    const shapes = document.querySelectorAll('.shape');
                    
                    shapes.forEach((shape, index) => {
                        const speed = 0.5 + (index * 0.1);
                        const yPos = -(scrolled * speed);
                        shape.style.transform = `translate3d(0, ${yPos}px, 0)`;
                    });
                    
                    ticking = false;
                }

                function requestTick() {
                    if (!ticking) {
                        requestAnimationFrame(updateParallax);
                        ticking = true;
                    }
                }

                window.addEventListener('scroll', requestTick);
            }

            setupFocusAnimations() {
                document.querySelectorAll('.form-input').forEach((input, index) => {
                    input.addEventListener('focus', () => {
                        input.parentElement.parentElement.style.transform = 'scale(1.02)';
                        input.parentElement.parentElement.style.zIndex = '10';
                        
                        // Smooth scroll into view
                        setTimeout(() => {
                            input.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center' 
                            });
                        }, 100);
                    });
                    
                    input.addEventListener('blur', () => {
                        input.parentElement.parentElement.style.transform = 'scale(1)';
                        input.parentElement.parentElement.style.zIndex = '1';
                    });
                });
            }
        }

        // Enhanced animations and interactions
        class EnhancedAnimations {
            constructor() {
                this.init();
            }

            init() {
                this.setupMouseTracker();
                this.setupTypingEffect();
                this.setupAdvancedHovers();
            }

            setupMouseTracker() {
                let mouseX = 0;
                let mouseY = 0;
                let isMoving = false;

                document.addEventListener('mousemove', (e) => {
                    mouseX = e.clientX;
                    mouseY = e.clientY;
                    
                    if (!isMoving) {
                        isMoving = true;
                        this.updateFloatingElements(mouseX, mouseY);
                        setTimeout(() => { isMoving = false; }, 16);
                    }
                });
            }

            updateFloatingElements(x, y) {
                const shapes = document.querySelectorAll('.shape');
                const centerX = window.innerWidth / 2;
                const centerY = window.innerHeight / 2;
                
                shapes.forEach((shape, index) => {
                    const intensity = 0.1 + (index * 0.05);
                    const moveX = (x - centerX) * intensity;
                    const moveY = (y - centerY) * intensity;
                    
                    shape.style.transform += ` translate(${moveX}px, ${moveY}px)`;
                });
            }

            setupTypingEffect() {
                const subtitle = document.querySelector('.card-subtitle');
                const text = subtitle.textContent;
                subtitle.textContent = '';
                
                let index = 0;
                const typeSpeed = 30;
                
                function typeText() {
                    if (index < text.length) {
                        subtitle.textContent += text.charAt(index);
                        index++;
                        setTimeout(typeText, typeSpeed);
                    }
                }

                // Start typing effect after card animation
                setTimeout(typeText, 1000);
            }

            setupAdvancedHovers() {
                // 3D tilt effect for cards
                document.querySelectorAll('.feature-card, .priority-card').forEach(card => {
                    card.addEventListener('mousemove', (e) => {
                        const rect = card.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        
                        const centerX = rect.width / 2;
                        const centerY = rect.height / 2;
                        
                        const rotateX = (y - centerY) / 10;
                        const rotateY = (centerX - x) / 10;
                        
                        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`;
                    });
                    
                    card.addEventListener('mouseleave', () => {
                        card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
                    });
                });
            }
        }

        // Enhanced form interactions
        class SmartFormFeatures {
            constructor() {
                this.setupSmartPlaceholders();
                this.setupProgressIndicator();
                this.setupAutoSave();
            }

            setupSmartPlaceholders() {
                const inputs = document.querySelectorAll('.form-input');
                
                inputs.forEach(input => {
                    const originalPlaceholder = input.placeholder;
                    
                    input.addEventListener('focus', () => {
                        input.placeholder = '';
                    });
                    
                    input.addEventListener('blur', () => {
                        if (!input.value) {
                            input.placeholder = originalPlaceholder;
                        }
                    });
                });
            }

            setupProgressIndicator() {
                const requiredFields = document.querySelectorAll('input[required]');
                const progressBar = this.createProgressBar();
                
                function updateProgress() {
                    const filledFields = Array.from(requiredFields).filter(field => field.value.trim() !== '');
                    const progress = (filledFields.length / requiredFields.length) * 100;
                    progressBar.style.width = `${progress}%`;
                }

                requiredFields.forEach(field => {
                    field.addEventListener('input', updateProgress);
                });
            }

            createProgressBar() {
                const progressContainer = document.createElement('div');
                progressContainer.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 4px;
                    background: rgba(255,255,255,0.2);
                    z-index: 1000;
                `;
                
                const progressBar = document.createElement('div');
                progressBar.style.cssText = `
                    height: 100%;
                    width: 0%;
                    background: linear-gradient(90deg, #667eea, #764ba2);
                    transition: width 0.3s ease;
                `;
                
                progressContainer.appendChild(progressBar);
                document.body.appendChild(progressContainer);
                
                return progressBar;
            }

            setupAutoSave() {
                const inputs = document.querySelectorAll('.form-input');
                
                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        const formData = new FormData(document.getElementById('bookRequestForm'));
                        const data = Object.fromEntries(formData);
                        
                        // In a real implementation, you'd save to localStorage or send to server
                        console.log('Auto-saving form data:', data);
                    });
                });
            }
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new BookRequestForm();
            new EnhancedAnimations();
            new SmartFormFeatures();
            
            // Add additional CSS classes for animations
            const style = document.createElement('style');
            style.textContent = `
                .animate-in {
                    animation: slideUpFade 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                }
                
                @keyframes slideUpFade {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                .feature-card, .priority-card {
                    will-change: transform;
                }
                
                @media (prefers-reduced-motion: reduce) {
                    *, *::before, *::after {
                        animation-duration: 0.01ms !important;
                        animation-iteration-count: 1 !important;
                        transition-duration: 0.01ms !important;
                    }
                }
            `;
            document.head.appendChild(style);
        });

        // Service Worker for offline functionality (optional enhancement)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => console.log('SW registered'))
                    .catch(error => console.log('SW registration failed'));
            });
        }
    </script>
</body>
</html>