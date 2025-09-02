<?php
// Shared footer
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
          <p class="footer-description">
            Transforming healthcare through innovative technology solutions. 
            Trusted by 500+ healthcare facilities worldwide.
          </p>
          <div class="footer-social">
            <a href="#" class="social-link" title="Twitter" aria-label="Follow us on Twitter">
              <span class="social-icon">🐦</span>
            </a>
            <a href="#" class="social-link" title="LinkedIn" aria-label="Connect on LinkedIn">
              <span class="social-icon">💼</span>
            </a>
            <a href="#" class="social-link" title="Facebook" aria-label="Like us on Facebook">
              <span class="social-icon">📘</span>
            </a>
            <a href="#" class="social-link" title="Instagram" aria-label="Follow us on Instagram">
              <span class="social-icon">📷</span>
            </a>
          </div>
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
            <h4>Resources</h4>
            <ul>
              <li><a href="#">Documentation</a></li>
              <li><a href="#">API Reference</a></li>
              <li><a href="#">Help Center</a></li>
              <li><a href="#">System Status</a></li>
            </ul>
          </div>
          
          <div class="footer-column">
            <h4>Legal</h4>
            <ul>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Terms of Service</a></li>
              <li><a href="#">HIPAA Compliance</a></li>
              <li><a href="#">Security</a></li>
            </ul>
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

  // Handle Enter key in chat input
  document.addEventListener('DOMContentLoaded', function() {
    const chatInput = document.querySelector('.chat-input-field');
    if (chatInput) {
      chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          sendMessage();
        }
      });
    }
  });

  // Auto-show chat widget after some time (optional)
  setTimeout(() => {
    const chatToggle = document.querySelector('.chat-toggle');
    if (chatToggle && !document.getElementById('chatWindow').classList.contains('active')) {
      chatToggle.style.animation = 'bounce 1s ease-in-out 3';
    }
  }, 10000); // Show after 10 seconds
</script>