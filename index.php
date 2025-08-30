<?php $page = 'home'; ?>
<?php
// Try to fetch the uploaded releases list from the umakant area
function sanitize_upload_html($html){
  if (!$html) return '';
  // Remove script tags entirely
  $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

  // If a full document is returned, try to extract the body content
  if (stripos($html, '<body') !== false) {
    if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $m)) {
      $html = $m[1];
    }
  } else {
    // Remove head and html wrappers if present
    $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
    $html = preg_replace('/<\/?html[^>]*>/is', '', $html);
  }

  // Trim and return
  return trim($html);
}

function fetch_upload_list_html(){
  $host = $_SERVER['HTTP_HOST'] ?? 'hospital.codeapka.com';
  $url = 'https://' . $host . '/umakant/public_upload_list.php';

  // Try cURL first
  if (function_exists('curl_version')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($resp !== false && $code >= 200 && $code < 400) {
      return sanitize_upload_html($resp);
    }
  }

  // Fallback to file_get_contents if allow_url_fopen is enabled
  if (ini_get('allow_url_fopen')) {
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $resp = @file_get_contents($url, false, $context);
    if ($resp !== false) return sanitize_upload_html($resp);
  }

  // As a last resort, try including the local file and capturing output (may require same permissions/session)
  $localPath = __DIR__ . '/umakant/upload_list.php';
  if (is_readable($localPath)) {
    ob_start();
    try { include $localPath; } catch (Throwable $e) { /* ignore */ }
    $out = ob_get_clean();
    if ($out) return sanitize_upload_html($out);
  }

  return '<div class="small">No releases available or failed to fetch the uploads list.</div>';
}

$uploadListHtml = fetch_upload_list_html();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Welcome — Pathology & Hospital Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="hero">
      <div class="hero-content">
        <h1>Welcome to the Pathology & Hospital Management System</h1>
        <p class="lead">Build faster workflows, access patient and lab data securely, and deliver better care. The system is designed for simplicity and scale — ideal for small clinics to large hospitals.</p>
        <p><a class="button" href="#features">Explore features</a></p>
      </div>
      <div class="hero-image">
        <div class="hero-image-placeholder">
          Hospital Management System
        </div>
      </div>
    </section>

    <section class="section">
      <div class="section-header">
        <h2>Latest Releases</h2>
        <p>Check out our latest software releases and updates</p>
      </div>
      <div class="card">
        <div class="small">Latest uploaded software releases and files from our uploads index.</div>
        <div class="mt-3">
          <?php echo $uploadListHtml; ?>
        </div>
      </div>
    </section>
    

    <section id="features" class="section">
      <div class="section-header">
        <h2>Key Features</h2>
        <p>Powerful tools to streamline your healthcare operations</p>
      </div>
      <div class="card-grid">
        <div class="card feature-card">
          <div class="feature-icon">📋</div>
          <h3>Patient Records</h3>
          <p class="small">Centralized EHR for quick access to patient history, visits and reports.</p>
        </div>
        <div class="card feature-card">
          <div class="feature-icon">📅</div>
          <h3>Appointments</h3>
          <p class="small">Online booking, doctor schedules and automated reminders.</p>
        </div>
        <div class="card feature-card">
          <div class="feature-icon">💰</div>
          <h3>Billing & Inventory</h3>
          <p class="small">Integrated billing, invoices and stock control for consumables.</p>
        </div>
        <div class="card feature-card">
          <div class="feature-icon">🔒</div>
          <h3>Secure Access</h3>
          <p class="small">Role-based access controls and audit logs to meet compliance needs.</p>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="section-header">
        <h2>Available Plans</h2>
        <p>Browse the plans we currently offer. Click a plan to learn more or contact us to purchase.</p>
      </div>
      <div class="card">
        <div class="mt-2">
          <?php include __DIR__ . '/umakant/public_plans.php'; ?>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="card text-center">
        <h3>Ready to Get Started?</h3>
        <p class="small">Want to try the system or customize it for your facility? Contact our team to schedule a demo or request a quote.</p>
        <p><a class="button" href="contact.php">Contact Us</a></p>
      </div>
    </section>

  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>