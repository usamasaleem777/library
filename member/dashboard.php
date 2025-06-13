<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

if (!isset($_SESSION['member_id'])) {
    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: ../auth/login.php");
    exit();
}

require_once("../config/db.php");

$member_id = (int)$_SESSION['member_id'];

// Updated query to include member_since date
$stmt = $conn->prepare("SELECT name, email, profile_image FROM members WHERE id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Member not found');
}

$member = $result->fetch_assoc();

// Store session values
$_SESSION['member_name'] = $member['name'];
$_SESSION['member_email'] = $member['email'];
$_SESSION['member_avatar'] = $member['profile_image'];

// Sanitize output
$member_name = htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8');
$member_email = htmlspecialchars($member['email'], ENT_QUOTES, 'UTF-8');


// Handle avatar image - corrected path to match your requirement
$avatar_base_path = '../uploads/profiles/';
$default_avatar = 'default-avatar.png';

// Sanitize the profile image filename
$member_avatar = !empty($member['profile_image']) ? 
    htmlspecialchars(basename($member['profile_image']), ENT_QUOTES, 'UTF-8') : 
    $default_avatar;

// Security check: only allow certain file extensions
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$file_extension = strtolower(pathinfo($member_avatar, PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions)) {
    $member_avatar = $default_avatar;
}

// Verify the image exists and is readable
$full_avatar_path = $avatar_base_path . $member_avatar;
if (!file_exists($full_avatar_path) || !is_readable($full_avatar_path)) {
    $member_avatar = $default_avatar;
    $full_avatar_path = $avatar_base_path . $default_avatar;
}

// Final check for default avatar
if (!file_exists($full_avatar_path)) {
    // Create a fallback if default avatar doesn't exist
    $member_avatar = 'data:image/svg+xml;base64,' . base64_encode(
        '<svg width="80" height="80" xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="40" r="40" fill="#e9ecef"/>
            <text x="40" y="50" text-anchor="middle" font-family="Arial" font-size="20" fill="#6c757d">
                ' . strtoupper(substr($member_name, 0, 1)) . '
            </text>
        </svg>'
    );
    $avatar_base_path = ''; // Reset path for data URL
}

// Rest of your session checks
if ($_SESSION['force_password_reset'] ?? false) {
    header("Location: ../auth/change_password.php?force=1");
    exit();
}

