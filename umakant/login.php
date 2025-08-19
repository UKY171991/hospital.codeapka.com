<?php
// login.php
require_once 'inc/connection.php';
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        // Login success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Pathology Lab Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #6a8dff 0%, #a084ee 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .login-container {
            background: #f9f6fb;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            padding: 40px 32px 32px 32px;
            width: 370px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-icon {
            background: #cbe7ff;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }
        .login-icon i {
            color: #3498f3;
            font-size: 2.2rem;
        }
        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #22223b;
            margin-bottom: 4px;
            text-align: center;
        }
        .login-subtitle {
            color: #7a7a7a;
            font-size: 1rem;
            margin-bottom: 22px;
            text-align: center;
        }
        .form-group {
            width: 100%;
            margin-bottom: 18px;
        }
        .form-control {
            width: 100%;
            padding: 12px 40px 12px 38px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            background: #fff;
            outline: none;
            transition: border 0.2s;
        }
        .form-control:focus {
            border-color: #3498f3;
        }
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #b0b0b0;
            font-size: 1.1rem;
        }
        .input-group {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #b0b0b0;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
        }
        .login-btn {
            width: 100%;
            background: #2196f3;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 8px;
            margin-bottom: 18px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .login-btn:hover {
            background: #1769aa;
        }
        .demo-box {
            width: 100%;
            background: #eaf4ff;
            border: 1px solid #b3d8fd;
            border-radius: 10px;
            padding: 14px 18px;
            color: #1769aa;
            font-size: 0.98rem;
            margin-top: 8px;
        }
        .demo-title {
            font-weight: 500;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            font-size: 1rem;
        }
        .demo-title i {
            margin-right: 6px;
        }
        .demo-credentials {
            margin-left: 18px;
            font-size: 0.97rem;
        }
        @media (max-width: 480px) {
            .login-container {
                width: 98vw;
                padding: 24px 4vw 18px 4vw;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <?php if ($error): ?>
            <div style="color:red; margin-bottom:12px; text-align:center;"> <?= $error ?> </div>
        <?php endif; ?>
        <div class="login-icon">
            <i class="fas fa-plus"></i>
        </div>
        <div class="login-title">Pathology Lab</div>
        <div class="login-subtitle">Management System</div>
        <form method="post" autocomplete="off">
            <div class="form-group input-group">
                <span class="input-icon"><i class="far fa-user"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="form-group input-group">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                <button type="button" class="toggle-password" onclick="togglePassword()"><i class="far fa-eye" id="eyeIcon"></i></button>
            </div>
            <button type="submit" class="login-btn">Sign In</button>
        </form>
        <div class="demo-box">
            <div class="demo-title"><i class="fas fa-info-circle"></i> Demo Credentials</div>
            <div class="demo-credentials">Admin: <b>admin</b> / <b>admin123</b></div>
            <div class="demo-credentials">User: <b>user</b> / <b>user123</b></div>
        </div>
    </div>
    <script>
        function togglePassword() {
            var pwd = document.getElementById('password');
            var eye = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
