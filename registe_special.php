<?php
// --- DATABASE CONNECTION ---
$host = "localhost";
$user = "root";
$pass = "password";
$dbname = "test_db"; // Change to your database name
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

    // Validation
    if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email address.";
    } elseif (strlen($password) < 6) {
        $message = "❌ Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } elseif (!preg_match('/^[A-Za-z0-9]{7,14}$/', $fields['idnumber'])) {
        $message = "❌ ID Number must be numeric and max 14 digits.";
    } elseif ($fields['idnumber'] !== $fields['confirm_id']) {
        $message = "❌ ID Number confirmation does not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email=? OR idnumber=?");
        $check->bind_param("ss", $fields['email'], $fields['idnumber']);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $message = "⚠️ Email or ID Number already exists.";
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
                $message = "✅ Registration successful. You may now <a href='login.php'>login</a>.";
                foreach ($fields as $key => $val) $fields[$key] = ''; // clear form
            } else {
                $message = "❌ Registration failed. Please try again.";
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
<title>User Registration</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #e3f2fd, #90caf9);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.container {
    max-width: 650px;
    background: #fff;
    margin-top: 50px;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}
header, footer {
    text-align: center;
    margin-bottom: 20px;
}
a {
    text-decoration: none;
}
</style>
</head>
<body>
<div class="container">
    <header>
        <h3 class="text-primary mb-3">User Registration</h3>
        <p><a href="login.php" class="text-decoration-none">Login</a> | <a href="admin.php" class="text-decoration-none">Admin</a></p>
    </header>

    <?php if($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">First Name</label>
            <input type="text" name="firstname" class="form-control" required value="<?= $fields['firstname'] ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middlename" class="form-control" value="<?= $fields['middlename'] ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Last Name</label>
            <input type="text" name="lastname" class="form-control" required value="<?= $fields['lastname'] ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Email (unique)</label>
            <input type="email" name="email" class="form-control" required value="<?= $fields['email'] ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Phone (optional)</label>
            <input type="text" name="phone" class="form-control" value="<?= $fields['phone'] ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Password (min 6)</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">ID Number (max 11 digits)</label>
            <input type="text" name="idnumber" class="form-control" required maxlength="15" value="<?= $fields['idnumber'] ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Confirm ID Number</label>
            <input type="text" name="confirm_id" class="form-control" required maxlength="15" value="<?= $fields['confirm_id'] ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Department</label>
            <select name="department" class="form-select" required>
                <option value="">--Select--</option>
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
            <label class="form-label">Institution</label>
            <select name="institution" class="form-select" required>
                <option value="">--Select--</option>
                <?php
                $institutions = ["Science", "Management", "Technology", "Arts"];
                foreach ($institutions as $i) {
                    $selected = ($fields['institution'] == $i) ? "selected" : "";
                    echo "<option value='$i' $selected>$i</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Username (optional)</label>
            <input type="text" name="username" class="form-control" value="<?= $fields['username'] ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Secret (optional)</label>
            <input type="text" name="secret" class="form-control" value="<?= $fields['secret'] ?>">
        </div>

        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-primary btn-lg w-100">Register</button>
        </div>
    </form>

    <footer class="mt-4">
        <p class="small text-muted">&copy; <?= date('Y'); ?> - Simple Register Form</p>
        <p><a href="login.php">Login</a> | <a href="admin.php">Admin</a></p>
    </footer>
</div>
</body>
</html>
