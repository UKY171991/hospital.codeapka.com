<?php $page = 'pricing'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pricing — Pathology & Hospital Management</title>
  <meta name="description" content="Explore flexible and transparent pricing plans for our hospital management system. Choose the perfect plan for your healthcare facility.">
  <meta name="keywords" content="hospital management pricing, healthcare software cost, medical system plans, pathology lab pricing, hospital administration pricing, healthcare software packages">
  <link rel="canonical" href="https://hospital.codeapka.com/pricing.php">
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  
  <!-- Schema.org structured data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Hospital Management Pricing Plans",
    "description": "Explore flexible and transparent pricing plans for our hospital management system. Choose the perfect plan for your healthcare facility.",
    "url": "https://hospital.codeapka.com/pricing.php",
    "mainEntity": {
      "@type": "Organization",
      "name": "Pathology & Hospital Management",
      "url": "https://hospital.codeapka.com",
      "logo": "https://hospital.codeapka.com/favicon.svg",
      "offers": {
        "@type": "AggregateOffer",
        "name": "Hospital Management Software Plans",
        "description": "Flexible pricing plans for healthcare facilities of all sizes"
      }
    }
  }
  </script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=2.2">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="favicon.svg">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.svg">
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

    <!-- Trust Strip -->
    <section class="pricing-trust-strip py-4 border-bottom bg-white">
      <div class="container">
        <div class="row g-3 text-center">
          <div class="col-6 col-lg-3">
            <div class="trust-item">
              <div class="trust-value">99.9%</div>
              <div class="trust-label">Platform Uptime</div>
            </div>
          </div>
          <div class="col-6 col-lg-3">
            <div class="trust-item">
              <div class="trust-value">24/7</div>
              <div class="trust-label">Support Availability</div>
            </div>
          </div>
          <div class="col-6 col-lg-3">
            <div class="trust-item">
              <div class="trust-value">Secure</div>
              <div class="trust-label">Cloud Data Backup</div>
            </div>
          </div>
          <div class="col-6 col-lg-3">
            <div class="trust-item">
              <div class="trust-value">Fast</div>
              <div class="trust-label">Onboarding in Days</div>
            </div>
          </div>
        </div>
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

    <!-- Included in Every Plan -->
    <section class="pricing-includes py-5">
      <div class="container">
        <div class="text-center mb-4">
          <h2 class="fw-bold mb-2">Everything You Need to Run a Modern Facility</h2>
          <p class="text-muted mb-0">Every plan is built with the essentials required by clinics, hospitals, and pathology labs.</p>
        </div>
        <div class="row g-3">
          <div class="col-md-6 col-lg-3">
            <div class="include-card h-100">
              <h3>Patient Records</h3>
              <p>Maintain complete OPD/IPD records, visits, and treatment history in one place.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="include-card h-100">
              <h3>Billing & Invoices</h3>
              <p>Generate professional bills, manage payments, and track pending balances quickly.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="include-card h-100">
              <h3>Reports & Insights</h3>
              <p>Access dashboards and daily summaries to make data-driven decisions.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="include-card h-100">
              <h3>Security First</h3>
              <p>Role-based access and secure data handling to protect sensitive health information.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section class="pricing-faq-section py-5 bg-light">
      <div class="container" style="max-width: 900px;">
        <div class="text-center mb-4">
          <h2 class="fw-bold mb-2">Frequently Asked Questions</h2>
          <p class="text-muted mb-0">Clear answers to help you choose the right plan.</p>
        </div>
        <div class="accordion shadow-sm rounded-4 overflow-hidden" id="pricingFaqAccordion">
          <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header" id="faqOneHeading">
              <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="true" aria-controls="faqOne">
                Is there any setup or onboarding fee?
              </button>
            </h2>
            <div id="faqOne" class="accordion-collapse collapse show" aria-labelledby="faqOneHeading" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body text-muted">
                No hidden charges. Our team will guide your initial setup and onboarding as part of your subscription.
              </div>
            </div>
          </div>
          <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header" id="faqTwoHeading">
              <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo" aria-expanded="false" aria-controls="faqTwo">
                Can I switch plans later?
              </button>
            </h2>
            <div id="faqTwo" class="accordion-collapse collapse" aria-labelledby="faqTwoHeading" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body text-muted">
                Yes. You can upgrade or change your plan as your requirements evolve, without losing your existing data.
              </div>
            </div>
          </div>
          <div class="accordion-item border-0">
            <h2 class="accordion-header" id="faqThreeHeading">
              <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree" aria-expanded="false" aria-controls="faqThree">
                Do you provide support and training?
              </button>
            </h2>
            <div id="faqThree" class="accordion-collapse collapse" aria-labelledby="faqThreeHeading" data-bs-parent="#pricingFaqAccordion">
              <div class="accordion-body text-muted">
                Absolutely. We offer product training, implementation guidance, and responsive support to your team.
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
    .pricing-trust-strip .trust-item {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 14px 12px;
      height: 100%;
    }
    .trust-value {
      font-weight: 800;
      color: #1d4ed8;
      font-size: 1.2rem;
      line-height: 1.2;
    }
    .trust-label {
      color: #6b7280;
      font-size: 0.92rem;
      margin-top: 2px;
    }
    .pricing-includes {
      background: #fff;
    }
    .include-card {
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 22px 18px;
      background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
      transition: transform .2s ease, box-shadow .2s ease;
    }
    .include-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(30, 58, 138, 0.09);
    }
    .include-card h3 {
      font-size: 1.05rem;
      margin-bottom: 8px;
      color: #1f2937;
      font-weight: 700;
    }
    .include-card p {
      margin-bottom: 0;
      color: #6b7280;
      font-size: .95rem;
    }
    .pricing-faq-section .accordion-button:not(.collapsed) {
      color: #1d4ed8;
      background: #eef4ff;
      box-shadow: none;
    }
    .pricing-faq-section .accordion-button:focus {
      box-shadow: none;
      border-color: #dbeafe;
    }
  </style>
</body>
</html>
