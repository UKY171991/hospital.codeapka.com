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
  <?php include_once __DIR__ . '/inc/header.php'; ?>

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

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
