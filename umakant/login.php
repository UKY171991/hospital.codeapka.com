<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // detect AJAX either via X-Requested-With or explicit ajax param
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (!empty($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1);

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$error]); exit; }
    } else {
        require_once 'inc/connection.php';
        try {
            // Check if user exists and is active (only admin allowed to access admin UI)
            $stmt = $pdo->prepare("SELECT id, username, password, role, is_active, full_name, email, created_at, updated_at, expire, added_by FROM users WHERE username = ? AND is_active = 1 AND role = 'admin'");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Update last login (MySQL)
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);

                // Set session variables (include a few helpful fields)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'] ?? '';
                $_SESSION['email'] = $user['email'] ?? '';
                $_SESSION['created_at'] = $user['created_at'] ?? '';
                $_SESSION['updated_at'] = $user['updated_at'] ?? '';
                $_SESSION['expire'] = $user['expire'] ?? '';
                $_SESSION['added_by'] = $user['added_by'] ?? null;

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success'=>true,'message'=>'Login successful','redirect'=>'index.php']);
                    exit();
                }
                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid username or password';
                if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$error]); exit; }
            }
        } catch (Exception $e) {
            // return JSON for AJAX, avoid 500 which triggers generic error in browser
            $msg = 'Server error while authenticating.';
            // append debug message for local troubleshooting
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success'=>false,'message'=>$msg . ' ' . $e->getMessage()]);
                exit;
            }
            // Non-AJAX: set error to show on page
            $error = $msg;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pathology Lab Management System | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="login.php"><b>Pathology Lab</b> Management</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form id="loginForm" action="login.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Username" name="username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <p class="mb-1">
                <a href="#">I forgot my password</a>
            </p>
            <p class="mb-0">
                <a href="register.php" class="text-center">Register a new membership</a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(function(){
    $('#loginForm').on('submit', function(e){
        e.preventDefault();
    var data = $(this).serialize() + '&ajax=1';
        $.ajax({
            url: 'login.php', type: 'POST', data: data, dataType: 'json',
            success: function(resp){
                if(resp.success){
                    toastr.success(resp.message||'Logged in');
                    setTimeout(function(){ window.location = resp.redirect || 'index.php'; }, 600);
                } else {
                    toastr.error(resp.message || 'Login failed');
                }
            },
            error: function(xhr){
                var msg = 'Server error';
                try {
                    var json = JSON.parse(xhr.responseText || '{}');
                    if (json && json.message) msg = json.message;
                } catch(e) {
                    if (xhr.responseText && xhr.responseText.length < 500) msg = xhr.responseText;
                }
                toastr.error(msg);
            }
        });
    });
});
</script>
</body>
</html>