<?php
// Shared header: expects $page variable to be set (e.g. 'home','about','contact','pricing')
?>
<header class="header site-header">
  <div class="container" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
    <div class="brand" style="display:flex;align-items:center;gap:0.75rem;">
      <div class="logo">PH</div>
      <div>
        <h1 style="margin:0;font-size:1.15rem;line-height:1">Pathology & Hospital Management</h1>
        <div class="small">Manage labs, records and hospital workflows with ease.</div>
      </div>
    </div>

    <nav class="site-nav" aria-label="Main navigation">
      <a href="index.php" class="<?php echo ($page=='home') ? 'active' : '' ?>">Home</a>
      <a href="about.php" class="<?php echo ($page=='about') ? 'active' : '' ?>">About</a>
      <a href="pricing.php" class="<?php echo ($page=='pricing') ? 'active' : '' ?>">Pricing</a>
      <a href="contact.php" class="<?php echo ($page=='contact') ? 'active' : '' ?>">Contact</a>
    </nav>

    <div class="header-cta">
      <a class="button small" href="contact.php">Get a Demo</a>
    </div>
  </div>
</header>
