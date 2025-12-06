<?php $page = 'pricing'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pricing — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=2.2">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="pricing-hero-section align-items-center d-flex">
      <div class="container text-center">
        <div class="hero-badge mx-auto mb-4 animate-in">
          <span class="badge-icon">💎</span>
          <span class="badge-text ps-2">Flexible Pricing Plans</span>
        </div>
        <h1 class="hero-title display-3 fw-bold mb-4 animate-in">
          Simple & <span class="gradient-text-rainbow glow-text">Transparent Pricing</span>
        </h1>
        <p class="hero-description lead text-secondary mb-5 mx-auto animate-in" style="max-width: 700px;">
          Choose the perfect plan for your healthcare facility. All plans include core features with options to scale as you grow. No hidden fees.
        </p>
        
        <div class="d-flex justify-content-center gap-4 hero-stats animate-in">
          <div class="d-flex align-items-center gap-2">
            <span class="fs-4">✅</span>
            <div class="text-start">
              <div class="fw-bold">14-Day Free Trial</div>
              <small class="text-muted">No credit card required</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="fs-4">🛡️</span>
            <div class="text-start">
              <div class="fw-bold">Secure Payment</div>
              <small class="text-muted">Encrypted transactions</small>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Plans -->
    <section class="pricing-plans-section py-5">
      <div class="container">
        <!-- Plans Included Here -->
        <div class="container-fluid animate-in">
          <?php include __DIR__ . '/umakant/public_plans.php'; ?>
        </div>
      </div>
    </section>

    <!-- Features Comparison -->
    <section class="features-comparison-section py-5 bg-light">
      <div class="container">
        <div class="section-header text-center mb-5 animate-in">
          <h2 class="fw-bold">Compare All Features</h2>
          <p class="text-secondary">Detailed breakdown of what's included in each plan</p>
        </div>
        <div class="comparison-table animate-in">
          <div class="table-row table-header">
            <div class="feature-name">Features</div>
            <div class="plan-name text-center">Starter</div>
            <div class="plan-name text-center">Professional</div>
            <div class="plan-name text-center">Enterprise</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Users</div>
            <div class="feature-value text-center text-secondary">Up to 10</div>
            <div class="feature-value text-center fw-bold">Up to 50</div>
            <div class="feature-value text-center text-primary fw-bold">Unlimited</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Patient Records</div>
            <div class="feature-value text-center text-success">✓</div>
            <div class="feature-value text-center text-success">✓</div>
            <div class="feature-value text-center text-success">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Appointments</div>
            <div class="feature-value text-center text-success">✓</div>
            <div class="feature-value text-center text-success">✓</div>
            <div class="feature-value text-center text-success">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Inventory Management</div>
            <div class="feature-value text-center text-muted">✗</div>
            <div class="feature-value text-center text-success">✓</div>
            <div class="feature-value text-center text-success">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Billing & Invoicing</div>
            <div class="feature-value text-center text-muted">✗</div>
            <div class="feature-value text-center text-success">✓</div>
            <div class="feature-value text-center text-success">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Advanced Analytics</div>
            <div class="feature-value text-center text-muted">✗</div>
            <div class="feature-value text-center text-success">✓</div>
            <div class="feature-value text-center text-success">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Multi-location</div>
            <div class="feature-value text-center text-muted">✗</div>
            <div class="feature-value text-center text-muted">✗</div>
            <div class="feature-value text-center text-success">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Custom Integrations</div>
            <div class="feature-value text-center text-muted">✗</div>
            <div class="feature-value text-center text-muted">✗</div>
            <div class="feature-value text-center text-success">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Support</div>
            <div class="feature-value text-center">Email</div>
            <div class="feature-value text-center fw-bold">Priority</div>
            <div class="feature-value text-center text-primary fw-bold">Dedicated Manager</div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="pricing-faq-section py-5">
      <div class="container">
        <div class="section-header text-center mb-5 animate-in">
          <h2 class="fw-bold">Frequently Asked Questions</h2>
          <p class="text-secondary">Everything you need to know about our pricing</p>
        </div>
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="faq-grid animate-in">
              <div class="faq-item">
                <div class="faq-question">
                  <span>Can I change my plan later?</span>
                  <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                  <p>Yes! You can upgrade or downgrade your plan at any time directly from your dashboard. Changes take effect immediately, and we'll prorate any payments.</p>
                </div>
              </div>
              <div class="faq-item">
                <div class="faq-question">
                  <span>Is there a setup fee?</span>
                  <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                  <p>No setup fees! We believe in transparent pricing with no hidden costs. You only pay the subscription fee for your chosen plan.</p>
                </div>
              </div>
              <div class="faq-item">
                <div class="faq-question">
                  <span>What's included in the free trial?</span>
                  <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                  <p>Your 14-day free trial includes all features of the Professional plan, allowing you to fully test our comprehensive solution before committing.</p>
                </div>
              </div>
              <div class="faq-item">
                <div class="faq-question">
                  <span>Do you offer custom pricing for large organizations?</span>
                  <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                  <p>Yes, for Enterprise customers with multiple locations or specific requirements, we offer custom pricing packages. Please contact our sales team for a quote.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section py-5 mb-5">
      <div class="container">
        <div class="cta-card p-5 text-center text-white rounded-5 animate-in" style="background: var(--primary-gradient); position: relative; overflow: hidden;">
          <div style="position: relative; z-index: 2;">
            <div class="display-3 mb-3">🚀</div>
            <h2 class="fw-bold mb-3">Ready to Get Started?</h2>
            <p class="lead mb-4 opacity-75">Join hundreds of healthcare facilities. Start your transformation today.</p>
            <div class="d-flex justify-content-center gap-3">
              <a href="contact.php" class="btn btn-light btn-lg px-4 fw-bold text-primary shadow-sm hover-lift">Start Free Trial</a>
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
    // Intersection Observer for fade-in animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-in');
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);

    // Initial styles for animation
    document.querySelectorAll('.animate-in').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
      observer.observe(el);
    });

    // FAQ Toggle
    document.querySelectorAll('.faq-question').forEach(question => {
      question.addEventListener('click', function() {
        const faqItem = this.parentElement;
        const isActive = faqItem.classList.contains('active');
        
        // Close all others
        document.querySelectorAll('.faq-item').forEach(item => {
          item.classList.remove('active');
          item.querySelector('.faq-toggle').textContent = '+';
        });

        if (!isActive) {
          faqItem.classList.add('active');
          this.querySelector('.faq-toggle').textContent = '−';
        }
      });
    });
  </script>
</body>
</html>