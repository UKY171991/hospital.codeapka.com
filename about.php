<?php $page = 'about'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>About Us — Advanced Pathology & Hospital Management System</title>
  <meta name="description" content="Learn about our mission to revolutionize healthcare through innovative hospital management solutions. Trusted by 500+ facilities worldwide.">
  
  <!-- Enhanced CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <!-- Enhanced Hero Section -->
    <section class="about-hero-section position-relative overflow-hidden">
      <div class="hero-background">
        <div class="hero-particles"></div>
        <div class="hero-gradient"></div>
        <div class="floating-shapes">
          <div class="shape shape-1"></div>
          <div class="shape shape-2"></div>
          <div class="shape shape-3"></div>
        </div>
      </div>
      <div class="container">
        <div class="row align-items-center min-vh-100">
          <div class="col-lg-6" data-aos="fade-right">
            <div class="hero-content">
              <div class="hero-badge animate__animated animate__fadeInDown">
                <span class="badge-icon">🏢</span>
                <span class="badge-text">Established 2010 • Industry Leader</span>
              </div>
              <h1 class="hero-title display-3 fw-bold">
                Transforming 
                <span class="gradient-text-rainbow glow-text">Healthcare</span>
                Through Innovation
              </h1>
              <p class="hero-description lead">
                We are pioneers in healthcare technology, crafting cutting-edge hospital management solutions that empower medical professionals and enhance patient experiences across the globe.
              </p>
              <div class="hero-stats row g-4 mt-4">
                <div class="col-4">
                  <div class="stat-item text-center">
                    <div class="stat-number h2 fw-bold text-primary mb-0" data-counter="13">0</div>
                    <div class="stat-label text-muted">Years Excellence</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="stat-item text-center">
                    <div class="stat-number h2 fw-bold text-success mb-0" data-counter="500">0</div>
                    <div class="stat-label text-muted">Facilities</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="stat-item text-center">
                    <div class="stat-number h2 fw-bold text-info mb-0" data-counter="50">0</div>
                    <div class="stat-label text-muted">Team Members</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6" data-aos="fade-left">
            <div class="hero-visual position-relative">
              <div class="hero-image-container">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                     alt="Modern Hospital" class="img-fluid rounded-4 shadow-lg">
                <div class="floating-card position-absolute top-0 start-0">
                  <div class="card border-0 shadow">
                    <div class="card-body p-3">
                      <h6 class="card-title mb-1"><i class="fas fa-heartbeat text-danger"></i> Real-time Monitoring</h6>
                      <p class="card-text small mb-0">24/7 Patient Care System</p>
                    </div>
                  </div>
                </div>
                <div class="floating-card position-absolute bottom-0 end-0">
                  <div class="card border-0 shadow">
                    <div class="card-body p-3">
                      <h6 class="card-title mb-1"><i class="fas fa-shield-alt text-success"></i> HIPAA Compliant</h6>
                      <p class="card-text small mb-0">Secure & Private</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="row g-5">
          <div class="col-lg-6" data-aos="fade-up">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body p-5">
                <div class="text-center mb-4">
                  <div class="icon-box bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-bullseye fa-2x"></i>
                  </div>
                </div>
                <h3 class="text-center mb-4">Our Mission</h3>
                <p class="text-muted text-center lead">
                  To revolutionize healthcare delivery by providing innovative, user-friendly technology solutions that enhance patient care, streamline operations, and empower healthcare professionals to focus on what matters most - healing.
                </p>
              </div>
            </div>
          </div>
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body p-5">
                <div class="text-center mb-4">
                  <div class="icon-box bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-eye fa-2x"></i>
                  </div>
                </div>
                <h3 class="text-center mb-4">Our Vision</h3>
                <p class="text-muted text-center lead">
                  To be the global leader in healthcare technology, creating a world where every healthcare facility has access to intelligent, efficient, and compassionate technology solutions that improve lives.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
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