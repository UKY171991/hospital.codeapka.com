<?php
$page = 'pricing';
$plan = $_GET['plan'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pricing — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="section">
      <div class="section-header">
        <h2>Simple, Transparent Pricing</h2>
        <p>Choose the perfect plan for your healthcare facility's needs</p>
      </div>
      
      <?php if ($plan): ?>
        <div class="card text-center">
          <div class="feature-icon mb-4" style="width: 120px; height: 120px; font-size: 4rem; margin: 0 auto;">📋</div>
          <h3>Selected Plan: <?php echo htmlspecialchars(ucfirst($plan)); ?></h3>
          <p class="small">Great choice! Our sales team will prepare your customized onboarding plan.</p>
          <p><a class="btn" href="contact.php">Contact Sales Team</a></p>
        </div>
      <?php else: ?>
        <div class="card">
          <h3>Our Plans</h3>
          <p class="small">All plans include core features with options to customize based on your facility's requirements.</p>
          <div class="mt-3">
            <?php include __DIR__ . '/umakant/public_plans.php'; ?>
          </div>
        </div>
      <?php endif; ?>
    </section>
    
    <section class="section">
      <div class="card">
        <div class="section-header">
          <h2>Frequently Asked Questions</h2>
          <p>Everything you need to know about our pricing</p>
        </div>
        <div class="mt-4">
          <h4>Can I switch plans later?</h4>
          <p class="small">Yes, you can upgrade or downgrade your plan at any time. Contact our support team to make changes to your subscription with no additional fees.</p>
          
          <h4>Do you offer discounts for non-profits?</h4>
          <p class="small">Yes, we offer special pricing for non-profit organizations and educational institutions. Please contact our sales team for more information and eligibility requirements.</p>
          
          <h4>Is there a free trial available?</h4>
          <p class="small">We offer a 14-day free trial for our Professional plan with full access to all features. No credit card required to start your trial.</p>
          
          <h4>What payment methods do you accept?</h4>
          <p class="small">We accept all major credit cards, bank transfers, and UPI payments. Enterprise customers can also opt for annual invoicing.</p>
        </div>
      </div>
    </section>
    
    <section class="section">
      <div class="card text-center">
        <h3>Still Have Questions?</h3>
        <p class="small">Our sales team is ready to help you find the perfect solution for your facility.</p>
        <div class="mt-3">
          <a class="btn" href="contact.php">Contact Sales</a>
          <a class="button ghost ml-2" href="tel:+15551234567">Call Us</a>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>