<?php $page = 'pricing'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pricing — Pathology & Hospital Management</title>
  <meta name="description" content="Explore flexible and transparent pricing plans for our hospital management system. Choose the perfect plan for your healthcare facility.">
  <meta name="keywords" content="hospital management pricing, healthcare software cost, medical system plans, pathology lab pricing, hospital administration pricing, healthcare software packages">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=2.2">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="pricing-hero-section position-relative overflow-hidden d-flex align-items-center" style="min-height: 400px; padding-top: 120px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
      <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
      </div>
      <div class="container text-center text-white position-relative z-1">
        <span class="badge rounded-pill mb-3 px-3 py-2 border border-light" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); color: #fff;">
          <i class="fas fa-gem me-2"></i> Flexible Pricing Plans
        </span>
        <h1 class="display-3 fw-bold mb-3">
          Simple & <span class="text-warning">Transparent Pricing</span>
        </h1>
        <p class="lead mb-4 opacity-90 mx-auto" style="max-width: 700px;">
          Choose the perfect plan for your healthcare facility. All plans include core features with options to scale as you grow. No hidden fees.
        </p>
      </div>
    </section>

    <!-- Pricing Plans -->
    <section class="pricing-plans-section py-5 bg-light">
      <div class="container">
        <div class="container-fluid">
          <?php include __DIR__ . '/umakant/public_plans.php'; ?>
        </div>
      </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section py-5 mb-5">
      <div class="container">
        <div class="cta-card p-5 text-center text-white rounded-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
          <div style="position: relative; z-index: 2;">
            <div class="display-3 mb-3">🚀</div>
            <h2 class="fw-bold mb-3">Ready to Get Started?</h2>
            <p class="lead mb-4 opacity-90">Join hundreds of healthcare facilities. Start your transformation today.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
              <a href="contact.php" class="btn btn-light btn-lg px-4 fw-bold shadow-sm hover-lift" style="color: #667eea;">Start Free Trial</a>
              <a href="contact.php" class="btn btn-outline-light btn-lg px-4 fw-bold hover-lift">Contact Sales</a>
            </div>
          </div>
          <!-- Decorative Background Elements -->
          <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
          <div style="position: absolute; bottom: -50px; left: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script>
    // Simple fade-in on scroll
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.pricing-card, .cta-card').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
      observer.observe(el);
    });
  </script>
  
  <style>
    .floating-shapes .shape {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(5px);
    }
    .shape-1 {
      width: 300px;
      height: 300px;
      top: -100px;
      left: -50px;
    }
    .shape-2 {
      width: 200px;
      height: 200px;
      bottom: 50px;
      right: -50px;
    }
    .hover-lift {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-lift:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
  </style>
</body>
</html>