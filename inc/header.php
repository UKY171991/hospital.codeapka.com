<?php
// Shared header: expects $page variable to be set (e.g. 'home','about','contact','pricing')
?>
<header class="site-header">
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark">
      <a class="navbar-brand" href="index.php">
        <div class="logo">PH</div>
        <div class="brand-text">
          <div class="brand-name">Pathology & Hospital</div>
          <div class="brand-subtitle">Advanced Healthcare Solutions</div>
        </div>
      </a>
      
      <!-- Enhanced Mobile Toggle -->
      <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="toggler-line"></span>
        <span class="toggler-line"></span>
        <span class="toggler-line"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <!-- Enhanced Navigation Menu -->
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link <?php echo ($page=='home') ? 'active' : '' ?>" href="index.php">
              <span class="nav-icon">🏠</span>
              Home
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link <?php echo ($page=='about') ? 'active' : '' ?>" href="about.php">
              <span class="nav-icon">ℹ️</span>
              About
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($page=='pricing') ? 'active' : '' ?>" href="pricing.php">
              <span class="nav-icon">💎</span>
              Pricing
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($page=='downloads') ? 'active' : '' ?>" href="downloads.php">
              <span class="nav-icon">📥</span>
              Downloads
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($page=='contact') ? 'active' : '' ?>" href="contact.php">
              <span class="nav-icon">📞</span>
              Contact
            </a>
          </li>
        </ul>
        
      </div>
    </nav>
  </div>
</header>