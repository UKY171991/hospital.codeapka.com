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
  <link rel="stylesheet" href="assets/css/style.css?v=2.2">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="pricing-hero-section align-items-center d-flex position-relative overflow-hidden" style="min-height: 400px; padding-top: 120px;">
      <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
      </div>
      <div class="container text-center text-white position-relative z-1">
        <span class="badge bg-white bg-opacity-20 backdrop-blur text-white px-3 py-2 rounded-pill mb-3 border border-white border-opacity-25" data-aos="fade-down">
          <i class="fas fa-headset me-2"></i> 24/7 Support Available
        </span>
        <h1 class="display-3 fw-bold mb-3" data-aos="fade-up" data-aos-delay="100">
          Get In <span class="text-gradient-warning">Touch</span>
        </h1>
        <p class="lead mb-4 opacity-90 mx-auto" style="max-width: 600px;" data-aos="fade-up" data-aos-delay="200">
          Have questions about our hospital management system? Our team is ready to help you transform your healthcare facility.
        </p>
      </div>
    </section>

    <!-- Contact Owners Section (Dynamic) -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
          <h2 class="section-title mb-3">Meet Our Support Team</h2>
          <p class="text-muted">Direct contact with our dedicated representatives</p>
        </div>
        
        <!-- Dynamic Owners Grid -->
        <div class="owners-grid-wrapper">
          <?php include __DIR__ . '/umakant/public_owners.php'; ?>
        </div>
      </div>
    </section>

    <!-- Main Contact Form & Info -->
    <section class="py-5 bg-white">
      <div class="container">
        <div class="row g-5">
          <!-- Contact Form -->
          <div class="col-lg-7" data-aos="fade-right">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
              <div class="card-header bg-white border-0 pt-4 px-4 px-md-5">
                <h3 class="fw-bold mb-0">Send us a Message</h3>
                <p class="text-muted small">We usually respond within 24 hours.</p>
              </div>
              <div class="card-body p-4 p-md-5 pt-2">
                <form action="#" method="POST" class="contact-form needs-validation" novalidate>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <div class="form-floating">
                        <input type="text" class="form-control bg-light border-0" id="firstName" placeholder="John" required>
                        <label for="firstName">First Name</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-floating">
                        <input type="text" class="form-control bg-light border-0" id="lastName" placeholder="Doe" required>
                        <label for="lastName">Last Name</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-floating">
                        <input type="email" class="form-control bg-light border-0" id="email" placeholder="name@example.com" required>
                        <label for="email">Email Address</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-floating">
                        <input type="tel" class="form-control bg-light border-0" id="phone" placeholder="+1234567890">
                        <label for="phone">Phone (Optional)</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-floating">
                        <select class="form-select bg-light border-0" id="subject" required>
                          <option value="" selected disabled>Select a topic...</option>
                          <option value="demo">Request a Demo</option>
                          <option value="pricing">Pricing Inquiry</option>
                          <option value="support">Technical Support</option>
                          <option value="other">Other</option>
                        </select>
                        <label for="subject">Subject</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-floating">
                        <textarea class="form-control bg-light border-0" placeholder="Type your message here..." id="message" style="height: 150px" required></textarea>
                        <label for="message">Message</label>
                      </div>
                    </div>
                    <div class="col-12 mt-4">
                      <button class="btn btn-primary btn-lg w-100 py-3 rounded-3 hover-lift shadow-sm" type="submit">
                        <i class="fas fa-paper-plane me-2"></i> Send Message
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          
          <!-- Additional Info & Map -->
          <div class="col-lg-5" data-aos="fade-left">
            <div class="d-flex flex-column gap-4 h-100">
              <!-- Info Card -->
              <div class="card border-0 bg-primary text-white shadow-lg rounded-4 p-4 text-center text-md-start">
                <div class="card-body">
                  <h4 class="mb-4 fw-bold"><i class="fas fa-clock me-2 text-warning opacity-75"></i> Office Hours</h4>
                  <ul class="list-unstyled opacity-90 space-y-2">
                    <li class="d-flex justify-content-between border-bottom border-white border-opacity-25 pb-2 mb-2">
                      <span>Mon - Fri:</span> <span>9:00 AM - 6:00 PM</span>
                    </li>
                    <li class="d-flex justify-content-between border-bottom border-white border-opacity-25 pb-2 mb-2">
                      <span>Saturday:</span> <span>10:00 AM - 4:00 PM</span>
                    </li>
                    <li class="d-flex justify-content-between">
                      <span>Sunday:</span> <span>Closed</span>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- General Contact Info -->
               <div class="card border-0 shadow-sm rounded-4 p-4">
                 <div class="card-body">
                   <h5 class="fw-bold mb-3">General Inquiries</h5>
                   <div class="d-flex align-items-center mb-3">
                     <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                       <i class="fas fa-envelope"></i>
                     </div>
                     <a href="mailto:support@hospital.codeapka.com" class="text-decoration-none text-dark fw-semibold">support@hospital.codeapka.com</a>
                   </div>
                   <div class="d-flex align-items-center">
                     <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                       <i class="fas fa-map-marker-alt"></i>
                     </div>
                     <span class="text-dark">123 Health Ave, Medical District, NY</span>
                   </div>
                 </div>
               </div>
               
               <!-- FAQ Teaser -->
               <div class="card border-0 bg-dark text-white shadow-sm rounded-4 p-4 mt-auto">
                 <div class="card-body d-flex align-items-center justify-content-between">
                   <div>
                     <h5 class="fw-bold mb-1">Have Questions?</h5>
                     <p class="small opacity-75 mb-0">Check out our frequently asked questions.</p>
                   </div>
                   <a href="pricing.php#faq" class="btn btn-light btn-sm rounded-pill px-3">View FAQ</a>
                 </div>
               </div>

            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>

  <!-- Enhanced JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>
    // Initialize AOS
    AOS.init({
      duration: 800,
      easing: 'ease-out-cubic',
      once: true,
      offset: 50
    });

    // Form submission stub
    document.querySelector('.contact-form').addEventListener('submit', function(e) {
      e.preventDefault();
      // Basic validation check
      if(!this.checkValidity()){
          e.stopPropagation();
          this.classList.add('was-validated');
          return;
      }
      
      const btn = this.querySelector('button[type="submit"]');
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending...';
      
      // Simulate network request
      setTimeout(() => {
        alert('Thank you! Your message has been sent successfully.');
        this.reset();
        this.classList.remove('was-validated');
        btn.disabled = false;
        btn.innerHTML = originalText;
      }, 1500);
    });

    // Floating shapes effect
    document.addEventListener('mousemove', (e) => {
      const shapes = document.querySelectorAll('.shape');
      const x = e.clientX / window.innerWidth;
      const y = e.clientY / window.innerHeight;
      
      shapes.forEach((shape, i) => {
        const speed = (i + 1) * 20;
        shape.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
      });
    });
  </script>

  <style>
    /* Page specific styles to complement style.css */
    .pricing-hero-section {
        /* Reusing the hero style from pricing page but ensuring it works here too */
        background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
    }
    
    .text-gradient-warning {
        background: linear-gradient(to right, #facc15, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: var(--primary);
        font-weight: 600;
        opacity: 0.8;
    }
    
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        border-color: var(--primary);
    }
    
    .contact-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg) !important;
    }
    
    /* Shape animations */
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
  </style>
</body>
</html>