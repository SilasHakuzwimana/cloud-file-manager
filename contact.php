<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .contact-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }

        .contact-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .contact-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .contact-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .contact-form {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .contact-header h1 {
                font-size: 2rem;
            }

            .contact-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="contact-container">
        <div class="contact-header">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you. Send us a message!</p>
        </div>

        <div class="contact-form">
            <div id="messageContainer"></div>

            <form id="contactForm" action="contact_process.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="fullName">Full Name *</label>
                    <input type="text" id="fullName" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="message">Your Message *</label>
                    <textarea id="message" name="message" placeholder="Tell us how we can help you..." required></textarea>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    Send Inquiry
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const messageContainer = document.getElementById('messageContainer');

            // Show loading state
            submitBtn.textContent = 'Sending...';
            submitBtn.classList.add('loading');

            const email = formData.get('email');
            if (!email.includes('@')) {
                messageContainer.innerHTML = '<div class="message error">Please enter a valid email.</div>';
                submitBtn.textContent = 'Send Inquiry';
                submitBtn.classList.remove('loading');
                return;
            }


            fetch('contact_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageContainer.innerHTML = '<div class="message success">Thank you! \nYour message has been sent successfully. \nWe\'ll get back to you soon.</div>';
                        setTimeout(() => {
                            messageContainer.innerHTML = '';
                        }, 5000);

                        // Reset the form
                        this.reset();
                    } else {
                        messageContainer.innerHTML = '<div class="message error">Error: ' + data.error + '</div>';
                        setTimeout(() => {
                            messageContainer.innerHTML = '';
                        }, 5000);

                    }
                })
                .catch(error => {
                    messageContainer.innerHTML = '<div class="message error">An error occurred. Please try again.</div>';
                })
                .finally(() => {
                    submitBtn.textContent = 'Send Inquiry';
                    submitBtn.classList.remove('loading');
                });
        });
    </script>
</body>

</html>