<?php
session_start();
require './config/db.php';
require './config/send_otp_email.php';

// Set timezone explicitly 
date_default_timezone_set('Africa/Kigali');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];

    // Basic validation     
    if (empty($email) || empty($password) || empty($passwordConfirm)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($password !== $passwordConfirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists         
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Hash password using bcrypt             
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Current datetime in Africa/Kigali             
            $createdAt = date('Y-m-d H:i:s');

            // Insert user             
            $stmt = $conn->prepare("INSERT INTO users (email, password_hash, created_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $passwordHash, $createdAt);
            if ($stmt->execute()) {
                $userId = $stmt->insert_id;
                $userName = explode('@', $email)[0]; // Use part before @ as username

                // After successful registration
                $result = sendWelcomeEmail($email, $userName);
                if (is_array($result) && isset($result['success']) && $result['success']) {
                    $_SESSION['success'] = "Registration successful! A welcome email has been sent to $email.";
                } else {
                    $error = "Registration successful, but failed to send welcome email. Please contact support.";
                }
                header('Location: index.php');
                exit;
            } else {
                $error = "Failed to register user. Try again later.";
            }
        }
    }
}
// Define sendWelcomeEmail if not already defined
if (!function_exists('sendWelcomeEmail')) {
    function sendWelcomeEmail($email, $userName) {
        // Example: Use mail() or a mail library here
        $subject = "Welcome to Remote File Manager";
        $message = "Hello $userName,\n\nThank you for registering at Remote File Manager!";
        $headers = "From: no-reply@yourdomain.com\r\n";
        $success = mail($email, $subject, $message, $headers);
        return ['success' => $success];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RFM - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/register.css">
</head>

<body>
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-logo">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1>Create Account</h1>
            <p class="subtitle">Join Remote File Manager today</p>
        </div>

        <div class="auth-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" novalidate>
                <div class="input-group d-flex">
                    <div class="input-wrapper mt-3">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Enter your email"
                            value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                            required autocomplete="email">
                    </div>
                </div>

                <div class="input-group">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Create a password" required autocomplete="new-password">
                            <i class="fas fa-eye password-toggle" id="passwordToggle" title="Show/Hide Password"></i>
                        </div>
                        <div class="password-match" id="passwordMatch"></div>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <small class="strength-text text-muted" id="strengthText">Enter a password to see strength</small>
                    </div>
                </div>

                <div class="input-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                            placeholder="Confirm your password" required autocomplete="new-password">
                        <i class="fas fa-eye password-toggle" id="confirmToggle" title="Show/Hide Password"></i>
                    </div>
                    <div class="password-match" id="passwordMatch"></div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>
                    Create Account
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <p>Already have an account? <a href="index.php" class="btn-link">Sign In</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggles
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirm');
        const passwordToggle = document.getElementById('passwordToggle');
        const confirmToggle = document.getElementById('confirmToggle');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const passwordMatch = document.getElementById('passwordMatch');

        // Toggle password visibility
        passwordToggle.addEventListener('click', function() {
            togglePasswordVisibility(passwordInput, passwordToggle);
        });

        confirmToggle.addEventListener('click', function() {
            togglePasswordVisibility(confirmInput, confirmToggle);
        });

        function togglePasswordVisibility(input, toggle) {
            if (input.type === 'password') {
                input.type = 'text';
                toggle.classList.remove('fa-eye');
                toggle.classList.add('fa-eye-slash');
                toggle.title = 'Hide Password';
            } else {
                input.type = 'password';
                toggle.classList.remove('fa-eye-slash');
                toggle.classList.add('fa-eye');
                toggle.title = 'Show Password';
            }
        }

        // Password strength checker
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);

            strengthFill.className = 'strength-fill strength-' + strength.class;
            strengthText.textContent = strength.text;
            strengthText.style.color = strength.color;

            // Check password match when password changes
            checkPasswordMatch();
        });

        // Password confirmation checker
        confirmInput.addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            if (confirm === '') {
                passwordMatch.textContent = '';
                passwordMatch.style.color = '';
                return;
            }

            if (password === confirm) {
                passwordMatch.textContent = '✓ Passwords match';
                passwordMatch.style.color = '#198754';
            } else {
                passwordMatch.textContent = '✗ Passwords do not match';
                passwordMatch.style.color = '#dc3545';
            }
        }

        function checkPasswordStrength(password) {
            if (password.length === 0) {
                return {
                    class: 'weak',
                    text: 'Enter a password to see strength',
                    color: '#6c757d'
                };
            }

            let score = 0;

            // Length check
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;

            // Character variety
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            if (score < 3) {
                return {
                    class: 'weak',
                    text: 'Weak password',
                    color: '#dc3545'
                };
            } else if (score < 5) {
                return {
                    class: 'fair',
                    text: 'Fair password',
                    color: '#ffc107'
                };
            } else if (score < 6) {
                return {
                    class: 'good',
                    text: 'Good password',
                    color: '#28a745'
                };
            } else {
                return {
                    class: 'strong',
                    text: 'Strong password',
                    color: '#198754'
                };
            }
        }

        // Form validation feedback
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            if (password !== confirm) {
                e.preventDefault();
                confirmInput.focus();
                passwordMatch.textContent = '✗ Passwords must match before submitting';
                passwordMatch.style.color = '#dc3545';
            }
        });

        // Enhanced keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.type !== 'submit') {
                const formElements = Array.from(form.elements);
                const currentIndex = formElements.indexOf(e.target);
                const nextElement = formElements[currentIndex + 1];

                if (nextElement && nextElement.type !== 'submit') {
                    nextElement.focus();
                    e.preventDefault();
                }
            }
        });
    </script>
</body>

</html>