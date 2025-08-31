<?php
// Shared header: expects $page variable to be set (e.g. 'home','about','contact','pricing')
?>
<a class="skip-link" href="#main">Skip to content</a>
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
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link <?php echo ($page=='home') ? 'active' : '' ?>" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($page=='about') ? 'active' : '' ?>" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($page=='pricing') ? 'active' : '' ?>" href="pricing.php">Pricing</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($page=='contact') ? 'active' : '' ?>" href="contact.php">Contact</a></li>
        </ul>
        <div class="d-flex align-items-center ms-3">
          <div class="header-social me-3 d-none d-md-flex" aria-hidden="false">
            <a href="#" class="social-link" title="Twitter">Twitter</a>
            <a href="#" class="social-link mx-2" title="LinkedIn">LinkedIn</a>
            <a href="#" class="social-link" title="Facebook">Facebook</a>
          </div>
          <div class="header-cta">
            <a class="btn btn-light" href="contact.php">Schedule Demo</a>
          </div>
        </div>
      </div>
    </nav>
  </div>
</header>