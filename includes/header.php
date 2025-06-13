<?php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF']);

// Check if user is logged in
$is_logged_in = isset($_SESSION['member_id']);
$user_name = $is_logged_in ? ($_SESSION['member_name'] ?? 'User') : 'Guest';
$user_role = $is_logged_in ? ($_SESSION['member_role'] ?? 'Member') : 'Visitor';

// Function to get the correct base path
function getBasePath() {
    $script_path = $_SERVER['SCRIPT_NAME'];
    $path_parts = explode('/', trim($script_path, '/'));
    
    // Remove the current file name
    array_pop($path_parts);
    
    // Count how many levels deep we are from root
    $levels = count($path_parts);
    
    // If we're in root, return empty string
    if ($levels <= 1) {
        return '';
    }
    
    // Return appropriate number of "../" to get to root
    return str_repeat('../', $levels - 1);
}

$base_path = getBasePath();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Library Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Header Styles */
        header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: white;
        }

        .logo-icon {
            background: rgba(255, 255, 255, 0.2);
            padding: 12px;
            border-radius: 50%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .logo-section:hover .logo-icon {
            transform: rotate(5deg) scale(1.05);
        }

        .logo-icon i {
            font-size: 24px;
            color: #fff;
        }

        .logo-text h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .logo-text p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
            font-weight: 300;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 25px;
            margin: 0;
            padding: 0;
        }

        .nav-links li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links li a:hover,
        .nav-links li a.active {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-left: 25px;
            border-left: 1px solid rgba(255, 255, 255, 0.3);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .user-details span {
            display: block;
            font-size: 14px;
        }

        .user-name {
            font-weight: 600;
        }

        .user-role {
            opacity: 0.8;
            font-size: 12px;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-login, .btn-register {
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-login {
            color: white;
            background: transparent;
        }

        .btn-register {
            background: white;
            color: #2c3e50;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
        }

        .mobile-nav {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
        }

        .mobile-nav.active {
            display: block;
        }

        .mobile-nav .nav-links {
            flex-direction: column;
            gap: 10px;
        }

        .mobile-nav .nav-links li a {
            padding: 12px 16px;
            border-radius: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .logo-text h1 {
                font-size: 20px;
            }

            .header-nav .nav-links {
                display: none;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .user-info {
                display: none;
            }

            .auth-buttons {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .logo-section {
                gap: 10px;
            }

            .logo-icon {
                padding: 8px;
            }

            .logo-icon i {
                font-size: 20px;
            }

            .logo-text h1 {
                font-size: 18px;
            }

            .logo-text p {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="<?php echo $base_path; ?>index.php" class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="logo-text">
                    <h1>LibraryMS</h1>
                    <p>Knowledge Management System</p>
                </div>
            </a>

            <nav class="header-nav">
                <ul class="nav-links">
                    <li><a href="<?php echo $base_path; ?>index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Home
                    </a></li>
                    <li><a href="<?php echo $base_path; ?>catalog.php" class="<?php echo ($current_page == 'catalog.php') ? 'active' : ''; ?>">
                        <i class="fas fa-book"></i> Catalog
                    </a></li>
                    <?php if ($is_logged_in): ?>
                        <li><a href="<?php echo $base_path; ?>member/dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a></li>
                        <li><a href="<?php echo $base_path; ?>member/view_books.php" class="<?php echo ($current_page == 'view_books.php') ? 'active' : ''; ?>">
                            <i class="fas fa-bookmark"></i> My Books
                        </a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base_path; ?>contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
                        <i class="fas fa-envelope"></i> Contact
                    </a></li>
                </ul>

                <?php if ($is_logged_in): ?>
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($user_role); ?></span>
                        </div>
                        <a href="<?php echo $base_path; ?>auth/logout.php" style="color: white; margin-left: 10px; font-size: 18px;" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="<?php echo $base_path; ?>auth/login.php" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="<?php echo $base_path; ?>auth/register.php" class="btn-register">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </div>
                <?php endif; ?>

                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-nav" id="mobileNav">
            <ul class="nav-links">
                <li><a href="<?php echo $base_path; ?>index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?php echo $base_path; ?>books/catalog.php"><i class="fas fa-book"></i> Catalog</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="<?php echo $base_path; ?>member/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="<?php echo $base_path; ?>member/view_books.php"><i class="fas fa-bookmark"></i> My Books</a></li>
                    <li><a href="<?php echo $base_path; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base_path; ?>auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="<?php echo $base_path; ?>auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
                <li><a href="<?php echo $base_path; ?>contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>
        </div>
    </header>

    <script>
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('active');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileNav = document.getElementById('mobileNav');
            const toggleButton = document.querySelector('.mobile-menu-toggle');
            
            if (!mobileNav.contains(event.target) && event.target !== toggleButton && !toggleButton.contains(event.target)) {
                mobileNav.classList.remove('active');
            }
        });
    </script>