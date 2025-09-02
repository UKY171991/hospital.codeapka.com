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
          
          <!-- Solutions Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="solutionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="nav-icon">⚕️</span>
              Solutions
            </a>
            <ul class="dropdown-menu modern-dropdown" aria-labelledby="solutionsDropdown">
              <li><a class="dropdown-item" href="#features">
                <span class="dropdown-icon">📋</span>
                <div>
                  <strong>Patient Management</strong>
                  <small>Complete EHR system</small>
                </div>
              </a></li>
              <li><a class="dropdown-item" href="#features">
                <span class="dropdown-icon">💰</span>
                <div>
                  <strong>Billing & Inventory</strong>
                  <small>Automated billing system</small>
                </div>
              </a></li>
              <li><a class="dropdown-item" href="#features">
                <span class="dropdown-icon">🔒</span>
                <div>
                  <strong>Security & Compliance</strong>
                  <small>HIPAA compliant platform</small>
                </div>
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="umakant/index.php">
                <span class="dropdown-icon">🚀</span>
                <div>
                  <strong>Admin Portal</strong>
                  <small>Access management system</small>
                </div>
              </a></li>
            </ul>
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
            <a class="nav-link <?php echo ($page=='contact') ? 'active' : '' ?>" href="contact.php">
              <span class="nav-icon">📞</span>
              Contact
            </a>
          </li>
        </ul>
        
        <!-- Enhanced Header Actions -->
        <div class="d-flex align-items-center ms-3">
          <!-- Search Button -->
          <button class="search-btn me-2" type="button" data-bs-toggle="modal" data-bs-target="#searchModal" title="Search">
            <span class="search-icon">🔍</span>
          </button>
          
          
          
          <!-- CTA Buttons -->
          <div class="header-cta">
            <a class="btn btn-outline-light me-2 d-none d-md-inline-block" href="umakant/login.php">Login</a>
            <a class="btn btn-light" href="contact.php">
              <span class="btn-icon">📅</span>
              Schedule Demo
            </a>
          </div>
        </div>
      </div>
    </nav>
  </div>
</header>

<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content search-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="searchModalLabel">Search Our Platform</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="search-form">
          <input type="text" class="form-control search-input" placeholder="Search for features, documentation, or help..." autofocus>
          <button class="search-submit" type="submit">
            <span class="search-icon">🔍</span>
          </button>
        </div>
        <div class="search-suggestions">
          <h6>Popular Searches:</h6>
          <div class="suggestion-tags">
            <span class="suggestion-tag">Patient Records</span>
            <span class="suggestion-tag">Billing System</span>
            <span class="suggestion-tag">User Management</span>
            <span class="suggestion-tag">Reports</span>
            <span class="suggestion-tag">API Documentation</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>