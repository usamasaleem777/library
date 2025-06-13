<?php
session_start();
include(__DIR__ . '/config/db.php');

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    header('Location: login.php');
    exit();
}

$member_id = $_SESSION['member_id'];
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update Profile Information
    if (isset($_POST['update_profile'])) {
        $email = trim($_POST['email']);

        // Validate inputs
        if (empty($email)) {
            $error = "All required fields must be filled";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format";
        } else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM members WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $member_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Email already exists";
            } else {
                // Update profile
                $stmt = $conn->prepare("UPDATE members SET  email=?  WHERE id=?");
                $stmt->bind_param("ssssssssi", 
                    $email,
                    $member_id
                );
                
                if ($stmt->execute()) {
                    $message = "Profile updated successfully!";
                } else {
                    $error = "Failed to update profile";
                }
            }
            $stmt->close();
        }
    }

    // Change Password
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "All password fields are required";
        } else {
            $stmt = $conn->prepare("SELECT password FROM members WHERE id = ?");
            $stmt->bind_param("i", $member_id);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            $stmt->close();

            if (!password_verify($current_password, $hashed_password)) {
                $error = "Current password is incorrect";
            } elseif (strlen($new_password) < 8) {
                $error = "New password must be at least 8 characters long";
            } elseif ($new_password !== $confirm_password) {
                $error = "New passwords do not match";
            } else {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE members SET password=?, updated_at=NOW() WHERE id=?");
                $stmt->bind_param("si", $new_hashed_password, $member_id);
                
                if ($stmt->execute()) {
                    $message = "Password changed successfully!";
                } else {
                    $error = "Failed to change password";
                }
                $stmt->close();
            }
        }
    }

    // Upload Profile Image
    if (isset($_POST['upload_image']) && isset($_FILES['profile_image'])) {
        $upload_dir = 'uploads/profiles/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Create upload directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $error = "Failed to create upload directory";
            }
        }

        if (empty($error)) {
            $file = $_FILES['profile_image'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                // Validate file
                $file_info = getimagesize($file['tmp_name']);
                if (!$file_info || !in_array($file_info['mime'], $allowed_types)) {
                    $error = "Only JPEG, PNG, and GIF files are allowed";
                } elseif ($file['size'] > $max_size) {
                    $error = "File size must be less than 5MB";
                } else {
                    // Generate unique filename
                    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_filename = $member_id . '_' . bin2hex(random_bytes(8)) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        // Delete old image if exists
                        $stmt = $conn->prepare("SELECT profile_image FROM members WHERE id = ?");
                        $stmt->bind_param("i", $member_id);
                        $stmt->execute();
                        $stmt->bind_result($old_image);
                        $stmt->fetch();
                        $stmt->close();

                        if ($old_image && file_exists($old_image)) {
                            @unlink($old_image);
                        }

                        // Update database
                        $stmt = $conn->prepare("UPDATE members SET profile_image=?, updated_at=NOW() WHERE id=?");
                        $stmt->bind_param("si", $upload_path, $member_id);
                        
                        if ($stmt->execute()) {
                            $message = "Profile image updated successfully!";
                        } else {
                            $error = "Failed to update profile image in database";
                            @unlink($upload_path); // Clean up uploaded file
                        }
                        $stmt->close();
                    } else {
                        $error = "Failed to upload file";
                    }
                }
            } else {
                $error = "Upload error: " . $this->getUploadError($file['error']);
            }
        }
    }

    // Delete Account
    if (isset($_POST['delete_account'])) {
        $confirm_delete = trim($_POST['confirm_delete']);
        $password_confirm = $_POST['password_confirm'];

        if (empty($confirm_delete) || empty($password_confirm)) {
            $error = "All fields are required for account deletion";
        } elseif (strtoupper($confirm_delete) !== 'DELETE') {
            $error = "Please type 'DELETE' to confirm";
        } else {
            $stmt = $conn->prepare("SELECT password, profile_image FROM members WHERE id = ?");
            $stmt->bind_param("i", $member_id);
            $stmt->execute();
            $stmt->bind_result($db_password, $profile_image);
            $stmt->fetch();
            $stmt->close();

            if (password_verify($password_confirm, $db_password)) {
                // Delete profile image if exists
                if ($profile_image && file_exists($profile_image)) {
                    @unlink($profile_image);
                }

                // Delete account
                $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
                $stmt->bind_param("i", $member_id);
                
                if ($stmt->execute()) {
                    session_destroy();
                    header('Location: index.php?message=account_deleted');
                    exit();
                } else {
                    $error = "Failed to delete account";
                }
                $stmt->close();
            } else {
                $error = "Password confirmation failed";
            }
        }
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Helper function for upload errors
function getUploadError($error_code) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File is too large',
        UPLOAD_ERR_FORM_SIZE => 'File is too large',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    return $errors[$error_code] ?? 'Unknown upload error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .account-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #667eea;
            color: white;
            border-radius: 8px;
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-radius: 12px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .btn-primary:hover {
            background-color: #5a6fd1;
            border-color: #5a6fd1;
        }
        
        .activity-item {
            padding: 1rem;
            border-left: 4px solid #667eea;
            margin-bottom: 1rem;
            background-color: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }
        
        .stats-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .danger-zone {
            border: 2px solid #dc3545;
            border-radius: 8px;
            background-color: #fff5f5;
        }
        
        .password-toggle {
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Account Header -->
<div class="account-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <?php
                    $initial = isset($user['name']) ? strtoupper($user['name'][0]) : 'U';
                    $profileImage = !empty($user['profile_image']) 
                        ? htmlspecialchars($user['profile_image']) 
                        : "https://via.placeholder.com/150x150/667eea/ffffff?text=$initial";
                ?>
                <img src="<?php echo $profileImage; ?>" alt="Profile" class="profile-image rounded-circle">
            </div>
        </div>
    </div>
</div>


    <div class="container my-5">
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="accountTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                    <i class="bi bi-person me-2"></i>Profile
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                    <i class="bi bi-shield-lock me-2"></i>Security
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="image-tab" data-bs-toggle="tab" data-bs-target="#image" type="button" role="tab">
                    <i class="bi bi-image me-2"></i>Profile Image
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">
                    <i class="bi bi-clock-history me-2"></i>Activity
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">
                    <i class="bi bi-gear me-2"></i>Settings
                </button>
            </li>
        </ul>

        <div class="tab-content" id="accountTabsContent">
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">

                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Account Statistics</h6>
                            </div>
                            <div class="card-body">
                                <div class="stats-card">
                                    <div class="stats-number"><?php echo rand(15, 150); ?></div>
                                    <small class="text-muted">Days Active</small>
                                </div>
                                <hr>
                                <div class="stats-card">
                                    <div class="stats-number"><?php echo rand(5, 50); ?></div>
                                    <small class="text-muted">Profile Updates</small>
                                </div>
                                <hr>
                                <div class="stats-card">
                                    <div class="stats-number"><?php echo rand(1, 20); ?></div>
                                    <small class="text-muted">Login Sessions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-key me-2"></i>Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            <span class="input-group-text password-toggle" data-target="current_password">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                                   minlength="8" required>
                                            <span class="input-group-text password-toggle" data-target="new_password">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                        <div class="form-text">Password must be at least 8 characters long</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            <span class="input-group-text password-toggle" data-target="confirm_password">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" name="change_password" class="btn btn-primary">
                                        <i class="bi bi-shield-check me-2"></i>Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Security Tips</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use a strong password</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Enable two-factor authentication</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Regular password updates</li>
                                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Avoid public Wi-Fi for sensitive actions</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Image Tab -->
          <div class="tab-pane fade" id="image" role="tabpanel">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-camera me-2"></i>Profile Image</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <?php
                                $profileImage = !empty($user['profile_image']) 
                                    ? htmlspecialchars($user['profile_image']) 
                                    : (
                                        !empty($user['first_name']) 
                                        ? 'https://via.placeholder.com/200x200/667eea/ffffff?text=' . strtoupper(substr($user['first_name'], 0, 1)) 
                                        : 'https://via.placeholder.com/200x200/667eea/ffffff?text=U'
                                    );
                            ?>
                            <img src="<?php echo $profileImage; ?>" 
                                 alt="Current Profile" 
                                 class="img-fluid rounded-circle" 
                                 style="width: 200px; height: 200px; object-fit: cover;">
                        </div>
                        <div class="col-md-8">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Choose New Image</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image" 
                                           accept="image/jpeg,image/png,image/gif" required>
                                    <div class="form-text">Supported formats: JPEG, PNG, GIF. Maximum size: 5MB</div>
                                </div>
                                
                                <button type="submit" name="upload_image" class="btn btn-primary">
                                    <i class="bi bi-upload me-2"></i>Upload Image
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Activity Tab -->
            <div class="tab-pane fade" id="activity" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Profile Updated</strong>
                                    <p class="mb-0 text-muted">Updated personal information</p>
                                </div>
<small class="text-muted">
    <?php echo isset($user['updated_at']) ? date('M j, g:i A', strtotime($user['updated_at'])) : 'Not updated'; ?>
</small>

                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Account Created</strong>
                                    <p class="mb-0 text-muted">Welcome to our platform!</p>
                                </div>
<small class="text-muted">
    <?php echo isset($user['created_at']) ? date('M j, g:i A', strtotime($user['created_at'])) : 'Not available'; ?>
</small>
                            </div>
                        </div>
                        
                        <!-- Add more activity items as needed -->
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Login Session</strong>
                                    <p class="mb-0 text-muted">Logged in from new device</p>
                                </div>
                                <small class="text-muted"><?php echo date('M j, g:i A', strtotime('-2 hours')); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="tab-pane fade" id="settings" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Notification Preferences</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                                        <label class="form-check-label" for="email_notifications">
                                            Email Notifications
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="sms_notifications">
                                        <label class="form-check-label" for="sms_notifications">
                                            SMS Notifications
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="marketing_emails">
                                        <label class="form-check-label" for="marketing_emails">
                                            Marketing Emails
                                        </label>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Save Preferences
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Danger Zone -->
                        <div class="card danger-zone">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-danger">Delete Account</h6>
                                <p class="text-muted mb-3">Once you delete your account, there is no going back. Please be certain.</p>
                                
                                <form method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.');">
                                    <div class="mb-3">
                                        <label for="confirm_delete" class="form-label">Type "DELETE" to confirm:</label>
                                        <input type="text" class="form-control" id="confirm_delete" name="confirm_delete" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password_confirm" class="form-label">Enter your password to confirm:</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                            <span class="input-group-text password-toggle" data-target="password_confirm">
                                                <i class="bi bi-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" name="delete_account" class="btn btn-danger">
                                        <i class="bi bi-trash me-2"></i>Delete My Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // File size validation
        document.getElementById('profile_image').addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                this.value = '';
            }
        });
        
        // Password toggle functionality
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        });
    </script>
</body>
</html>