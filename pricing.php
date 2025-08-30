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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-3mJ3mKqz2V6z6qzv1x0nQ5JYf6Y6kq2ZQ5N9m7f6v6Jq6Z2W" crossorigin="anonymous">
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
      <div class="card">

        <?php if ($plan): ?>
          <h3>Selected plan: <?php echo htmlspecialchars(ucfirst($plan)); ?></h3>
          <p class="small">Please contact our sales team to finalize the purchase.</p>
          <p><a class="button" href="contact.php">Contact Sales</a></p>
        <?php else: ?>
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
