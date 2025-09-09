<?php
session_start();

if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

require_once 'inc/connection.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$valid = false;
$userId = null;

if ($token) {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            user_id INT NOT NULL,\n            token VARCHAR(128) NOT NULL,\n            expires_at DATETIME NOT NULL,\n            used TINYINT(1) NOT NULL DEFAULT 0,\n            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n            INDEX(user_id), INDEX(token)\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 LIMIT 1");
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            if (strtotime($row['expires_at']) >= time()) { $valid = true; $userId = (int)$row['user_id']; }
            else { $error = 'This link has expired. Please request a new one.'; }
        } else {
            $error = 'Invalid or used link.';
        }
    } catch (Throwable $e) { $error = 'Unable to verify link.'; }
} else {
    $error = 'Missing token.';
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])==='xmlhttprequest') || (!empty($_REQUEST['ajax']) && $_REQUEST['ajax']==1);
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if ($password === '' || strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 LIMIT 1");
            $stmt->execute([$token]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row || strtotime($row['expires_at']) < time()) {
                $error = 'This link is invalid or expired.';
            } else {
                $userId = (int)$row['user_id'];
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?")->execute([$hash, $userId]);
                $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?")->execute([$row['id']]);
                $success = 'Password has been reset. You can login now.';
            }
        } catch (Throwable $e) { $error = 'Unable to reset password.'; if($isAjax){ $error .= ' ' . $e->getMessage(); } }
    }
    if (!empty($_REQUEST['ajax'])) { header('Content-Type: application/json'); echo json_encode(['success'=>$error==='','message'=>$error===''?$success:$error,'redirect'=>$error===''?'login.php':null]); exit; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo"><a href="login.php"><b>Pathology Lab</b> Management</a></div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Choose a new password</p>
      <?php if ($error && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
      <?php if ($valid || $token): ?>
      <form id="resetForm" method="post" action="reset_password.php">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="New password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="confirm" placeholder="Confirm password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <div class="row">
          <div class="col-8"><a href="login.php">Back to login</a></div>
          <div class="col-4"><button type="submit" class="btn btn-primary btn-block">Reset</button></div>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(function(){
  $('#resetForm').on('submit', function(e){
    e.preventDefault();
    var $f=$(this);
    $.ajax({url:'reset_password.php', type:'POST', data:$f.serialize()+'&ajax=1', dataType:'json'})
      .done(function(r){ if(r.success){ toastr.success(r.message||'Password reset'); setTimeout(function(){ window.location=r.redirect||'login.php'; },800);} else { toastr.error(r.message||'Failed'); } })
      .fail(function(){ toastr.error('Server error'); });
  });
});
</script>
</body>
</html>
