<?php $page = 'security'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Security — Pathology & Hospital Management</title>
  <meta name="description" content="Discover our comprehensive security measures and protocols that protect your healthcare data and ensure system reliability.">
  <meta name="keywords" content="healthcare security, hospital management security, medical data protection, pathology lab security, hospital administration security, patient data safety">
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
            <h1 class="display-4 fw-bold mb-4">Security</h1>
            <p class="text-muted mb-5">Enterprise-Grade Security for Healthcare Data</p>

            <div class="content">
              <div class="alert alert-success">
                <i class="fas fa-lock me-2"></i>
                <strong>Bank-Level Security:</strong> We protect your data with the same security standards used by financial institutions.
              </div>

              <h3 class="fw-bold mt-4 mb-3">Data Encryption</h3>
              <p>All data is protected using industry-standard encryption:</p>
              <ul>
                <li><strong>In Transit:</strong> TLS 1.3 encryption for all data transmission</li>
                <li><strong>At Rest:</strong> AES-256 encryption for stored data</li>
                <li><strong>Database:</strong> Encrypted database connections and storage</li>
                <li><strong>Backups:</strong> All backups are encrypted and stored securely</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Access Control</h3>
              <ul>
                <li><strong>Multi-Factor Authentication (MFA):</strong> Optional 2FA for enhanced security</li>
                <li><strong>Role-Based Access:</strong> Granular permissions based on user roles</li>
                <li><strong>Session Management:</strong> Automatic timeout and secure session handling</li>
                <li><strong>IP Whitelisting:</strong> Restrict access to specific IP addresses</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Infrastructure Security</h3>
              <ul>
                <li>Hosted on secure, SOC 2 certified cloud infrastructure</li>
                <li>24/7 monitoring and intrusion detection</li>
                <li>Regular security patches and updates</li>
                <li>DDoS protection and mitigation</li>
                <li>Redundant systems for high availability</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Application Security</h3>
              <ul>
                <li>Regular penetration testing by third-party security firms</li>
                <li>Secure coding practices and code reviews</li>
                <li>Protection against OWASP Top 10 vulnerabilities</li>
                <li>SQL injection and XSS prevention</li>
                <li>CSRF token protection</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Data Backup & Recovery</h3>
              <ul>
                <li>Automated daily backups</li>
                <li>Geographically distributed backup storage</li>
                <li>Point-in-time recovery capabilities</li>
                <li>Regular backup testing and validation</li>
                <li>99.9% uptime SLA</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Compliance & Auditing</h3>
              <ul>
                <li>HIPAA compliant infrastructure</li>
                <li>SOC 2 Type II certified</li>
                <li>ISO 27001 certified</li>
                <li>Comprehensive audit logs</li>
                <li>Regular third-party security audits</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Incident Response</h3>
              <p>We maintain a comprehensive incident response plan:</p>
              <ul>
                <li>24/7 security monitoring and alerting</li>
                <li>Dedicated incident response team</li>
                <li>Rapid containment and remediation procedures</li>
                <li>Transparent communication with affected parties</li>
                <li>Post-incident analysis and improvements</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Security Best Practices for Users</h3>
              <p>We recommend the following security practices:</p>
              <ul>
                <li>Use strong, unique passwords</li>
                <li>Enable multi-factor authentication</li>
                <li>Regularly review user access permissions</li>
                <li>Keep software and browsers up to date</li>
                <li>Report suspicious activity immediately</li>
              </ul>

              <h3 class="fw-bold mt-4 mb-3">Report a Security Issue</h3>
              <p>If you discover a security vulnerability, please report it to:</p>
              <p><strong>Email:</strong> security@hospital.codeapka.com<br>
              <strong>Response Time:</strong> Within 24 hours</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
