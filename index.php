<?php $page = 'home'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Welcome — Pathology & Hospital Management</title>
  <meta name="description" content="Transform your healthcare operations with our comprehensive hospital management system. Trusted by 500+ facilities worldwide.">
  <meta name="keywords" content="hospital management system, pathology lab, healthcare software, medical management, patient records, hospital administration, healthcare operations, medical billing, hospital software">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css?v=2.2">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="hero-section position-relative overflow-hidden d-flex align-items-center" style="min-height: 600px; padding-top: 120px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
      <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
      </div>
      <div class="container position-relative z-1">
        <div class="row align-items-center">
          <div class="col-lg-6 text-white">
            <span class="badge rounded-pill mb-3 px-3 py-2 border border-light" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); color: #fff;">
              <i class="fas fa-award me-2"></i> Trusted by 500+ Healthcare Facilities
            </span>
            <h1 class="display-3 fw-bold mb-3">
              Transform Your <span class="text-warning">Healthcare Operations</span>
            </h1>
            <p class="lead mb-4 opacity-90">
              Streamline workflows, enhance patient care, and boost efficiency with our comprehensive hospital management system.
            </p>
            <div class="d-flex gap-3 mb-4 flex-wrap">
              <a href="pricing.php" class="btn btn-light btn-lg px-4 fw-bold shadow-sm hover-lift" style="color: #1e3a8a;">
                <i class="fas fa-rocket me-2"></i> Get Started
              </a>
              <a href="contact.php" class="btn btn-outline-light btn-lg px-4 fw-bold hover-lift">
                <i class="fas fa-calendar me-2"></i> Schedule Demo
              </a>
            </div>
            
            <!-- Stats -->
            <div class="row g-3 mt-4">
              <div class="col-4">
                <div class="text-center">
                  <h4 class="fw-bold mb-0">99.9%</h4>
                  <small class="opacity-75">Uptime</small>
                </div>
              </div>
              <div class="col-4">
                <div class="text-center">
                  <h4 class="fw-bold mb-0">24/7</h4>
                  <small class="opacity-75">Support</small>
                </div>
              </div>
              <div class="col-4">
                <div class="text-center">
                  <h4 class="fw-bold mb-0">500+</h4>
                  <small class="opacity-75">Facilities</small>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-6 d-none d-lg-block">
            <div class="text-center">
              <div class="card border-0 shadow-lg p-4" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                <div class="card-body">
                  <div class="display-1 mb-3">🏥</div>
                  <h4 class="fw-bold mb-3">Advanced Healthcare Management</h4>
                  <p class="text-muted mb-4">AI-Powered • Secure • Scalable</p>
                  <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2">
                      <i class="fas fa-check-circle text-success"></i>
                      <span>Real-time Analytics</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <i class="fas fa-check-circle text-success"></i>
                      <span>HIPAA Compliant</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <i class="fas fa-check-circle text-success"></i>
                      <span>Lightning Fast</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light" id="features">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold mb-3">Powerful Features</h2>
          <p class="text-muted">Everything you need to manage your healthcare facility</p>
        </div>
        
        <div class="row g-4">
          <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body p-4 text-center">
                <div class="display-4 mb-3">📋</div>
                <h5 class="fw-bold mb-3">Patient Management</h5>
                <p class="text-muted">Complete EHR system with patient records, history, and appointments</p>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body p-4 text-center">
                <div class="display-4 mb-3">💰</div>
                <h5 class="fw-bold mb-3">Billing & Inventory</h5>
                <p class="text-muted">Automated billing, invoicing, and inventory management</p>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body p-4 text-center">
                <div class="display-4 mb-3">📊</div>
                <h5 class="fw-bold mb-3">Analytics & Reports</h5>
                <p class="text-muted">Real-time insights and comprehensive reporting tools</p>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body p-4 text-center">
                <div class="display-4 mb-3">🔒</div>
                <h5 class="fw-bold mb-3">Security & Compliance</h5>
                <p class="text-muted">HIPAA compliant with enterprise-grade security</p>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body p-4 text-center">
                <div class="display-4 mb-3">📱</div>
                <h5 class="fw-bold mb-3">Mobile Access</h5>
                <p class="text-muted">Access your system anywhere, anytime on any device</p>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body p-4 text-center">
                <div class="display-4 mb-3">🤝</div>
                <h5 class="fw-bold mb-3">24/7 Support</h5>
                <p class="text-muted">Round-the-clock support from our expert team</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5 mb-5">
      <div class="container">
        <div class="cta-card p-5 text-center text-white rounded-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
          <div style="position: relative; z-index: 2;">
            <div class="display-3 mb-3">🚀</div>
            <h2 class="fw-bold mb-3">Ready to Transform Your Healthcare Facility?</h2>
            <p class="lead mb-4 opacity-90">Join hundreds of healthcare providers who have revolutionized their operations.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
              <a href="pricing.php" class="btn btn-light btn-lg px-4 fw-bold shadow-sm hover-lift" style="color: #667eea;">View Pricing</a>
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

  <script>
    // Simple fade-in animation
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.card, .cta-card').forEach(el => {
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
      width: 400px;
      height: 400px;
      top: -150px;
      left: -100px;
    }
    .shape-2 {
      width: 300px;
      height: 300px;
      bottom: -100px;
      right: -100px;
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