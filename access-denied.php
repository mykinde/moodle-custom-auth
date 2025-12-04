<?php
session_start();

// Destroy current session immediately
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #343a40, #212529);
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0 25px rgba(0,0,0,0.4);
            background-color: #fff;
            color: #000;
            max-width: 450px;
            text-align: center;
            padding: 2rem;
        }
        .btn-custom {
            border-radius: 30px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="mb-4">
            <img src="https://cdn-icons-png.flaticon.com/512/753/753345.png" alt="Access Denied" width="90">
        </div>
        <h2 class="text-danger fw-bold mb-3">Access Denied</h2>
        <p class="mb-4">You do not have permission to view this page.<br>Please log in with an admin account to continue.</p>
        <a href="admin.php" class="btn btn-primary btn-custom">Login Again</a>
    </div>
</body>
</html>
