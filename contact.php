<?php 
// Includes header.php from the includes folder inside the same directory
include(__DIR__ . '/includes/header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Library Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

  

        .contact-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .contact-info {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .contact-info:hover {
            transform: translateY(-5px);
        }

        .contact-info h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.8rem;
            position: relative;
            padding-bottom: 10px;
        }

        .contact-info h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .info-icon {
            background-color: var(--light-color);
            color: var(--primary-color);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            font-size: 1.2rem;
        }

        .info-content h3 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: var(--secondary-color);
        }

        .info-content p, .info-content a {
            color: #666;
            text-decoration: none;
            transition: color 0.3s;
        }

        .info-content a:hover {
            color: var(--primary-color);
        }

        .contact-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background 0.3s, transform 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .map-container {
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .opening-hours {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .opening-hours h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.8rem;
            position: relative;
            padding-bottom: 10px;
        }

        .opening-hours h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .hours-table {
            width: 100%;
            border-collapse: collapse;
        }

        .hours-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .hours-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .hours-table td:first-child {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .hours-table .closed {
            color: var(--accent-color);
            font-weight: 600;
        }

 /* Footer Styles */
        footer {
            background: #2c3e50;
            color: white;
            padding: 2rem 0 1rem;
            margin-top: auto;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            font-size: 18px;
            margin-bottom: 1rem;
            color: #3498db;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-section p,
        .footer-section li {
            font-size: 14px;
            line-height: 1.8;
            opacity: 0.9;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .footer-section ul li a:hover {
            color: #3498db;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(52, 152, 219, 0.2);
            border-radius: 50%;
            color: white;
            font-size: 18px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-links a:hover {
            background: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-left p {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }

        .footer-links {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            opacity: 0.8;
            transition: all 0.3s ease;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .footer-links a:hover {
            opacity: 1;
            color: #3498db;
            background: rgba(52, 152, 219, 0.1);
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-to-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .back-to-top.show {
            display: flex;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr;
                text-align: left;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }

            .footer-links {
                justify-content: center;
                gap: 15px;
            }

            .social-links {
                justify-content: center;
            }

            .footer-container {
                padding: 0 1rem;
            }
        }

        @media (max-width: 480px) {
            .footer-links {
                flex-direction: column;
                gap: 10px;
            }

            .footer-links a {
                text-align: center;
            }

            .back-to-top {
                bottom: 15px;
                right: 15px;
                width: 45px;
                height: 45px;
                font-size: 16px;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Print Styles */
        @media print {
            footer {
                display: none;
            }
            
            .back-to-top {
                display: none;
            }
        }
        /* Form validation styles */
        .form-group.success .form-control {
            border-color: var(--success-color);
        }

        .form-group.error .form-control {
            border-color: var(--accent-color);
        }

        .form-group .error-message {
            color: var(--accent-color);
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }

        .form-group.error .error-message {
            display: block;
        }

        /* Success message */
        .success-message {
            background-color: rgba(46, 204, 113, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="contact-container">
            <div class="contact-info">
                <h2>General Inquiries</h2>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>Email Us</h3>
                        <p><a href="mailto:info@librarysystem.com">info@librarysystem.com</a></p>
                        <p><a href="mailto:support@librarysystem.com">support@librarysystem.com</a></p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Call Us</h3>
                        <p><a href="tel:+18005551234">+1 (800) 555-1234</a></p>
                        <p>Mon-Fri: 9am-6pm</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Visit Us</h3>
                        <p>123 Library Street</p>
                        <p>Knowledge City, KC 12345</p>
                    </div>
                </div>
            </div>

            <div class="contact-info">
                <h2>Specialized Help</h2>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="info-content">
                        <h3>Collection Questions</h3>
                        <p><a href="mailto:collections@librarysystem.com">collections@librarysystem.com</a></p>
                        <p>Ext. 123</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="info-content">
                        <h3>Membership Services</h3>
                        <p><a href="mailto:membership@librarysystem.com">membership@librarysystem.com</a></p>
                        <p>Ext. 456</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div class="info-content">
                        <h3>Technical Support</h3>
                        <p><a href="mailto:techsupport@librarysystem.com">techsupport@librarysystem.com</a></p>
                        <p>Ext. 789</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <div class="success-message" id="successMessage">
                Thank you for your message! We'll get back to you within 24 hours.
            </div>
            <h2>Send Us a Message</h2>
            <form id="contactForm">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" class="form-control" placeholder="Enter your name" required>
                    <div class="error-message">Please enter your name</div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" class="form-control" placeholder="Enter your email" required>
                    <div class="error-message">Please enter a valid email address</div>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <select id="subject" class="form-control" required>
                        <option value="" disabled selected>Select a subject</option>
                        <option value="general">General Inquiry</option>
                        <option value="membership">Membership Question</option>
                        <option value="technical">Technical Issue</option>
                        <option value="collection">Collection Question</option>
                        <option value="event">Event Information</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="error-message">Please select a subject</div>
                </div>
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" class="form-control" placeholder="Type your message here" required></textarea>
                    <div class="error-message">Please enter your message</div>
                </div>
                <button type="submit" class="btn btn-block">Send Message</button>
            </form>
        </div>

        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1623251234567!5m2!1sen!2s" allowfullscreen="" loading="lazy"></iframe>
        </div>

        <div class="opening-hours">
            <h2>Opening Hours</h2>
            <table class="hours-table">
                <tr>
                    <td>Monday - Thursday</td>
                    <td>9:00 AM - 8:00 PM</td>
                </tr>
                <tr>
                    <td>Friday</td>
                    <td>9:00 AM - 6:00 PM</td>
                </tr>
                <tr>
                    <td>Saturday</td>
                    <td>10:00 AM - 5:00 PM</td>
                </tr>
                <tr>
                    <td>Sunday</td>
                    <td class="closed">Closed</td>
                </tr>
                <tr>
                    <td>Holidays</td>
                    <td class="closed">Closed</td>
                </tr>
            </table>
        </div>
    </div>

 <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-book-open"></i> About LibraryMS</h3>
                    <p>A comprehensive digital library management system designed to streamline book cataloging, member management, and resource accessibility for modern libraries.</p>
                    <div class="social-links">
                        <a href="#" title="Facebook" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" title="Twitter" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" title="LinkedIn" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" title="Instagram" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" title="YouTube" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-link"></i> Quick Links</h3>
                    <ul>
                        <li><a href="../books/catalog.php"><i class="fas fa-book"></i> Book Catalog</a></li>
                        <li><a href="../books/search.php"><i class="fas fa-search"></i> Advanced Search</a></li>
                        <?php if (isset($_SESSION['member_id'])): ?>
                            <li><a href="../member/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li><a href="../member/profile.php"><i class="fas fa-user-edit"></i> My Profile</a></li>
                            <li><a href="../books/my-books.php"><i class="fas fa-bookmark"></i> My Books</a></li>
                            <li><a href="../member/history.php"><i class="fas fa-history"></i> Borrow History</a></li>
                        <?php else: ?>
                            <li><a href="../auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                            <li><a href="../auth/login.php"><i class="fas fa-sign-in-alt"></i> Member Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-cog"></i> Services</h3>
                    <ul>
                        <li><a href="../services/digital-reading.php"><i class="fas fa-book-reader"></i> Digital Reading</a></li>
                        <li><a href="../services/downloads.php"><i class="fas fa-download"></i> Resource Downloads</a></li>
                        <li><a href="../services/events.php"><i class="fas fa-calendar"></i> Library Events</a></li>
                        <li><a href="../services/research.php"><i class="fas fa-graduation-cap"></i> Research Support</a></li>
                        <li><a href="../services/study-rooms.php"><i class="fas fa-users"></i> Study Rooms</a></li>
                        <li><a href="../services/book-recommendations.php"><i class="fas fa-thumbs-up"></i> Book Recommendations</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Contact Info</h3>
                    <ul>
                        <li><a href="mailto:info@libraryms.com"><i class="fas fa-envelope"></i> info@libraryms.com</a></li>
                        <li><a href="tel:+15551234567"><i class="fas fa-phone"></i> +1 (555) 123-4567</a></li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Library Street<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Knowledge City, KC 12345</li>
                        <li><i class="fas fa-clock"></i> 
                            <strong>Library Hours:</strong><br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mon-Fri: 8:00 AM - 8:00 PM<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Saturday: 10:00 AM - 6:00 PM<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sunday: 12:00 PM - 5:00 PM
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-left">
                    <p>&copy; <?php echo date("Y"); ?> LibraryMS - Library Management System. All rights reserved.</p>
                    <p style="font-size: 12px; opacity: 0.8; margin-top: 5px;">
                        Developed with <i class="fas fa-heart" style="color: #e74c3c;"></i> for knowledge seekers worldwide
                    </p>
                </div>
                <div class="footer-links">
                    <a href="../legal/privacy.php">Privacy Policy</a>
                    <a href="../legal/terms.php">Terms of Service</a>
                    <a href="../legal/cookies.php">Cookie Policy</a>
                    <a href="../help/faq.php">FAQ</a>
                    <a href="../help/support.php">Support</a>
                    <a href="../about.php">About Us</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const successMessage = document.getElementById('successMessage');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset all error states
                const formGroups = document.querySelectorAll('.form-group');
                formGroups.forEach(group => {
                    group.classList.remove('error', 'success');
                });

                // Validate form
                let isValid = true;
                const name = document.getElementById('name');
                const email = document.getElementById('email');
                const subject = document.getElementById('subject');
                const message = document.getElementById('message');

                if (!name.value.trim()) {
                    name.parentElement.classList.add('error');
                    isValid = false;
                } else {
                    name.parentElement.classList.add('success');
                }

                if (!email.value.trim() || !isValidEmail(email.value)) {
                    email.parentElement.classList.add('error');
                    isValid = false;
                } else {
                    email.parentElement.classList.add('success');
                }

                if (!subject.value) {
                    subject.parentElement.classList.add('error');
                    isValid = false;
                } else {
                    subject.parentElement.classList.add('success');
                }

                if (!message.value.trim()) {
                    message.parentElement.classList.add('error');
                    isValid = false;
                } else {
                    message.parentElement.classList.add('success');
                }

                if (isValid) {
                    // Here you would typically send the form data to the server
                    // For demonstration, we'll just show the success message
                    form.reset();
                    successMessage.style.display = 'block';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 5000);
                }
            });

            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            // Add animation on scroll
            const animateOnScroll = function() {
                const elements = document.querySelectorAll('.contact-info, .contact-form, .map-container, .opening-hours');
                
                elements.forEach(element => {
                    const elementPosition = element.getBoundingClientRect().top;
                    const screenPosition = window.innerHeight / 1.3;

                    if (elementPosition < screenPosition) {
                        element.style.animationPlayState = 'running';
                    }
                });
            };

            // Run once on load
            animateOnScroll();

            // Run on scroll
            window.addEventListener('scroll', animateOnScroll);
        });
    </script>
</body>
</html>