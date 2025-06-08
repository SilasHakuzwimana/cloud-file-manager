<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
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
            background: linear-gradient(135deg, var(--danger-color), #e74c3c);
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
            transform: rotate(15deg);
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

        .search-container {
            background: var(--light-bg);
            border-radius: var(--border-radius-sm);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .search-container h5 {
            color: var(--dark-text);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .search-input {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1rem;
            width: 100%;
            font-size: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
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
            font-size: 0.9rem;
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

        .suggestions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .suggestion-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            padding: 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--dark-text);
        }

        .suggestion-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            color: var(--primary-color);
            text-decoration: none;
        }

        .suggestion-card i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .suggestion-card h6 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .suggestion-card p {
            font-size: 0.9rem;
            color: var(--secondary-color);
            margin: 0;
        }

        .footer-info {
            background: var(--light-bg);
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border-color);
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .animate-bounce {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
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
            .suggestions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <i class="fas fa-search error-icon"></i>
            <div class="error-number">404</div>
            <h1 class="error-title">Page Not Found</h1>
        </div>
        
        <div class="error-body">
            <p class="error-message">
                <i class="fas fa-exclamation-triangle animate-bounce" style="color: var(--warning-color); margin-right: 0.5rem;"></i>
                Oops! The page you're looking for seems to have wandered off into the digital wilderness. 
                Don't worry, we'll help you find your way back home.
            </p>
            
            <div class="search-container">
                <h5><i class="fas fa-search me-2"></i>Search Our Site</h5>
                <input type="text" class="search-input" placeholder="What are you looking for?">
                <button class="btn-primary">
                    <i class="fas fa-search me-2"></i>Search
                </button>
            </div>
            
            <div class="suggestions">
                <a href="/" class="suggestion-card">
                    <i class="fas fa-home"></i>
                    <h6>Homepage</h6>
                    <p>Return to our main page</p>
                </a>
                <a href="/dashboard" class="suggestion-card">
                    <i class="fas fa-tachometer-alt"></i>
                    <h6>Dashboard</h6>
                    <p>Access your account</p>
                </a>
                <a href="/files" class="suggestion-card">
                    <i class="fas fa-folder"></i>
                    <h6>Files</h6>
                    <p>Manage your files</p>
                </a>
                <a href="/help" class="suggestion-card">
                    <i class="fas fa-question-circle"></i>
                    <h6>Help Center</h6>
                    <p>Get support</p>
                </a>
            </div>
            
            <div class="text-center">
                <a href="/" class="btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
                <a href="javascript:history.back()" class="btn-secondary">
                    <i class="fas fa-undo me-2"></i>Go Back
                </a>
            </div>
        </div>
        
        <div class="footer-info">
            <i class="fas fa-info-circle me-2"></i>
            If you believe this is an error, please contact our support team with the URL you were trying to access.
        </div>
    </div>
</body>
</html>