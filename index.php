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
  <?php include_once __DIR__ . '/inc/header.php'; ?>

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

    <section id="pricing" class="section">
      <h3 style="margin-top:0">Pricing Plans</h3>
      <p class="small">Flexible plans for clinics, laboratories and hospitals. All plans include basic support and regular updates. Choose a plan and contact our sales team to purchase or schedule a demo.</p>
      <div class="card-grid pricing-grid">
        <div class="card plan">
          <h3>Starter</h3>
    <div class="price">₹16,499 <span class="small">/ month</span></div>
          <div class="features small">
            <div>• Up to 3 users</div>
            <div>• Basic pathology module</div>
            <div>• Email support</div>
          </div>
          <p style="margin-top:0.75rem"><a class="button" href="pricing.php?plan=starter">Buy Now</a></p>
        </div>

        <div class="card plan">
          <h3>Professional</h3>
          <div class="price">₹41,499 <span class="small">/ month</span></div>
          <div class="features small">
            <div>• Up to 15 users</div>
            <div>• Full pathology + hospital modules</div>
            <div>• Priority support</div>
          </div>
          <p style="margin-top:0.75rem"><a class="button" href="pricing.php?plan=professional">Buy Now</a></p>
        </div>

        <div class="card plan">
          <h3>Enterprise</h3>
          <div class="price">Custom</div>
          <div class="features small">
            <div>• Unlimited users</div>
            <div>• Custom integrations</div>
            <div>• Dedicated support & training</div>
          </div>
          <p style="margin-top:0.75rem"><a class="button" href="pricing.php?plan=enterprise">Contact Sales</a></p>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="card">
        <h3>Get Started</h3>
        <p class="small">Want to try the system or customize it for your facility? Contact our team to schedule a demo or request a quote.</p>
        <p><a class="button" href="contact.php">Contact Us</a></p>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
