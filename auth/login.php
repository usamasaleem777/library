<?php
session_start();
include("../config/db.php");

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $pass = trim($_POST['password']);

    if (empty($email) || empty($pass)) {
        $error_message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // First check in members table
        $stmt = mysqli_prepare($conn, "SELECT id, email, password FROM members WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['member_id'] = $user['id'];
            $_SESSION['member_email'] = $user['email'];
            session_regenerate_id(true);
            header("Location: ../member/dashboard.php");
            exit();
        } else {
            // If not found in members, check admins
            $stmt = mysqli_prepare($conn, "SELECT id, email, password FROM admins WHERE email = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $admin = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($admin && password_verify($pass, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                session_regenerate_id(true);
                header("Location: ../admin/dashboard.php");
                exit();
            } else {
                $error_message = "Invalid email or password. Please try again.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <style>
        /* Login Container */
.login-container {
    max-width: 450px;
    width: 90%;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Login Header */
.login-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.login-header h2 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 1.8rem;
}

.login-header p {
    color: #7f8c8d;
    font-size: 0.95rem;
}

.login-header i {
    margin-right: 10px;
    color: #3498db;
}

/* Form Groups */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    font-weight: 500;
}

.input-wrapper {
    position: relative;
}

.form-control {
    width: 100%;
    padding: 12px 40px 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    outline: none;
}

.input-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #7f8c8d;
    cursor: pointer;
}

/* Button Styles */
.btn-login {
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 0.5rem;
}

.btn-login:hover {
    background-color: #2980b9;
}

.btn-login:disabled {
    background-color: #95a5a6;
    cursor: not-allowed;
}

.btn-login i {
    margin-right: 8px;
}

/* Footer Links */
.login-footer {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.9rem;
    color: #7f8c8d;
}

.login-footer a {
    color: #3498db;
    text-decoration: none;
    transition: color 0.3s ease;
}

.login-footer a:hover {
    color: #2980b9;
    text-decoration: underline;
}

/* Alert Messages */
.alert {
    padding: 12px 15px;
    margin-bottom: 1.5rem;
    border-radius: 5px;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
}

.alert-error {
    background-color: #fdecea;
    color: #d32f2f;
    border-left: 4px solid #d32f2f;
}

.alert-success {
    background-color: #e8f5e9;
    color: #388e3c;
    border-left: 4px solid #388e3c;
}

.alert i {
    margin-right: 10px;
    font-size: 1.2rem;
}

/* Loading Spinner */
.spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
    vertical-align: middle;
    margin-right: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive Adjustments */
@media (max-width: 576px) {
    .login-container {
        padding: 1.5rem;
    }
    
    .login-header h2 {
        font-size: 1.5rem;
    }
    
    .form-control {
        padding: 10px 35px 10px 12px;
    }
    
    .btn-login {
        padding: 10px;
    }
}

@media (max-width: 400px) {
    .login-container {
        width: 95%;
        padding: 1.2rem;
    }
    
    .login-header h2 {
        font-size: 1.3rem;
    }
    
    .login-footer {
        font-size: 0.85rem;
    }
}
    </style>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="fas fa-user-circle"></i> Member Login</h2>
            <p>Welcome back! Please sign in to your account</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="Enter your email address"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        required
                    >
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Enter your password"
                        required
                    >
                    <i class="fas fa-eye input-icon" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="login-footer">
            <a href="../auth/forgot-password.php"><i class="fas fa-key"></i> Forgot Password?</a>
            <br><br>
            <span>Don't have an account? <a href="../auth/register.php">Sign up here</a></span>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const toggleIcon = this;
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            
            // Basic client-side validation
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields.');
                return;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
            
            // Show loading state
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<div class="spinner"></div> Signing In...';
        });

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }, 5000);
    </script>
</body>
</html>
