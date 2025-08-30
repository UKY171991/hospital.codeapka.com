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
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="section">
      <div class="section-header">
        <h2>Pricing Plans</h2>
        <p>Select a plan and contact sales to complete your purchase. For Enterprise, we'll prepare a custom quote and onboarding plan.</p>
      </div>
      
      <?php if ($plan): ?>
        <div class="card text-center">
          <div class="feature-icon mb-3" style="width: 100px; height: 100px; font-size: 3rem; margin: 0 auto;">📋</div>
          <h3>Selected plan: <?php echo htmlspecialchars(ucfirst($plan)); ?></h3>
          <p class="small">Please contact our sales team to finalize the purchase.</p>
          <p><a class="button" href="contact.php">Contact Sales</a></p>
        </div>
      <?php else: ?>
        <div class="card">
          <div class="mt-2">
            <?php include __DIR__ . '/umakant/public_plans.php'; ?>
          </div>
        </div>
      <?php endif; ?>
    </section>
    
    <section class="section">
      <div class="card">
        <h3 class="text-center">Frequently Asked Questions</h3>
        <div class="mt-4">
          <h4>Can I switch plans later?</h4>
          <p class="small">Yes, you can upgrade or downgrade your plan at any time. Contact our support team to make changes to your subscription.</p>
          
          <h4>Do you offer discounts for non-profits?</h4>
          <p class="small">Yes, we offer special pricing for non-profit organizations. Please contact our sales team for more information.</p>
          
          <h4>Is there a free trial available?</h4>
          <p class="small">We offer a 14-day free trial for our Professional plan. Contact us to get started with your trial.</p>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>