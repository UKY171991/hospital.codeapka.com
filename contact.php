<?php
$page = 'contact';
$sent = false;
$error = '';
$name = '';
$email = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Basic input handling and validation
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if ($name === '' || $email === '' || $message === '') {
    $error = 'Please fill all required fields.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please provide a valid email address.';
  } else {
    // Prepare email
    $to = 'uky171991@gmail.com';
    $subject = "Contact form message from {$name} — Pathology & Hospital Management";
    $site = ($_SERVER['HTTP_HOST'] ?? 'hospital.codeapka.com');
    $body = "You have received a new contact form message from {$site}\n\n";
    $body .= "Name: {$name}\n";
    $body .= "Email: {$email}\n";
    $body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "\n";
    $body .= "User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'N/A') . "\n\n";
    $body .= "Message:\n{$message}\n";

    // Headers - set a sensible From and Reply-To
    $headers = [];
    $headers[] = 'From: noreply@' . preg_replace('/^www\./', '', $site);
    $headers[] = 'Reply-To: ' . $email;
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';

    // Attempt to send email
    $sentMail = false;
    try {
      $sentMail = mail($to, $subject, $body, implode("\r\n", $headers));
    } catch (Exception $e) {
      $sentMail = false;
    }

    if ($sentMail) {
      $sent = true;
    } else {
      $error = 'Failed to send message. Please try again or contact us directly at uky171991@gmail.com.';
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Contact — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-3mJ3mKqz2V6z6qzv1x0nQ5JYf6Y6kq2ZQ5N9m7f6v6Jq6Z2W" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="section">
      <div class="card">
        <h2>Contact Us</h2>
        <?php if ($sent): ?>
          <p class="small">Thank you, <?php echo htmlspecialchars($name); ?>. Your message has been received. We'll get back to <?php echo htmlspecialchars($email); ?> soon.</p>
        <?php else: ?>
          <?php if ($error): ?>
            <p class="small" style="color:#b00020;margin-bottom:0.75rem"><?php echo htmlspecialchars($error); ?></p>
          <?php endif; ?>
          <form method="post" action="contact.php">
            <div class="form-row">
              <input type="text" name="name" placeholder="Your Name" required value="<?php echo htmlspecialchars($name); ?>" />
              <input type="email" name="email" placeholder="Your Email" required value="<?php echo htmlspecialchars($email); ?>" />
            </div>
            <div style="margin-top:0.75rem">
              <textarea name="message" rows="5" placeholder="Your Message" required><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <div style="margin-top:0.75rem">
              <button type="submit">Send Message</button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
