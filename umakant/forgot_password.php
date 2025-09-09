<?php
session_start();

// If logged in, redirect
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // AJAX detection
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])==='xmlhttprequest') || (!empty($_REQUEST['ajax']) && $_REQUEST['ajax']==1);

    $emailOrUser = trim($_POST['email_or_username'] ?? '');
    if ($emailOrUser === '') {
        $error = 'Please enter your email or username.';
    } else {
        require_once 'inc/connection.php';
        try {
            // Find user by email or username, must be active
            $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE (email = ? OR username = ?) AND is_active = 1 LIMIT 1");
            $stmt->execute([$emailOrUser, $emailOrUser]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                // Don't reveal account existence
                $success = 'If an account matches, a reset link has been sent.';
            } else {
                // Ensure table exists (idempotent)
                $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (\n                    id INT AUTO_INCREMENT PRIMARY KEY,\n                    user_id INT NOT NULL,\n                    token VARCHAR(128) NOT NULL,\n                    expires_at DATETIME NOT NULL,\n                    used TINYINT(1) NOT NULL DEFAULT 0,\n                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n                    INDEX(user_id), INDEX(token)\n                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // Generate token valid for 30 minutes
                $token = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', time() + 30*60);

                // Invalidate previous tokens for this user
                $pdo->prepare("UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0")->execute([$user['id']]);

                $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)")
                    ->execute([$user['id'], $token, $expiresAt]);

                $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['REQUEST_URI']), '/');
                $link = $baseUrl . '/reset_password.php?token=' . urlencode($token);

                // Try to send mail via PHP mail() if email exists, else just show link for dev
                $message = "Hello " . ($user['username'] ?: 'user') . ",\n\n" .
                           "We received a request to reset your password. Click the link below to choose a new password. This link expires in 30 minutes.\n\n" .
                           $link . "\n\nIf you did not request this, you can ignore this email.";
                $sent = false;
                if (!empty($user['email'])) {
                    $headers = "From: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'example.com');
                    $sent = @mail($user['email'], 'Password Reset', $message, $headers);
                }
                // For development, surface the link if email missing or mail failed
                if (!$sent) {
                    $success = 'Password reset link (copy and open in browser): ' . $link;
                } else {
                    $success = 'If an account matches, a reset link has been sent.';
                }
            }
        } catch (Throwable $e) {
            $error = 'Unable to process request.';
            if ($isAjax) { $error .= ' ' . $e->getMessage(); }
        }
    }

    if (!empty($_REQUEST['ajax'])) {
        header('Content-Type: application/json');
        $ok = $error === '';
        echo json_encode(['success'=>$ok,'message'=>$ok?$success:$error]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="login.php"><b>Pathology Lab</b> Management</a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Reset your password</p>
            <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
            <form id="forgotForm" action="forgot_password.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="email_or_username" placeholder="Email or Username" required>
                    <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <a href="login.php">Back to login</a>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Send Link</button>
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
  $('#forgotForm').on('submit', function(e){
    e.preventDefault();
    var $f=$(this);
    $.ajax({url:'forgot_password.php', type:'POST', data:$f.serialize()+'&ajax=1', dataType:'json'})
      .done(function(r){ if(r.success){ toastr.success(r.message||'Link sent'); } else { toastr.error(r.message||'Failed'); } })
      .fail(function(){ toastr.error('Server error'); });
  });
});
</script>
</body>
</html>
