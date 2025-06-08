<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
        }

        .error-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            text-align: center;
        }

        .error-header {
            background: linear-gradient(135deg, var(--warning-color), #f39c12);
            color: white;
            padding: 3rem 2rem 2rem 2rem;
            position: relative;
        }

        .error-number {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            margin: 0;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            background: linear-gradient(45deg, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-icon {
            position: absolute;
            top: -20px;
            right: 20px;
            font-size: 6rem;
            opacity: 0.1;
            transform: rotate(-15deg);
        }

        .error-title {
            font-size: 1.5rem;
            margin: 1rem 0 0 0;
            font-weight: 600;
        }

        .error-body {
            padding: 2.5rem 2rem;
        }

        .error-message {
            font-size: 1.1rem;
            color: var(--secondary-color);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .security-notice {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border: 1px solid var(--warning-color);
            border-radius: var(--border-radius-sm);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--warning-color);
        }

        .security-notice h5 {
            color: var(--dark-text);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .security-notice p {
            color: #856404;
            margin: 0;
            font-size: 0.95rem;
        }

        .actions-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            padding: 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--dark-text);
        }

        .action-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            color: var(--primary-color);
            text-decoration: none;
        }

        .action-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .action-card h6 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .action-card p {
            font-size: 0.9rem;
            color: var(--secondary-color);
            margin: 0;
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
            text-decoration: none;
            display: inline-block;
            color: white;
            margin: 0 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
            text-decoration: none;
        }

        .btn-secondary {
            background: var(--secondary-color);
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin: 0 0.5rem;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
            color: white;
            text-decoration: none;
        }

        .contact-info {
            background: var(--light-bg);
            border-radius: var(--border-radius-sm);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .contact-info h5 {
            color: var(--dark-text);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .contact-info p {
            color: var(--secondary-color);
            margin: 0;
            font-size: 0.95rem;
        }

        .footer-info {
            background: var(--light-bg);
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border-color);
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .shield-icon {
            animation: pulse 2s infinite;
            color: var(--warning-color);
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .lock-animation {
            animation: shake 0.5s ease-in-out infinite alternate;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            100% { transform: translateX(5px); }
        }

        @media (max-width: 768px) {
            .error-number {
                font-size: 5rem;
            }
            .error-header {
                padding: 2rem 1rem 1.5rem 1rem;
            }
            .error-body {
                padding: 2rem 1rem;
            }
            .actions-container {
                grid-template-columns: 1fr;
            }
            .btn-primary, .btn-secondary {
                display: block;
                margin: 0.5rem 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <i class="fas fa-shield-alt error-icon lock-animation"></i>
            <div class="error-number">403</div>
            <h1 class="error-title">Access Forbidden</h1>
        </div>
        
        <div class="error-body">
            <p class="error-message">
                <i class="fas fa-shield-alt shield-icon"></i>
                You don't have permission to access this resource. This area is restricted and requires 
                proper authorization to view the content.
            </p>
            
            <div class="security-notice">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Security Notice</h5>
                <p>
                    This restriction is in place to protect sensitive information and maintain system security. 
                    If you believe you should have access to this resource, please contact your administrator.
                </p>
            </div>
            
            <div class="actions-container">
                <a href="/index" class="action-card">
                    <i class="fas fa-sign-in-alt"></i>
                    <h6>Sign In</h6>
                    <p>Log in with proper credentials</p>
                </a>
                <a href="/contact" class="action-card">
                    <i class="fas fa-headset"></i>
                    <h6>Contact Support</h6>
                    <p>Request access assistance</p>
                </a>
            </div>
            
            <div class="contact-info">
                <h5><i class="fas fa-info-circle me-2"></i>Need Access?</h5>
                <p>
                    If you believe this is an error or you need access to this resource, 
                    please contact your system administrator or submit a support request with 
                    details about what you were trying to access.
                </p>
            </div>
            
            <div class="text-center">
                <a href="/" class="btn-primary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
                <a href="javascript:history.back()" class="btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Go Back
                </a>
            </div>
        </div>
        
        <div class="footer-info">
            <i class="fas fa-shield-alt me-2"></i>
            This security measure helps protect sensitive data and system resources. 
            Error Code: 403 | Access Denied
        </div>
    </div>
</body>
</html>