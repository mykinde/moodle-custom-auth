<?php
session_start();

// --- DATABASE CONNECTION ---
$host = "localhost";
$user = "root";
$pass = "password";
$dbname = "test_db";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- LOGIN HANDLER ---
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            if ($user['role'] !== 'admin') {
                header("Location: access-denied.php");
                exit();
            }
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "No account found with that email.";
    }
    $stmt->close();
}

// --- LOGOUT HANDLER ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- REDIRECT NON-ADMINS ---
if (isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'admin') {
    header("Location: access-denied.php");
    exit();
}

// --- PAGINATION + SEARCH ---
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$searchSql = "";
$params = [];
if ($search) {
    $searchSql = "WHERE firstname LIKE ? OR lastname LIKE ? OR CONCAT(firstname, ' ', lastname) LIKE ?";
    $like = "%$search%";
    $params = [$like, $like, $like];
}

$query = "SELECT * FROM users $searchSql ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($query);

if ($search) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$totalQuery = "SELECT COUNT(*) as total FROM users " . ($search ? $searchSql : "");
$totalStmt = $conn->prepare($totalQuery);
if ($search) $totalStmt->bind_param(str_repeat("s", count($params)), ...$params);
$totalStmt->execute();
$totalRes = $totalStmt->get_result()->fetch_assoc();
$totalUsers = $totalRes['total'];
$totalPages = ceil($totalUsers / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: white;
        }

        .login-header {
            background: var(--gradient-bg);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .login-body {
            padding: 2.5rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: var(--gradient-bg);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .dashboard-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .welcome-badge {
            background: var(--gradient-bg);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .search-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .stats-badge {
            background: var(--gradient-bg);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table-card .card-header {
            background: var(--gradient-bg);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: none;
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.01);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
        }

        .role-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .role-admin {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .role-user {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .pagination .page-link {
            border: none;
            color: #667eea;
            font-weight: 600;
            margin: 0 0.2rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .pagination .page-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
        }

        .btn-gradient {
            background: var(--gradient-bg);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .input-group-text {
            background: var(--gradient-bg);
            border: none;
            color: white;
        }

        .alert {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['user'])): ?>
    <!-- LOGIN FORM -->
    <div class="login-container">
        <div class="col-md-5 col-lg-4">
            <div class="login-card">
                <div class="login-header">
                    <i class="fas fa-user-shield"></i>
                    <h3 class="mb-0">Admin Portal</h3>
                    <p class="mb-0 mt-2 opacity-75">Sign in to continue</p>
                </div>
                <div class="login-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-envelope me-2 text-muted"></i>Email Address
                            </label>
                            <input type="email" name="email" required class="form-control form-control-lg" placeholder="Enter your email">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-muted"></i>Password
                            </label>
                            <input type="password" name="password" required class="form-control form-control-lg" placeholder="Enter your password">
                        </div>
                        <button type="submit" name="login" class="btn btn-primary btn-login btn-lg w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- DASHBOARD -->
    <div class="container-fluid py-4">
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="mb-2">
                        <i class="fas fa-tachometer-alt me-2" style="color: #667eea;"></i>Admin Dashboard
                    </h2>
                    <span class="welcome-badge">
                        <i class="fas fa-user me-2"></i>Welcome, <?= htmlspecialchars($_SESSION['user']['firstname']) ?>
                    </span>
                </div>
                <a href="?logout=1" class="btn btn-danger btn-lg mt-3 mt-md-0">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- SEARCH SECTION -->
        <div class="search-card">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search me-2 text-muted"></i>Search Users
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="fas fa-user-search"></i></span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                               class="form-control" placeholder="Search by first name or last name...">
                    </div>
                </div>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-gradient btn-lg me-2">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                    <a href="login_dashboard.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- USERS TABLE -->
        <div class="table-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>Users Directory
                    </h4>
                    <span class="stats-badge">
                        <i class="fas fa-database me-2"></i><?= $totalUsers ?> Total Users
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>#</th>
                                <th><i class="fas fa-user me-2"></i>First Name</th>
                                <th><i class="fas fa-user me-2"></i>Last Name</th>
                                <th><i class="fas fa-envelope me-2"></i>Email</th>
                                <th><i class="fas fa-id-card me-2"></i>ID Number</th>
                                <th><i class="fas fa-building me-2"></i>Department</th>
                                <th><i class="fas fa-university me-2"></i>Institution</th>
                                <th><i class="fas fa-user-tag me-2"></i>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($result->num_rows > 0):
                            $i = $offset + 1;
                            while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td class="fw-bold text-muted"><?= $i++ ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['firstname']) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['lastname']) ?></td>
                                <td><i class="fas fa-envelope text-muted me-2"></i><?= htmlspecialchars($row['email']) ?></td>
                                <td><span class="badge bg-light text-dark"><?= htmlspecialchars($row['idnumber']) ?></span></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td><?= htmlspecialchars($row['institution']) ?></td>
                                <td>
                                    <span class="role-badge <?= $row['role'] === 'admin' ? 'role-admin' : 'role-user' ?>">
                                        <i class="fas <?= $row['role'] === 'admin' ? 'fa-user-shield' : 'fa-user' ?> me-1"></i>
                                        <?= htmlspecialchars($row['role']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">No users found</h5>
                                    <p class="text-muted">Try adjusting your search criteria</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                <div class="p-4">
                    <nav>
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>