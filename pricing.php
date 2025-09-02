<?php $page = 'pricing'; ?>
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

  <main>
    <!-- Hero Section -->
    <section class="pricing-hero-section">
      <div class="hero-background">
        <div class="hero-particles"></div>
        <div class="hero-gradient"></div>
      </div>
      <div class="container">
        <div class="hero-content">
          <div class="hero-left">
            <div class="hero-badge">
              <span class="badge-icon">💎</span>
              <span class="badge-text">Flexible Pricing Plans</span>
            </div>
            <h1 class="hero-title">
              Simple & 
              <span class="gradient-text-rainbow glow-text">Transparent Pricing</span>
            </h1>
            <p class="hero-description">
              Choose the perfect plan for your healthcare facility. All plans include core features with options to scale as you grow.
            </p>
            <div class="hero-stats">
              <div class="stat-item">
                <div class="stat-number">14-Day</div>
                <div class="stat-label">Free Trial</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">No Setup</div>
                <div class="stat-label">Fees</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
              </div>
            </div>
          </div>
          <div class="hero-right">
            <div class="hero-visual">
              <div class="floating-card main-card">
                <div class="card-icon">💰</div>
                <h3>Value-Driven Pricing</h3>
                <p>Maximum ROI for Your Facility</p>
                <div class="card-features">
                  <span>💡 Pay-as-you-grow</span>
                  <span>🔄 Easy upgrades</span>
                  <span>📊 Transparent costs</span>
                </div>
              </div>
              <div class="floating-card secondary-card">
                <div class="mini-icon">⚡</div>
                <span>Quick Setup</span>
              </div>
              <div class="floating-card tertiary-card">
                <div class="mini-icon">🎯</div>
                <span>Best Value</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Plans -->
    <section class="pricing-plans-section">
      <div class="container">
      <div class="section-header">
          <div class="section-badge">💎 Pricing Plans</div>
          <h2>Choose Your Perfect Plan</h2>
          <p>Flexible solutions tailored to your facility's needs and budget</p>
        </div>
        <div class="pricing-toggle">
          <span class="toggle-label">Billing Cycle:</span>
          <div class="toggle-buttons">
            <button class="toggle-btn active" data-period="monthly">Monthly</button>
            <button class="toggle-btn" data-period="yearly">Yearly <span class="discount">Save 20%</span></button>
          </div>
        </div>
        <?php include __DIR__ . '/umakant/public_plans.php'; ?>
      </div>
    </section>

    <!-- Features Comparison -->
    <section class="features-comparison-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📊 Feature Comparison</div>
          <h2>Compare All Features</h2>
          <p>See what's included in each plan</p>
        </div>
        <div class="comparison-table">
          <div class="table-header">
            <div class="feature-name">Features</div>
            <div class="plan-name">Starter</div>
            <div class="plan-name">Professional</div>
            <div class="plan-name">Enterprise</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Users</div>
            <div class="feature-value">Up to 10</div>
            <div class="feature-value">Up to 50</div>
            <div class="feature-value">Unlimited</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Patient Records</div>
            <div class="feature-value">✓</div>
            <div class="feature-value">✓</div>
            <div class="feature-value">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Appointments</div>
            <div class="feature-value">✓</div>
            <div class="feature-value">✓</div>
            <div class="feature-value">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Inventory Management</div>
            <div class="feature-value">✗</div>
            <div class="feature-value">✓</div>
            <div class="feature-value">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Billing & Invoicing</div>
            <div class="feature-value">✗</div>
            <div class="feature-value">✓</div>
            <div class="feature-value">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Advanced Analytics</div>
            <div class="feature-value">✗</div>
            <div class="feature-value">✓</div>
            <div class="feature-value">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Multi-location</div>
            <div class="feature-value">✗</div>
            <div class="feature-value">✗</div>
            <div class="feature-value">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Custom Integrations</div>
            <div class="feature-value">✗</div>
            <div class="feature-value">✗</div>
            <div class="feature-value">✓</div>
          </div>
          <div class="table-row">
            <div class="feature-name">Support</div>
            <div class="feature-value">Email</div>
            <div class="feature-value">Priority</div>
            <div class="feature-value">Dedicated</div>
        </div>
          </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="pricing-faq-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">❓ FAQ</div>
          <h2>Frequently Asked Questions</h2>
          <p>Everything you need to know about our pricing</p>
        </div>
        <div class="faq-grid">
          <div class="faq-item">
            <div class="faq-question">
              <h3>Can I change my plan later?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Yes! You can upgrade or downgrade your plan at any time. Changes take effect immediately.</p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>Is there a setup fee?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>No setup fees! We believe in transparent pricing with no hidden costs.</p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>What's included in the free trial?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Your 14-day free trial includes all Professional plan features with full support.</p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>Do you offer custom pricing?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Yes, for Enterprise customers we offer custom pricing based on your specific needs.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-card">
          <div class="cta-content">
            <div class="cta-icon">🚀</div>
            <h2>Ready to Get Started?</h2>
            <p>Join hundreds of healthcare facilities that trust us with their management needs. Start your free trial today.</p>
            <div class="cta-buttons">
              <a href="contact.php" class="btn-primary">Start Free Trial</a>
              <a href="contact.php" class="btn-secondary">Contact Sales</a>
            </div>
            <div class="cta-features">
              <span>✅ 14-Day Free Trial</span>
              <span>✅ No Credit Card Required</span>
              <span>✅ Cancel Anytime</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>

  <script>
    // Enhanced animations and interactions
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-in');
        }
      });
    }, observerOptions);

    // Observe all elements for animation
    document.querySelectorAll('.pricing-card, .faq-item, .table-row').forEach(el => {
      observer.observe(el);
    });

    // Floating animation for hero cards
    const floatingCards = document.querySelectorAll('.floating-card');
    floatingCards.forEach((card, index) => {
      card.style.animationDelay = `${index * 0.2}s`;
    });

    // Enhanced button interactions
    document.querySelectorAll('.btn-primary, .btn-secondary, .plan-btn').forEach(button => {
      button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-4px) scale(1.02)';
      });
      
      button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });

    // Pricing toggle functionality
    document.querySelectorAll('.toggle-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');
        
        // Update pricing display based on period
        const period = this.dataset.period;
        updatePricing(period);
      });
    });

    function updatePricing(period) {
      const prices = {
        monthly: {
          starter: 99,
          professional: 299,
          enterprise: 599
        },
        yearly: {
          starter: 79,
          professional: 239,
          enterprise: 479
        }
      };
      
      const amounts = document.querySelectorAll('.amount');
      amounts[0].textContent = prices[period].starter;
      amounts[1].textContent = prices[period].professional;
      amounts[2].textContent = prices[period].enterprise;
      
      const periods = document.querySelectorAll('.period');
      periods.forEach(periodEl => {
        periodEl.textContent = period === 'yearly' ? '/month (billed yearly)' : '/month';
      });
    }

    // FAQ Toggle functionality
    document.querySelectorAll('.faq-question').forEach(question => {
      question.addEventListener('click', function() {
        const faqItem = this.parentElement;
        const answer = faqItem.querySelector('.faq-answer');
        const toggle = this.querySelector('.faq-toggle');
        
        if (faqItem.classList.contains('active')) {
          faqItem.classList.remove('active');
          toggle.textContent = '+';
        } else {
          faqItem.classList.add('active');
          toggle.textContent = '−';
        }
      });
    });
  </script>
</body>
</html>