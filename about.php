<?php $page = 'about'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>About — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="section">
      <div class="section-header">
        <h2>Who We Are</h2>
        <p>Learn more about our mission and vision</p>
      </div>
      <div class="card">
        <h3>Who we are</h3>
        <p class="small">We build easy-to-use, secure software for laboratories, clinics and hospitals that streamlines patient workflows, laboratory processes and administrative operations. Our platform is modular so organizations can adopt the features they need and scale over time.</p>

        <h3>Our mission</h3>
        <p class="small">To enable faster and more accurate diagnostics by removing friction from clinical workflows and empowering healthcare teams with the right data at the right time.</p>

        <h3>Our vision</h3>
        <p class="small">A connected healthcare ecosystem where clinical teams collaborate seamlessly, patients receive timely care, and administrators can operate efficiently with confidence and compliance.</p>
      </div>
    </section>

    <section class="section">
      <div class="section-header">
        <h2>Core Benefits</h2>
        <p>Why choose our hospital management system</p>
      </div>
      <div class="card">
        <div class="card-grid">
          <div class="card feature-card">
            <div class="feature-icon">🔬</div>
            <h3>Pathology Module</h3>
            <p class="small">Comprehensive test catalogue, sample tracking and automated reports</p>
          </div>
          <div class="card feature-card">
            <div class="feature-icon">🏥</div>
            <h3>Hospital Tools</h3>
            <p class="small">Modular hospital management tools — appointments, billing, inventory</p>
          </div>
          <div class="card feature-card">
            <div class="feature-icon">🛡️</div>
            <h3>Security</h3>
            <p class="small">Secure user roles, audit trails and data export for compliance</p>
          </div>
          <div class="card feature-card">
            <div class="feature-icon">🔌</div>
            <h3>Integrations</h3>
            <p class="small">Flexible integrations (LIS, third-party labs, payment gateways)</p>
          </div>
        </div>
        <div class="text-center mt-4">
          <p><a class="button" href="contact.php">Contact Sales or Support</a></p>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>