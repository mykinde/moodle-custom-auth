<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "password";
$dbname = "test_db";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100),
    lastname VARCHAR(100),
    middlename VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    idnumber VARCHAR(11) UNIQUE,
    department VARCHAR(100),
    institution VARCHAR(100),
    phone VARCHAR(50),
    username VARCHAR(100),
    secret VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$message = "";
$message_type = "";

// Initialize persistence variables
$fields = [
    'firstname' => '', 'lastname' => '', 'middlename' => '', 'email' => '',
    'idnumber' => '', 'confirm_id' => '', 'department' => '', 'institution' => '',
    'phone' => '', 'username' => '', 'secret' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($fields as $key => $val) {
        $fields[$key] = htmlspecialchars(trim($_POST[$key] ?? ''));
    }

    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Case formatting
    $fields['firstname']  = ucwords(strtolower($fields['firstname']));
    $fields['lastname']   = ucwords(strtolower($fields['lastname']));
    $fields['middlename'] = ucwords(strtolower($fields['middlename']));
    $fields['email']      = strtolower($fields['email']);
    $fields['idnumber']   = strtolower($fields['idnumber']);
    $fields['confirm_id'] = strtolower($fields['confirm_id']);

    // Validation
    if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
        $message_type = "danger";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $message_type = "danger";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = "danger";
    } elseif (!preg_match('/^[0-9]{7,11}$/', $fields['idnumber'])) {
        $message = "ID Number must be numeric and max 11 digits.";
        $message_type = "danger";
    } elseif ($fields['idnumber'] !== $fields['confirm_id']) {
        $message = "ID Number confirmation does not match.";
        $message_type = "danger";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email=? OR idnumber=?");
        $check->bind_param("ss", $fields['email'], $fields['idnumber']);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $message = "Email or ID Number already exists.";
            $message_type = "warning";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users 
                (firstname, lastname, middlename, email, password, idnumber, department, institution, phone, username, secret)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "sssssssssss",
                $fields['firstname'], $fields['lastname'], $fields['middlename'], $fields['email'],
                $hashed, $fields['idnumber'], $fields['department'], $fields['institution'],
                $fields['phone'], $fields['username'], $fields['secret']
            );
            if ($stmt->execute()) {
                $message = "Registration successful! You may now <a href='login.php' class='alert-link fw-bold'>login here</a>.";
                $message_type = "success";
                foreach ($fields as $key => $val) $fields[$key] = '';
            } else {
                $message = "Registration failed. Please try again.";
                $message_type = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>User Registration | Create Your Account</title>
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

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    padding: 20px 0;
}

.registration-container {
    max-width: 750px;
    margin: 0 auto;
}

.card-custom {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: var(--shadow-xl);
    border: none;
    overflow: hidden;
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

.card-header-custom {
    background: var(--primary-gradient);
    color: white;
    padding: 2.5rem 2rem;
    text-align: center;
    border: none;
}

.card-header-custom h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header-custom p {
    margin: 0;
    opacity: 0.95;
    font-size: 1rem;
}

.card-body-custom {
    padding: 2.5rem 2rem;
}

.nav-links {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    margin-top: 1rem;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 0.4rem 1rem;
    border-radius: 8px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
}

.nav-links a:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-2px);
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.form-label i {
    color: #667eea;
    font-size: 0.85rem;
}

.form-control, .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    background: white;
    outline: none;
}

.form-control:hover, .form-select:hover {
    border-color: #9ca3af;
}

.form-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1f2937;
    margin: 2rem 0 1.5rem 0;
    padding-bottom: 0.75rem;
    border-bottom: 3px solid #667eea;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.form-section-title i {
    color: #667eea;
    font-size: 1.3rem;
}

