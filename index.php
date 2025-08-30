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

  // As a last resort, try including the local file and capturing output (may require same permissions/session)
  $localPath = __DIR__ . '/umakant/upload_list.php';
  if (is_readable($localPath)) {
    ob_start();
    try { include $localPath; } catch (Throwable $e) { /* ignore */ }
    $out = ob_get_clean();
    if ($out) return sanitize_upload_html($out);
  }

  return '<div class="small">No releases available or failed to fetch the uploads list.</div>';
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
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <div class="hero-badge">
          <span>🏆 Trusted by 500+ Healthcare Facilities</span>
        </div>
        <h1>Transform Your Healthcare Operations</h1>
        <p class="lead">Streamline workflows, enhance patient care, and boost efficiency with our comprehensive hospital management system designed for modern healthcare facilities.</p>
        <div class="hero-buttons">
          <a class="button" href="#features">Explore Features</a>
          <a class="button ghost" href="contact.php">Schedule Demo</a>
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
      <div class="hero-image">
        <div class="hero-image-placeholder floating">
          <div class="hero-content-inner">
            <div class="hero-icon">🏥</div>
            <div class="hero-text">Advanced Healthcare Management</div>
            <div class="hero-subtitle">AI-Powered • Secure • Scalable</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Trust Indicators Section -->
    <section class="section trust-section">
      <div class="trust-logos">
        <div class="trust-item">
          <div class="trust-icon">🔒</div>
          <span>HIPAA Compliant</span>
        </div>
        <div class="trust-item">
          <div class="trust-icon">🛡️</div>
          <span>ISO 27001</span>
        </div>
        <div class="trust-item">
          <div class="trust-icon">⚡</div>
          <span>99.9% Uptime</span>
        </div>
        <div class="trust-item">
          <div class="trust-icon">🌐</div>
          <span>Global Support</span>
        </div>
      </div>
    </section>

    <!-- Latest Releases Section -->
    <section class="section releases-section">
      <div class="section-header scroll-reveal">
        <div class="section-badge">🆕 Latest Updates</div>
        <h2>Latest Releases</h2>
        <p>Stay up-to-date with our latest software updates and features</p>
      </div>
      <div class="card hover-lift">
        <div class="releases-header">
          <h3>Software Releases</h3>
          <p class="small">Access our most recent updates and enhancements to the platform.</p>
        </div>
        <div class="releases-content">
          <?php echo $uploadListHtml; ?>
        </div>
        <div class="releases-footer">
          <a href="#" class="button ghost">View All Updates</a>
        </div>
      </div>
    </section>
    
    <!-- Features Section -->
    <section id="features" class="section features-section">
      <div class="section-header scroll-reveal">
        <div class="section-badge">✨ Core Features</div>
        <h2>Powerful Features</h2>
        <p>Everything you need to manage your healthcare facility efficiently</p>
      </div>
      <div class="card-grid">
        <div class="card feature-card hover-scale">
          <div class="feature-icon">📋</div>
          <h3>Patient Records</h3>
          <p class="small">Centralized EHR for quick access to patient history, visits and reports with advanced search capabilities.</p>
          <div class="feature-highlights">
            <span>EHR Integration</span>
            <span>Advanced Search</span>
            <span>Secure Access</span>
          </div>
        </div>
        <div class="card feature-card hover-scale">
          <div class="feature-icon">📅</div>
          <h3>Appointments</h3>
          <p class="small">Online booking, doctor schedules and automated reminders with calendar integration.</p>
          <div class="feature-highlights">
            <span>Online Booking</span>
            <span>Auto Reminders</span>
            <span>Calendar Sync</span>
          </div>
        </div>
        <div class="card feature-card hover-scale">
          <div class="feature-icon">💰</div>
          <h3>Billing & Inventory</h3>
          <p class="small">Integrated billing, invoices and stock control for consumables with real-time tracking.</p>
          <div class="feature-highlights">
            <span>Auto Billing</span>
            <span>Stock Tracking</span>
            <span>Real-time Data</span>
          </div>
        </div>
        <div class="card feature-card hover-scale">
          <div class="feature-icon">🔒</div>
          <h3>Secure Access</h3>
          <p class="small">Role-based access controls and audit logs to meet compliance needs with multi-factor authentication.</p>
          <div class="feature-highlights">
            <span>Role-based Access</span>
            <span>Audit Logs</span>
            <span>MFA Support</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section class="section pricing-section">
      <div class="section-header scroll-reveal">
        <div class="section-badge">💎 Pricing Plans</div>
        <h2>Available Plans</h2>
        <p>Flexible solutions tailored to your facility's needs</p>
      </div>
      <div class="card hover-lift">
        <div class="pricing-intro">
          <h3>Choose Your Perfect Plan</h3>
          <p class="small">All plans include core features with options to customize based on your facility's requirements.</p>
        </div>
        <div class="pricing-content">
          <?php include __DIR__ . '/umakant/public_plans.php'; ?>
        </div>
        <div class="pricing-contact">
          <h4>Need Help Choosing a Plan?</h4>
          <p class="small">Our team is here to help you find the perfect solution for your healthcare facility.</p>
          <div class="contact-buttons">
            <a href="https://wa.me/919876543210?text=Hi! I'm interested in your hospital management plans. Can you help me choose the right one?" 
               class="btn whatsapp-btn" 
               target="_blank" 
               rel="noopener noreferrer">
              <span class="whatsapp-icon">📱</span>
              Chat on WhatsApp
            </a>
            <a href="contact.php" class="button ghost">
              Contact Sales Team
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section testimonials-section">
      <div class="section-header scroll-reveal">
        <div class="section-badge">💬 Success Stories</div>
        <h2>What Our Clients Say</h2>
        <p>Real feedback from healthcare professionals using our platform</p>
      </div>
      <div class="testimonials-grid">
        <div class="card testimonial-card hover-lift">
          <div class="testimonial-content">
            <p>"This platform has revolutionized our hospital operations. The efficiency gains are incredible!"</p>
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
        <div class="card testimonial-card hover-lift">
          <div class="testimonial-content">
            <p>"The patient management features are intuitive and the support team is always helpful."</p>
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
    </section>

    <!-- CTA Section -->
    <section class="section cta-section">
      <div class="card text-center hover-lift">
        <div class="cta-content">
          <div class="feature-icon mb-4 floating" style="width: 120px; height: 120px; font-size: 3.5rem; margin: 0 auto;">🚀</div>
          <h3>Ready to Transform Your Healthcare Facility?</h3>
          <p class="small">Join hundreds of healthcare providers who have revolutionized their operations with our platform. Schedule a demo today and see the difference.</p>
          <div class="cta-buttons">
            <a class="btn" href="contact.php">Schedule a Demo</a>
            <a class="button ghost" href="about.php">Learn More</a>
          </div>
          <div class="cta-features">
            <span>✅ 14-Day Free Trial</span>
            <span>✅ No Setup Fees</span>
            <span>✅ 24/7 Support</span>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>

  <script>
    // Enhanced scroll reveal animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
        }
      });
    }, observerOptions);

    // Observe all scroll-reveal elements
    document.querySelectorAll('.scroll-reveal').forEach(el => {
      observer.observe(el);
    });

    // Add floating animation to feature icons
    document.querySelectorAll('.feature-icon').forEach((icon, index) => {
      icon.style.animationDelay = `${index * 0.2}s`;
    });

    // Enhanced button hover effects
    document.querySelectorAll('.button, .btn').forEach(button => {
      button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-4px) scale(1.02)';
      });
      
      button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });
  </script>
</body>
</html>