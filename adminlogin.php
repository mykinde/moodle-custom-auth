<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "password";
$dbname = "test_db"; // Change to your database name
$port = 3307; 


$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

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
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            header("Location: admin.php");
            exit;
        } else {
            $message = "❌ Invalid password.";
        }
    } else {
        $message = "❌ No user found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #bbdefb, #64b5f6);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.container {
    max-width: 450px;
    margin-top: 80px;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}
header, footer { text-align: center; margin-bottom: 20px; }
</style>
</head>
<body>
<div class="container">
    <header>
        <h3 class="text-primary mb-3">Login</h3>
        <p><a href="register.php">Register</a> | <a href="admin.php">Admin</a></p>
    </header>

    <?php if($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <footer class="mt-4">
        <p class="small text-muted">&copy; <?= date('Y'); ?> - Secure Login</p>
        <p><a href="register.php">Register</a> | <a href="admin.php">Admin</a></p>
    </footer>
</div>
</body>
</html>
