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
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main class="container">
    <section class="hero">
      <div>
        <h2 style="margin:0 0 0.5rem 0">Welcome to the Pathology & Hospital Management System</h2>
        <p class="lead">Build faster workflows, access patient and lab data securely, and deliver better care. The system is designed for simplicity and scale — ideal for small clinics to large hospitals.</p>
        <p><a class="button" href="#features">Explore features</a></p>
      </div>
      <div>
        <div class="card">
          <h3>Pathology Module</h3>
          <p class="small">Manage lab tests, sample tracking, digital reports and results delivery.</p>
          <div class="feature-list">
            <span> Test catalogue & pricing</span>
            <!-- <span>• Sample barcoding</span> -->
            <span> Automated report templates</span>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <h3>Releases</h3>
      <div class="card">
        <div class="small">Latest uploaded software releases and files from our uploads index.</div>
        <div style="margin-top:0.75rem">
          <?php echo $uploadListHtml; ?>
        </div>
      </div>
    </section>
    

    <section id="features" class="section">
      <h3 style="margin-top:0">Key Features</h3>
      <div class="card-grid">
        <div class="card">
          <h3>Patient Records</h3>
          <p class="small">Centralized EHR for quick access to patient history, visits and reports.</p>
        </div>
        <div class="card">
          <h3>Appointments</h3>
          <p class="small">Online booking, doctor schedules and automated reminders.</p>
        </div>
        <div class="card">
          <h3>Billing & Inventory</h3>
          <p class="small">Integrated billing, invoices and stock control for consumables.</p>
        </div>
        <div class="card">
          <h3>Secure Access</h3>
          <p class="small">Role-based access controls and audit logs to meet compliance needs.</p>
        </div>
      </div>
    </section>

    <!-- <section id="pricing" class="section">
      <h3 style="margin-top:0">Pricing Plans</h3>
      <p class="small">Flexible plans for clinics, laboratories and hospitals. All plans include basic support and regular updates. Choose a plan and contact our sales team to purchase or schedule a demo.</p>
      <div class="card-grid pricing-grid">
        <div class="card plan">
          <h3>Starter</h3>
    <div class="price">₹16,499 <span class="small">/ month</span></div>
          <div class="features small">
            <div>• Up to 3 users</div>
            <div>• Basic pathology module</div>
            <div>• Email support</div>
          </div>
          <p style="margin-top:0.75rem"><a class="button" href="pricing.php?plan=starter">Buy Now</a></p>
        </div>

        <div class="card plan">
          <h3>Professional</h3>
          <div class="price">₹41,499 <span class="small">/ month</span></div>
          <div class="features small">
            <div>• Up to 15 users</div>
            <div>• Full pathology + hospital modules</div>
            <div>• Priority support</div>
          </div>
          <p style="margin-top:0.75rem"><a class="button" href="pricing.php?plan=professional">Buy Now</a></p>
        </div>

        <div class="card plan">
          <h3>Enterprise</h3>
          <div class="price">Custom</div>
          <div class="features small">
            <div>• Unlimited users</div>
            <div>• Custom integrations</div>
            <div>• Dedicated support & training</div>
          </div>
          <p style="margin-top:0.75rem"><a class="button" href="pricing.php?plan=enterprise">Contact Sales</a></p>
        </div>
      </div>
    </section> -->

    <section class="section">
      <h3 style="margin-top:0">Available Plans</h3>
      <div class="card">
        <div class="small">Browse the plans we currently offer. Click a plan to learn more or contact us to purchase.</div>
        <div style="margin-top:0.75rem">
          <?php include __DIR__ . '/umakant/public_plans.php'; ?>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="card">
        <h3>Get Started</h3>
        <p class="small">Want to try the system or customize it for your facility? Contact our team to schedule a demo or request a quote.</p>
        <p><a class="button" href="contact.php">Contact Us</a></p>
      </div>
    </section>

  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
