<?php $page = 'about'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>About Us — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="about-hero-section">
      <div class="hero-background">
        <div class="hero-particles"></div>
        <div class="hero-gradient"></div>
      </div>
      <div class="container">
        <div class="hero-content">
          <div class="hero-left">
            <div class="hero-badge">
              <span class="badge-icon">🏢</span>
              <span class="badge-text">Established 2010</span>
            </div>
            <h1 class="hero-title">
              About 
              <span class="gradient-text-rainbow glow-text">Our Company</span>
            </h1>
            <p class="hero-description">
              We are a leading healthcare technology company dedicated to revolutionizing hospital management through innovative software solutions that enhance patient care and operational efficiency.
            </p>
            <div class="hero-stats">
              <div class="stat-item">
                <div class="stat-number">13+</div>
                <div class="stat-label">Years</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Facilities</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Team Members</div>
              </div>
            </div>
          </div>
          <div class="hero-right">
            <div class="hero-visual">
              <div class="floating-card main-card">
                <div class="card-icon">🏥</div>
                <h3>Healthcare Innovation</h3>
                <p>Transforming Healthcare Management</p>
                <div class="card-features">
                  <span>🎯 Mission-Driven</span>
                  <span>💡 Innovation-Focused</span>
                  <span>🤝 Customer-Centric</span>
                </div>
              </div>
              <div class="floating-card secondary-card">
                <div class="mini-icon">🌟</div>
                <span>Excellence</span>
              </div>
              <div class="floating-card tertiary-card">
                <div class="mini-icon">🚀</div>
                <span>Innovation</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Company Story -->
    <section class="company-story-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📖 Our Story</div>
          <h2>The Journey That Shaped Us</h2>
          <p>From humble beginnings to becoming a trusted partner in healthcare technology</p>
        </div>
        <div class="story-grid stagger-animation">
          <div class="story-card card-hover-lift interactive-hover">
            <div class="story-icon pulse-glow">🎯</div>
            <h3>Our Mission</h3>
            <p>To revolutionize healthcare management by providing innovative, reliable, and user-friendly software solutions that enhance patient care and operational efficiency.</p>
          </div>
          <div class="story-card card-hover-lift interactive-hover">
            <div class="story-icon pulse-glow">👁️</div>
            <h3>Our Vision</h3>
            <p>To be the global leader in healthcare technology, empowering healthcare facilities worldwide with cutting-edge solutions that improve patient outcomes.</p>
          </div>
          <div class="story-card card-hover-lift interactive-hover">
            <div class="story-icon pulse-glow">💎</div>
            <h3>Our Values</h3>
            <p>Innovation, integrity, excellence, and customer satisfaction drive everything we do. We believe in building lasting partnerships based on trust and mutual success.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Timeline Section -->
    <section class="timeline-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📅 Timeline</div>
          <h2>Our Growth Journey</h2>
          <p>Key milestones that shaped our company's evolution</p>
        </div>
        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-marker">2010</div>
            <div class="timeline-content">
              <h3>Company Founded</h3>
              <p>Started with a vision to transform healthcare management through technology</p>
            </div>
          </div>
          <div class="timeline-item">
            <div class="timeline-marker">2015</div>
            <div class="timeline-content">
              <h3>First Major Client</h3>
              <p>Successfully deployed our system in a 500-bed hospital</p>
            </div>
          </div>
          <div class="timeline-item">
            <div class="timeline-marker">2018</div>
            <div class="timeline-content">
              <h3>Market Expansion</h3>
              <p>Expanded to serve healthcare facilities across multiple states</p>
            </div>
          </div>
          <div class="timeline-item">
            <div class="timeline-marker">2023</div>
            <div class="timeline-content">
              <h3>500+ Facilities</h3>
              <p>Reached a major milestone serving over 500 healthcare facilities</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">👥 Our Team</div>
          <h2>Meet the Experts</h2>
          <p>Dedicated professionals committed to healthcare innovation</p>
        </div>
        <div class="team-grid">
          <div class="team-card">
            <div class="team-avatar">👨‍💼</div>
            <h3>Dr. Sarah Johnson</h3>
            <div class="team-role">Chief Executive Officer</div>
            <p>20+ years of healthcare technology experience with a passion for innovation and patient care improvement.</p>
            <div class="team-social">
              <span>LinkedIn</span>
              <span>Email</span>
            </div>
          </div>
          <div class="team-card">
            <div class="team-avatar">👩‍💻</div>
            <h3>Michael Chen</h3>
            <div class="team-role">Chief Technology Officer</div>
            <p>Leading our technical innovation with expertise in AI, cloud computing, and healthcare systems.</p>
            <div class="team-social">
              <span>LinkedIn</span>
              <span>Email</span>
            </div>
          </div>
          <div class="team-card">
            <div class="team-avatar">👨‍⚕️</div>
            <h3>Dr. Emily Rodriguez</h3>
            <div class="team-role">Chief Medical Officer</div>
            <p>Ensuring our solutions meet the highest medical standards and regulatory requirements.</p>
            <div class="team-social">
              <span>LinkedIn</span>
              <span>Email</span>
            </div>
          </div>
          <div class="team-card">
            <div class="team-avatar">👩‍🎨</div>
            <h3>David Kim</h3>
            <div class="team-role">Head of Product Design</div>
            <p>Creating intuitive user experiences that healthcare professionals love to use.</p>
            <div class="team-social">
              <span>LinkedIn</span>
              <span>Email</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📊 By The Numbers</div>
          <h2>Our Impact</h2>
          <p>Real numbers that demonstrate our commitment to healthcare excellence</p>
        </div>
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">🏥</div>
            <div class="stat-number">500+</div>
            <div class="stat-label">Healthcare Facilities</div>
            <div class="stat-description">Serving hospitals, clinics, and medical centers nationwide</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-number">50+</div>
            <div class="stat-label">Team Members</div>
            <div class="stat-description">Dedicated professionals across development, support, and sales</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">📈</div>
            <div class="stat-number">99.9%</div>
            <div class="stat-label">Uptime</div>
            <div class="stat-description">Reliable system performance you can count on</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-number">4.9/5</div>
            <div class="stat-label">Customer Rating</div>
            <div class="stat-description">Consistently high satisfaction from our clients</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Awards Section -->
    <section class="awards-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">🏆 Recognition</div>
          <h2>Awards & Recognition</h2>
          <p>Industry recognition for our commitment to healthcare innovation</p>
        </div>
        <div class="awards-grid">
          <div class="award-card">
            <div class="award-icon">🏆</div>
            <h3>Best Healthcare Software 2023</h3>
            <p>Healthcare Technology Association</p>
          </div>
          <div class="award-card">
            <div class="award-icon">🌟</div>
            <h3>Innovation Excellence Award</h3>
            <p>Digital Health Summit</p>
          </div>
          <div class="award-card">
            <div class="award-icon">💎</div>
            <h3>Customer Choice Award</h3>
            <p>Healthcare IT News</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-card">
          <div class="cta-content">
            <div class="cta-icon">🤝</div>
            <h2>Ready to Partner With Us?</h2>
            <p>Join hundreds of healthcare facilities that trust us with their management needs. Let's discuss how we can help transform your operations.</p>
            <div class="cta-buttons">
              <a href="contact.php" class="btn-primary">Get Started Today</a>
              <a href="pricing.php" class="btn-secondary">View Pricing</a>
            </div>
            <div class="cta-features">
              <span>✅ Free Consultation</span>
              <span>✅ Custom Solutions</span>
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
        }
      });
    }, observerOptions);

    // Observe all elements for animation
    document.querySelectorAll('.story-card, .team-card, .stat-card, .award-card, .timeline-item').forEach(el => {
      observer.observe(el);
    });

    // Floating animation for hero cards
    const floatingCards = document.querySelectorAll('.floating-card');
    floatingCards.forEach((card, index) => {
      card.style.animationDelay = `${index * 0.2}s`;
    });

    // Enhanced button interactions
    document.querySelectorAll('.btn-primary, .btn-secondary').forEach(button => {
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