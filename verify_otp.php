<?php 
session_start(); 
require './config/db.php';  

if (!isset($_SESSION['pending_user_id'])) {     
    header('Location: index.php');     
    exit; 
}  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
    $otpInput = trim($_POST['otp']);     
    $userId = $_SESSION['pending_user_id'];  
    $email = $_SESSION['email'];    
    
    $stmt = $conn->prepare("SELECT otp, otp_expires_at FROM users WHERE id = ?");     
    $stmt->bind_param("i", $userId);     
    $stmt->execute();     
    $result = $stmt->get_result();      
    
    if ($user = $result->fetch_assoc()) {         
        if ($user['otp'] === $otpInput) {             
            if (new DateTime() <= new DateTime($user['otp_expires_at'])) {                 
                // Mark user as verified and clear OTP                 
                $stmt2 = $conn->prepare("UPDATE users SET is_verified = 1, otp = NULL, otp_expires_at = NULL WHERE id = ?");                 
                $stmt2->bind_param("i", $userId);                 
                $stmt2->execute();                  
                
                // Set session logged in                 
                $_SESSION['user_id'] = $userId;                 
                unset($_SESSION['pending_user_id']);                  
                
                header('Location: dashboard.php');                  
                exit;             
            } else {                 
                $error = "OTP expired. Please login again.";                 
                session_destroy();             
            }         
        } else {             
            $error = "Invalid OTP code.";         
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
    <title>RFM - Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .auth-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="70" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="30" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        }

        .brand-logo {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .auth-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .auth-header .subtitle {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }

        .auth-body {
            padding: 40px 35px;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
            text-align: center;
        }

        .otp-input {
            text-align: center;
            font-size: 2rem;
            letter-spacing: 0.5rem;
            font-weight: bold;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            width: 100%;
        }

        .otp-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            background: white;
            outline: none;
        }

        .otp-input::placeholder {
            color: #ced4da;
            font-weight: normal;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
        }

        .btn-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c2c7);
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        .alert-info {
            background: linear-gradient(135deg, #cff4fc, #b6effb);
            color: #055160;
            border-left: 4px solid var(--info-color);
        }

        .otp-timer {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .timer-active {
            color: var(--warning-color);
            font-weight: 600;
        }

        .timer-expired {
            color: var(--danger-color);
            font-weight: 600;
        }

        .resend-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .otp-digits {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .digit-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .digit-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            outline: none;
        }

        @media (max-width: 576px) {
            .auth-card {
                margin: 10px;
                border-radius: 15px;
            }
            
            .auth-header {
                padding: 25px 20px;
            }
            
            .auth-body {
                padding: 30px 25px;
            }
            
            .auth-header h1 {
                font-size: 1.75rem;
            }

            .otp-input {
                font-size: 1.5rem;
                letter-spacing: 0.3rem;
                padding: 15px;
            }

            .digit-input {
                width: 40px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1>Verify Your Identity</h1>
            <p class="subtitle">Enter the 6-digit code sent to your email</p>
        </div>
        
        <div class="auth-body">
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Check your email for the verification code. It may take a few minutes to arrive.
            </div>
            
            <form method="POST" action="verify_otp.php">
                <div class="input-group">
                    <label for="otp" class="form-label">Enter OTP Code</label>
                    <input type="text" class="otp-input" id="otp" name="otp" 
                           placeholder="000000" maxlength="6" 
                           pattern="[0-9]{6}" required autofocus>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>
                    Verify Code
                </button>
            </form>

            <div class="otp-timer">
                <i class="fas fa-clock me-1"></i>
                <span id="timerText">Code expires in <span id="countdown">10:00</span></span>
            </div>
            
            <div class="resend-section">
                <p class="text-muted mb-2">Didn't receive the code?</p>
                <a href="login.php" class="btn-link">
                    <i class="fas fa-redo me-1"></i>
                    Back to Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // OTP input formatting and validation
        const otpInput = document.getElementById('otp');

        otpInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 6) value = value.slice(0, 6);
            e.target.value = value;
        });

        otpInput.addEventListener('keypress', function(e) {
            if (!/\d/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
        });

        // Countdown timer (10 minutes)
        let timeLeft = 10 * 60; // 10 minutes in seconds
        const countdownElement = document.getElementById('countdown');
        const timerText = document.getElementById('timerText');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const formattedTime = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            countdownElement.textContent = formattedTime;
            
            if (timeLeft <= 60) {
                countdownElement.parentElement.className = 'timer-expired';
            } else if (timeLeft <= 180) {
                countdownElement.parentElement.className = 'timer-active';
            }
            
            if (timeLeft <= 0) {
                timerText.innerHTML = '<i class="fas fa-times-circle me-1"></i>Code has expired';
                timerText.className = 'timer-expired';
                clearInterval(timerInterval);
            }
            
            timeLeft--;
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer(); // Initial call

        // Auto-submit form when 6 digits are entered
        otpInput.addEventListener('input', function(e) {
            if (e.target.value.length === 6) {
                // Small delay to ensure user sees the complete input
                setTimeout(() => {
                    e.target.form.submit();
                }, 500);
            }
        });

        // Focus on load
        otpInput.focus();
    </script>
</body>
</html>