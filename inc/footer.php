<?php
// Shared footer
// Fetch owner contact details for footer
$footerPhone = '+1 (555) 123-4567'; // Default
$footerWhatsapp = '+1 (555) 123-4567'; // Default
$footerEmail = 'support@hospital.codeapka.com'; // Default

try {
    // Include database connection if not already included
    if (!isset($pdo)) {
        require_once __DIR__ . '/../umakant/inc/connection.php';
    }
    
    // Fetch the first owner record for footer contact info
    $stmt = $pdo->query("SELECT phone, whatsapp, email FROM owners ORDER BY id ASC LIMIT 1");
    $owner = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($owner) {
        if (!empty($owner['phone'])) {
            $footerPhone = $owner['phone'];
        }
        if (!empty($owner['whatsapp'])) {
            $footerWhatsapp = $owner['whatsapp'];
        }
        if (!empty($owner['email'])) {
            $footerEmail = $owner['email'];
        }
    }
} catch (Exception $e) {
    // Silently fail and use defaults
    error_log("Footer owner fetch error: " . $e->getMessage());
}
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-main">
        <div class="footer-brand">
          <div class="footer-logo">
            <div class="logo">PH</div>
            <div class="brand-text">
              <div class="brand-name">Pathology & Hospital</div>
              <div class="brand-subtitle">Advanced Healthcare Solutions</div>
            </div>
          </div>
          <p>Transforming healthcare through innovative technology solutions. 
            Trusted by 500+ healthcare facilities worldwide.
          </p>
        </div>
        
        <div class="footer-links">
          <div class="footer-column">
            <h4>Solutions</h4>
            <ul>
              <li><a href="#features">Patient Management</a></li>
              <li><a href="#features">Billing & Inventory</a></li>
              <li><a href="#features">Security & Compliance</a></li>
              <li><a href="umakant/index.php">Admin Portal</a></li>
            </ul>
          </div>
          
          <div class="footer-column">
            <h4>Company</h4>
            <ul>
              <li><a href="about.php">About Us</a></li>
              <li><a href="pricing.php">Pricing</a></li>
              <li><a href="contact.php">Contact</a></li>
              <li><a href="#" onclick="openChatWidget()">Support</a></li>
            </ul>
          </div>
          
          <div class="footer-column">
            <h4>Legal</h4>
            <ul>
              <li><a href="privacy.php">Privacy Policy</a></li>
              <li><a href="terms.php">Terms of Service</a></li>
              <li><a href="hipaa.php">HIPAA Compliance</a></li>
              <li><a href="security.php">Security</a></li>
            </ul>
          </div>
          
          <div class="footer-column">
            <h4>Contact Info</h4>
            <div class="footer-contact">
              <div class="contact-item mb-3">
                <i class="fas fa-phone-alt text-primary me-2"></i>
                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $footerPhone); ?>" class="text-white text-decoration-none"><?php echo htmlspecialchars($footerPhone); ?></a>
              </div>
              <div class="contact-item mb-3">
                <i class="fab fa-whatsapp text-success me-2"></i>
                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $footerWhatsapp); ?>" target="_blank" class="text-white text-decoration-none"><?php echo htmlspecialchars($footerWhatsapp); ?></a>
              </div>
              <div class="contact-item">
                <i class="fas fa-envelope text-info me-2"></i>
                <a href="mailto:<?php echo htmlspecialchars($footerEmail); ?>" class="text-white text-decoration-none"><?php echo htmlspecialchars($footerEmail); ?></a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom">
        <div class="footer-bottom-left">
          <p>&copy; <?php echo date('Y'); ?> Pathology & Hospital Management System. All rights reserved.</p>
        </div>
        <div class="footer-bottom-right">
          <p>Designed with ❤️ for healthcare professionals everywhere</p>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Live Chat Widget -->
<div class="chat-widget" id="chatWidget">
  <div class="chat-toggle" onclick="toggleChat()">
    <span class="chat-icon">💬</span>
    <span class="chat-text">Chat with us</span>
  </div>
  <div class="chat-window" id="chatWindow">
    <div class="chat-header">
      <div class="chat-header-info">
        <div class="chat-avatar">👨‍⚕️</div>
        <div class="chat-details">
          <div class="chat-name">Healthcare Support</div>
          <div class="chat-status">Online</div>
        </div>
      </div>
      <button class="chat-close" onclick="toggleChat()">×</button>
    </div>
    <div class="chat-messages">
      <div class="chat-message bot-message">
        <div class="message-avatar">🤖</div>
        <div class="message-content">
          <p>Hello! How can I help you with our healthcare management platform today?</p>
          <div class="message-time">Just now</div>
        </div>
      </div>
    </div>
    <div class="chat-input">
      <input type="text" placeholder="Type your message..." class="chat-input-field">
      <button class="chat-send" onclick="sendMessage()">
        <span class="send-icon">📤</span>
      </button>
    </div>
  </div>
