<?php $page = 'contact'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Contact Us — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="contact-hero-section">
      <div class="hero-background">
        <div class="hero-particles"></div>
        <div class="hero-gradient"></div>
      </div>
      <div class="container">
        <div class="hero-content">
          <div class="hero-left">
            <div class="hero-badge">
              <span class="badge-icon">📞</span>
              <span class="badge-text">24/7 Support Available</span>
            </div>
            <h1 class="hero-title">
              Get In 
              <span class="gradient-text">Touch</span>
            </h1>
            <p class="hero-description">
              Ready to transform your healthcare facility? Our team is here to help you get started with our comprehensive hospital management solution.
            </p>
            <div class="hero-stats">
              <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">< 2hr</div>
                <div class="stat-label">Response</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Satisfaction</div>
              </div>
            </div>
          </div>
          <div class="hero-right">
            <div class="hero-visual">
              <div class="floating-card main-card">
                <div class="card-icon">💬</div>
                <h3>Expert Support</h3>
                <p>Always Here to Help</p>
                <div class="card-features">
                  <span>📞 Phone Support</span>
                  <span>💬 Live Chat</span>
                  <span>📧 Email Support</span>
                </div>
              </div>
              <div class="floating-card secondary-card">
                <div class="mini-icon">🎯</div>
                <span>Quick Response</span>
              </div>
              <div class="floating-card tertiary-card">
                <div class="mini-icon">🤝</div>
                <span>Personal Touch</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Options -->
    <section class="contact-options-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📞 Contact Options</div>
          <h2>Multiple Ways to Reach Us</h2>
          <p>Choose the method that works best for you</p>
        </div>
        <div class="contact-options-grid">
          <div class="contact-option-card">
            <div class="option-icon">📞</div>
            <h3>Phone Support</h3>
            <p>Speak directly with our healthcare technology experts</p>
            <div class="option-details">
              <div class="detail-item">
                <span class="detail-label">Main Line:</span>
                <span class="detail-value">+1 (555) 123-4567</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Hours:</span>
                <span class="detail-value">24/7 Available</span>
              </div>
            </div>
            <a href="tel:+15551234567" class="option-action">Call Now</a>
          </div>
          <div class="contact-option-card">
            <div class="option-icon">💬</div>
            <h3>Live Chat</h3>
            <p>Get instant answers from our support team</p>
            <div class="option-details">
              <div class="detail-item">
                <span class="detail-label">Response:</span>
                <span class="detail-value">< 2 minutes</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Hours:</span>
                <span class="detail-value">8 AM - 8 PM EST</span>
              </div>
            </div>
            <button class="option-action" onclick="openChat()">Start Chat</button>
          </div>
          <div class="contact-option-card">
            <div class="option-icon">📧</div>
            <h3>Email Support</h3>
            <p>Send us detailed inquiries and get comprehensive responses</p>
            <div class="option-details">
              <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value">support@hospital.codeapka.com</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Response:</span>
                <span class="detail-value">< 4 hours</span>
              </div>
            </div>
            <a href="mailto:support@hospital.codeapka.com" class="option-action">Send Email</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">📝 Contact Form</div>
          <h2>Send Us a Message</h2>
          <p>Fill out the form below and we'll get back to you promptly</p>
        </div>
        <div class="form-container">
          <div class="form-card">
            <form action="#" method="POST" class="contact-form">
              <div class="form-row">
                <div class="form-group">
                  <label for="firstName">First Name *</label>
                  <input type="text" id="firstName" name="firstName" required>
                </div>
                <div class="form-group">
                  <label for="lastName">Last Name *</label>
                  <input type="text" id="lastName" name="lastName" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label for="email">Email Address *</label>
                  <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                  <label for="phone">Phone Number</label>
                  <input type="tel" id="phone" name="phone">
                </div>
              </div>
              <div class="form-group">
                <label for="organization">Organization Name *</label>
                <input type="text" id="organization" name="organization" required>
              </div>
              <div class="form-group">
                <label for="subject">Subject *</label>
                <select id="subject" name="subject" required>
                  <option value="">Select a subject</option>
                  <option value="general">General Inquiry</option>
                  <option value="demo">Request Demo</option>
                  <option value="pricing">Pricing Information</option>
                  <option value="support">Technical Support</option>
                  <option value="partnership">Partnership Opportunity</option>
                </select>
              </div>
              <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" rows="6" required placeholder="Tell us about your healthcare facility and how we can help..."></textarea>
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" name="newsletter" value="1">
                  <span class="checkmark"></span>
                  I'd like to receive updates about new features and healthcare technology insights
                </label>
              </div>
              <div class="form-actions">
                <button type="submit" class="submit-btn">
                  <span class="btn-icon">📤</span>
                  Send Message
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">❓ FAQ</div>
          <h2>Frequently Asked Questions</h2>
          <p>Quick answers to common questions about our services</p>
        </div>
        <div class="faq-grid">
          <div class="faq-item">
            <div class="faq-question">
              <h3>How quickly can we get started?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Most healthcare facilities can be up and running within 2-4 weeks. We offer rapid deployment options for urgent needs.</p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>Do you offer training and support?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Yes! We provide comprehensive training for your team and ongoing support to ensure smooth operations.</p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>Is the system HIPAA compliant?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Absolutely. Our system is fully HIPAA compliant with enterprise-grade security and regular audits.</p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>Can we customize the system?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Yes, we offer extensive customization options to meet your specific workflow and requirements.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Support Section -->
    <section class="support-section">
      <div class="container">
        <div class="section-header">
          <div class="section-badge">🛠️ Support</div>
          <h2>Comprehensive Support Services</h2>
          <p>We're here to ensure your success every step of the way</p>
        </div>
        <div class="support-grid">
          <div class="support-card">
            <div class="support-icon">🎓</div>
            <h3>Training & Onboarding</h3>
            <p>Comprehensive training programs to get your team up to speed quickly and efficiently.</p>
            <ul class="support-features">
              <li>Custom training sessions</li>
              <li>Video tutorials</li>
              <li>User guides</li>
              <li>Best practices</li>
            </ul>
          </div>
          <div class="support-card">
            <div class="support-icon">🔧</div>
            <h3>Technical Support</h3>
            <p>Expert technical support available 24/7 to resolve any issues and keep your system running smoothly.</p>
            <ul class="support-features">
              <li>24/7 phone support</li>
              <li>Live chat assistance</li>
              <li>Email support</li>
              <li>Remote troubleshooting</li>
            </ul>
          </div>
          <div class="support-card">
            <div class="support-icon">📈</div>
            <h3>Implementation</h3>
            <p>Dedicated implementation specialists to ensure smooth deployment and optimal configuration.</p>
            <ul class="support-features">
              <li>Custom setup</li>
              <li>Data migration</li>
              <li>Integration support</li>
              <li>Go-live assistance</li>
            </ul>
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
            <p>Join hundreds of healthcare providers who have revolutionized their operations with our platform. Let's start the conversation today.</p>
            <div class="cta-buttons">
              <a href="tel:+15551234567" class="btn-primary">Call Now</a>
              <a href="pricing.php" class="btn-secondary">View Pricing</a>
            </div>
            <div class="cta-features">
              <span>✅ Free Consultation</span>
              <span>✅ No Obligation</span>
              <span>✅ Quick Setup</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
    document.querySelectorAll('.contact-option-card, .support-card, .faq-item').forEach(el => {
      observer.observe(el);
    });

    // Floating animation for hero cards
    const floatingCards = document.querySelectorAll('.floating-card');
    floatingCards.forEach((card, index) => {
      card.style.animationDelay = `${index * 0.2}s`;
    });

    // Enhanced button interactions
    document.querySelectorAll('.btn-primary, .btn-secondary, .option-action').forEach(button => {
      button.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-4px) scale(1.02)';
      });
      
      button.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });

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

    // Chat function placeholder
    function openChat() {
      alert('Live chat feature coming soon! Please use phone or email support for now.');
    }

    // Form submission handling
    document.querySelector('.contact-form').addEventListener('submit', function(e) {
      e.preventDefault();
      // Add form submission logic here
      alert('Thank you for your message! We\'ll get back to you within 4 hours.');
    });
  </script>
</body>
</html>