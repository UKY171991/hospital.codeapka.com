<?php $page = 'about'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>About — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-3mJ3mKqz2V6z6qzv1x0nQ5JYf6Y6kq2ZQ5N9m7f6v6Jq6Z2W" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="section">
      <div class="card">
        <h2>Who we are</h2>
        <p class="small">We build easy-to-use, secure software for laboratories, clinics and hospitals that streamlines patient workflows, laboratory processes and administrative operations. Our platform is modular so organizations can adopt the features they need and scale over time.</p>

        <h3>Our mission</h3>
        <p class="small">To enable faster and more accurate diagnostics by removing friction from clinical workflows and empowering healthcare teams with the right data at the right time.</p>

        <h3>Our vision</h3>
        <p class="small">A connected healthcare ecosystem where clinical teams collaborate seamlessly, patients receive timely care, and administrators can operate efficiently with confidence and compliance.</p>
      </div>
    </section>

    <section class="section">
      <div class="card">
        <h3>Core benefits</h3>
        <ul class="small">
          <li>Comprehensive test catalogue, sample tracking and automated reports</li>
          <li>Modular hospital management tools — appointments, billing, inventory</li>
          <li>Secure user roles, audit trails and data export for compliance</li>
          <li>Flexible integrations (LIS, third-party labs, payment gateways)</li>
          <li>Professional support, onboarding and optional custom development</li>
        </ul>
        <p style="margin-top:0.75rem"><a class="button" href="contact.php">Contact Sales or Support</a></p>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
