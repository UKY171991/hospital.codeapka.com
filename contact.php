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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="section">
      <div class="section-header">
        <h2>Contact Us</h2>
        <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
      </div>
      
      <div class="card">
        <?php if ($sent): ?>
          <div class="text-center">
            <div class="feature-icon mb-3" style="width: 100px; height: 100px; font-size: 3rem; margin: 0 auto;">✅</div>
            <h3>Thank You!</h3>
            <p class="small">Thank you, <?php echo htmlspecialchars($name); ?>. Your message has been received. We'll get back to <?php echo htmlspecialchars($email); ?> soon.</p>
            <p><a class="button" href="contact.php">Send Another Message</a></p>
          </div>
        <?php else: ?>
          <?php if ($error): ?>
            <div class="alert alert-danger small mb-4"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          
          <form method="post" action="contact.php">
            <div class="form-row">
              <div>
                <label for="name" class="small mb-1">Your Name *</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required value="<?php echo htmlspecialchars($name); ?>" />
              </div>
              <div>
                <label for="email" class="small mb-1">Your Email *</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($email); ?>" />
              </div>
            </div>
            <div class="mb-3">
              <label for="message" class="small mb-1">Your Message *</label>
              <textarea id="message" name="message" rows="6" placeholder="Enter your message here..." required><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <div class="text-center">
              <button type="submit" class="button">Send Message</button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </section>
    
    <section class="section">
      <div class="card">
        <div class="card-grid">
          <div class="card feature-card">
            <div class="feature-icon">📧</div>
            <h3>Email Us</h3>
            <p class="small">uky171991@gmail.com</p>
          </div>
          <div class="card feature-card">
            <div class="feature-icon">📍</div>
            <h3>Visit Us</h3>
            <p class="small">123 Healthcare Avenue<br>Medical District, HC 10001</p>
          </div>
          <div class="card feature-card">
            <div class="feature-icon">🕒</div>
            <h3>Business Hours</h3>
            <p class="small">Monday - Friday: 9AM - 6PM<br>Saturday: 10AM - 4PM</p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>