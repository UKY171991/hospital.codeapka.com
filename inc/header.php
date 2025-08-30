<?php
// Shared header: expects $page variable to be set (e.g. 'home','about','contact','pricing')
?>
<header class="site-header">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <div class="logo me-2">PH</div>
        <div>
          <div style="font-weight:700;line-height:1">Pathology &amp; Hospital</div>
          <div class="small">Manage labs & records</div>
        </div>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link <?php echo ($page=='home') ? 'active' : '' ?>" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($page=='about') ? 'active' : '' ?>" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($page=='pricing') ? 'active' : '' ?>" href="pricing.php">Pricing</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($page=='contact') ? 'active' : '' ?>" href="contact.php">Contact</a></li>
        </ul>
        <div class="d-flex ms-3">
          <a class="btn btn-light btn-sm" href="contact.php">Get a Demo</a>
        </div>
      </div>
    </div>
  </nav>
</header>
