<?php $page = 'home'; ?>
<?php
// Try to fetch the uploaded releases list from the umakant area
function sanitize_upload_html($html){
  if (!$html) return '';
  // Remove script tags entirely
  $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

  // If a full document is returned, try to extract the body content
  if (stripos($html, '<body') !== false) {
    if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $m)) {
      $html = $m[1];
    }
  } else {
    // Remove head and html wrappers if present
    $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
    $html = preg_replace('/<\/?html[^>]*>/is', '', $html);
  }

  // Trim and return
  return trim($html);
}

function fetch_upload_list_html(){
  // Directly include our updated public_upload_list.php file
  $localPath = __DIR__ . '/umakant/public_upload_list.php';
  if (is_readable($localPath)) {
    ob_start();
    try { 
      include $localPath; 
    } catch (Throwable $e) { 
      return '<div class="releases-empty">Error loading releases: ' . $e->getMessage() . '</div>';
    }
    $out = ob_get_clean();
    if ($out) return $out;
  }

  // Fallback to HTTP request if direct include fails
  $host = $_SERVER['HTTP_HOST'] ?? 'hospital.codeapka.com';
  $url = 'https://' . $host . '/umakant/public_upload_list.php';

  // Try cURL first
  if (function_exists('curl_version')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($resp !== false && $code >= 200 && $code < 400) {
      return sanitize_upload_html($resp);
    }
  }

  // Fallback to file_get_contents if allow_url_fopen is enabled
  if (ini_get('allow_url_fopen')) {
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $resp = @file_get_contents($url, false, $context);
    if ($resp !== false) return sanitize_upload_html($resp);
  }

  return '<div class="releases-empty">No releases available at this time.</div>';
}

