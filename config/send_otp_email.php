<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


// Load configuration
require_once 'config.php';

// Define constants for mail configuration
class EmailNotificationService {
    private $mail;
    private $brandColor = '#0d6efd';
    private $brandName = 'Remote File Manager';
    private $websiteUrl = 'https://cloudfilemanager.ct.ws';
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }
    
    private function configureSMTP() {
        $this->mail->isSMTP();
        $this->mail->Host = MAILHOST;    
        $this->mail->SMTPAuth = true;
        $this->mail->Username = USERNAME; 
        $this->mail->Password = PASSWORD;         
        $this->mail->SMTPSecure = 'tls';                  
        $this->mail->Port = PORT;
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }
    
    /**
     * Send OTP verification email
     */
    public function sendOtpEmail($toEmail, $otp, $userName = null) {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            // Recipients
            $this->mail->setFrom(USERNAME, $this->brandName);
            $this->mail->addAddress($toEmail, $userName);
            $this->mail->addReplyTo(USERNAME, $this->brandName . ' Support');
            
            // Content
            $this->mail->Subject = 'Your Security Code - ' . $this->brandName;
            $this->mail->Body = $this->getOtpEmailTemplate($otp, $userName);
            $this->mail->AltBody = $this->getOtpEmailPlainText($otp, $userName);
            
            $this->mail->send();
            return ['success' => true, 'message' => 'OTP email sent successfully'];
        } catch (Exception $e) {
            error_log("OTP Email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return ['success' => false, 'message' => 'Failed to send email', 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send welcome email for new registrations
     */
    public function sendWelcomeEmail($toEmail, $userName) {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            // Recipients
            $this->mail->setFrom(USERNAME, $this->brandName);
            $this->mail->addAddress($toEmail, $userName);
            $this->mail->addReplyTo(USERNAME, $this->brandName . ' Support');
            
            // Content
            $this->mail->Subject = 'Welcome to ' . $this->brandName . '!';
            $this->mail->Body = $this->getWelcomeEmailTemplate($userName);
            $this->mail->AltBody = $this->getWelcomeEmailPlainText($userName);
            
            $this->mail->send();
            return ['success' => true, 'message' => 'Welcome email sent successfully'];
        } catch (Exception $e) {
            error_log("Welcome Email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return ['success' => false, 'message' => 'Failed to send email', 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($toEmail, $resetToken, $userName = null) {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            // Recipients
            $this->mail->setFrom(USERNAME, $this->brandName);
            $this->mail->addAddress($toEmail, $userName);
            $this->mail->addReplyTo(USERNAME, $this->brandName . ' Support');
            
            // Content
            $resetUrl = $this->websiteUrl . '/reset-password.php?token=' . $resetToken;
            $this->mail->Subject = 'Password Reset Request - ' . $this->brandName;
            $this->mail->Body = $this->getPasswordResetEmailTemplate($resetUrl, $userName);
            $this->mail->AltBody = $this->getPasswordResetEmailPlainText($resetUrl, $userName);
            
            $this->mail->send();
            return ['success' => true, 'message' => 'Password reset email sent successfully'];
        } catch (Exception $e) {
            error_log("Password Reset Email could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return ['success' => false, 'message' => 'Failed to send email', 'error' => $e->getMessage()];
        }
    }
    
    /**
     * OTP Email HTML Template
     */
    private function getOtpEmailTemplate($otp, $userName = null) {
        $greeting = $userName ? "Hello " . htmlspecialchars($userName) : "Hello";
        $currentYear = date('Y');
        
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Your Security Code</title>
            <!--[if mso]>
            <noscript>
                <xml>
                    <o:OfficeDocumentSettings>
                        <o:PixelsPerInch>96</o:PixelsPerInch>
                    </o:OfficeDocumentSettings>
                </xml>
            </noscript>
            <![endif]-->
            <style>
                /* Reset styles */
                body, table, td, p, a, li, blockquote { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
                table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
                img { -ms-interpolation-mode: bicubic; border: 0; }
                
                /* Base styles */
                body {
                    margin: 0 !important;
                    padding: 0 !important;
                    background-color: #f4f7fa !important;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
                }
                
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                }
                
                .header {
                    background: linear-gradient(135deg, {$this->brandColor} 0%, #0056b3 100%);
                    padding: 40px 30px;
                    text-align: center;
                }
                
                .header h1 {
                    color: #ffffff;
                    font-size: 28px;
                    font-weight: 700;
                    margin: 0;
                    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
                }
                
                .header .subtitle {
                    color: rgba(255,255,255,0.9);
                    font-size: 16px;
                    margin: 10px 0 0 0;
                }
                
                .content {
                    padding: 50px 40px;
                    text-align: center;
                }
                
                .greeting {
                    font-size: 24px;
                    font-weight: 600;
                    color: #2c3e50;
                    margin-bottom: 20px;
                }
                
                .message {
                    font-size: 16px;
                    line-height: 1.6;
                    color: #5a6c7d;
                    margin-bottom: 40px;
                }
                
                .otp-container {
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                    border: 2px dashed {$this->brandColor};
                    border-radius: 15px;
                    padding: 30px;
                    margin: 30px 0;
                    text-align: center;
                }
                
                .otp-label {
                    font-size: 14px;
                    font-weight: 600;
                    color: #6c757d;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    margin-bottom: 15px;
                }
                
                .otp-code {
                    font-size: 36px;
                    font-weight: 700;
                    color: {$this->brandColor};
                    font-family: 'Courier New', monospace;
                    letter-spacing: 8px;
                    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    margin: 0;
                    padding: 10px;
                    background: #ffffff;
                    border-radius: 8px;
                    display: inline-block;
                    border: 1px solid #dee2e6;
                }
                
                .security-notice {
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    border-radius: 10px;
                    padding: 20px;
                    margin: 30px 0;
                    text-align: left;
                }
                
                .security-notice .icon {
                    color: #856404;
                    font-size: 20px;
                    margin-right: 10px;
                }
                
                .security-notice h3 {
                    color: #856404;
                    font-size: 16px;
                    font-weight: 600;
                    margin: 0 0 10px 0;
                }
                
                .security-notice p {
                    color: #856404;
                    font-size: 14px;
                    margin: 0;
                    line-height: 1.5;
                }
                
                .footer {
                    background: #2c3e50;
                    color: #ffffff;
                    padding: 30px;
                    text-align: center;
                    font-size: 14px;
                }
                
                .footer p {
                    margin: 0 0 10px 0;
                    opacity: 0.8;
                }
                
                .footer .links {
                    margin: 20px 0;
                }
                
                .footer .links a {
                    color: #ffffff;
                    text-decoration: none;
                    margin: 0 15px;
                    font-weight: 500;
                }
                
                .footer .links a:hover {
                    text-decoration: underline;
                }
                
                /* Mobile responsiveness */
                @media only screen and (max-width: 600px) {
                    .email-container {
                        max-width: 100% !important;
                    }
                    
                    .header, .content, .footer {
                        padding: 30px 20px !important;
                    }
                    
                    .otp-code {
                        font-size: 28px !important;
                        letter-spacing: 4px !important;
                    }
                    
                    .greeting {
                        font-size: 20px !important;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <!-- Header -->
                <div class='header'>
                    <h1>{$this->brandName}</h1>
                    <p class='subtitle'>Secure Access Verification</p>
                </div>
                
                <!-- Content -->
                <div class='content'>
                    <h2 class='greeting'>{$greeting}!</h2>
                    
                    <p class='message'>
                        We received a request to access your account. To continue, please use the security code below:
                    </p>
                    
                    <div class='otp-container'>
                        <div class='otp-label'>Your Security Code</div>
                        <div class='otp-code'>{$otp}</div>
                    </div>
                    
                    <p class='message'>
                        This code will expire in <strong>10 minutes</strong> for your security.
                    </p>
                    
                    <div class='security-notice'>
                        <h3>🔒 Security Notice</h3>
                        <p>
                            • Never share this code with anyone<br>
                            • We will never ask for this code via phone or email<br>
                            • If you didn't request this code, please ignore this email<br>
                            • For security concerns, contact our support team immediately
                        </p>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class='footer'>
                    <p>&copy; {$currentYear} {$this->brandName}. All rights reserved.</p>
                    <div class='links'>
                        <a href='{$this->websiteUrl}'>Website</a>
                        <a href='{$this->websiteUrl}/support'>Support</a>
                        <a href='{$this->websiteUrl}/privacy'>Privacy Policy</a>
                    </div>
                    <p>This is an automated message, please don't reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Welcome Email HTML Template
     */
    private function getWelcomeEmailTemplate($userName) {
        $currentYear = date('Y');
        
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Welcome to {$this->brandName}</title>
            <style>
                body { margin: 0; padding: 0; background-color: #f4f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
                .header { background: linear-gradient(135deg, {$this->brandColor} 0%, #0056b3 100%); padding: 40px 30px; text-align: center; }
                .header h1 { color: #ffffff; font-size: 28px; font-weight: 700; margin: 0; }
                .content { padding: 50px 40px; text-align: center; }
                .welcome-message { font-size: 24px; font-weight: 600; color: #2c3e50; margin-bottom: 20px; }
                .description { font-size: 16px; line-height: 1.6; color: #5a6c7d; margin-bottom: 30px; }
                .cta-button { display: inline-block; background: linear-gradient(135deg, {$this->brandColor}, #0056b3); color: #ffffff; text-decoration: none; padding: 15px 30px; border-radius: 25px; font-weight: 600; margin: 20px 0; }
                .features { text-align: left; margin: 40px 0; }
                .feature { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid {$this->brandColor}; }
                .feature h3 { color: #2c3e50; margin: 0 0 5px 0; font-size: 16px; }
                .feature p { color: #5a6c7d; margin: 0; font-size: 14px; }
                .footer { background: #2c3e50; color: #ffffff; padding: 30px; text-align: center; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>🎉 Welcome to {$this->brandName}!</h1>
                </div>
                
                <div class='content'>
                    <h2 class='welcome-message'>Hello " . htmlspecialchars($userName) . "!</h2>
                    
                    <p class='description'>
                        Thank you for joining {$this->brandName}! We're excited to have you on board. 
                        Your account has been successfully created and you're ready to start managing your files securely.
                    </p>
                    
                    <a href='{$this->websiteUrl}/dashboard' class='cta-button'>Get Started Now</a>
                    
                    <div class='features'>
                        <div class='feature'>
                            <h3>🔒 Secure File Storage</h3>
                            <p>Your files are protected with enterprise-grade encryption and security measures.</p>
                        </div>
                        <div class='feature'>
                            <h3>☁️ Remote Access</h3>
                            <p>Access your files from anywhere in the world with our secure remote access system.</p>
                        </div>
                        <div class='feature'>
                            <h3>🚀 Easy Management</h3>
                            <p>Intuitive interface makes file management simple and efficient for everyone.</p>
                        </div>
                        <div class='feature'>
                            <h3>💬 24/7 Support</h3>
                            <p>Our support team is always here to help you with any questions or issues.</p>
                        </div>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>&copy; {$currentYear} {$this->brandName}. All rights reserved.</p>
                    <p>Need help? Contact us at <a href='mailto:" . USERNAME . "' style='color: #ffffff;'>" . USERNAME . "</a></p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Password Reset Email HTML Template
     */
    private function getPasswordResetEmailTemplate($resetUrl, $userName = null) {
        $greeting = $userName ? "Hello " . htmlspecialchars($userName) : "Hello";
        $currentYear = date('Y');
        
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Password Reset Request</title>
            <style>
                body { margin: 0; padding: 0; background-color: #f4f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
                .header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 40px 30px; text-align: center; }
                .header h1 { color: #ffffff; font-size: 28px; font-weight: 700; margin: 0; }
                .content { padding: 50px 40px; text-align: center; }
                .message { font-size: 16px; line-height: 1.6; color: #5a6c7d; margin-bottom: 30px; }
                .reset-button { display: inline-block; background: linear-gradient(135deg, #dc3545, #c82333); color: #ffffff; text-decoration: none; padding: 15px 30px; border-radius: 25px; font-weight: 600; margin: 20px 0; }
                .security-info { background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 10px; padding: 20px; margin: 30px 0; text-align: left; }
                .footer { background: #2c3e50; color: #ffffff; padding: 30px; text-align: center; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>🔐 Password Reset</h1>
                </div>
                
                <div class='content'>
                    <h2>{$greeting}!</h2>
                    
                    <p class='message'>
                        We received a request to reset your password for your {$this->brandName} account. 
                        Click the button below to create a new password:
                    </p>
                    
                    <a href='{$resetUrl}' class='reset-button'>Reset My Password</a>
                    
                    <p class='message'>
                        This link will expire in 1 hour for security reasons.
                    </p>
                    
                    <div class='security-info'>
                        <h3 style='color: #721c24; margin: 0 0 10px 0;'>Important Security Information</h3>
                        <p style='color: #721c24; margin: 0; font-size: 14px;'>
                            • If you did not request this password reset, please ignore this email<br>
                            • Never share your password reset link with anyone<br>
                            • Always use a strong, unique password<br>
                            • Contact support if you have any security concerns
                        </p>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>&copy; {$currentYear} {$this->brandName}. All rights reserved.</p>
                    <p>If the button doesn't work, copy this link: {$resetUrl}</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    // Plain text versions for better compatibility
    private function getOtpEmailPlainText($otp, $userName = null) {
        $greeting = $userName ? "Hello " . $userName : "Hello";
        return "{$greeting}!\n\nYour security code for {$this->brandName} is: {$otp}\n\nThis code will expire in 10 minutes.\n\nFor security reasons, never share this code with anyone.\n\nBest regards,\n{$this->brandName} Team";
    }
    
    private function getWelcomeEmailPlainText($userName) {
        return "Welcome to {$this->brandName}, " . $userName . "!\n\nThank you for joining us. Your account has been successfully created.\n\nVisit {$this->websiteUrl} to get started.\n\nBest regards,\n{$this->brandName} Team";
    }
    
    private function getPasswordResetEmailPlainText($resetUrl, $userName = null) {
        $greeting = $userName ? "Hello " . $userName : "Hello";
        return "{$greeting}!\n\nWe received a request to reset your password.\n\nClick this link to reset your password: {$resetUrl}\n\nThis link expires in 1 hour.\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\n{$this->brandName} Team";
    }
}

// Usage Examples:

// Initialize the email service
$emailService = new EmailNotificationService();

// Send OTP Email
function sendOtpEmail($toEmail, $otp, $userName = null) {
    global $emailService;
    return $emailService->sendOtpEmail($toEmail, $otp, $userName);
}

// Send Welcome Email
function sendWelcomeEmail($toEmail, $userName) {
    global $emailService;
    return $emailService->sendWelcomeEmail($toEmail, $userName);
}

// Send Password Reset Email
function sendPasswordResetEmail($toEmail, $resetToken, $userName = null) {
    global $emailService;
    return $emailService->sendPasswordResetEmail($toEmail, $resetToken, $userName);
}

/* 
Usage in your registration/login files:

// After successful registration
$result = sendWelcomeEmail($email, $userName);
if ($result['success']) {
    // Email sent successfully
} else {
    // Handle error: $result['message']
}

// For OTP verification
$result = sendOtpEmail($email, $otp, $userName);
if ($result['success']) {
    // OTP email sent successfully
} else {
    // Handle error
}

// For password reset
$result = sendPasswordResetEmail($email, $resetToken, $userName);
if ($result['success']) {
    // Reset email sent successfully
} else {
    // Handle error
}
*/
?>