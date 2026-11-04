<?php
session_start();

// // Database configuration
// $host = 'localhost';
// $dbname = 'your_database_name';
// $username = 'your_db_username';
// $password = 'your_db_password';

try {
    // $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    include("config.php");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';
$error = '';
$validToken = false;
$user = null;

// Get token from URL
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    $error = "Invalid reset link. Please request a new password reset.";
} else {
    // Verify token
    $stmt = $db->prepare("
        SELECT pr.*, u.id as user_id, u.email, u.first_name, u.last_name, u.active 
        FROM password_resets pr 
        JOIN users u ON pr.user_id = u.id 
        WHERE pr.token = ? AND pr.expires > NOW() AND pr.used = 0
    ");
    $stmt->execute([$token]);
    $resetData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resetData) {
        $error = "This reset link is invalid or has expired. Please request a new password reset.";
    } elseif ($resetData['active'] == 0) {
        $error = "This account is currently inactive. Please contact support.";
    } else {
        $validToken = true;
        $user = $resetData;
    }
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($newPassword) || empty($confirmPassword)) {
        $error = "Please fill in all fields.";
    } elseif (strlen($newPassword) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $newPassword)) {
        $error = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
    } else {
        try {
            // Start transaction
            $db->beginTransaction();
            
            // Update user password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$hashedPassword, $user['user_id']]);
            
            // Mark token as used
            $stmt = $db->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
            $stmt->execute([$token]);
            
            // Delete old unused tokens for this user
            $stmt = $db->prepare("DELETE FROM password_resets WHERE user_id = ? AND used = 0");
            $stmt->execute([$user['user_id']]);
            
            // Commit transaction
            $db->commit();
            
            $message = "Your password has been successfully reset. You can now log in with your new password.";
            $validToken = false; // Hide form after successful reset
            
        } catch (Exception $e) {
            $db->rollback();
            $error = "An error occurred while resetting your password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 28px;
            font-weight: 300;
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .reset-header h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .reset-header p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .user-info p {
            margin: 0;
            color: #333;
            font-size: 14px;
        }
        
        .user-info strong {
            color: #667eea;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .password-requirements {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
            line-height: 1.4;
        }
        
        .password-requirements ul {
            margin: 5px 0 0 20px;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .links {
            text-align: center;
            margin-top: 20px;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            margin: 0 10px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .divider {
            margin: 20px 0;
            text-align: center;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            color: #666;
            font-size: 14px;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-login a {
            display: inline-flex;
            align-items: center;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .back-to-login a:hover {
            text-decoration: underline;
        }
        
        .back-to-login a::before {
            content: '←';
            margin-right: 5px;
            font-size: 16px;
        }
        
        .success-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .success-actions a {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .success-actions a:hover {
            background: #5a6fd8;
        }
        
        .password-strength {
            margin-top: 5px;
            height: 4px;
            background: #ddd;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .strength-weak { background: #dc3545; }
        .strength-fair { background: #ffc107; }
        .strength-good { background: #28a745; }
        .strength-strong { background: #007bff; }
        
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 24px;
            }
            
            .reset-header h2 {
                font-size: 20px;
            }
            
            .success-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>Your Website</h1>
        </div>
        
        <?php if ($validToken): ?>
            <div class="reset-header">
                <h2>Reset Your Password</h2>
                <p>Enter your new password below. Make sure it's strong and secure.</p>
            </div>
            
            <div class="user-info">
                <p>Resetting password for: <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
                <p>Account: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
            </div>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="resetForm">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your new password" minlength="8">
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="password-requirements">
                        Password must contain:
                        <ul>
                            <li>At least 8 characters</li>
                            <li>One uppercase letter</li>
                            <li>One lowercase letter</li>
                            <li>One number</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           placeholder="Confirm your new password">
                </div>
                
                <button type="submit" class="btn" id="submitBtn">Reset Password</button>
            </form>
            
        <?php elseif ($message): ?>
            <div class="reset-header">
                <h2>Password Reset Successful</h2>
                <p>Your password has been successfully updated.</p>
            </div>
            
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
            
            <div class="success-actions">
                <a href="login.php">Go to Login</a>
            </div>
            
        <?php else: ?>
            <div class="reset-header">
                <h2>Invalid Reset Link</h2>
                <p>This password reset link is either invalid or has expired.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="links">
                <a href="forgotPassword.php">Request New Reset Link</a>
            </div>
        <?php endif; ?>
        
        <div class="divider">
            <span>or</span>
        </div>
        
        <div class="back-to-login">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
    
    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const confirmInput = document.getElementById('confirm_password');
        const submitBtn = document.getElementById('submitBtn');
        
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = checkPasswordStrength(password);
                updateStrengthBar(strength);
                validateForm();
            });
        }
        
        if (confirmInput) {
            confirmInput.addEventListener('input', validateForm);
        }
        
        function checkPasswordStrength(password) {
            let score = 0;
            
            if (password.length >= 8) score++;
            if (password.match(/[a-z]/)) score++;
            if (password.match(/[A-Z]/)) score++;
            if (password.match(/[0-9]/)) score++;
            if (password.match(/[^a-zA-Z0-9]/)) score++;
            
            return score;
        }
        
        function updateStrengthBar(strength) {
            const strengthBar = document.getElementById('strengthBar');
            const widths = [0, 20, 40, 60, 80, 100];
            const classes = ['', 'strength-weak', 'strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
            
            strengthBar.style.width = widths[strength] + '%';
            strengthBar.className = 'password-strength-bar ' + classes[strength];
        }
        
        function validateForm() {
            const password = passwordInput ? passwordInput.value : '';
            const confirmPassword = confirmInput ? confirmInput.value : '';
            const submitBtn = document.getElementById('submitBtn');
            
            if (!submitBtn) return;
            
            const isValid = password.length >= 8 && 
                           password === confirmPassword && 
                           password.match(/[a-z]/) && 
                           password.match(/[A-Z]/) && 
                           password.match(/[0-9]/);
            
            submitBtn.disabled = !isValid;
        }
        
        // Form submission with loading state
        const form = document.getElementById('resetForm');
        if (form) {
            form.addEventListener('submit', function() {
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    submitBtn.innerHTML = 'Resetting...';
                    submitBtn.disabled = true;
                }
            });
        }
    </script>
</body>
</html>