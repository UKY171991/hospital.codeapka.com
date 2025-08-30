<?php
// Shared header: expects $page variable to be set (e.g. 'home','about','contact','pricing')
?>
<header class="header">
  <div class="container">
    <div class="brand">
      <div class="logo">PH</div>
      <div>
        <h1 style="margin:0;font-size:1.25rem">Pathology & Hospital Management</h1>
        <div class="small">A modern platform to manage labs, records and hospital workflows.</div>
      </div>
    </div>
    <nav class="site-nav" aria-label="Main navigation">
      <a href="index.php" class="<?php echo ($page=='home') ? 'active' : '' ?>">Home</a>
      <a href="about.php" class="<?php echo ($page=='about') ? 'active' : '' ?>">About</a>
      <a href="pricing.php" class="<?php echo ($page=='pricing') ? 'active' : '' ?>">Pricing</a>
      <a href="contact.php" class="<?php echo ($page=='contact') ? 'active' : '' ?>">Contact</a>
    </nav>
  </div>
</header>
