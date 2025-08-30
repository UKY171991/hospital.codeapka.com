<?php $page = 'home'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Welcome — Pathology & Hospital Management</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="brand">
        <div class="logo">PH</div>
        <div>
          <h1 style="margin:0;font-size:1.25rem">Pathology & Hospital Management</h1>
          <div class="small">A modern platform to manage labs, records and hospital workflows.</div>
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
    <section class="hero">
      <div>
        <h2 style="margin:0 0 0.5rem 0">Welcome to the Pathology & Hospital Management System</h2>
        <p class="lead">Build faster workflows, access patient and lab data securely, and deliver better care. The system is designed for simplicity and scale — ideal for small clinics to large hospitals.</p>
        <p><a class="button" href="#features">Explore features</a></p>
      </div>
      <div>
        <div class="card">
          <h3>Pathology Module</h3>
          <p class="small">Manage lab tests, sample tracking, digital reports and results delivery.</p>
          <div class="feature-list">
            <span>• Test catalogue & pricing</span>
            <span>• Sample barcoding</span>
            <span>• Automated report templates</span>
          </div>
        </div>
      </div>
    </section>

    <section id="features" class="section">
      <h3 style="margin-top:0">Key Features</h3>
      <div class="card-grid">
        <div class="card">
          <h3>Patient Records</h3>
          <p class="small">Centralized EHR for quick access to patient history, visits and reports.</p>
        </div>
        <div class="card">
          <h3>Appointments</h3>
          <p class="small">Online booking, doctor schedules and automated reminders.</p>
        </div>
        <div class="card">
          <h3>Billing & Inventory</h3>
          <p class="small">Integrated billing, invoices and stock control for consumables.</p>
        </div>
        <div class="card">
          <h3>Secure Access</h3>
          <p class="small">Role-based access controls and audit logs to meet compliance needs.</p>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="card">
        <h3>Get Started</h3>
        <p class="small">Want to try the system or customize it for your facility? Contact our team to schedule a demo.</p>
        <p><a class="button" href="contact.php">Contact Us</a></p>
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
