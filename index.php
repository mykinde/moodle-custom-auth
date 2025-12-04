
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Exam Portal | Welcome</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-color: #10b981;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
    --shadow-xl: 0 20px 40px rgba(0,0,0,0.15);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    overflow-x: hidden;
}

/* Navigation */
.navbar-custom {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: var(--shadow-lg);
    padding: 1rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.navbar-brand {
    font-size: 1.5rem;
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-link-custom {
    color: #374151;
    font-weight: 600;
    padding: 0.5rem 1.2rem;
    margin: 0 0.3rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
}

.nav-link-custom:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    transform: translateY(-2px);
}

.nav-link-custom.active {
    background: var(--primary-gradient);
    color: white;
}

/* Hero Section */
.hero-section {
    padding: 6rem 0;
    text-align: center;
    color: white;
    animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    text-shadow: 0 4px 6px rgba(0,0,0,0.2);
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 1.3rem;
    margin-bottom: 3rem;
    opacity: 0.95;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.hero-icon {
    font-size: 5rem;
    margin-bottom: 2rem;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

/* Feature Cards */
.features-section {
    padding: 4rem 0;
}

.feature-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: var(--shadow-xl);
    transition: all 0.4s ease;
    border: none;
    height: 100%;
    animation: slideUp 0.6s ease-out;
    animation-fill-mode: both;
}

.feature-card:nth-child(1) { animation-delay: 0.1s; }
.feature-card:nth-child(2) { animation-delay: 0.2s; }
.feature-card:nth-child(3) { animation-delay: 0.3s; }
.feature-card:nth-child(4) { animation-delay: 0.4s; }
.feature-card:nth-child(5) { animation-delay: 0.5s; }

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

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
}

.feature-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary-gradient);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1.5rem auto;
    box-shadow: var(--shadow-md);
}

.feature-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
}

.feature-description {
    color: #6b7280;
    line-height: 1.7;
    margin-bottom: 1.5rem;
}

.btn-feature {
    background: var(--primary-gradient);
    border: none;
    padding: 0.8rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 10px;
    color: white;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    display: inline-block;
}

.btn-feature:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: white;
    background: linear-gradient(135deg, #7c93f5 0%, #8b5cb8 100%);
}

/* CTA Section */
.cta-section {
    padding: 5rem 0;
    text-align: center;
    color: white;
}

.cta-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 3rem;
    box-shadow: var(--shadow-xl);
    max-width: 800px;
    margin: 0 auto;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

.cta-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 2rem;
}

.btn-cta {
    padding: 1rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-md);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.7rem;
}

.btn-cta-primary {
    background: white;
    color: #667eea;
}

.btn-cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    color: #667eea;
}

.btn-cta-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid white;
}

.btn-cta-secondary:hover {
    background: white;
    color: #667eea;
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

/* Footer */
.footer-custom {
    background: rgba(0, 0, 0, 0.2);
    color: white;
    padding: 3rem 0 1.5rem 0;
    margin-top: 4rem;
}

.footer-content {
    text-align: center;
}

.footer-links {
    display: flex;
    gap: 2rem;
    justify-content: center;
    margin: 1.5rem 0;
    flex-wrap: wrap;
}

.footer-links a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-links a:hover {
    transform: translateX(5px);
    opacity: 0.8;
}

.footer-bottom {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    opacity: 0.8;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .feature-card {
        margin-bottom: 2rem;
    }
    
    .cta-title {
        font-size: 2rem;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-cta {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

/* Stats Section */
.stats-section {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 3rem 0;
    margin: 3rem 0;
}

.stat-item {
    text-align: center;
    color: white;
    padding: 1.5rem;
}

.stat-number {
    font-size: 3rem;
    font-weight: 800;
    display: block;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1.1rem;
    opacity: 0.9;
}
</style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-graduation-cap"></i> Exam Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link-custom active" href="#"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-custom" href="exam1.php"><i class="fas fa-file-alt"></i> Exam-1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-custom" href="exam2.php"><i class="fas fa-clipboard-list"></i> Exam-2</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-custom" href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-custom" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-custom" href="admin.php"><i class="fas fa-user-shield"></i> Admin</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h1 class="hero-title">Welcome to Exam Portal</h1>
        <p class="hero-subtitle">Your comprehensive online examination system. Take exams, track your progress, and achieve academic excellence all in one place.</p>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <span class="stat-number"><i class="fas fa-users"></i> 2,500+</span>
                    <span class="stat-label">Active Students</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <span class="stat-number"><i class="fas fa-file-alt"></i> 150+</span>
                    <span class="stat-label">Exams Available</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <span class="stat-number"><i class="fas fa-chart-line"></i> 95%</span>
                    <span class="stat-label">Success Rate</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <span class="stat-number"><i class="fas fa-clock"></i> 24/7</span>
                    <span class="stat-label">Access</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="feature-title">Exam-1</h3>
                    <p class="feature-description">Take your first examination with comprehensive questions covering all course materials. Get instant results and detailed feedback.</p>
                    <a href="exam1.php" class="btn-feature">
                        <i class="fas fa-arrow-right"></i> Start Exam-1
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3 class="feature-title">Exam-2</h3>
                    <p class="feature-description">Continue your assessment journey with advanced level questions. Test your knowledge and improve your skills.</p>
                    <a href="exam2.php" class="btn-feature">
                        <i class="fas fa-arrow-right"></i> Start Exam-2
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="feature-title">Register</h3>
                    <p class="feature-description">Create your account to access all exams and track your progress. Quick and easy registration process.</p>
                    <a href="register.php" class="btn-feature">
                        <i class="fas fa-arrow-right"></i> Sign Up Now
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <h3 class="feature-title">Login</h3>
                    <p class="feature-description">Already have an account? Login to access your dashboard, view results, and continue your examination journey.</p>
                    <a href="login.php" class="btn-feature">
                        <i class="fas fa-arrow-right"></i> Login Here
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="feature-title">Admin Panel</h3>
                    <p class="feature-description">Administrative access for managing exams, users, and viewing comprehensive reports and analytics.</p>
                    <a href="admin.php" class="btn-feature">
                        <i class="fas fa-arrow-right"></i> Admin Access
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="feature-title">Track Progress</h3>
                    <p class="feature-description">Monitor your performance with detailed analytics, scores, and personalized recommendations for improvement.</p>
                    <a href="#" class="btn-feature">
                        <i class="fas fa-arrow-right"></i> View Progress
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <h2 class="cta-title">Ready to Get Started?</h2>
            <p style="font-size: 1.2rem; margin-bottom: 0;">Join thousands of students already using our platform to excel in their examinations.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn-cta btn-cta-primary">
                    <i class="fas fa-user-plus"></i> Create Account
                </a>
                <a href="login.php" class="btn-cta btn-cta-secondary">
                    <i class="fas fa-sign-in-alt"></i> Login Now
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-custom">
    <div class="container">
        <div class="footer-content">
            <h4><i class="fas fa-graduation-cap"></i> Exam Portal</h4>
            <p>Your trusted partner in online examination and assessment</p>
            
            <div class="footer-links">
                <a href="#"><i class="fas fa-home"></i> Home</a>
                <a href="exam1.php"><i class="fas fa-file-alt"></i> Exam-1</a>
                <a href="exam2.php"><i class="fas fa-clipboard-list"></i> Exam-2</a>
                <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="admin.php"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Exam Portal. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>