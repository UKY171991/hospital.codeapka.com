<?php
// register.php
require_once 'inc/connection.php';
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if username or email exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Username or email already exists!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)');
            if ($stmt->execute([$username, $hash, $email])) {
                $success = 'Registration successful! You can now <a href="login.php">login</a>.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Pathology Lab Management System</title>
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
        .register-container {
            background: #f9f6fb;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            padding: 40px 32px 32px 32px;
            width: 370px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .register-icon {
            background: #cbe7ff;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }
        .register-icon i {
            color: #3498f3;
            font-size: 2.2rem;
        }
        .register-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #22223b;
            margin-bottom: 4px;
            text-align: center;
        }
        .register-subtitle {
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
        .register-btn {
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
        .register-btn:hover {
            background: #1769aa;
        }
        .login-link {
            color: #1769aa;
            text-align: center;
            font-size: 1rem;
            text-decoration: none;
            display: block;
            margin-top: 8px;
        }
        .login-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .register-container {
                width: 98vw;
                padding: 24px 4vw 18px 4vw;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <?php if ($success): ?>
            <div style="color:green; margin-bottom:12px; text-align:center;"> <?= $success ?> </div>
        <?php elseif ($error): ?>
            <div style="color:red; margin-bottom:12px; text-align:center;"> <?= $error ?> </div>
        <?php endif; ?>
        <div class="register-icon">
            <i class="fas fa-plus"></i>
        </div>
        <div class="register-title">Pathology Lab</div>
        <div class="register-subtitle">Create Your Account</div>
        <form method="post" autocomplete="off">
            <div class="form-group input-group">
                <span class="input-icon"><i class="far fa-user"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="form-group input-group">
                <span class="input-icon"><i class="far fa-envelope"></i></span>
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="form-group input-group">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                <button type="button" class="toggle-password" onclick="togglePassword()"><i class="far fa-eye" id="eyeIcon"></i></button>
            </div>
            <div class="form-group input-group">
                <span class="input-icon"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="register-btn">Register</button>
        </form>
        <a href="login.php" class="login-link">Already have an account? Sign In</a>
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
