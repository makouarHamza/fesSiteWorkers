<?php
// contact.php
session_start();

// For localhost development - alternative email methods
// You can choose one of these methods:

// Method 1: Simple file logging (for testing)
function saveToFile($name, $email, $subject, $message) {
    $logData = "=== New Contact Form Submission ===\n";
    $logData .= "Date: " . date('Y-m-d H:i:s') . "\n";
    $logData .= "Name: $name\n";
    $logData .= "Email: $email\n";
    $logData .= "Subject: $subject\n";
    $logData .= "Message: $message\n";
    $logData .= "=====================================\n\n";
    
    file_put_contents('contact_messages.txt', $logData, FILE_APPEND | LOCK_EX);
    return true;
}

// Method 2: Using PHPMailer with Gmail SMTP (recommended for production)
// Uncomment this function if you want to use Gmail SMTP
/*
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // You'll need to install PHPMailer via Composer

function sendEmailWithGmail($name, $email, $subject, $message) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-gmail@gmail.com';  // Your Gmail
        $mail->Password   = 'your-app-password';     // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->setFrom('your-gmail@gmail.com', 'Contact Form');
        $mail->addAddress('hamzamek@gmail.com');
        $mail->addReplyTo($email, $name);
        
        $mail->isHTML(false);
        $mail->Subject = 'Contact Form: ' . $subject;
        $mail->Body    = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
*/

// Initialize variables
$name = $email = $subject = $message = '';
$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        // For localhost development, we'll save to file instead of sending email
        // This creates a text file with all form submissions for testing
        
        if (saveToFile($name, $email, $subject, $message)) {
            $success = true;
            // Clear form data on success
            $name = $email = $subject = $message = '';
        } else {
            $errors[] = 'Sorry, there was an error processing your message. Please try again.';
        }
        
        // Alternative: Use this for Gmail SMTP (uncomment if PHPMailer is installed)
        /*
        if (sendEmailWithGmail($name, $email, $subject, $message)) {
            $success = true;
            $name = $email = $subject = $message = '';
        } else {
            $errors[] = 'Sorry, there was an error sending your message. Please try again.';
        }
        */
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/contact.css">

    <title>Contact Us</title>
    <style>
        
    </style>
</head>
<body>
    <?php include("header.php") ?>
    <div class="container">
        <div class="headerC">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>

        <div class="form-container">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <strong>Success!</strong> Your message has been sent successfully. We'll get back to you soon.
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Please correct the following errors:</strong>
                    <ul style="margin-top: 10px; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="row">
                    <div class="col-half">
                        <div class="form-group">
                            <label for="name">Full Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                    </div>
                    <div class="col-half">
                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Subject <span class="required">*</span></label>
                    <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" required>
                </div>

                <div class="form-group">
                    <label for="message">Message <span class="required">*</span></label>
                    <textarea id="message" name="message" placeholder="Tell us how we can help you..." required><?php echo htmlspecialchars($message); ?></textarea>
                </div>

                <button type="submit" class="btn">Send Message</button>
            </form>
        </div>

        <div class="contact-info">
            <h3>Get in Touch</h3>
            <div class="info-item">
                <svg class="info-icon" viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                <span>contact@example.com</span>
            </div>
            <div class="info-item">
                <svg class="info-icon" viewBox="0 0 24 24">
                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                </svg>
                <span>+1 (555) 123-4567</span>
            </div>
            <div class="info-item">
                <svg class="info-icon" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <span>123 Main Street, City, State 12345</span>
            </div>
        </div>
    </div>
</body>
</html>