$uploadListHtml = fetch_upload_list_html();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Welcome — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-particles"></div>
        <div class="hero-gradient"></div>
        <div class="particles-bg">
          <div class="particle"></div>
          <div class="particle"></div>
          <div class="particle"></div>
          <div class="particle"></div>
          <div class="particle"></div>
          <div class="particle"></div>
          <div class="particle"></div>
          <div class="particle"></div>
          <div class="particle"></div>
        </div>
      </div>
      <div class="container">
        <div class="hero-content">
          <div class="hero-left">
            <div class="hero-badge">
              <span class="badge-icon">🏆</span>
              <span class="badge-text">Trusted by 500+ Healthcare Facilities</span>
            </div>
            <h1 class="hero-title">
              Transform Your 
              <span class="gradient-text-rainbow glow-text">Healthcare Operations</span>
            </h1>
            <p class="hero-description">
              Streamline workflows, enhance patient care, and boost efficiency with our comprehensive hospital management system designed for modern healthcare facilities.
            </p>
            <div class="hero-buttons">
              <a href="#features" class="btn-primary btn-magnetic ripple">
                <span class="btn-icon">✨</span>
                Explore Features
              </a>
              <a href="contact.php" class="btn-secondary btn-magnetic ripple">
                <span class="btn-icon">📅</span>
                Schedule Demo
              </a>
            </div>
            <div class="hero-stats">
              <div class="stat-item">
                <div class="stat-number">99.9%</div>
                <div class="stat-label">Uptime</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Facilities</div>
              </div>
            </div>
          </div>
          <div class="hero-right">
            <div class="hero-visual">
              <div class="floating-card main-card">
                <div class="card-icon">🏥</div>
                <h3>Advanced Healthcare Management</h3>
                <p>AI-Powered • Secure • Scalable</p>
                <div class="card-features">
                  <span>📊 Real-time Analytics</span>
                  <span>🔒 HIPAA Compliant</span>
                  <span>⚡ Lightning Fast</span>
                </div>
              </div>
              <div class="floating-card secondary-card">
                <div class="mini-icon">📋</div>
                <span>Patient Records</span>
              </div>
              <div class="floating-card tertiary-card">
                <div class="mini-icon">💰</div>
                <span>Billing System</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Trust Indicators -->
    <section class="trust-section">
      <div class="container">
        <div class="trust-grid stagger-animation">
          <div class="trust-item card-hover-glow">
            <div class="trust-icon pulse-glow">🔒</div>
            <h4>HIPAA Compliant</h4>
            <p>Full compliance with healthcare data regulations</p>
          </div>
          <div class="trust-item card-hover-glow">
            <div class="trust-icon pulse-glow">🛡️</div>
            <h4>ISO 27001</h4>
            <p>Enterprise-grade security standards</p>
          </div>
          <div class="trust-item card-hover-glow">
            <div class="trust-icon pulse-glow">⚡</div>
            <h4>99.9% Uptime</h4>
            <p>Reliable performance you can count on</p>
          </div>
          <div class="trust-item card-hover-glow">
            <div class="trust-icon pulse-glow">🌐</div>
            <h4>Global Support</h4>
            <p>24/7 support across all time zones</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Live Statistics Counter -->
    <section class="stats-counter-section">
      <div class="container">
        <div class="stats-counter-grid">
          <div class="counter-item">
            <div class="counter-icon">🏥</div>
            <div class="counter-number" data-target="500">0</div>
            <div class="counter-label">Healthcare Facilities</div>
          </div>
          <div class="counter-item">
            <div class="counter-icon">👥</div>
            <div class="counter-number" data-target="50000">0</div>
            <div class="counter-label">Active Users</div>
          </div>
          <div class="counter-item">
            <div class="counter-icon">📊</div>
            <div class="counter-number" data-target="1000000">0</div>
            <div class="counter-label">Patient Records</div>
          </div>
          <div class="counter-item">
            <div class="counter-icon">🌍</div>
            <div class="counter-number" data-target="25">0</div>
            <div class="counter-label">Countries Served</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Client Logos Section -->
    <section class="clients-section">
      <div class="container">
        <div class="section-header">
          <h2>Trusted by Leading Healthcare Organizations</h2>
          <p>Join the growing community of healthcare professionals who rely on our platform</p>
        </div>
        <div class="clients-carousel">
          <div class="client-logo">
            <div class="logo-placeholder">🏥 City General</div>
          </div>
          <div class="client-logo">
            <div class="logo-placeholder">⚕️ Regional Medical</div>
          </div>
          <div class="client-logo">
            <div class="logo-placeholder">🩺 Health Plus</div>
          </div>
          <div class="client-logo">
            <div class="logo-placeholder">🏥 Metro Hospital</div>
          </div>
          <div class="client-logo">
            <div class="logo-placeholder">⚕️ Care Center</div>
          </div>
          <div class="client-logo">
            <div class="logo-placeholder">🩺 Wellness Clinic</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">✨ Core Features</div>
          <h2>Powerful Features for Modern Healthcare</h2>
          <p>Everything you need to manage your healthcare facility efficiently and securely</p>
        </div>
        <div class="features-grid stagger-animation">
          <div class="feature-card card-hover-lift interactive-hover">
            <div class="feature-icon">
              <span>📋</span>
            </div>
            <h3>Patient Records</h3>
            <p>Centralized EHR for quick access to patient history, visits and reports with advanced search capabilities.</p>
            <div class="feature-tags">
              <span>EHR Integration</span>
              <span>Advanced Search</span>
              <span>Secure Access</span>
            </div>
            <div class="feature-action">
              <a href="#" class="learn-more">Learn More →</a>
            </div>
          </div>
          <div class="feature-card card-hover-lift interactive-hover">
            <div class="feature-icon">
              <span>💰</span>
            </div>
            <h3>Billing & Inventory</h3>
            <p>Integrated billing, invoices and stock control for consumables with real-time tracking.</p>
            <div class="feature-tags">
              <span>Auto Billing</span>
              <span>Stock Tracking</span>
              <span>Real-time Data</span>
            </div>
            <div class="feature-action">
              <a href="#" class="learn-more">Learn More →</a>
            </div>
          </div>
          <div class="feature-card card-hover-lift interactive-hover">
            <div class="feature-icon">
              <span>🔒</span>
            </div>
            <h3>Secure Access</h3>
            <p>Role-based access controls and audit logs to meet compliance needs with multi-factor authentication.</p>
            <div class="feature-tags">
              <span>Role-based Access</span>
              <span>Audit Logs</span>
              <span>MFA Support</span>
            </div>
            <div class="feature-action">
              <a href="#" class="learn-more">Learn More →</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Latest Releases -->
    <section class="releases-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">🆕 Latest Updates</div>
          <h2>Latest Releases</h2>
          <p>Stay up-to-date with our latest software updates and features</p>
        </div>
        <div class="releases-card">
          <div class="releases-header">
            <h3>Software Releases</h3>
            <p>Access our most recent updates and enhancements to the platform.</p>
          </div>
          <div class="releases-content">
            <?php echo $uploadListHtml; ?>
          </div>
          <div class="releases-footer">
            <a href="#" class="btn-outline">View All Updates</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">💎 Pricing Plans</div>
          <h2>Choose Your Perfect Plan</h2>
          <p>Flexible solutions tailored to your facility's needs</p>
        </div>
        <div class="pricing-content">
          <div class="pricing-intro">
            <h3>All plans include core features with options to customize based on your facility's requirements.</h3>
          </div>
          <div class="container-fluid">
            <?php include __DIR__ . '/umakant/public_plans.php'; ?>
          </div>
          <div class="pricing-contact">
            <h4>Need Help Choosing a Plan?</h4>
            <p>Our team is here to help you find the perfect solution for your healthcare facility.</p>
            <div class="contact-options">
              <?php 
              $whatsapp_number = trim(file_get_contents(__DIR__ . '/get_whatsapp.php'));
              ?>
              <a href="https://wa.me/<?php echo urlencode($whatsapp_number); ?>?text=Hi! I'm interested in your hospital management plans. Can you help me choose the right one?" 
                 class="whatsapp-btn" 
                 target="_blank" 
                 rel="noopener noreferrer">
                <span class="whatsapp-icon">📱</span>
                Chat on WhatsApp
              </a>
              <a href="contact.php" class="contact-btn">
                Contact Sales Team
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">💬 Success Stories</div>
          <h2>What Our Clients Say</h2>
          <p>Real feedback from healthcare professionals using our platform</p>
        </div>
        <div class="testimonials-grid">
          <div class="testimonial-card">
            <div class="testimonial-content">
              <div class="quote-icon">"</div>
              <p>This platform has revolutionized our hospital operations. The efficiency gains are incredible and the support team is always helpful.</p>
            </div>
            <div class="testimonial-author">
              <div class="author-avatar">👨‍⚕️</div>
              <div class="author-info">
                <div class="author-name">Dr. Sarah Johnson</div>
                <div class="author-title">Chief Medical Officer</div>
                <div class="author-hospital">City General Hospital</div>
              </div>
            </div>
          </div>
          <div class="testimonial-card">
            <div class="testimonial-content">
              <div class="quote-icon">"</div>
              <p>The patient management features are intuitive and the support team is always helpful. Highly recommended for any healthcare facility.</p>
            </div>
            <div class="testimonial-author">
              <div class="author-avatar">👩‍⚕️</div>
              <div class="author-info">
                <div class="author-name">Dr. Michael Chen</div>
                <div class="author-title">Hospital Administrator</div>
                <div class="author-hospital">Regional Medical Center</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
      <div class="container">
        <div class="newsletter-card glass-card">
          <div class="newsletter-content">
            <div class="newsletter-icon pulse-glow">📧</div>
            <h2>Stay Updated with Healthcare Innovation</h2>
            <p>Get the latest updates on healthcare technology, industry insights, and platform enhancements delivered to your inbox.</p>
            <form class="newsletter-form" id="newsletterForm">
              <div class="form-group">
                <input type="email" class="form-control newsletter-input" placeholder="Enter your email address" required>
                <button type="submit" class="newsletter-btn btn-magnetic ripple">
                  <span class="btn-text">Subscribe</span>
                  <span class="btn-icon">→</span>
                </button>
              </div>
              <div class="newsletter-privacy">
                <small>We respect your privacy. Unsubscribe at any time.</small>
              </div>
            </form>
            <div class="newsletter-benefits">
              <div class="benefit-item">
                <span class="benefit-icon">📊</span>
                <span>Industry Reports</span>
              </div>
              <div class="benefit-item">
                <span class="benefit-icon">🔔</span>
                <span>Feature Updates</span>
              </div>
              <div class="benefit-item">
                <span class="benefit-icon">💡</span>
                <span>Best Practices</span>
              </div>
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
            <h2>Ready to Transform Your Healthcare Facility?</h2>
            <p>Join hundreds of healthcare providers who have revolutionized their operations with our platform. Schedule a demo today and see the difference.</p>
            <div class="cta-buttons">
              <a href="contact.php" class="btn-primary btn-magnetic ripple">Schedule a Demo</a>
              <a href="about.php" class="btn-secondary btn-magnetic ripple">Learn More</a>
            </div>
            <div class="cta-features">
              <span>✅ 14-Day Free Trial</span>
              <span>✅ No Setup Fees</span>
              <span>✅ 24/7 Support</span>
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
          
          // Trigger counter animation
          if (entry.target.classList.contains('stats-counter-section')) {
            animateCounters();
          }
        }
      });
    }, observerOptions);

    // Observe all elements for animation
    document.querySelectorAll('.feature-card, .testimonial-card, .trust-item, .stats-counter-section, .client-logo').forEach(el => {
      observer.observe(el);
    });

    // Counter Animation
    function animateCounters() {
      const counters = document.querySelectorAll('.counter-number');
      counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const increment = target / 100;
        let current = 0;
        
        const updateCounter = () => {
          if (current < target) {
            current += increment;
            counter.textContent = Math.floor(current).toLocaleString();
            requestAnimationFrame(updateCounter);
          } else {
            counter.textContent = target.toLocaleString();
            counter.classList.add('animate');
          }
        };
        
        updateCounter();
      });
    }

    // Floating animation for hero cards
    const floatingCards = document.querySelectorAll('.floating-card');
    floatingCards.forEach((card, index) => {
      card.style.animationDelay = `${index * 0.2}s`;
    });

    // Enhanced button interactions
    document.querySelectorAll('.btn-primary, .btn-secondary, .whatsapp-btn, .newsletter-btn').forEach(button => {
      button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-4px) scale(1.02)';
      });
      
      button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });

    // Newsletter Form Handling
    document.getElementById('newsletterForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const email = this.querySelector('input[type="email"]').value;
      const btn = this.querySelector('.newsletter-btn');
      const originalText = btn.innerHTML;
      
      // Show loading state
      btn.innerHTML = '<span class="btn-text">Subscribing...</span><span class="btn-icon">⏳</span>';
      btn.disabled = true;
      
      // Simulate API call
      setTimeout(() => {
        btn.innerHTML = '<span class="btn-text">Subscribed!</span><span class="btn-icon">✅</span>';
        btn.style.background = 'var(--gradient-success)';
        
        setTimeout(() => {
          btn.innerHTML = originalText;
          btn.disabled = false;
          btn.style.background = 'var(--gradient-primary)';
          this.reset();
        }, 2000);
      }, 1500);
    });

    // Search Modal Functionality
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.querySelector('.search-input');
      const suggestionTags = document.querySelectorAll('.suggestion-tag');
      
      // Handle suggestion tag clicks
      suggestionTags.forEach(tag => {
        tag.addEventListener('click', function() {
          searchInput.value = this.textContent;
          searchInput.focus();
        });
      });
      
      // Handle search form submission
      document.querySelector('.search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query) {
          // Simulate search functionality
          console.log('Searching for:', query);
          // In a real implementation, you would perform the search here
        }
      });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Client logos infinite scroll animation
    const clientsCarousel = document.querySelector('.clients-carousel');
    if (clientsCarousel) {
      let scrollAmount = 0;
      const scrollSpeed = 0.5;
      
      function autoScroll() {
        scrollAmount += scrollSpeed;
        if (scrollAmount >= clientsCarousel.scrollWidth / 2) {
          scrollAmount = 0;
        }
        clientsCarousel.style.transform = `translateX(-${scrollAmount}px)`;
        requestAnimationFrame(autoScroll);
      }
      
      // Uncomment the line below to enable auto-scrolling
      // autoScroll();
    }

    // Add loading states for better UX
    window.addEventListener('load', function() {
      document.body.classList.add('loaded');
    });
  </script>
</body>
</html>