if ($_SESSION['account_locked'] ?? false) {
    header("Location: ../auth/account_locked.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Dummy values
$books_borrowed = 12;
$books_reserved = 3;
$reading_goal = 45;
$reading_goal_progress = min(round(($books_borrowed / $reading_goal) * 100), 100);

$recommendations = [
    ['title' => 'The Silent Patient', 'author' => 'Alex Michaelides', 'cover' => 'silent-patient.jpg'],
    ['title' => 'Educated', 'author' => 'Tara Westover', 'cover' => 'educated.jpg'],
    ['title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'cover' => 'sapiens.jpg']
];

include("../includes/header.php");
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Digital Library Member Portal - Access your account, browse books, and manage your reading experience">
    <meta name="robots" content="noindex, nofollow"> <!-- Remove in production -->
    
    <title>Digital Library - Member Portal</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" as="style">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <!-- Chart.js for data visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a6fd1;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --success-color: #4facfe;
            --warning-color: #f5576c;
            --dark-color: #2c3e50;
            --light-color: #f8f9fa;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --border-radius: 16px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        [data-theme="dark"] {
            --primary-color: #7c93fd;
            --primary-dark: #6a7ce4;
            --secondary-color: #8a4dbf;
            --accent-color: #e580ff;
            --success-color: #5fb8ff;
            --warning-color: #ff6b81;
            --dark-color: #1a1a2e;
            --light-color: #16213e;
            --text-primary: #f8f9fa;
            --text-secondary: #adb5bd;
            --bg-primary: #0f0f23;
            --bg-secondary: #1a1a2e;
            --glass-bg: rgba(26, 26, 46, 0.85);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: var(--transition);
            position: relative;
            overflow-x: hidden;
        }

        /* Theme toggle button */
        .theme-toggle {
            position: fixed;
            bottom: 200px;
            right: 1rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: none;
            transition: var(--transition);
        }

        .theme-toggle:hover {
            transform: scale(1.1) rotate(30deg);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);
        }

        /* Dashboard Header */
        .dashboard-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 2.5rem;
            margin-bottom: 3rem;
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .welcome-text {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .member-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .member-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .member-details {
            display: flex;
            flex-direction: column;
        }

        .member-id {
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .member-email {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 0.2rem;
        }

        .member-since {
            font-size: 0.85rem;
            color: var(--text-secondary);
            opacity: 0.8;
        }

        .current-time {
            position: absolute;
            top: 1.5rem;
            right: 2rem;
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
            background: var(--glass-bg);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
        }

        /* Dashboard Grid Layout */
        .dashboard-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 1200px) {
            .dashboard-layout {
                grid-template-columns: 2fr 1fr;
            }
        }

        /* Main Content Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-decoration: none;
            color: var(--text-primary);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: var(--box-shadow);
            display: flex;
            flex-direction: column;
            min-height: 220px;
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            transition: var(--transition);
        }

        .dashboard-card:hover .card-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 25px rgba(102, 126, 234, 0.4);
        }

        .card-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            line-height: 1.3;
        }

        .card-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .card-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--warning-color);
            color: white;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
        }

        /* Special card variations */
        .card-books {
            border-top: 4px solid var(--primary-color);
        }

        .card-request {
            border-top: 4px solid var(--accent-color);
        }

        .card-request .card-icon {
            background: linear-gradient(135deg, var(--accent-color), var(--warning-color));
        }

        .card-logout {
            border-top: 4px solid var(--warning-color);
        }

        .card-logout .card-icon {
            background: linear-gradient(135deg, var(--warning-color), #d23369);
        }

        .card-success {
            border-top: 4px solid var(--success-color);
        }

        .card-success .card-icon {
            background: linear-gradient(135deg, var(--success-color), #00f2fe);
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            transition: var(--transition);
            box-shadow: var(--box-shadow);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Progress Bar */
        .progress-container {
            width: 100%;
            height: 8px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 4px;
            transition: width 1s ease-in-out;
        }

        /* Sidebar */
        .dashboard-sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--box-shadow);
            height: fit-content;
            transition: var(--transition);
        }

        .sidebar-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Recommendations */
        .recommendations-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .recommendation-item {
            display: flex;
            gap: 1rem;
            align-items: center;
            padding: 0.8rem;
            border-radius: 12px;
            transition: var(--transition);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
        }

        .recommendation-item:hover {
            background: var(--bg-secondary);
            transform: translateX(5px);
        }

        .recommendation-cover {
            width: 50px;
            height: 70px;
            border-radius: 6px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .recommendation-info {
            flex: 1;
        }

        .recommendation-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
            color: var(--text-primary);
        }

        .recommendation-author {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* Chart Container */
        .chart-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--box-shadow);
        }

        .chart-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        /* Notifications */
        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 18px;
            height: 18px;
            background: var(--warning-color);
            color: white;
            border-radius: 50%;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .dashboard-sidebar {
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem;
            }

            .welcome-text {
                font-size: 2rem;
            }

            .current-time {
                position: static;
                margin-top: 1rem;
                margin-bottom: 1rem;
                display: inline-block;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .welcome-text {
                font-size: 1.8rem;
            }

            .dashboard-card {
                padding: 1.5rem;
                min-height: auto;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .member-info {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Custom Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }

        .pulse {
            animation: pulse 2s ease-in-out infinite;
        }

        /* Ripple Effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1100;
            max-width: 350px;
        }

        .custom-toast {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-left: 4px solid var(--primary-color);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        /* Tooltips */
        .custom-tooltip {
            --bs-tooltip-bg: var(--primary-color);
            --bs-tooltip-color: white;
        }

        /* Dark mode toggle animation */
        .dark-mode-toggle {
            transition: all 0.5s ease;
        }

        .dark-mode-toggle.dark {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- Toast Notifications -->
<div class="toast-container" id="toastContainer">
    <!-- Toasts will be added here dynamically -->
</div>

<div class="container-fluid px-4 py-5">
    <!-- Dashboard Header -->
    <div class="dashboard-header" data-aos="fade-down" data-aos-duration="800">
        <!-- Current Time -->
        <div class="current-time" id="currentTime"></div>

        <!-- Welcome Message -->
        <div class="welcome-text">
            <i class="bi bi-stars me-2"></i>Welcome back, <?php echo htmlspecialchars($member_name); ?>!
        </div>

        <!-- Member Info -->
     <div class="member-info d-flex align-items-center mt-3">
            <img src="<?php echo $avatar_base_path . $member_avatar; ?>" 
                 alt="<?php echo htmlspecialchars($member_name . "'s Avatar"); ?>" 
                 class="member-avatar rounded-circle me-3" 
                 style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #dee2e6;"
                 onerror="this.src='<?php echo $avatar_base_path . $default_avatar; ?>'; this.onerror=null;">

            <div class="member-details">
                <div class="member-id fw-bold">Member ID: <?php echo $member_id; ?></div>
                <div class="member-email text-muted"><?php echo $member_email; ?></div>
                <div class="member-since text-muted">
                    Member since:11-6-2025
                </div>
            </div>
        </div>


    <!-- Main Dashboard Layout -->
    <div class="dashboard-layout">
        <div class="dashboard-main">
            <!-- Statistics Cards -->
            <div class="stats-grid" data-aos="fade-up" data-aos-duration="600" data-aos-delay="200">
                <div class="stat-card">
                    <div class="stat-number">156</div>
                    <div class="stat-label">Available Books</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $books_borrowed; ?></div>
                    <div class="stat-label">Books Borrowed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $books_reserved; ?></div>
                    <div class="stat-label">Pending Requests</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $reading_goal; ?></div>
                    <div class="stat-label">Reading Goal</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?php echo $reading_goal_progress; ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Reading Activity Chart -->
            <div class="chart-container" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
                <h3 class="chart-title"><i class="bi bi-bar-chart-line me-2"></i>Your Reading Activity</h3>
                <canvas id="readingChart" height="250"></canvas>
            </div>

            <!-- Main Dashboard Cards -->
            <div class="dashboard-grid">
                <!-- View Books Card -->
                <a href="view_books.php" class="dashboard-card card-books" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
                    <div class="card-icon">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <h3 class="card-title">Explore Library</h3>
                    <p class="card-description">
                        Browse our extensive collection of books across all genres. Search, filter, and discover your next great read.
                    </p>
                </a>

                <!-- Request Book Card -->
                <a href="request_book.php" class="dashboard-card card-request" data-aos="fade-up" data-aos-duration="600" data-aos-delay="450">
                    <div class="card-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                    <h3 class="card-title">Request Books</h3>
                    <p class="card-description">
                        Can't find what you're looking for? Submit a book request and we'll do our best to add it.
                    </p>
                </a>

                <!-- My Account Card -->
                <a href="/library/account.php" class="dashboard-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="500">

                    <div class="card-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h3 class="card-title">My Account</h3>
                    <p class="card-description">
                        Manage your profile, view borrowing history, and update your preferences.
                    </p>
                </a>

                <!-- My Reservations Card -->
                <a href="my_reservations.php" class="dashboard-card card-success" data-aos="fade-up" data-aos-duration="600" data-aos-delay="550">
                    <div class="card-icon">
                        <i class="bi bi-bookmark-star-fill"></i>
                    </div>
                    <h3 class="card-title">My Reservations</h3>
                    <p class="card-description">
                        Check your current reservations, due dates, and manage borrowed books.
                    </p>
                    <?php if ($books_reserved > 0): ?>
                        <span class="card-badge"><?php echo $books_reserved; ?> active</span>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <!-- Dashboard Sidebar -->
        <div class="dashboard-sidebar" data-aos="fade-left" data-aos-duration="600" data-aos-delay="200">
            <!-- Recommendations -->
            <h3 class="sidebar-title"><i class="bi bi-lightbulb"></i> Recommended For You</h3>
            <div class="recommendations-list">
                <?php foreach ($recommendations as $book): ?>
                    <a href="book_details.php?id=<?php echo urlencode($book['title']); ?>" class="recommendation-item">
                        <img src="../assets/covers/<?php echo htmlspecialchars($book['cover']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="recommendation-cover">
                        <div class="recommendation-info">
                            <div class="recommendation-title"><?php echo htmlspecialchars($book['title']); ?></div>
                            <div class="recommendation-author"><?php echo htmlspecialchars($book['author']); ?></div>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Quick Actions -->
            <h3 class="sidebar-title mt-4"><i class="bi bi-lightning"></i> Quick Actions</h3>
            <div class="d-grid gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="bi bi-search me-2"></i>Quick Search
                </button>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                    <i class="bi bi-chat-square-text me-2"></i>Send Feedback
                </button>
                <a href="../auth/logout.php" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                </a>
            </div>

            <!-- System Status -->
            <h3 class="sidebar-title mt-4"><i class="bi bi-server"></i> System Status</h3>
            <div class="alert alert-success small p-2 mb-2">
                <i class="bi bi-check-circle-fill me-2"></i>All systems operational
            </div>
            <div class="alert alert-warning small p-2">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Scheduled maintenance: June 15, 2-4 AM
            </div>
        </div>
    </div>
</div>

<!-- Theme Toggle Button -->
<button class="theme-toggle" id="themeToggle" title="Toggle dark mode">
    <i class="bi bi-sun-fill" id="themeIcon"></i>
</button>

<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-search me-2"></i>Search Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search by title, author, or ISBN" id="searchInput">
                    <button class="btn btn-primary" type="button" id="searchButton">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="search-suggestions" id="searchSuggestions">
                    <small class="text-muted">Try searching for: "Science Fiction", "History", or "Programming"</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-chat-square-text me-2"></i>Send Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="feedbackForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="feedbackType" class="form-label">Feedback Type</label>
                        <select class="form-select" id="feedbackType" name="type" required>
                            <option value="">Select type</option>
                            <option value="suggestion">Suggestion</option>
                            <option value="bug">Bug Report</option>
                            <option value="compliment">Compliment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="feedbackMessage" class="form-label">Your Feedback</label>
                        <textarea class="form-control" id="feedbackMessage" rows="4" name="message" required></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send-fill me-2"></i>Send Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });

    // Hide loading overlay
    setTimeout(() => {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.opacity = '0';
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 500);
        }
    }, 1000);

    // Throttle time updates to reduce CPU usage
    let timeUpdateInterval = null;
    
    function updateTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };
        
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = now.toLocaleDateString('en-US', options);
        }
        
        // Update page title less frequently to reduce overhead
        if (now.getSeconds() % 30 === 0) {
            document.title = `Library Dashboard - ${now.toLocaleTimeString()}`;
        }
    }

    // Start time updates
    updateTime();
    timeUpdateInterval = setInterval(updateTime, 1000);
    
    // Pause time updates when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (timeUpdateInterval) {
                clearInterval(timeUpdateInterval);
                timeUpdateInterval = null;
            }
        } else {
            updateTime();
            if (!timeUpdateInterval) {
                timeUpdateInterval = setInterval(updateTime, 1000);
            }
        }
    });

    // Theme toggle functionality (using in-memory storage)
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const html = document.documentElement;
    
    // In-memory theme storage (since localStorage is not available)
    let currentTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    
    // Apply the current theme
    function applyTheme(theme) {
        currentTheme = theme;
        if (theme === 'dark') {
            html.setAttribute('data-theme', 'dark');
            if (themeIcon) {
                themeIcon.classList.remove('bi-sun-fill');
                themeIcon.classList.add('bi-moon-fill');
            }
        } else {
            html.setAttribute('data-theme', 'light');
            if (themeIcon) {
                themeIcon.classList.remove('bi-moon-fill');
                themeIcon.classList.add('bi-sun-fill');
            }
        }
    }
    
    applyTheme(currentTheme);

    // Toggle theme on button click
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(newTheme);
            showToast(`Switched to ${newTheme} theme`, 'info');
        });
    }

    // Real-time reading activity chart with dynamic data
    let readingChart = null;
    const readingData = {
        daily: {
            labels: [],
            data: []
        },
        monthly: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            data: [3, 5, 2, 4, 6, 8, 5, 7, 9, 6, 4, 5]
        },
        weekly: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            data: [2, 1, 3, 0, 2, 4, 1]
        }
    };

    // Generate daily data for the past 30 days
    function generateDailyData() {
        const today = new Date();
        const labels = [];
        const data = [];
        
        for (let i = 29; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);
            labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
            // Simulate reading activity with some randomness but realistic patterns
            const baseActivity = Math.sin(i * 0.2) * 2 + 2; // Creates wave pattern
            data.push(Math.max(0, Math.round(baseActivity + (Math.random() - 0.5) * 2)));
        }
        
        readingData.daily.labels = labels;
        readingData.daily.data = data;
    }

    generateDailyData();

    function initializeChart(view = 'monthly') {
        const readingCtx = document.getElementById('readingChart');
        if (!readingCtx) return;

        // Set explicit canvas size to prevent overflow
        const container = readingCtx.parentElement;
        if (container) {
            const containerWidth = Math.min(container.clientWidth || 400, 800);
            const containerHeight = Math.min(container.clientHeight || 300, 400);
            
            readingCtx.style.width = containerWidth + 'px';
            readingCtx.style.height = containerHeight + 'px';
            readingCtx.width = containerWidth;
            readingCtx.height = containerHeight;
        }

        // Destroy existing chart
        if (readingChart) {
            readingChart.destroy();
            readingChart = null;
        }

        const ctx = readingCtx.getContext('2d');
        const currentData = readingData[view];
        
        readingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: currentData.labels,
                datasets: [{
                    label: view === 'daily' ? 'Pages Read' : view === 'weekly' ? 'Hours Read' : 'Books Read',
                    data: [...currentData.data],
                    backgroundColor: currentTheme === 'dark' ? '#818cf8' : '#667eea',
                    borderColor: currentTheme === 'dark' ? '#6366f1' : '#5a6fd1',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                devicePixelRatio: 1,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 400,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: currentTheme === 'dark' ? '#e5e7eb' : '#374151',
                            font: {
                                size: 11,
                                weight: 'normal'
                            },
                            boxWidth: 12,
                            padding: 10
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: currentTheme === 'dark' ? 'rgba(17, 24, 39, 0.95)' : 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        titleFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 11
                        },
                        padding: 8,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            afterLabel: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                if (total > 0) {
                                    const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                                    return `${percentage}% of total`;
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.max(...currentData.data) * 1.2,
                        grid: {
                            color: currentTheme === 'dark' ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)',
                            lineWidth: 1
                        },
                        ticks: {
                            stepSize: Math.max(1, Math.ceil(Math.max(...currentData.data) / 5)),
                            color: currentTheme === 'dark' ? '#9ca3af' : '#6b7280',
                            font: {
                                size: 10
                            },
                            maxTicksLimit: 6
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: currentTheme === 'dark' ? '#9ca3af' : '#6b7280',
                            maxRotation: view === 'daily' ? 45 : 0,
                            font: {
                                size: 10
                            },
                            maxTicksLimit: view === 'daily' ? 15 : 12
                        }
                    }
                }
            }
        });
    }

    // Initialize chart
    initializeChart();

    // Chart view switcher
    const chartViewButtons = document.querySelectorAll('[data-chart-view]');
    chartViewButtons.forEach(button => {
        button.addEventListener('click', () => {
            const view = button.getAttribute('data-chart-view');
            
            // Update active button
            chartViewButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Update chart
            initializeChart(view);
            showToast(`Switched to ${view} view`, 'info');
        });
    });

    // Optimize real-time data updates to prevent performance issues
    let updateInterval = null;
    
    function simulateRealTimeUpdate() {
        // Throttle updates to prevent system hanging
        if (document.hidden) return; // Don't update when tab is not visible
        
        try {
            // Add new data point to daily chart
            const now = new Date();
            const newLabel = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            const newValue = Math.floor(Math.random() * 5) + 1;
            
            // Remove oldest data point and add new one
            readingData.daily.labels.shift();
            readingData.daily.labels.push(newLabel);
            readingData.daily.data.shift();
            readingData.daily.data.push(newValue);
            
            // Update chart if it's currently showing daily view and chart exists
            const activeButton = document.querySelector('[data-chart-view].active');
            if (activeButton && activeButton.getAttribute('data-chart-view') === 'daily' && readingChart) {
                // Use minimal update to prevent performance issues
                readingChart.data.labels = [...readingData.daily.labels];
                readingChart.data.datasets[0].data = [...readingData.daily.data];
                readingChart.update('none'); // No animation for better performance
            }
            
            showToast(`New reading activity: ${newValue} pages`, 'success', 3000);
        } catch (error) {
            console.warn('Chart update failed:', error);
        }
    }

    // Reduce update frequency and add visibility check
    updateInterval = setInterval(simulateRealTimeUpdate, 60000); // Reduced to 1 minute
    
    // Pause updates when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (updateInterval) {
                clearInterval(updateInterval);
                updateInterval = null;
            }
        } else {
            if (!updateInterval) {
                updateInterval = setInterval(simulateRealTimeUpdate, 60000);
            }
        }
    });

    // Enhanced ripple effect for buttons
    document.querySelectorAll('.btn, .dashboard-card, [data-chart-view]').forEach(element => {
        element.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
            ripple.style.pointerEvents = 'none';
            
            // Ensure parent has relative positioning
            if (getComputedStyle(this).position === 'static') {
                this.style.position = 'relative';
            }
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Enhanced feedback form submission
    const feedbackForm = document.getElementById('feedbackForm');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Validate form
            const name = formData.get('name')?.trim();
            const email = formData.get('email')?.trim();
            const message = formData.get('message')?.trim();
            
            if (!name || !email || !message) {
                showToast('Please fill in all required fields', 'error');
                return;
            }
            
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showToast('Please enter a valid email address', 'error');
                return;
            }
            
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...';
            submitBtn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                showToast('Thank you! Your feedback has been submitted successfully.', 'success');
                
                // Reset form
                feedbackForm.reset();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Close modal if exists
                const modal = document.getElementById('feedbackModal');
                if (modal && bootstrap.Modal.getInstance(modal)) {
                    bootstrap.Modal.getInstance(modal).hide();
                }
            }, 1500);
        });
    }

    // Enhanced search functionality with real-time suggestions
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    
    if (searchInput) {
        let searchTimeout;
        
        // Sample search suggestions
        const searchSuggestions = [
            'JavaScript Programming', 'Python for Beginners', 'Data Science', 
            'Machine Learning', 'Web Development', 'React Tutorial',
            'Node.js Guide', 'Database Design', 'API Development'
        ];
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length > 2) {
                searchTimeout = setTimeout(() => {
                    // Simulate search suggestions
                    const matches = searchSuggestions.filter(item => 
                        item.toLowerCase().includes(query.toLowerCase())
                    );
                    
                    if (matches.length > 0) {
                        console.log('Search suggestions:', matches);
                        // In a real app, you would show these suggestions in a dropdown
                    }
                }, 300);
            }
        });
        
        function performSearch() {
            const query = searchInput.value.trim();
            if (query !== '') {
                showToast(`Searching for: "${query}"`, 'info');
                // In a real app, this would make an API call or redirect
                searchInput.value = '';
            }
        }
        
        if (searchButton) {
            searchButton.addEventListener('click', performSearch);
        }
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    // Enhanced toast notification system
    let toastCounter = 0;
    function showToast(message, type = 'info', duration = 5000) {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        const toastId = `toast-${++toastCounter}`;
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast show align-items-center border-0 bg-${type} mb-2`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.style.minWidth = '300px';
        toast.style.animation = 'slideInRight 0.3s ease-out';
        
        const iconMap = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };
        
        toast.innerHTML = `
            <div class="d-flex w-100">
                <div class="toast-body text-white">
                    <i class="bi ${iconMap[type]} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="removeToast('${toastId}')" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove toast
        setTimeout(() => {
            removeToast(toastId);
        }, duration);
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
    
    // Make removeToast globally accessible
    window.removeToast = function(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    };

    // Optimize system status updates
    let statusUpdateInterval = null;
    
    function updateSystemStatus() {
        // Only update if elements exist
        if (document.hidden) return;
        
        const statusElements = {
            online: document.getElementById('onlineStatus'),
            lastSync: document.getElementById('lastSync'),
            performance: document.getElementById('performanceStatus')
        };
        
        // Update online status
        if (statusElements.online) {
            statusElements.online.textContent = navigator.onLine ? 'Online' : 'Offline';
            statusElements.online.className = navigator.onLine ? 'text-success' : 'text-danger';
        }
        
        // Update last sync time
        if (statusElements.lastSync) {
            statusElements.lastSync.textContent = new Date().toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Simulate performance monitoring with less randomness
        if (statusElements.performance) {
            const performance = Math.random() > 0.9 ? 'Slow' : 'Good';
            statusElements.performance.textContent = performance;
            statusElements.performance.className = performance === 'Good' ? 'text-success' : 'text-warning';
        }
    }
    
    // Update system status with reduced frequency
    updateSystemStatus();
    statusUpdateInterval = setInterval(updateSystemStatus, 30000); // Every 30 seconds
    
    // Pause status updates when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (statusUpdateInterval) {
                clearInterval(statusUpdateInterval);
                statusUpdateInterval = null;
            }
        } else {
            updateSystemStatus();
            if (!statusUpdateInterval) {
                statusUpdateInterval = setInterval(updateSystemStatus, 30000);
            }
        }
    });

    // Network status monitoring
    window.addEventListener('online', () => {
        showToast('Connection restored', 'success');
        updateSystemStatus();
    });
    
    window.addEventListener('offline', () => {
        showToast('Connection lost - working offline', 'warning');
        updateSystemStatus();
    });

    // Optimize notifications - reduce frequency and add limits
    let notificationCount = 0;
    const MAX_NOTIFICATIONS = 5;
    
    // Welcome message with delay
    setTimeout(() => {
        if (notificationCount < MAX_NOTIFICATIONS) {
            showToast('Welcome back to your library dashboard!', 'info', 4000);
            notificationCount++;
        }
    }, 2000);

    // Simulate book due reminders - only once
    setTimeout(() => {
        if (Math.random() > 0.8 && notificationCount < MAX_NOTIFICATIONS) {
            showToast('📚 You have 1 book due tomorrow!', 'warning', 6000);
            notificationCount++;
        }
    }, 5000);
    
    // Simulate new book recommendations - only once
    setTimeout(() => {
        if (Math.random() > 0.7 && notificationCount < MAX_NOTIFICATIONS) {
            showToast('📖 New recommendations available!', 'info', 4000);
            notificationCount++;
        }
    }, 8000);

    // Add CSS for animations
    if (!document.getElementById('dynamicStyles')) {
        const style = document.createElement('style');
        style.id = 'dynamicStyles';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            .ripple {
                width: 20px;
                height: 20px;
            }
        `;
        document.head.appendChild(style);
    }
});
</script>
<?php include('C:/xampp/htdocs/library/includes/footer.php'); ?>
   