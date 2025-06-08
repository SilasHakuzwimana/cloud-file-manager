<?php
require './config/db.php';
date_default_timezone_set('Africa/Kigali');
$token = $_GET['token'] ?? '';
$valid = false;
if (!empty($token)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    $valid = $stmt->num_rows > 0;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Update password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires_at = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $passwordHash, $token);
        if ($stmt->execute()) {
            $message = "Password has been reset successfully.";
            header("Location: index.php?message=" . urlencode($message));
            $valid = false;
        } else {
            $error = "Failed to reset password.";
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

        .reset-container {
            max-width: 500px;
            margin: 2rem auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .reset-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .reset-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.75rem;
        }

        .reset-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .reset-body {
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

        .alert-warning {
            background: linear-gradient(135deg, var(--warning-color), #f39c12);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .password-requirements {
            background: var(--light-bg);
            border-radius: var(--border-radius-sm);
            padding: 1rem;
            margin-top: 1rem;
            border-left: 4px solid var(--primary-color);
        }

        .password-requirements small {
            color: var(--secondary-color);
            font-size: 0.875rem;
        }

        .success-icon {
            text-align: center;
            margin-bottom: 1rem;
        }

        .success-icon i {
            font-size: 4rem;
            color: var(--success-color);
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .back-link a:hover {
            color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="reset-container">
            <div class="reset-header">
                <i class="fas fa-key"></i>
                <h2>Reset Password</h2>
            </div>
            
            <div class="reset-body">
                <?php if (!empty($message)): ?>
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $message ?>
                    </div>
                    <div class="back-link">
                        <a href="login.php">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Login
                        </a>
                    </div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($valid): ?>
                <form method="POST">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-lock me-2"></i>
                            New Password
                        </label>
                        <input type="password" name="password" required class="form-control" 
                               placeholder="Enter your new password">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-lock me-2"></i>
                            Confirm Password
                        </label>
                        <input type="password" name="confirm" required class="form-control" 
                               placeholder="Confirm your new password">
                    </div>
                    
                    <div class="password-requirements">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Password Requirements:</strong><br>
                            • Minimum 6 characters<br>
                            • Both passwords must match
                        </small>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Reset Password
                        </button>
                    </div>
                </form>
                <?php elseif (!$message): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Invalid or expired token.
                    </div>
                    <div class="back-link">
                        <a href="forgot_password.php">
                            <i class="fas fa-arrow-left me-1"></i>
                            Request New Reset Link
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>