.btn-submit {
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
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    background: linear-gradient(135deg, #7c93f5 0%, #8b5cb8 100%);
}

.btn-submit:active {
    transform: translateY(0);
}

.alert-custom {
    border-radius: 12px;
    border: none;
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    font-weight: 500;
    box-shadow: var(--shadow-sm);
    animation: slideDown 0.3s ease-out;
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

.footer-custom {
    text-align: center;
    padding: 2rem 0 1rem 0;
    color: white;
    margin-top: 2rem;
}

.footer-custom p {
    margin: 0.5rem 0;
    opacity: 0.9;
}

.footer-links {
    display: flex;
    gap: 2rem;
    justify-content: center;
    margin-top: 1rem;
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

.required-asterisk {
    color: #ef4444;
    margin-left: 2px;
}

.input-group-text {
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    border-right: none;
    border-radius: 10px 0 0 10px;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 10px 10px 0;
}

.input-group:focus-within .input-group-text {
    border-color: #667eea;
    background: white;
}

@media (max-width: 768px) {
    .card-body-custom {
        padding: 1.5rem 1rem;
    }
    
    .card-header-custom {
        padding: 2rem 1rem;
    }
    
    .form-section-title {
        font-size: 1rem;
    }
}

.tooltip-icon {
    color: #9ca3af;
    font-size: 0.85rem;
    cursor: help;
    margin-left: 0.3rem;
}
</style>
</head>
<body>
<div class="registration-container">
    <div class="card-custom">
        <div class="card-header-custom">
            <h2><i class="fas fa-user-plus"></i> User Registration</h2>
            <p>Create your account to get started</p>
            <div class="nav-links">
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="admin.php"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
        </div>

        <div class="card-body-custom">
            <?php if($message): ?>
                <div class="alert alert-<?= $message_type ?> alert-custom alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : ($message_type === 'danger' ? 'exclamation-circle' : 'exclamation-triangle') ?>"></i>
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="row g-3">
                <!-- Personal Information Section -->
                <div class="col-12">
                    <div class="form-section-title">
                        <i class="fas fa-user"></i> Personal Information
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-id-badge"></i> First Name<span class="required-asterisk">*</span>
                    </label>
                    <input type="text" name="firstname" class="form-control" required value="<?= $fields['firstname'] ?>" placeholder="Enter first name">
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-id-badge"></i> Middle Name
                    </label>
                    <input type="text" name="middlename" class="form-control" value="<?= $fields['middlename'] ?>" placeholder="Enter middle name">
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-id-badge"></i> Last Name<span class="required-asterisk">*</span>
                    </label>
                    <input type="text" name="lastname" class="form-control" required value="<?= $fields['lastname'] ?>" placeholder="Enter last name">
                </div>

                <!-- Contact Information Section -->
                <div class="col-12">
                    <div class="form-section-title">
                        <i class="fas fa-address-book"></i> Contact Information
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email Address<span class="required-asterisk">*</span>
                    </label>
                    <input type="email" name="email" class="form-control" required value="<?= $fields['email'] ?>" placeholder="your.email@example.com">
                    <small class="text-muted">Must be unique</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-phone"></i> Phone Number
                    </label>
                    <input type="text" name="phone" class="form-control" value="<?= $fields['phone'] ?>" placeholder="+234 XXX XXX XXXX">
                </div>

                <!-- Security Information Section -->
                <div class="col-12">
                    <div class="form-section-title">
                        <i class="fas fa-lock"></i> Security Information
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-key"></i> Password<span class="required-asterisk">*</span>
                    </label>
                    <input type="password" name="password" class="form-control" required placeholder="Min 6 characters">
                    <small class="text-muted">Minimum 6 characters</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-key"></i> Confirm Password<span class="required-asterisk">*</span>
                    </label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Re-enter password">
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-fingerprint"></i> ID Number<span class="required-asterisk">*</span>
                    </label>
                    <input type="text" name="idnumber" class="form-control" required maxlength="11" value="<?= $fields['idnumber'] ?>" placeholder="Enter ID number">
                    <small class="text-muted">Max 11 digits, numeric only</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-fingerprint"></i> Confirm ID Number<span class="required-asterisk">*</span>
                    </label>
                    <input type="text" name="confirm_id" class="form-control" required maxlength="11" value="<?= $fields['confirm_id'] ?>" placeholder="Re-enter ID number">
                </div>

                <!-- Academic Information Section -->
                <div class="col-12">
                    <div class="form-section-title">
                        <i class="fas fa-graduation-cap"></i> Academic Information
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-building"></i> Department<span class="required-asterisk">*</span>
                    </label>
                    <select name="department" class="form-select" required>
                        <option value="">-- Select Department --</option>
                        <?php
                        $departments = ["Business", "Nursing", "Law", "Engineering"];
                        foreach ($departments as $d) {
                            $selected = ($fields['department'] == $d) ? "selected" : "";
                            echo "<option value='$d' $selected>$d</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-university"></i> Institution<span class="required-asterisk">*</span>
                    </label>
                    <select name="institution" class="form-select" required>
                        <option value="">-- Select Institution --</option>
                        <?php
                        $institutions = ["Science", "Management", "Technology", "Arts"];
                        foreach ($institutions as $i) {
                            $selected = ($fields['institution'] == $i) ? "selected" : "";
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Additional Information Section -->
                <div class="col-12">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i> Additional Information
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-user-tag"></i> Username
                    </label>
                    <input type="text" name="username" class="form-control" value="<?= $fields['username'] ?>" placeholder="Choose a username">
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-shield-alt"></i> Secret Code
                    </label>
                    <input type="text" name="secret" class="form-control" value="<?= $fields['secret'] ?>" placeholder="Optional secret code">
                </div>

                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-submit w-100">
                        <i class="fas fa-user-check"></i> Create Account
                    </button>
                </div>

                <div class="col-12 text-center mt-3">
                    <small class="text-muted">
                        <span class="required-asterisk">*</span> Required fields
                    </small>
                </div>
            </form>
        </div>
    </div>

    <div class="footer-custom">
        <p>&copy; <?= date('Y'); ?> - User Registration System</p>
        <div class="footer-links">
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="admin.php"><i class="fas fa-user-shield"></i> Admin Panel</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>