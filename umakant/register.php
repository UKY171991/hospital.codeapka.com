<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'inc/connection.php';
    // detect AJAX either via X-Requested-With or explicit ajax param
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (!empty($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1);

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $user_type = trim($_POST['user_type'] ?? 'Pathology');

    if ($username == '' || $password == '') {
        $error = 'Username and password are required.';
    } else {
        try {
            // check username uniqueness
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username is already taken.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                // Insert with explicit columns that exist in the users table (match table structure)
                // Set expire_date to one month from now
                $insert = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role, user_type, added_by, is_active, expire_date, created_at) VALUES (?, ?, ?, ?, 'user', ?, NULL, 1, DATE_ADD(NOW(), INTERVAL 1 MONTH), NOW())");
                $res = $insert->execute([$username, $hash, $full_name, $email, $user_type]);
                if ($res) {
                    $success = 'Registration successful. You can now login.';
                } else {
                    $error = 'Failed to register. Please try again.';
                }
            }
        } catch (Exception $e) {
            // on exception, set a safe message; include exception message when running locally only
            $error = 'Server error while registering.';
            // If ajax, include the exception text for debugging (non-production use)
            if ($isAjax) {
                $error .= ' ' . $e->getMessage();
            }
        }
    }

    // If this was an AJAX request, always respond with JSON (success or error)
    if ($isAjax) {
        header('Content-Type: application/json');
        $ok = empty($error) && !empty($success);
        echo json_encode(['success' => $ok, 'message' => $ok ? $success : $error, 'redirect' => $ok ? 'login.php' : null]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Pathology Lab Management</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
    <div class="register-logo">
        <a href="register.php"><b>Pathology Lab</b> Management</a>
    </div>

    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Register a new membership</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form id="registerForm" action="register.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Full name" name="full_name" value="<?php echo htmlspecialchars($full_name ?? ''); ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Username" name="username" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
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
                <div class="input-group mb-3">
                    <select class="form-control" name="user_type">
                        <option value="Pathology" <?php echo (isset($user_type) && $user_type === 'Pathology') ? 'selected' : ''; ?>>Pathology</option>
                        <option value="Hospital" <?php echo (isset($user_type) && $user_type === 'Hospital') ? 'selected' : ''; ?>>Hospital</option>
                        <option value="School" <?php echo (isset($user_type) && $user_type === 'School') ? 'selected' : ''; ?>>School</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-8">
                        <a href="login.php" class="text-center">I already have an account</a>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(function(){
    $('#registerForm').on('submit', function(e){
        e.preventDefault();
        var $f = $(this);
        $.ajax({
            url: 'register.php',
            type: 'POST',
            data: $f.serialize() + '&ajax=1',
            dataType: 'json',
            success: function(resp){
                if(resp.success){
                    toastr.success(resp.message||'Registered');
                    setTimeout(function(){ window.location = resp.redirect || 'login.php'; },700);
                } else {
                    toastr.error(resp.message||'Register failed');
                }
            },
            error: function(xhr){
                var msg = 'Server error';
                try {
                    var json = JSON.parse(xhr.responseText || '{}');
                    if (json && json.message) msg = json.message;
                } catch(e) {
                    // not JSON, show responseText if short
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
