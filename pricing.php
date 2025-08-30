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
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="section">
      <div class="card">
        <h2>Pricing Plans</h2>
        <p class="small">Select a plan and contact sales to complete your purchase. For Enterprise, we'll prepare a custom quote and onboarding plan.</p>

        <?php if ($plan): ?>
          <h3>Selected plan: <?php echo htmlspecialchars(ucfirst($plan)); ?></h3>
          <p class="small">Please contact our sales team to finalize the purchase.</p>
          <p><a class="button" href="contact.php">Contact Sales</a></p>
        <?php else: ?>
          <div class="card-grid pricing-grid">
            <div class="card plan">
              <h3>Starter</h3>
              <div class="price">₹16,499 <span class="small">/ month</span></div>
              <p class="small">Best for small clinics and labs.</p>
              <p><a class="button" href="pricing.php?plan=starter">Choose</a></p>
            </div>

            <div class="card plan">
              <h3>Professional</h3>
              <div class="price">₹41,499 <span class="small">/ month</span></div>
              <p class="small">For larger clinics and hospitals.</p>
              <p><a class="button" href="pricing.php?plan=professional">Choose</a></p>
            </div>

            <div class="card plan">
              <h3>Enterprise</h3>
              <div class="price">Custom</div>
              <p class="small">Tailored solutions for large organizations.</p>
              <p><a class="button" href="pricing.php?plan=enterprise">Contact Sales</a></p>
            </div>
          </div>
          <div style="margin-top:1rem">
            <?php include __DIR__ . '/umakant/public_plans.php'; ?>
          </div>
        <?php endif; ?>

      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