</div>
<!-- Bootstrap JS bundle for interactive components -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toggle sticky header style on scroll
  (function(){
    const header = document.querySelector('.site-header');
    if (!header) return;
    const onScroll = () => {
      if (window.scrollY > 20) header.classList.add('scrolled');
      else header.classList.remove('scrolled');
    };
    document.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Auto-collapse mobile navbar when a nav link is clicked
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const bsCollapseEl = document.getElementById('mainNavbar');
    if (bsCollapseEl) {
      const bsCollapse = new bootstrap.Collapse(bsCollapseEl, { toggle: false });
      navLinks.forEach(link => link.addEventListener('click', () => {
        if (bsCollapseEl.classList.contains('show')) bsCollapse.hide();
      }));
    }
  })();

  // Chat Widget Functionality
  function toggleChat() {
    const chatWindow = document.getElementById('chatWindow');
    const chatToggle = document.querySelector('.chat-toggle');
    
    if (chatWindow.classList.contains('active')) {
      chatWindow.classList.remove('active');
      chatToggle.innerHTML = '<span class="chat-icon">💬</span><span class="chat-text">Chat with us</span>';
    } else {
      chatWindow.classList.add('active');
      chatToggle.innerHTML = '<span class="chat-icon">💬</span><span class="chat-text">Close chat</span>';
    }
  }

  function openChatWidget() {
    const chatWindow = document.getElementById('chatWindow');
    const chatToggle = document.querySelector('.chat-toggle');
    
    if (!chatWindow.classList.contains('active')) {
      chatWindow.classList.add('active');
      chatToggle.innerHTML = '<span class="chat-icon">💬</span><span class="chat-text">Close chat</span>';
    }
  }

  function sendMessage() {
    const input = document.querySelector('.chat-input-field');
    const message = input.value.trim();
    
    if (message) {
      const messagesContainer = document.querySelector('.chat-messages');
      
      // Add user message
      const userMessage = document.createElement('div');
      userMessage.className = 'chat-message user-message';
      userMessage.innerHTML = `
        <div class="message-avatar">👤</div>
        <div class="message-content">
          <p>${message}</p>
          <div class="message-time">Just now</div>
        </div>
      `;
      messagesContainer.appendChild(userMessage);
      
      // Clear input
      input.value = '';
      
      // Scroll to bottom
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
      
      // Simulate bot response
      setTimeout(() => {
        const botMessage = document.createElement('div');
        botMessage.className = 'chat-message bot-message';
        botMessage.innerHTML = `
          <div class="message-avatar">🤖</div>
          <div class="message-content">
            <p>Thank you for your message! Our support team will get back to you shortly. In the meantime, you can explore our <a href="pricing.php">pricing plans</a> or <a href="contact.php">schedule a demo</a>.</p>
            <div class="message-time">Just now</div>
          </div>
        `;
        messagesContainer.appendChild(botMessage);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }, 1000);
    }
  }

  // Handle Enter key in chat input and Enhanced dropdown functionality
  document.addEventListener('DOMContentLoaded', function() {
    const chatInput = document.querySelector('.chat-input-field');
    if (chatInput) {
      chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          sendMessage();
        }
      });
    }

    // Enhanced dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
      const toggle = dropdown.querySelector('.dropdown-toggle');
      const menu = dropdown.querySelector('.dropdown-menu');
      
      // Handle hover for desktop
      if (window.innerWidth > 992) {
        dropdown.addEventListener('mouseenter', function() {
          this.classList.add('show');
          menu.classList.add('show');
          toggle.setAttribute('aria-expanded', 'true');
        });
        
        dropdown.addEventListener('mouseleave', function() {
          this.classList.remove('show');
          menu.classList.remove('show');
          toggle.setAttribute('aria-expanded', 'false');
        });
      }
      
      // Handle click for mobile and desktop
      toggle.addEventListener('click', function(e) {
        e.preventDefault();
        const isOpen = dropdown.classList.contains('show');
        
        // Close all other dropdowns
        dropdowns.forEach(d => {
          d.classList.remove('show');
          d.querySelector('.dropdown-menu').classList.remove('show');
          d.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
        });
        
        // Toggle current dropdown
        if (!isOpen) {
          dropdown.classList.add('show');
          menu.classList.add('show');
          toggle.setAttribute('aria-expanded', 'true');
        }
      });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.dropdown')) {
        dropdowns.forEach(dropdown => {
          dropdown.classList.remove('show');
          dropdown.querySelector('.dropdown-menu').classList.remove('show');
          dropdown.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
        });
      }
    });
  });

  // Auto-show chat widget after some time (optional)
  setTimeout(() => {
    const chatToggle = document.querySelector('.chat-toggle');
    if (chatToggle && !document.getElementById('chatWindow').classList.contains('active')) {
      chatToggle.style.animation = 'bounce 1s ease-in-out 3';
    }
  }, 10000); // Show after 10 seconds
</script>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Expose minimal user info to frontend scripts for page behaviors (no secrets)
$appUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 'null';
$appUserRole = isset($_SESSION['role']) ? json_encode($_SESSION['role']) : 'null';
?>
<script>
  window.AppUser = {
    id: <?php echo $appUserId; ?>,
    role: <?php echo $appUserRole; ?>
  };
</script>