<?php $page = 'hipaa'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>HIPAA Compliance — Pathology & Hospital Management</title>
  <meta name="description" content="Learn about our HIPAA compliance measures and how we ensure the security and privacy of patient health information.">
  <meta name="keywords" content="HIPAA compliance, healthcare security, patient data protection, hospital management HIPAA, medical privacy compliance, pathology lab security">
  <link rel="canonical" href="https://hospital.codeapka.com/hipaa.php">
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=2.2">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <section class="py-5" style="padding-top: 120px !important;">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <h1 class="display-4 fw-bold mb-4">HIPAA Compliance</h1>
            <p class="text-muted mb-5">Our Commitment to Healthcare Data Protection</p>

            <div class="content">
              <div class="alert alert-info">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Fully HIPAA Compliant:</strong> Our system meets all requirements of the Health Insurance Portability and Accountability Act.
              </div>

              <h3 class="fw-bold mt-4 mb-3">HIPAA Compliance Overview</h3>
              <p>We are committed to maintaining the highest standards of data protection and privacy for Protected Health Information (PHI). Our platform is designed and operated in full compliance with HIPAA regulations.</p>

              <h3 class="fw-bold mt-4 mb-3">Technical Safeguards</h3>
              <ul>
                <li><strong>Encryption:</strong> All data is encrypted in transit (TLS 1.3) and at rest (AES-256)</li>
                <li><strong>Access Controls:</strong> Role-based access control (RBAC) ensures users only access necessary data</li>
                <li><strong>Audit Logs:</strong> Comprehensive logging of all PHI access and modifications</li>
                <li><strong>Authentication:</strong> Multi-factor authentication (MFA) available for all users</li>
                <li><strong>Automatic Logoff:</strong> Sessions automatically terminate after periods of inactivity</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Administrative Safeguards</h3>
              <ul>
                <li>Regular security risk assessments</li>
                <li>Workforce training on HIPAA compliance</li>
                <li>Business Associate Agreements (BAA) with all vendors</li>
                <li>Incident response and breach notification procedures</li>
                <li>Regular policy reviews and updates</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Physical Safeguards</h3>
              <ul>
                <li>Data centers with 24/7 security monitoring</li>
                <li>Redundant power and cooling systems</li>
                <li>Biometric access controls</li>
                <li>Regular facility security audits</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Business Associate Agreement</h3>
              <p>We provide a comprehensive Business Associate Agreement (BAA) to all customers, as required by HIPAA. This agreement outlines our responsibilities in protecting PHI.</p>

              <h3 class="fw-bold mt-4 mb-3">Breach Notification</h3>
              <p>In the unlikely event of a data breach, we have procedures in place to:</p>
              <ul>
                <li>Identify and contain the breach immediately</li>
                <li>Notify affected parties within 60 days</li>
                <li>Report to the Department of Health and Human Services (HHS)</li>
                <li>Provide detailed breach reports</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Compliance Certifications</h3>
              <p>Our platform maintains the following certifications:</p>
              <ul>
                <li>HIPAA Compliant</li>
                <li>SOC 2 Type II Certified</li>
                <li>ISO 27001 Certified</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Contact Our Compliance Team</h3>
              <p>For questions about our HIPAA compliance, contact:</p>
              <p><strong>Email:</strong> compliance@hospital.codeapka.com<br>
              <strong>Phone:</strong> +1 (555) 123-4567</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
