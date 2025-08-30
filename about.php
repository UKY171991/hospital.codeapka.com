<?php $page = 'about'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>About — Pathology & Hospital Management</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="brand">
        <div class="logo">PH</div>
        <div>
          <h1 style="margin:0;font-size:1.25rem">Pathology & Hospital Management</h1>
          <div class="small">About our platform</div>
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
        <h2>Who we are</h2>
        <p class="small">We provide a unified software solution for laboratories and hospitals to manage clinical workflows, reporting and administration. Our focus is usability, security and extensibility.</p>
        <h3>Mission</h3>
        <p class="small">Improve clinical outcomes by delivering tools that reduce administrative overhead and accelerate diagnostics.</p>
      </div>
    </section>

    <section class="section">
      <div class="card">
        <h3>Why choose us?</h3>
        <ul class="small">
          <li>Modular features for clinics of every size</li>
          <li>Secure patient data handling and audit logs</li>
          <li>Customizable reports and integrations</li>
        </ul>
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
