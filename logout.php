<?php
session_start();

// Database configuration
// $host = 'localhost';
// $dbname = 'your_database_name';
// $username = 'your_db_username';
// $password = 'your_db_password';

try {
    // $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    include("config.php");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database connection fails, still proceed with logout
    error_log("Database connection failed during logout: " . $e->getMessage());
}

// Update last_login to current time before logging out (optional)
if (isset($_SESSION['user_id']) && isset($db)) {
    try {
        $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch(PDOException $e) {
        error_log("Failed to update last_login during logout: " . $e->getMessage());
    }
}

// Store user's first name for goodbye message (optional)
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';

// Destroy all session data
session_unset();
session_destroy();

// Start a new session for the logout message
session_start();
$_SESSION['logout_message'] = "You have been successfully logged out.";
if ($firstName) {
    $_SESSION['logout_message'] = "Goodbye, " . htmlspecialchars($firstName) . "! You have been successfully logged out.";
}

// Redirect to login page
header("Location: login.php");
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .logout-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        // Redirect after 3 seconds if header redirect fails
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 3000);
    </script>
</head>
<body>
    <div class="logout-container">
        <h2>Logging Out...</h2>
        <div class="spinner"></div>
        <p>Please wait while we securely log you out.</p>
        <p>You will be redirected to the login page shortly.</p>
        <a href="login.php" class="btn">Go to Login Page</a>
    </div>
</body>
</html>