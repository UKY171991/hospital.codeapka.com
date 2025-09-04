<?php $page = 'contact'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Contact Us — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="py-5 bg-gradient-primary text-white position-relative overflow-hidden">
      <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
      </div>
      <div class="container">
        <div class="row align-items-center min-vh-75">
          <div class="col-lg-6" data-aos="fade-right">
            <div class="hero-badge d-inline-block px-3 py-2 bg-white bg-opacity-20 rounded-pill mb-4">
              <i class="fas fa-headset me-2"></i>24/7 Support Available
            </div>
            <h1 class="display-3 fw-bold mb-4">
              Get In <span class="text-warning">Touch</span>
            </h1>
            <p class="lead mb-4 opacity-90">
              Ready to transform your healthcare facility? Our team is here to help you get started with our comprehensive hospital management solution.
            </p>
            <div class="row g-3 mb-4">
              <div class="col-4">
                <div class="text-center">
                  <h3 class="fw-bold" data-counter="24">0</h3>
                  <small class="opacity-75">Hours Support</small>
                </div>
              </div>
              <div class="col-4">
                <div class="text-center">
                  <h3 class="fw-bold" data-counter="2">0</h3>
                  <small class="opacity-75">Hour Response</small>
                </div>
              </div>
              <div class="col-4">
                <div class="text-center">
                  <h3 class="fw-bold" data-counter="100">0</h3>
                  <small class="opacity-75">% Satisfaction</small>
                </div>
              </div>
            </div>
            <div class="d-flex flex-wrap gap-3">
              <a href="#contact-form" class="btn btn-light btn-lg px-4 py-3 shadow hover-lift">
                <i class="fas fa-paper-plane me-2"></i>Send Message
              </a>
              <a href="tel:+1234567890" class="btn btn-outline-light btn-lg px-4 py-3 hover-lift">
                <i class="fas fa-phone me-2"></i>Call Now
              </a>
            </div>
          </div>
          <div class="col-lg-6" data-aos="fade-left">
            <div class="contact-visual text-center">
              <div class="contact-icons-grid">
                <div class="contact-icon-item" data-aos="zoom-in" data-aos-delay="100">
                  <i class="fas fa-envelope fa-3x text-warning"></i>
                </div>
                <div class="contact-icon-item" data-aos="zoom-in" data-aos-delay="200">
                  <i class="fas fa-phone fa-3x text-success"></i>
                </div>
                <div class="contact-icon-item" data-aos="zoom-in" data-aos-delay="300">
                  <i class="fas fa-map-marker-alt fa-3x text-info"></i>
                </div>
                <div class="contact-icon-item" data-aos="zoom-in" data-aos-delay="400">
                  <i class="fas fa-comments fa-3x text-danger"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Contact Options -->
    <section class="py-5">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
          <div class="section-badge d-inline-block px-3 py-2 bg-primary text-white rounded-pill mb-3">
            <i class="fas fa-phone-alt me-2"></i>Contact Options
          </div>
          <h2 class="display-5 fw-bold">Multiple Ways to Reach Us</h2>
          <p class="lead text-muted">Choose the method that works best for you</p>
        </div>
        
        <div class="row g-4 mb-5">
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="contact-card text-center h-100 p-4 border rounded-3 shadow-sm hover-lift">
              <div class="contact-icon mb-3">
                <i class="fas fa-phone fa-3x text-primary"></i>
              </div>
              <h5 class="fw-bold">Phone Support</h5>
              <p class="text-muted mb-3">Speak directly with our experts</p>
              <div class="contact-details">
                <p class="mb-1"><strong>Main:</strong> +1 (555) 123-4567</p>
                <p class="mb-3"><strong>Hours:</strong> 24/7 Available</p>
              </div>
              <a href="tel:+15551234567" class="btn btn-outline-primary">Call Now</a>
            </div>
          </div>
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="contact-card text-center h-100 p-4 border rounded-3 shadow-sm hover-lift">
              <div class="contact-icon mb-3">
                <i class="fas fa-envelope fa-3x text-success"></i>
              </div>
              <h5 class="fw-bold">Email Support</h5>
              <p class="text-muted mb-3">Get detailed written responses</p>
              <div class="contact-details">
                <p class="mb-1"><strong>Email:</strong> support@hospital.com</p>
                <p class="mb-3"><strong>Response:</strong> < 4 hours</p>
              </div>
              <a href="mailto:support@hospital.com" class="btn btn-outline-success">Send Email</a>
            </div>
          </div>
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="contact-card text-center h-100 p-4 border rounded-3 shadow-sm hover-lift">
              <div class="contact-icon mb-3">
                <i class="fas fa-comments fa-3x text-warning"></i>
              </div>
              <h5 class="fw-bold">Live Chat</h5>
              <p class="text-muted mb-3">Get instant answers online</p>
              <div class="contact-details">
                <p class="mb-1"><strong>Response:</strong> < 2 minutes</p>
                <p class="mb-3"><strong>Hours:</strong> 8 AM - 8 PM EST</p>
              </div>
              <button class="btn btn-outline-warning" onclick="openChat()">Start Chat</button>
            </div>
          </div>
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="contact-card text-center h-100 p-4 border rounded-3 shadow-sm hover-lift">
              <div class="contact-icon mb-3">
                <i class="fas fa-map-marker-alt fa-3x text-info"></i>
              </div>
              <h5 class="fw-bold">Visit Us</h5>
              <p class="text-muted mb-3">Come to our office location</p>
              <div class="contact-details">
                <p class="mb-1"><strong>Address:</strong> 123 Healthcare Ave</p>
                <p class="mb-3"><strong>Hours:</strong> Mon-Fri 9-5</p>
              </div>
              <a href="#map" class="btn btn-outline-info">View Map</a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Contact Form Section -->
    <section class="py-5 bg-light" id="contact-form">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
          <div class="section-badge d-inline-block px-3 py-2 bg-success text-white rounded-pill mb-3">
            <i class="fas fa-paper-plane me-2"></i>Send Message
          </div>
          <h2 class="display-5 fw-bold">Get In Touch With Us</h2>
          <p class="lead text-muted">Fill out the form below and we'll get back to you promptly</p>
        </div>
        
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="card shadow-lg border-0" data-aos="fade-up" data-aos-delay="200">
              <div class="card-body p-5">
                <form action="#" method="POST" class="contact-form">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="firstName" class="form-label fw-semibold">First Name *</label>
                      <input type="text" class="form-control form-control-lg" id="firstName" name="firstName" required>
                    </div>
                    <div class="col-md-6">
                      <label for="lastName" class="form-label fw-semibold">Last Name *</label>
                      <input type="text" class="form-control form-control-lg" id="lastName" name="lastName" required>
                    </div>
                  </div>
                  
                  <div class="row g-3 mt-2">
                    <div class="col-md-6">
                      <label for="email" class="form-label fw-semibold">Email Address *</label>
                      <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                    </div>
                    <div class="col-md-6">
                      <label for="phone" class="form-label fw-semibold">Phone Number</label>
                      <input type="tel" class="form-control form-control-lg" id="phone" name="phone">
                    </div>
                  </div>
                  
                  <div class="mt-3">
                    <label for="organization" class="form-label fw-semibold">Organization Name *</label>
                    <input type="text" class="form-control form-control-lg" id="organization" name="organization" required>
                  </div>
                  
                  <div class="mt-3">
                    <label for="subject" class="form-label fw-semibold">Subject *</label>
                    <select class="form-select form-select-lg" id="subject" name="subject" required>
                      <option value="">Select a subject</option>
                      <option value="general">General Inquiry</option>
                      <option value="demo">Request Demo</option>
                      <option value="pricing">Pricing Information</option>
                      <option value="support">Technical Support</option>
                      <option value="partnership">Partnership Opportunity</option>
                    </select>
                  </div>
                  
                  <div class="mt-3">
                    <label for="message" class="form-label fw-semibold">Message *</label>
                    <textarea class="form-control" id="message" name="message" rows="6" required 
                              placeholder="Tell us about your healthcare facility and how we can help..."></textarea>
                  </div>
                  
                  <div class="mt-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" value="1">
                      <label class="form-check-label" for="newsletter">
                        I'd like to receive updates about new features and healthcare technology insights
                      </label>
                    </div>
                  </div>
                  
                  <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3 shadow hover-lift">
                      <i class="fas fa-paper-plane me-2"></i>Send Message
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <!-- FAQ Section -->
    <section class="py-5">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
          <div class="section-badge d-inline-block px-3 py-2 bg-warning text-white rounded-pill mb-3">
            <i class="fas fa-question-circle me-2"></i>FAQ
          </div>
          <h2 class="display-5 fw-bold">Frequently Asked Questions</h2>
          <p class="lead text-muted">Get quick answers to common questions</p>
        </div>
        
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="accordion" id="faqAccordion" data-aos="fade-up" data-aos-delay="200">
              <div class="accordion-item border-0 shadow-sm mb-3">
                <h2 class="accordion-header">
                  <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                    How quickly can we get started?
                  </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    Most healthcare facilities can be up and running within 2-4 weeks. We offer rapid deployment options for urgent needs and have dedicated implementation specialists to ensure smooth setup.
                  </div>
                </div>
              </div>
              
              <div class="accordion-item border-0 shadow-sm mb-3">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    Do you offer training and support?
                  </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    Yes! We provide comprehensive training for your team including custom training sessions, video tutorials, user guides, and ongoing 24/7 support to ensure smooth operations.
                  </div>
                </div>
              </div>
              
              <div class="accordion-item border-0 shadow-sm mb-3">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    Is the system HIPAA compliant?
                  </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    Absolutely. Our system is fully HIPAA compliant with enterprise-grade security, encrypted data transmission, secure cloud storage, and regular security audits to protect patient information.
                  </div>
                </div>
              </div>
              
              <div class="accordion-item border-0 shadow-sm mb-3">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                    Can we customize the system?
                  </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    Yes, we offer extensive customization options to meet your specific workflow and requirements. Our system can be tailored to match your facility's unique processes and branding.
                  </div>
                </div>
              </div>
              
              <div class="accordion-item border-0 shadow-sm mb-3">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                    What about data migration from our current system?
                  </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    We handle complete data migration from your existing systems with zero data loss. Our migration specialists work closely with your team to ensure seamless transition of all patient records, reports, and historical data.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Support Section -->
    <section class="py-5 bg-dark text-white">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
          <div class="section-badge d-inline-block px-3 py-2 bg-gradient-primary text-white rounded-pill mb-3">
            <i class="fas fa-tools me-2"></i>Support Services
          </div>
          <h2 class="display-5 fw-bold">Comprehensive Support</h2>
          <p class="lead opacity-75">We're here to ensure your success every step of the way</p>
        </div>
        
        <div class="row g-4">
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="support-card text-center h-100 p-4 bg-white bg-opacity-10 rounded-3 backdrop-blur">
              <div class="support-icon mb-3">
                <i class="fas fa-graduation-cap fa-3x text-warning"></i>
              </div>
              <h5 class="fw-bold mb-3">Training & Onboarding</h5>
              <p class="mb-4 opacity-75">Comprehensive training programs to get your team up to speed quickly</p>
              <ul class="list-unstyled text-start">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Custom training sessions</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Video tutorials</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>User guides</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Best practices</li>
              </ul>
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="support-card text-center h-100 p-4 bg-white bg-opacity-10 rounded-3 backdrop-blur">
              <div class="support-icon mb-3">
                <i class="fas fa-wrench fa-3x text-info"></i>
              </div>
              <h5 class="fw-bold mb-3">Technical Support</h5>
              <p class="mb-4 opacity-75">Expert technical support available 24/7 to keep your system running</p>
              <ul class="list-unstyled text-start">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>24/7 phone support</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Live chat assistance</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email support</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Remote troubleshooting</li>
              </ul>
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="support-card text-center h-100 p-4 bg-white bg-opacity-10 rounded-3 backdrop-blur">
              <div class="support-icon mb-3">
                <i class="fas fa-chart-line fa-3x text-success"></i>
              </div>
              <h5 class="fw-bold mb-3">Implementation</h5>
              <p class="mb-4 opacity-75">Dedicated specialists ensure smooth deployment and optimal configuration</p>
              <ul class="list-unstyled text-start">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Custom setup</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Data migration</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Integration support</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Go-live assistance</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="py-5" id="map">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
          <div class="section-badge d-inline-block px-3 py-2 bg-info text-white rounded-pill mb-3">
            <i class="fas fa-map-marker-alt me-2"></i>Our Location
          </div>
          <h2 class="display-5 fw-bold">Visit Our Office</h2>
          <p class="lead text-muted">Come see us in person at our headquarters</p>
        </div>
        
        <div class="row align-items-center">
          <div class="col-lg-6" data-aos="fade-right">
            <div class="office-info">
              <h4 class="fw-bold mb-3">Headquarters</h4>
              <div class="contact-detail mb-3">
                <i class="fas fa-map-marker-alt text-primary me-3"></i>
                <div>
                  <strong>Address:</strong><br>
                  123 Healthcare Avenue<br>
                  Medical District, NY 10001
                </div>
              </div>
              <div class="contact-detail mb-3">
                <i class="fas fa-clock text-primary me-3"></i>
                <div>
                  <strong>Office Hours:</strong><br>
                  Monday - Friday: 9:00 AM - 5:00 PM<br>
                  Saturday: 10:00 AM - 2:00 PM<br>
                  Sunday: Closed
                </div>
              </div>
              <div class="contact-detail mb-3">
                <i class="fas fa-parking text-primary me-3"></i>
                <div>
                  <strong>Parking:</strong><br>
                  Free visitor parking available<br>
                  Accessible parking spots
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6" data-aos="fade-left">
            <div class="map-container bg-light rounded-3 p-4">
              <div class="map-placeholder text-center py-5">
                <i class="fas fa-map fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Interactive Map</h5>
                <p class="text-muted">Google Maps integration would be placed here</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-gradient-primary text-white">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-8" data-aos="fade-right">
            <h2 class="display-6 fw-bold mb-3">Ready to Transform Your Healthcare Facility?</h2>
            <p class="lead mb-4">Join hundreds of healthcare providers who have revolutionized their operations with our platform. Let's start the conversation today.</p>
            <div class="d-flex flex-wrap gap-3">
              <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>Free Consultation</span>
              </div>
              <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>No Obligation</span>
              </div>
              <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>Quick Setup</span>
              </div>
            </div>
          </div>
          <div class="col-lg-4 text-center" data-aos="fade-left">
            <div class="d-grid gap-3">
              <a href="tel:+15551234567" class="btn btn-light btn-lg px-4 py-3 shadow hover-lift">
                <i class="fas fa-phone me-2"></i>Call Now
              </a>
              <a href="pricing.php" class="btn btn-outline-light btn-lg px-4 py-3 hover-lift">
                <i class="fas fa-tag me-2"></i>View Pricing
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php include_once __DIR__ . '/inc/footer.php'; ?>

  <!-- Enhanced JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>
    // Initialize AOS
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: true
    });

    // Counter Animation
    function animateCounters() {
      const counters = document.querySelectorAll('[data-counter]');
      
      counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-counter'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
          current += increment;
          if (current >= target) {
            current = target;
            clearInterval(timer);
          }
          counter.textContent = Math.floor(current);
        }, 16);
      });
    }

    // Trigger counter animation when section is visible
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounters();
          observer.unobserve(entry.target);
        }
      });
    });

    document.addEventListener('DOMContentLoaded', () => {
      const heroSection = document.querySelector('.py-5.bg-gradient-primary');
      if (heroSection) {
        observer.observe(heroSection);
      }
    });

    // Parallax effect for floating shapes
    document.addEventListener('mousemove', (e) => {
      const shapes = document.querySelectorAll('.shape');
      const mouseX = e.clientX / window.innerWidth;
      const mouseY = e.clientY / window.innerHeight;
      
      shapes.forEach((shape, index) => {
        const speed = (index + 1) * 2;
        const x = mouseX * speed;
        const y = mouseY * speed;
        shape.style.transform = `translate(${x}px, ${y}px)`;
      });
    });

    // Chat function placeholder
    function openChat() {
      alert('Live chat feature coming soon! Please use phone or email support for now.');
    }

    // Form submission handling
    document.querySelector('.contact-form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Basic form validation
      const firstName = document.getElementById('firstName').value;
      const lastName = document.getElementById('lastName').value;
      const email = document.getElementById('email').value;
      const organization = document.getElementById('organization').value;
      const subject = document.getElementById('subject').value;
      const message = document.getElementById('message').value;
      
      if (!firstName || !lastName || !email || !organization || !subject || !message) {
        alert('Please fill in all required fields.');
        return;
      }
      
      // Show success message
      alert('Thank you for your message! We\'ll get back to you within 4 hours.');
      
      // Reset form
      this.reset();
    });
  </script>

  <style>
    .hover-lift {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .floating-shapes {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: -1;
    }
    
    .shape {
      position: absolute;
      border-radius: 50%;
      background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
      backdrop-filter: blur(10px);
    }
    
    .shape-1 {
      width: 100px;
      height: 100px;
      top: 20%;
      left: 10%;
      animation: float 6s ease-in-out infinite;
    }
    
    .shape-2 {
      width: 150px;
      height: 150px;
      top: 60%;
      right: 10%;
      animation: float 8s ease-in-out infinite reverse;
    }
    
    .shape-3 {
      width: 80px;
      height: 80px;
      bottom: 20%;
      left: 50%;
      animation: float 7s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }
    
    .contact-icons-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 2rem;
      max-width: 300px;
      margin: 0 auto;
    }
    
    .contact-icon-item {
      padding: 2rem;
      background: rgba(255,255,255,0.1);
      border-radius: 15px;
      backdrop-filter: blur(10px);
      transition: transform 0.3s ease;
    }
    
    .contact-icon-item:hover {
      transform: translateY(-10px);
    }
    
    .bg-gradient-primary {
      background: linear-gradient(135deg, #007bff 0%, #6f42c1 100%);
    }
    
    .contact-detail {
      display: flex;
      align-items-flex-start;
    }
    
    .backdrop-blur {
      backdrop-filter: blur(10px);
    }
  </style>
</body>
</html>