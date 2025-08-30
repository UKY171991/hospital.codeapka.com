<?php $page = 'contact';
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real app you'd validate and send or store the message.
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    // Simulate success
    $sent = true;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Contact — Pathology & Hospital Management</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="brand">
        <div class="logo">PH</div>
        <div>
          <h1 style="margin:0;font-size:1.25rem">Pathology & Hospital Management</h1>
          <div class="small">Get in touch</div>
        </div>
      </div>
      <nav class="site-nav" aria-label="Main navigation">
        <a href="index.php" class="<?php echo $page=='home' ? 'active' : '' ?>">Home</a>
        <a href="about.php" class="<?php echo $page=='about' ? 'active' : '' ?>">About</a>
        <a href="contact.php" class="<?php echo $page=='contact' ? 'active' : '' ?>">Contact</a>
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="section">
      <div class="card">
        <h2>Contact Us</h2>
        <?php if ($sent): ?>
          <p class="small">Thank you, <?php echo $name; ?>. Your message has been received. We'll get back to <?php echo $email; ?> soon.</p>
        <?php else: ?>
          <form method="post" action="contact.php">
            <div class="form-row">
              <input type="text" name="name" placeholder="Your Name" required />
              <input type="email" name="email" placeholder="Your Email" required />
            </div>
            <div style="margin-top:0.75rem">
              <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
            </div>
            <div style="margin-top:0.75rem">
              <button type="submit">Send Message</button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <footer>
    <div class="container" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
      <div class="small">© <?php echo date('Y'); ?> Pathology & Hospital Management</div>
      <div class="small">Made with care for better healthcare.</div>
    </div>
  </footer>
</body>
</html>
