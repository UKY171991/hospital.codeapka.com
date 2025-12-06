<?php
// public_owners.php - outputs a grid of owner contacts
require_once __DIR__ . '/inc/connection.php';

// Safe check for columns existence
function publicOwnerColumnExists(PDO $pdo, string $column): bool {
    static $cache = [];
    if (array_key_exists($column, $cache)) return $cache[$column];
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'owners' AND COLUMN_NAME = ?");
        $stmt->execute([$column]);
        $cache[$column] = $stmt->fetchColumn() > 0;
    } catch (Throwable $e) {
        $cache[$column] = false;
    }
    return $cache[$column];
}

try {
    $hasLink = publicOwnerColumnExists($pdo, 'link');
    
    $fields = ['id', 'name', 'phone', 'whatsapp', 'email', 'address'];
    if ($hasLink) $fields[] = 'link';
    
    $stmt = $pdo->query("SELECT " . implode(',', $fields) . " FROM owners ORDER BY id DESC");
    $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($owners)) {
        // Fallback static content if no owners found
        echo '<div class="alert alert-info text-center">
                <h5><i class="fas fa-info-circle me-2"></i> Contact Info Updating</h5>
                <p class="mb-0">Please use the main contact form or general support number temporarily.</p>
              </div>';
        return;
    }

    echo '<div class="row g-4">';
    foreach ($owners as $owner) {
        $name = htmlspecialchars($owner['name'] ?? 'Support Agent');
        $phone = htmlspecialchars($owner['phone'] ?? '');
        $wa = htmlspecialchars($owner['whatsapp'] ?? '');
        $email = htmlspecialchars($owner['email'] ?? '');
        $address = htmlspecialchars($owner['address'] ?? '');
        $link = $hasLink ? htmlspecialchars($owner['link'] ?? '') : '';

        // Determine icon based on likely role (just for visual variety)
        $iconClass = 'fa-user-tie'; 
        if (stripos($name, 'tech') !== false) $iconClass = 'fa-headset';
        if (stripos($name, 'sale') !== false) $iconClass = 'fa-chart-line';

        echo '<div class="col-lg-4 col-md-6" data-aos="fade-up">';
        echo '  <div class="contact-card h-100 p-4 border rounded-3 shadow-sm hover-lift bg-white">';
        echo '    <div class="d-flex align-items-center mb-4">';
        echo '      <div class="contact-icon-circle bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">';
        echo '        <i class="fas ' . $iconClass . ' fa-2x"></i>';
        echo '      </div>';
        echo '      <div>';
        echo '        <h5 class="fw-bold mb-1">' . $name . '</h5>';
        echo '        <span class="badge bg-light text-dark border">Representative</span>';
        echo '      </div>';
        echo '    </div>';
        
        echo '    <div class="contact-details space-y-3">';
        if ($phone) {
            echo '<p class="mb-2"><i class="fas fa-phone-alt text-primary me-2"></i>';
            echo '<a href="tel:' . $phone . '" class="text-decoration-none text-dark">' . $phone . '</a></p>';
        }
        if ($wa) {
            echo '<p class="mb-2"><i class="fab fa-whatsapp text-success me-2"></i>';
            echo '<a href="https://wa.me/' . preg_replace('/[^0-9]/', '', $wa) . '" class="text-decoration-none text-dark" target="_blank">' . $wa . '</a></p>';
        }
        if ($email) {
            echo '<p class="mb-2"><i class="fas fa-envelope text-info me-2"></i>';
            echo '<a href="mailto:' . $email . '" class="text-decoration-none text-dark">' . $email . '</a></p>';
        }
        if ($link) {
            echo '<p class="mb-2"><i class="fas fa-link text-secondary me-2"></i>';
            echo '<a href="' . $link . '" class="text-decoration-none text-dark" target="_blank">Portfolio / Profile</a></p>';
        }
        if ($address) {
            echo '<p class="mb-0 small text-muted border-top pt-3 mt-3">';
            echo '<i class="fas fa-map-marker-alt text-danger me-2"></i>' . $address;
            echo '</p>';
        }
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }
    echo '</div>'; // End row

} catch (Throwable $e) {
    // Silent fail for public facing page
    error_log("Public Owners Error: " . $e->getMessage());
}
?>
