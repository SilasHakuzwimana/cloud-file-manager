<?php
session_start();
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}
?>

<?php
require './config/db.php';
require './config/send_otp_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch user by email     
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            // Generate OTP             
            $otp = random_int(100000, 999999);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Save OTP and expiry             
            $stmt2 = $conn->prepare("UPDATE users SET otp = ?, otp_expires_at = ?, is_verified = 0 WHERE id = ?");
            $stmt2->bind_param("ssi", $otp, $expiresAt, $user['id']);
            $stmt2->execute();

            // Send OTP email             
            if (sendOtpEmail($email, $otp)) {
                $_SESSION['pending_user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                header('Location: verify_otp.php');
                exit;
            } else {
                $error = "Failed to send OTP email. Try again.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RFM - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/register.css">
</head>

<body>
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-logo">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <h1>Welcome Back</h1>
            <p class="subtitle">Sign in to your Remote File Manager</p>
        </div>

        <div class="auth-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
            <?php unset($_SESSION['success']);
            endif; ?>

            <form method="POST" action="index.php">
                <div class="input-group">
                    <div class="input-wrapper mt-3">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Enter your email"
                            value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                            required>
                    </div>
                </div>

                <div class="input-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your password" required>
                        <i class="fas fa-eye password-toggle" id="passwordToggle" title="Show/Hide Password"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Sign In
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <p class="mb-0">Don't have an account? <a href="register.php" class="btn-link">Create Account</a></p>
            <p class="mb-0"><a href="forgot_password.php" class="btn-link">Forgot Password?</a></p>
        </div>
    </div>

    <script>
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        passwordToggle.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>