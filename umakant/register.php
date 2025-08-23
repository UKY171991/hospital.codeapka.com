<?php
// register.php
require_once 'inc/connection.php';
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = 'user'; // Default role for new registrations
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required!';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long!';
    } else {
        try {
            // Check if username already exists
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Username already exists!';
                goto render_page;
            }
            
            // Check if email already exists
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Email already exists!';
                goto render_page;
            }
            
            // Create new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, email, full_name, password_hash, role, added_by) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$username, $email, $full_name, $hashed_password, $role, 0]); // 0 for self-registration
            
            $success = 'Account created successfully! You can now login.';
        } catch (PDOException $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}

render_page:
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
            margin: 0;
            padding: 20px;
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
            position: relative;
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
            text-align: center;
            margin-top: 15px;
            font-size: 0.95rem;
            color: #555;
        }
        .login-link a {
            color: #2196f3;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 100%;
            text-align: center;
        }
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
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
        <?php if ($error): ?>
            <div class="alert alert-error"> <?= $error ?> </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"> <?= $success ?> </div>
        <?php endif; ?>
        
        <div class="register-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="register-title">Create Account</div>
        <div class="register-subtitle">Pathology Lab Management System</div>
        
        <form method="post" autocomplete="off">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-signature"></i></span>
                    <input type="text" class="form-control" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($full_name ?? '') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', 'eyeIcon')">
                        <i class="far fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', 'confirmEyeIcon')">
                        <i class="far fa-eye" id="confirmEyeIcon"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="register-btn">Create Account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>
    
    <script>
        function togglePassword(inputId, eyeId) {
            var pwd = document.getElementById(inputId);
            var eye = document.getElementById(eyeId);
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