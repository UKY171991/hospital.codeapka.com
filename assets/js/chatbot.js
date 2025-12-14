// AI Chatbot for Hospital Management System
class HospitalChatbot {
  constructor() {
    this.isOpen = false;
    this.messages = [];
    this.isTyping = false;
    this.init();
  }

  init() {
    this.createChatbotHTML();
    this.attachEventListeners();
    this.loadMessages();
  }

  createChatbotHTML() {
    const chatbotHTML = `
      <div class="ai-chatbot">
        <button class="chat-toggle" id="chatToggle">
          <i class="fas fa-robot"></i>
        </button>
        
        <div class="chat-container" id="chatContainer">
          <div class="chat-header">
            <h3>AI Hospital Assistant</h3>
            <button class="chat-close" id="chatClose">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <div class="chat-messages" id="chatMessages">
            <div class="message bot">
              <div class="message-avatar">AI</div>
              <div class="message-bubble">
                Hello! I'm your AI hospital assistant. I can help you with information about our hospital management system, pricing, features, and support. How can I assist you today?
              </div>
            </div>
          </div>
          
          <div class="typing-indicator" id="typingIndicator">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
          </div>
          
          <div class="chat-input-container">
            <div class="chat-input-wrapper">
              <input type="text" class="chat-input" id="chatInput" placeholder="Type your message..." />
              <button class="chat-send" id="chatSend">
                <i class="fas fa-paper-plane"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', chatbotHTML);
  }

  attachEventListeners() {
    const toggle = document.getElementById('chatToggle');
    const close = document.getElementById('chatClose');
    const send = document.getElementById('chatSend');
    const input = document.getElementById('chatInput');

    toggle.addEventListener('click', () => this.toggleChat());
    close.addEventListener('click', () => this.closeChat());
    send.addEventListener('click', () => this.sendMessage());
    input.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') this.sendMessage();
    });
  }

  toggleChat() {
    const container = document.getElementById('chatContainer');
    const toggle = document.getElementById('chatToggle');
    
    this.isOpen = !this.isOpen;
    
    if (this.isOpen) {
      container.classList.add('active');
      toggle.classList.add('active');
      toggle.innerHTML = '<i class="fas fa-times"></i>';
    } else {
      container.classList.remove('active');
      toggle.classList.remove('active');
      toggle.innerHTML = '<i class="fas fa-robot"></i>';
    }
  }

  closeChat() {
    const container = document.getElementById('chatContainer');
    const toggle = document.getElementById('chatToggle');
    
    this.isOpen = false;
    container.classList.remove('active');
    toggle.classList.remove('active');
    toggle.innerHTML = '<i class="fas fa-robot"></i>';
  }

  sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (!message || this.isTyping) return;
    
    this.addMessage(message, 'user');
    input.value = '';
    
    this.showTypingIndicator();
    this.isTyping = true;
    
    // Simulate AI response
    setTimeout(() => {
      this.hideTypingIndicator();
      const response = this.generateResponse(message);
      this.addMessage(response, 'bot');
      this.isTyping = false;
    }, 1000 + Math.random() * 1000);
  }

  addMessage(text, sender) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender}`;
    
    const avatar = sender === 'bot' ? 'AI' : 'You';
    messageDiv.innerHTML = `
      <div class="message-avatar">${avatar}</div>
      <div class="message-bubble">${text}</div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    this.messages.push({ text, sender, timestamp: new Date() });
    this.saveMessages();
  }

  generateResponse(userMessage) {
    const message = userMessage.toLowerCase();
    
    // Hospital management system responses
    if (message.includes('pricing') || message.includes('cost') || message.includes('price')) {
      return 'We offer flexible pricing plans for healthcare facilities of all sizes. Our plans start from basic packages for small clinics to enterprise solutions for large hospitals. Would you like me to direct you to our pricing page for detailed information?';
    }
    
    if (message.includes('features') || message.includes('what') || message.includes('capabilities')) {
      return 'Our hospital management system includes patient records management, appointment scheduling, billing, laboratory integration, pharmacy management, and comprehensive reporting. All modules are designed to streamline healthcare operations and improve patient care.';
    }
    
    if (message.includes('demo') || message.includes('trial') || message.includes('test')) {
      return 'I\'d be happy to help you schedule a demo! You can contact our sales team through the contact page, or I can guide you there. Would you like to see a live demonstration of our system?';
    }
    
    if (message.includes('support') || message.includes('help') || message.includes('issue')) {
      return 'Our support team is available Monday through Friday, 9 AM to 6 PM. We provide 24/7 emergency support for critical issues. You can reach us via phone, email, or through our support ticket system.';
    }
    
    if (message.includes('security') || message.includes('hipaa') || message.includes('data protection')) {
      return 'Security is our top priority. Our system is HIPAA compliant, uses end-to-end encryption, regular security audits, and follows industry best practices for data protection. All patient data is securely stored and transmitted.';
    }
    
    if (message.includes('integration') || message.includes('compatible') || message.includes('connect')) {
      return 'Our system integrates with most major laboratory equipment, pharmacy systems, billing software, and electronic health record systems. We also provide API access for custom integrations.';
    }
    
    // Default responses
    const defaultResponses = [
      'I can help you with information about our hospital management system, features, pricing, and support. What specific aspect would you like to know more about?',
      'Thank you for your question! Our hospital management system is designed to streamline healthcare operations. Is there a particular feature or service you\'d like to explore?',
      'I\'m here to assist you with any questions about our healthcare software solutions. Feel free to ask about pricing, features, or technical support.',
      'Based on your interest, I recommend exploring our comprehensive hospital management solutions. Would you like specific information about any module?'
    ];
    
    return defaultResponses[Math.floor(Math.random() * defaultResponses.length)];
  }

  showTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    indicator.classList.add('active');
  }

  hideTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    indicator.classList.remove('active');
  }

  saveMessages() {
    try {
      localStorage.setItem('hospitalChatMessages', JSON.stringify(this.messages));
    } catch (e) {
      console.log('Could not save chat messages');
    }
  }

  loadMessages() {
    try {
      const saved = localStorage.getItem('hospitalChatMessages');
      if (saved) {
        this.messages = JSON.parse(saved);
      }
    } catch (e) {
      console.log('Could not load chat messages');
    }
  }
}

// Initialize chatbot when page loads
document.addEventListener('DOMContentLoaded', () => {
  new HospitalChatbot();
});
