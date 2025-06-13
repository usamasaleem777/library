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

    <style>
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
    </style>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" onclick="scrollToTop()" title="Back to Top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script>
        // Back to top functionality
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').
        forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Update URL without jumping
                    if (history.pushState) {
                        history.pushState(null, null, targetId);
                    } else {
                        window.location.hash = targetId;
                    }
                }
            });
        });

        // Loading state for buttons
        document.querySelectorAll('button[type="submit"]').forEach(button => {
            button.addEventListener('click', function() {
                if (this.form && this.form.checkValidity()) {
                    this.innerHTML = '<span class="loading-spinner"></span> Processing...';
                    this.disabled = true;
                }
            });
        });

        // Print functionality
        document.querySelectorAll('.print-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                window.print();
            });
        });

        // Current year update
        document.addEventListener('DOMContentLoaded', function() {
            const yearElement = document.querySelector('.current-year');
            if (yearElement) {
                yearElement.textContent = new Date().getFullYear();
            }
        });
    </script>
</body>
</html>