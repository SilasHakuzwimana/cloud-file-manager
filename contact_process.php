<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once './config/db.php';
require_once './config/config.php';
require './vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Set content type for JSON response
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Sanitize and validate input
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');
$user_id  = $_SESSION['user_id'] ?? null;
$created_at = date('Y-m-d H:i:s');


// Validation
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}
if (strlen($name) < 2) {
    echo json_encode(['success' => false, 'error' => 'Name must be at least 2 characters long']);
    exit;
}
if (strlen($message) < 10) {
    echo json_encode(['success' => false, 'error' => 'Message must be at least 10 characters long']);
    exit;
}
// Validate email format

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Please enter a valid email address']);
    exit;
}

if (strlen($name) > 100) {
    echo json_encode(['success' => false, 'error' => 'Name is too long']);
    exit;
}

if (strlen($message) > 5000) {
    echo json_encode(['success' => false, 'error' => 'Message is too long']);
    exit;
}

// Get logged-in user_id if set
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Insert into database
try {
    if ($user_id === null) {
        $stmt = $conn->prepare("INSERT INTO inquiries (user_id, name, email, message, created_at) VALUES (NULL, ?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $message, $created_at);
    } else {
        $stmt = $conn->prepare("INSERT INTO inquiries (user_id, name, email, message, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $name, $email, $message, $created_at);
    }

    if (!$stmt->execute()) {
        throw new Exception('Database error: ' . $stmt->error);
    }

    $inquiry_id = $conn->insert_id;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

// Send professional email
try {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = MAILHOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = USERNAME;
    $mail->Password   = PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = PORT;

    // Recipients
    $mail->setFrom(USERNAME, 'Website Contact Form');
    $mail->addAddress(ADMIN_EMAIL, 'Customer Service');
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = "New Contact Inquiry - Ref: #" . str_pad($inquiry_id, 6, '0', STR_PAD_LEFT);

    // Professional HTML email template
    $mail->Body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Contact Inquiry</title>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); padding: 30px; text-align: center; color: white; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 300;">New Contact Inquiry</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9; font-size: 16px;">Reference: #' . str_pad($inquiry_id, 6, '0', STR_PAD_LEFT) . '</p>
        </div>
        
        <div style="background: #f8f9fa; padding: 30px; border-left: 4px solid #667eea;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e1e8ed;">
                        <strong style="color: #2c3e50; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Full Name</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 2px solid #e1e8ed; font-size: 16px;">
                        ' . htmlspecialchars($name) . '
                    </td>
                </tr>
                
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e1e8ed;">
                        <strong style="color: #2c3e50; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 2px solid #e1e8ed; font-size: 16px;">
                        <a href="mailto:' . htmlspecialchars($email) . '" style="color: #667eea; text-decoration: none;">' . htmlspecialchars($email) . '</a>
                    </td>
                </tr>
                
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e1e8ed;">
                        <strong style="color: #2c3e50; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Message</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 2px solid #e1e8ed; font-size: 16px; line-height: 1.6;">
                        ' . nl2br(htmlspecialchars($message)) . '
                    </td>
                </tr>
                
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e1e8ed;">
                        <strong style="color: #2c3e50; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Submitted On</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; font-size: 16px; color: #666;">
                        ' . date('F j, Y \a\t g:i A', strtotime($created_at)) . '
                    </td>
                </tr>
            </table>
        </div>
        
        <div style="background: #fff; padding: 25px; border-radius: 0 0 10px 10px; border-top: 3px solid #667eea;">
            <p style="margin: 0; color: #666; font-size: 14px; text-align: center;">
                <strong>Action Required:</strong> Please respond to this inquiry within 24 hours.<br>
                You can reply directly to this email to contact the sender.
            </p>
        </div>
        
        <div style="text-align: center; margin-top: 20px; padding: 20px; color: #999; font-size: 12px;">
            <p style="margin: 0;">This email was automatically generated from your website contact form.</p>
            <p style="margin: 5px 0 0 0;">© ' . date('Y') . ' Remote File Manager. All rights reserved.</p>
        </div>
    </body>
    </html>';

    // Plain text version
    $mail->AltBody = "New Contact Inquiry - Reference: #" . str_pad($inquiry_id, 6, '0', STR_PAD_LEFT) . "\n\n" .
        "Full Name: " . $name . "\n" .
        "Email: " . $email . "\n" .
        "Message: " . $message . "\n\n" .
        "Submitted: " . date('F j, Y \a\t g:i A', strtotime($created_at)) . "\n\n" .
        "Please respond to this inquiry within 24 hours.";

    $mail->send();

    // Optional: Send confirmation email to the user
    $confirmationMail = new PHPMailer(true);
    $confirmationMail->isSMTP();
    $confirmationMail->Host       = MAILHOST;
    $confirmationMail->SMTPAuth   = true;
    $confirmationMail->Username   = USERNAME;
    $confirmationMail->Password   = PASSWORD;
    $confirmationMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $confirmationMail->Port       = PORT;

    $confirmationMail->setFrom(USERNAME, 'Customer Service');
    $confirmationMail->addAddress($email, $name);

    $confirmationMail->isHTML(true);
    $confirmationMail->Subject = "Thank you for contacting us - Ref: #" . str_pad($inquiry_id, 6, '0', STR_PAD_LEFT);

    $confirmationMail->Body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thank You</title>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; color: white; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 300;">Thank You!</h1>
                <p style="margin: 10px 0 0 0; opacity: 0.9; font-size: 16px;">Your message has been received. Reference: #' . str_pad($inquiry_id, 6, '0', STR_PAD_LEFT) . '</p>
</div>

<div style="background: #f8f9fa; padding: 30px; border-left: 4px solid #764ba2;">
    <p style="font-size: 16px;">Dear ' . htmlspecialchars($name) . ',</p>
    <p>Thank you for contacting us. We have received your message and our support team will respond as soon as possible.</p>
    <p><strong>Your Message:</strong></p>
    <blockquote style="border-left: 4px solid #ccc; margin: 10px 0; padding-left: 15px; color: #555;">' . nl2br(htmlspecialchars($message)) . '</blockquote>
    <p style="color: #555;">Sent on: ' . date('F j, Y \a\t g:i A', strtotime($created_at)) . '</p>
</div>

<div style="background: #fff; padding: 25px; border-radius: 0 0 10px 10px; border-top: 3px solid #764ba2;">
    <p style="margin: 0; color: #666; font-size: 14px; text-align: center;">
        This is a confirmation that your inquiry has been logged. If you have any further questions, feel free to reply to this email.
    </p>
</div>

<div style="text-align: center; margin-top: 20px; padding: 20px; color: #999; font-size: 12px;">
    <p style="margin: 0;">This is an automated email. Please do not reply directly.</p>
    <p style="margin: 5px 0 0 0;">© ' . date('Y') . ' Remote File Manager. All rights reserved.</p>
</div>
</body>
</html>';

   $confirmationMail->AltBody = "Dear $name,\n\n" .
    "Thank you for contacting us. Your message has been received and we will get back to you shortly.\n\n" .
    "Reference #: #" . str_pad($inquiry_id, 6, '0', STR_PAD_LEFT) . "\n" .
    "Your Message:\n" . $message . "\n\n" .
    "Sent on: " . date('F j, Y \a\t g:i A', strtotime($created_at)) . "\n\n" .
    "Best regards,\nCustomer Service Team";

    $confirmationMail->send();

echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Email sending failed: ' . $e->getMessage()]);
    exit;
}
catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
