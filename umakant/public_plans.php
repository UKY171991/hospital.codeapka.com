<?php
// public_plans.php - outputs a simple, safe HTML fragment of available plans
require_once __DIR__ . '/inc/connection.php';

try{
    $stmt = $pdo->prepare('SELECT id,name,description,price,upi,time_type,qr_code FROM plans WHERE price IS NOT NULL ORDER BY price ASC');
    $stmt->execute();
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(Throwable $e){
    echo '<div class="error-state">';
    echo '<h3>⚠️ Unable to Load Plans</h3>';
    echo '<p>We are experiencing technical difficulties. Please try again later or contact our support team.</p>';
    echo '</div>';
    return;
}

if (!$plans || count($plans) === 0){
    echo '<div class="error-state">';
    echo '<h3>🚫 No Plans Available</h3>';
    echo '<p>We are currently updating our pricing plans. Please check back soon.</p>';
    echo '</div>';
    return;
}

// Enhanced pricing display with better row layout
echo '<div class="pricing-plans-row">';

$planCount = count($plans);
$colClass = 'col-lg-4 col-md-6 col-sm-12'; // Default for 3 columns
if ($planCount == 2) {
    $colClass = 'col-lg-6 col-md-6 col-sm-12'; // 2 columns
} elseif ($planCount == 1) {
    $colClass = 'col-lg-6 col-md-6 col-sm-12 col-gl-offset-4'; // 1 column centered
} elseif ($planCount > 3) {
    $colClass = 'col-lg-3 col-md-6 col-sm-12'; // 4+ columns
}

foreach($plans as $index => $p){
    $name = htmlspecialchars($p['name'] ?? '');
    $desc = htmlspecialchars($p['description'] ?? '');
    $price = $p['price'] !== null ? number_format((float)$p['price'], 0) : 'Contact';
    $type = htmlspecialchars($p['time_type'] ?? 'monthly');
    $upi = htmlspecialchars($p['upi'] ?? '');
    $qr = $p['qr_code'] ?? null;
    
    // normalize qr path to public URL
    if ($qr && !preg_match('#^https?://#i', $qr)){
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'hospital.codeapka.com';
        if (strpos($qr, '/') === 0) $qr = $scheme . '://' . $host . $qr;
        else $qr = $scheme . '://' . $host . '/umakant/' . ltrim($qr,'/');
    }

    // Determine if this plan should be highlighted as popular
    // Logic: If yearly plan, or if it has "Pro" or "Premium" in name
    $isPopular = (stripos($name, 'yearly') !== false || stripos($name, 'professional') !== false || stripos($name, 'premium') !== false);
    
    echo '<div class="' . $colClass . '">';
    echo '<div class="pricing-card' . ($isPopular ? ' popular' : '') . '">';
    
    // Add popular badge
    if ($isPopular) {
        echo '<div class="popular-badge">⭐ Most Popular</div>';
    }
    
    // Plan Header
    echo '<div class="plan-header">';
    // Icon selection based on name or index
    $icon = '💎';
    if (stripos($name, 'basic') !== false) $icon = '🚀';
    if (stripos($name, 'yearly') !== false) $icon = '🏆';
    echo '<div class="plan-icon">' . $icon . '</div>';
    echo '<h3>' . $name . '</h3>';
    if ($desc) {
         echo '<p>' . $desc . '</p>';
    }
    echo '</div>'; // end plan-header
    
    // Price Section
    echo '<div class="price-section">';
    echo '<div class="price-display">';
    if ($price !== 'Contact') {
        echo '<span class="currency">₹</span>';
        echo '<span class="amount">' . $price . '</span>';
        echo '<span class="period">/' . ($type === 'yearly' ? 'year' : 'month') . '</span>';
    } else {
        echo '<span class="contact-price">Contact for Pricing</span>';
    }
    echo '</div>'; // end price-display
    
    if ($type === 'yearly' && $price !== 'Contact') {
        $monthlyPrice = round((float)str_replace(',','',$price) / 12);
        echo '<div class="price-note">Equivalent to ₹' . number_format($monthlyPrice) . ' per month</div>';
    }
    echo '</div>'; // end price-section
    
    // Features Section
    echo '<div class="plan-features">';
    
    // Default features if description is short or empty, otherwise try to use description or just show generic
    // Since we don't have a features column, we'll hardcode some logical defaults for these types of plans
    // OR just parse description if it has new lines
    $features = [];
    
    // If description has newlines, use them as features
    if (strpos($p['description'], "\n") !== false) {
        $lines = explode("\n", $p['description']);
        foreach($lines as $line) {
            $line = trim($line);
            if($line) $features[] = ['icon' => '✅', 'text' => $line];
        }
    } else {
        // Fallback features based on plan type for better UI
        if (stripos($name, 'basic') !== false) {
             $features = [
                ['icon' => '👥', 'text' => 'Single User limit'],
                ['icon' => '📅', 'text' => 'Appointment Scheduling'],
                ['icon' => '📃', 'text' => 'Basic Reports'],
                ['icon' => '📧', 'text' => 'Email Support'],
            ];
        } elseif (stripos($name, 'yearly') !== false) {
             $features = [
                ['icon' => '👥', 'text' => 'Unlimited Users'],
                ['icon' => '🏥', 'text' => 'Complete Hospital Management'],
                ['icon' => '📊', 'text' => 'Advanced Analytics & Reports'],
                ['icon' => '🔔', 'text' => 'SMS & Email Notifications'],
                ['icon' => '🛡️', 'text' => 'Priority 24/7 Support'],
                ['icon' => '💰', 'text' => 'Get 2 Months Free!'],
            ];
        } else {
             $features = [
                ['icon' => '✅', 'text' => 'Full Access to Features'],
                ['icon' => '✅', 'text' => 'Secure Data Storage'],
                ['icon' => '✅', 'text' => 'Regular Updates'],
            ];
        }
    }

    foreach ($features as $feature) {
        echo '<div class="feature-item">';
        echo '<span class="feature-icon">' . $feature['icon'] . '</span>';
        echo '<span class="feature-text">' . htmlspecialchars($feature['text']) . '</span>';
        echo '</div>';
    }
    
    if($upi) {
        echo '<div class="feature-item" style="border-color:var(--success); background:rgba(16, 185, 129, 0.05);">';
        echo '<span class="feature-icon" style="background:var(--success);">💳</span>';
        echo '<span class="feature-text" style="color:var(--gray-900); font-weight:600;">UPI: ' . $upi . '</span>';
        echo '</div>';
    }
    echo '</div>'; // end plan-features
    
    // QR Code Section
    if($qr){
        echo '<div class="qr-section">';
        echo '<div class="qr-header"><h5>Scan to Pay</h5></div>';
        echo '<div class="qr-wrap">';
        echo '<img class="qr-thumb" src="' . htmlspecialchars($qr) . '" alt="QR code" loading="lazy">';
        echo '</div>';
        echo '<div class="qr-actions">';
        echo '<a class="qr-download" href="' . htmlspecialchars($qr) . '" target="_blank" download>';
        echo '<span class="download-icon">⬇️</span> Download QR';
        echo '</a>';
        echo '</div>';
        echo '</div>'; // end qr-section
    }
    
    // Action Button
    echo '<div class="plan-action">';
    echo '<a href="contact.php" class="plan-btn' . ($isPopular ? ' primary' : '') . '">';
    echo '<span class="btn-text">Choose Plan</span>';
    echo '<span class="btn-arrow">→</span>';
    echo '</a>';
    echo '</div>';
    
    echo '</div>'; // end pricing-card
    echo '</div>'; // end col
}
echo '</div>'; // end row
?>