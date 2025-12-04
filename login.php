<?php
session_start();
// Database connection
$host = "localhost";
$user = "root";
$pass = "password";
$dbname = "test_db";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, firstname, lastname, email, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid password. Please try again.";
            $message_type = "danger";
        }
    } else {
        $message = "No account found with that email address.";
        $message_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | Secure Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
    --shadow-xl: 0 20px 40px rgba(0,0,0,0.15);
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-container {
    max-width: 480px;
    width: 100%;
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-custom {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: var(--shadow-xl);
    border: none;
    overflow: hidden;
}

.card-header-custom {
    background: var(--primary-gradient);
    color: white;
    padding: 3rem 2rem;
    text-align: center;
    border: none;
    position: relative;
    overflow: hidden;
}

.card-header-custom::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
}

.login-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 1;
}

.login-icon i {
    font-size: 2.5rem;
    color: white;
}

.card-header-custom h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
}

.card-header-custom p {
    margin: 0;
    opacity: 0.95;
    font-size: 1rem;
    position: relative;
    z-index: 1;
}

.card-body-custom {
    padding: 2.5rem 2rem;
}

.nav-links {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    margin-top: 1rem;
    position: relative;
    z-index: 1;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.nav-links a:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-2px);
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.6rem;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-label i {
    color: #667eea;
    font-size: 0.9rem;
}

.form-control {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.9rem 1.2rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    background: white;
    outline: none;
}

.form-control:hover {
    border-color: #9ca3af;
}

.form-control::placeholder {
    color: #9ca3af;
}

.password-wrapper {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 0.5rem;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: #667eea;
}

.btn-login {
    background: var(--primary-gradient);
    border: none;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 12px;
    color: white;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-md);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 1rem;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    background: linear-gradient(135deg, #7c93f5 0%, #8b5cb8 100%);
}

.btn-login:active {
    transform: translateY(0);
}

.alert-custom {
    border-radius: 12px;
    border: none;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 500;
    box-shadow: var(--shadow-sm);
    animation: slideDown 0.3s ease-out;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.alert-custom i {
    font-size: 1.2rem;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 2rem 0 1.5rem;
    color: #9ca3af;
    font-size: 0.9rem;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e5e7eb;
}

.divider span {
    padding: 0 1rem;
    font-weight: 500;
}

.quick-links {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.quick-links a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.quick-links a:hover {
    color: #764ba2;
    transform: translateX(3px);
}

.footer-custom {
    text-align: center;
    padding: 1.5rem 0 0 0;
    color: white;
    margin-top: 2rem;
}

.footer-custom p {
    margin: 0.5rem 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

.security-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.1);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    margin-top: 1rem;
    backdrop-filter: blur(10px);
}

.security-badge i {
    color: #10b981;
}

@media (max-width: 576px) {
    .card-body-custom {
        padding: 2rem 1.5rem;
    }
    
    .card-header-custom {
        padding: 2.5rem 1.5rem;
    }
    
    .nav-links {
        flex-direction: column;
        gap: 0.8rem;
    }
    
    .quick-links {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.remember-me input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #667eea;
}

.remember-me label {
    margin: 0;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    font-size: 0.9rem;
}
</style>
</head>
<body>
<div class="login-container">
    <div class="card-custom">
        <div class="card-header-custom">
            <div class="login-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2>Welcome Back</h2>
            <p>Sign in to access your account</p>
            <div class="nav-links">
                <a href="register.php">
                    <i class="fas fa-user-plus"></i> Create Account
                </a>
                <a href="dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
            <div class="security-badge">
                <i class="fas fa-shield-alt"></i>
                <span>Secure Login</span>
            </div>
        </div>

        <div class="card-body-custom">
            <?php if($message): ?>
                <div class="alert alert-<?= $message_type ?> alert-custom alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($message) ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm">
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="your.email@example.com" 
                           required
                           autocomplete="email">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-key"></i> Password
                    </label>
                    <div class="password-wrapper">
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="form-control" 
                               placeholder="Enter your password" 
                               required
                               autocomplete="current-password">
                        <button type="button" 
                                class="password-toggle" 
                                onclick="togglePassword()"
                                aria-label="Toggle password visibility">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="rememberMe" name="remember">
                    <label for="rememberMe">Remember me</label>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="divider">
                <span>Need Help?</span>
            </div>

            <div class="quick-links">
                <a href="forgot_password.php">
                    <i class="fas fa-question-circle"></i> Forgot Password?
                </a>
                <a href="register.php">
                    <i class="fas fa-user-plus"></i> Create Account
                </a>
            </div>
        </div>
    </div>

    <div class="footer-custom">
        <p>&copy; <?= date('Y'); ?> User Login Portal. All rights reserved.</p>
        <p style="font-size: 0.85rem; opacity: 0.8;">
            <i class="fas fa-lock"></i> Your connection is secure and encrypted
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Add enter key support for password toggle
document.getElementById('password').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('loginForm').submit();
    }
});
</script>
</body>
</html>