<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get account age
$created = new DateTime($user['created_at']);
$now = new DateTime();
$diff = $now->diff($created);
$account_age = $diff->days;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard | <?= htmlspecialchars($user['firstname']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
    --shadow-xl: 0 20px 40px rgba(0,0,0,0.15);
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    padding: 20px 0;
}

.dashboard-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 15px;
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-xl);
    color: white;
    position: relative;
    overflow: hidden;
}

.header-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1) rotate(0deg);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.1) rotate(180deg);
        opacity: 0.8;
    }
}

.header-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.avatar {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    backdrop-filter: blur(10px);
    border: 3px solid rgba(255,255,255,0.3);
}

.user-details h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.8rem;
    font-weight: 700;
}

.user-details p {
    margin: 0;
    opacity: 0.9;
    font-size: 1rem;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.btn-header {
    padding: 0.6rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
}

.btn-header:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-2px);
    color: white;
}

.btn-logout {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.4);
}

.btn-logout:hover {
    background: rgba(239, 68, 68, 0.3);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    animation: slideUp 0.5s ease-out;
    animation-fill-mode: both;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stat-icon.purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.stat-icon.blue {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.stat-label {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-top: 0.5rem;
}

.main-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-xl);
    overflow: hidden;
    animation: slideUp 0.5s ease-out 0.4s;
    animation-fill-mode: both;
}

.card-header-main {
    background: linear-gradient(to right, #f9fafb, #ffffff);
    padding: 2rem;
    border-bottom: 2px solid #e5e7eb;
}

.card-header-main h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.card-header-main h3 i {
    color: #667eea;
}

.card-body-main {
    padding: 0;
}

.info-group {
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
}

.info-group:last-child {
    border-bottom: none;
}

.info-group:hover {
    background: #f9fafb;
}

.info-row {
    display: flex;
    padding: 1.5rem 2rem;
    gap: 2rem;
}

.info-label {
    flex: 0 0 200px;
    font-weight: 600;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.95rem;
}

.info-label i {
    color: #667eea;
    width: 20px;
    text-align: center;
}

.info-value {
    flex: 1;
    color: #1f2937;
    font-weight: 500;
    word-break: break-word;
}

.badge-custom {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.badge-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.section-divider {
    background: linear-gradient(to right, #667eea, #764ba2);
    height: 3px;
    margin: 0;
}

.footer-dashboard {
    text-align: center;
    padding: 2rem 0;
    color: white;
    margin-top: 3rem;
}

.footer-dashboard p {
    margin: 0.5rem 0;
    opacity: 0.9;
}

.footer-links {
    display: flex;
    gap: 2rem;
    justify-content: center;
    margin-top: 1rem;
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
    padding: 0.5rem 1rem;
    border-radius: 8px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
}

.footer-links a:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .user-info {
        flex-direction: column;
    }
    
    .header-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .btn-header {
        width: 100%;
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .info-row {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .info-label {
        flex: none;
    }
}

.empty-state {
    color: #9ca3af;
    font-style: italic;
}
</style>
</head>
<body>
<div class="dashboard-container">
    <!-- Header Card -->
    <div class="header-card">
        <div class="header-content">
            <div class="user-info">
                <div class="avatar">
                    <?= strtoupper(substr($user['firstname'], 0, 1)) ?>
                </div>
                <div class="user-details">
                    <h2>Welcome back, <?= htmlspecialchars($user['firstname']) ?>!</h2>
                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
            <div class="header-actions">
                <a href="edit_profile.php" class="btn-header">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="logout.php" class="btn-header btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-label">Member Since</div>
            <div class="stat-value"><?= date('M d, Y', strtotime($user['created_at'])) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-label">Account Status</div>
            <div class="stat-value">Active</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-label">Days Active</div>
            <div class="stat-value"><?= $account_age ?> <?= $account_age == 1 ? 'day' : 'days' ?></div>
        </div>
    </div>

    <!-- Main Profile Card -->
    <div class="main-card">
        <div class="card-header-main">
            <h3>
                <i class="fas fa-user-circle"></i>
                Profile Information
            </h3>
        </div>
        
        <div class="section-divider"></div>
        
        <div class="card-body-main">
            <!-- Personal Information -->
            <div class="info-group">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-user"></i> Full Name
                    </div>
                    <div class="info-value">
                        <?= ucwords($user['firstname'] . ' ' . ($user['middlename'] ? $user['middlename'] . ' ' : '') . $user['lastname']) ?>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </div>
                    <div class="info-value">
                        <?= strtolower($user['email']) ?>
                        <span class="badge-custom badge-success ms-2">
                            <i class="fas fa-check"></i> Verified
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-fingerprint"></i> ID Number
                    </div>
                    <div class="info-value">
                        <?= strtoupper($user['idnumber']) ?>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-phone"></i> Phone Number
                    </div>
                    <div class="info-value">
                        <?= $user['phone'] ? htmlspecialchars($user['phone']) : '<span class="empty-state">Not provided</span>' ?>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="section-divider"></div>

            <div class="info-group">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-building"></i> Department
                    </div>
                    <div class="info-value">
                        <span class="badge-custom badge-primary">
                            <?= htmlspecialchars($user['department']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-university"></i> Institution
                    </div>
                    <div class="info-value">
                        <span class="badge-custom badge-primary">
                            <?= htmlspecialchars($user['institution']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="section-divider"></div>

            <div class="info-group">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-user-tag"></i> Username
                    </div>
                    <div class="info-value">
                        <?= $user['username'] ? htmlspecialchars($user['username']) : '<span class="empty-state">Not set</span>' ?>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-shield-alt"></i> Secret Code
                    </div>
                    <div class="info-value">
                        <?= $user['secret'] ? htmlspecialchars($user['secret']) : '<span class="empty-state">Not set</span>' ?>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-clock"></i> Registration Date
                    </div>
                    <div class="info-value">
                        <?= date('F j, Y \a\t g:i A', strtotime($user['created_at'])) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-dashboard">
        <p>&copy; <?= date('Y'); ?> User Dashboard. All rights reserved.</p>
        <div class="footer-links">
            <a href="register.php">
                <i class="fas fa-user-plus"></i> Register
            </a>
            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="admin.php">
                <i class="fas fa-user-shield"></i> Admin
            </a>
        </div>
        <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 1rem;">
            <i class="fas fa-lock"></i> Your data is secure and encrypted
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>