<?php $page = 'about'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>About — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="section about-hero-section">
      <div class="section-header scroll-reveal">
        <h2>About Our Platform</h2>
        <p>Learn about our mission to transform healthcare management</p>
      </div>
      <div class="card hover-lift">
        <h3>Who We Are</h3>
        <p class="small">We build easy-to-use, secure software for laboratories, clinics and hospitals that streamlines patient workflows, laboratory processes and administrative operations. Our platform is modular so organizations can adopt the features they need and scale over time.</p>

        <h3>Our Mission</h3>
        <p class="small">To enable faster and more accurate diagnostics by removing friction from clinical workflows and empowering healthcare teams with the right data at the right time.</p>

        <h3>Our Vision</h3>
        <p class="small">A connected healthcare ecosystem where clinical teams collaborate seamlessly, patients receive timely care, and administrators can operate efficiently with confidence and compliance.</p>
      </div>
    </section>

    <section class="section benefits-section">
      <div class="section-header scroll-reveal">
        <h2>Core Benefits</h2>
        <p>Why healthcare providers choose our solution</p>
      </div>
      <div class="card hover-lift">
        <div class="card-grid">
          <div class="card feature-card hover-scale">
            <div class="feature-icon">🔬</div>
            <h3>Pathology Module</h3>
            <p class="small">Comprehensive test catalogue, sample tracking and automated reports with customizable templates.</p>
          </div>
          <div class="card feature-card hover-scale">
            <div class="feature-icon">🏥</div>
            <h3>Hospital Tools</h3>
            <p class="small">Modular hospital management tools — appointments, billing, inventory with real-time analytics.</p>
          </div>
          <div class="card feature-card hover-scale">
            <div class="feature-icon">🛡️</div>
            <h3>Security</h3>
            <p class="small">Secure user roles, audit trails and data export for compliance with HIPAA standards.</p>
          </div>
          <div class="card feature-card hover-scale">
            <div class="feature-icon">🔌</div>
            <h3>Integrations</h3>
            <p class="small">Flexible integrations (LIS, third-party labs, payment gateways) with API access.</p>
          </div>
        </div>
        <div class="text-center mt-4">
          <p><a class="btn" href="contact.php">Contact Sales Team</a></p>
        </div>
      </div>
    </section>
    
    <section class="section stats-section">
      <div class="card text-center hover-lift">
        <h3>Trusted by Healthcare Professionals</h3>
        <p class="small">Join thousands of medical professionals who rely on our platform daily to deliver exceptional patient care.</p>
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number">500+</div>
            <div class="stat-label">Healthcare Facilities</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">10K+</div>
            <div class="stat-label">Medical Professionals</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">99.9%</div>
            <div class="stat-label">Uptime Guarantee</div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>