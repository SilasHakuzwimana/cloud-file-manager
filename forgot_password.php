<?php 
require './config/db.php'; 
require './config/send_otp_email.php';   

date_default_timezone_set('Africa/Kigali');  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
    $email = trim($_POST['email']);      
    
    // Check if email exists     
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");     
    $stmt->bind_param("s", $email);     
    $stmt->execute();     
    $stmt->store_result();     
    if ($stmt->num_rows > 0) {         
        // Generate token         
        $token = bin2hex(random_bytes(32));         
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));          
        
        // Save to DB         
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE email = ?");         
        $stmt->bind_param("sss", $token, $expires, $email);         
        $stmt->execute();          
        
        // Send reset email         
        $resetLink = "http://localhost/cloudfilemanager/reset_password.php?token=$token";         
        $subject = "Password Reset Request";         
        $body = "             
            <p>You requested a password reset.</p>             
            <p>Click the link below to reset your password:</p>             
            <a href='$resetLink'>$resetLink</a>             
            <p>This link expires in 1 hour.</p>         
        ";          
        
        if (sendPasswordResetEmail($email, $subject, $body)) {             
            $message = "A reset link has been sent to your email.";         
        } else {             
            $error = "Failed to send reset email.";         
        }     
    } else {         
        $error = "Email address not found.";     
    } 
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-dark: #0b5ed7;
            --primary-light: #6ea8fe;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #fd7e14;
            --info-color: #0dcaf0;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
            --border-color: #dee2e6;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            --border-radius: 0.75rem;
            --border-radius-sm: 0.5rem;
        }

        body {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .forgot-container {
            max-width: 500px;
            margin: 2rem auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .forgot-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .forgot-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.75rem;
        }

        .forgot-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .forgot-header p {
            margin: 1rem 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .forgot-body {
            padding: 2rem;
        }

        .form-control {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }

        .alert {
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .alert-danger {
            background: linear-gradient(135deg, var(--danger-color), #e74c3c);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .info-box {
            background: var(--light-bg);
            border-radius: var(--border-radius-sm);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--info-color);
        }

        .info-box h5 {
            color: var(--dark-text);
            margin-bottom: 0.75rem;
            font-weight: 600;
        }

        .info-box p {
            color: var(--secondary-color);
            margin: 0;
            font-size: 0.9rem;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: var(--primary-dark);
        }

        .email-sent-icon {
            text-align: center;
            margin-bottom: 1rem;
        }

        .email-sent-icon i {
            font-size: 4rem;
            color: var(--success-color);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            z-index: 5;
        }

        .form-control.with-icon {
            padding-left: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="forgot-container">
            <div class="forgot-header">
                <i class="fas fa-envelope"></i>
                <h2>Forgot Password</h2>
                <p>Enter your email address and we'll send you a reset link</p>
            </div>
            
            <div class="forgot-body">
                <?php if (!empty($message)): ?>
                    <div class="email-sent-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $message ?>
                    </div>
                    <div class="info-box">
                        <h5><i class="fas fa-info-circle me-2"></i>Check Your Email</h5>
                        <p>
                            We've sent a password reset link to your email address. 
                            The link will expire in 1 hour for security reasons.
                        </p>
                    </div>
                    <div class="back-link">
                        <a href="index.php">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Login
                        </a>
                    </div>
                <?php else: ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-box">
                        <h5><i class="fas fa-shield-alt me-2"></i>Password Recovery</h5>
                        <p>
                            Enter the email address associated with your account. 
                            We'll send you a secure link to reset your password.
                        </p>
                    </div>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-envelope me-2"></i>
                                Email Address
                            </label>
                            <div class="input-group">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" required class="form-control with-icon" 
                                       placeholder="Enter your email address">
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>
                                Send Reset Link
                            </button>
                        </div>
                    </form>
                    
                    <div class="back-link">
                        <a href="index.php">
                            <i class="fas fa-arrow-left me-1"></i>
                            Remember your password? Sign In
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>