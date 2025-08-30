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
  <header class="header">
    <div class="container">
      <div class="brand">
        <div class="logo">PH</div>
        <div>
          <h1 style="margin:0;font-size:1.25rem">Pathology & Hospital Management</h1>
          <div class="small">Pricing & Purchase</div>
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
              <div class="price">$199 <span class="small">/ month</span></div>
              <p class="small">Best for small clinics and labs.</p>
              <p><a class="button" href="pricing.php?plan=starter">Choose</a></p>
            </div>

            <div class="card plan">
              <h3>Professional</h3>
              <div class="price">$499 <span class="small">/ month</span></div>
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
        <?php endif; ?>

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
