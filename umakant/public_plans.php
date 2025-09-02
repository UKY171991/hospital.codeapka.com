<?php
// public_plans.php - outputs a simple, safe HTML fragment of available plans
require_once __DIR__ . '/inc/connection.php';

try{
    $stmt = $pdo->prepare('SELECT id,name,description,price,upi,time_type,qr_code FROM plans WHERE price IS NOT NULL ORDER BY price ASC');
    $stmt->execute();
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(Throwable $e){
    echo '<div class="pricing-error">';
    echo '<h3>⚠️ Unable to Load Plans</h3>';
    echo '<p>We are experiencing technical difficulties. Please try again later or contact our support team.</p>';
    echo '<a href="contact.php" class="retry-btn">Contact Support</a>';
    echo '</div>';
    return;
}

if (!$plans || count($plans) === 0){
    echo '<div class="pricing-error">';
    echo '<h3>🚫 No Plans Available</h3>';
    echo '<p>We are currently updating our pricing plans. Please check back soon or contact us for more information.</p>';
    echo '<a href="contact.php" class="retry-btn">Contact Us</a>';
    echo '</div>';
    return;
}

// Enhanced pricing display with better row layout
echo '<div class="row pricing-plans-row">';

$planCount = count($plans);
$colClass = 'col-lg-4 col-md-6 col-sm-12'; // Default for 3 columns
if ($planCount == 2) {
    $colClass = 'col-lg-6 col-md-6 col-sm-12'; // 2 columns
} elseif ($planCount == 1) {
    $colClass = 'col-lg-8 col-md-10 col-sm-12 mx-auto'; // 1 column centered
} elseif ($planCount > 3) {
    $colClass = 'col-lg-3 col-md-6 col-sm-12'; // 4+ columns
}

foreach($plans as $index => $p){
    $name = htmlspecialchars($p['name'] ?? '');
    $desc = htmlspecialchars($p['description'] ?? '');
    $price = $p['price'] !== null ? number_format((float)$p['price'], 0) : 'Contact'; // Remove decimals for cleaner look
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

    // Determine if this plan should be highlighted as popular (middle plan or professional)
    $isPopular = (stripos($name, 'professional') !== false || stripos($name, 'premium') !== false || 
                  ($planCount >= 3 && $index == 1)); // Middle plan is popular
    
    echo '<div class="' . $colClass . ' mb-4">';
    echo '<div class="pricing-card card-hover-lift interactive-hover' . ($isPopular ? ' popular' : '') . '">';
    
    // Add popular badge for highlighted plans
    if ($isPopular) {
        echo '<div class="popular-badge pulse-glow">⭐ Most Popular</div>';
    }
    
    echo '<div class="plan-header">';
    echo '<div class="plan-icon">' . ($index == 0 ? '🚀' : ($index == 1 ? '💎' : '🏆')) . '</div>';
    echo '<h3>' . $name . '</h3>';
    echo '<p>' . $desc . '</p>';
    echo '</div>';
    
    echo '<div class="price-section">';
    echo '<div class="price-display">';
    if ($price !== 'Contact') {
        echo '<span class="currency">₹</span>';
        echo '<span class="amount">' . $price . '</span>';
        echo '<span class="period">/' . ($type === 'yearly' ? 'year' : 'month') . '</span>';
    } else {
        echo '<span class="contact-price">Contact for Pricing</span>';
    }
    echo '</div>';
    if ($type === 'yearly' && $price !== 'Contact') {
        $monthlyPrice = round((float)$price / 12);
        echo '<div class="price-note">₹' . number_format($monthlyPrice) . ' per month</div>';
    }
    echo '</div>';
    
    echo '<div class="plan-features">';
    
    // Enhanced features based on plan type and position
    $features = [];
    if (stripos($name, 'basic') !== false || $index == 0) {
        $features = [
            ['icon' => '👥', 'text' => 'Up to 10 users'],
            ['icon' => '📋', 'text' => 'Basic patient management'],
            ['icon' => '📅', 'text' => 'Appointment scheduling'],
            ['icon' => '📊', 'text' => 'Basic reporting'],
            ['icon' => '📧', 'text' => 'Email support'],
            ['icon' => '💾', 'text' => '5GB storage']
        ];
    } elseif (stripos($name, 'professional') !== false || stripos($name, 'premium') !== false || $index == 1) {
        $features = [
            ['icon' => '👥', 'text' => 'Up to 50 users'],
            ['icon' => '🏥', 'text' => 'Advanced patient management'],
            ['icon' => '📦', 'text' => 'Inventory management'],
            ['icon' => '💰', 'text' => 'Billing & invoicing'],
            ['icon' => '📈', 'text' => 'Advanced analytics'],
            ['icon' => '🎯', 'text' => 'Priority support'],
            ['icon' => '💾', 'text' => '50GB storage'],
            ['icon' => '🔄', 'text' => 'Auto backups']
        ];
    } else {
        $features = [
            ['icon' => '👥', 'text' => 'Unlimited users'],
            ['icon' => '🏢', 'text' => 'Multi-location support'],
            ['icon' => '🔗', 'text' => 'Custom integrations'],
            ['icon' => '🛡️', 'text' => 'Advanced security'],
            ['icon' => '🎧', 'text' => 'Dedicated support'],
            ['icon' => '🎓', 'text' => 'Custom training'],
            ['icon' => '💾', 'text' => 'Unlimited storage'],
            ['icon' => '🔄', 'text' => 'Real-time sync'],
            ['icon' => '📱', 'text' => 'Mobile apps']
        ];
    }
    
    foreach ($features as $feature) {
        echo '<div class="feature-item">';
        echo '<span class="feature-icon">' . $feature['icon'] . '</span>';
        echo '<span class="feature-text">' . $feature['text'] . '</span>';
        echo '</div>';
    }
    
    if($upi) {
        echo '<div class="feature-item upi-info">';
        echo '<span class="feature-icon">💳</span>';
        echo '<span class="feature-text">UPI: ' . $upi . '</span>';
        echo '</div>';
    }
    echo '</div>';
    
    // QR Code section with better styling
    if($qr){
        echo '<div class="qr-section">';
        echo '<div class="qr-header">';
        echo '<h5>💳 Quick Payment</h5>';
        echo '</div>';
        echo '<div class="qr-wrap">';
        echo '<img class="qr-thumb" src="' . htmlspecialchars($qr) . '" alt="QR code for ' . $name . ' plan payment" loading="lazy">';
        echo '</div>';
        echo '<div class="qr-actions">';
        echo '<a class="qr-download btn-sm" href="' . htmlspecialchars($qr) . '" target="_blank" download>';
        echo '<span class="download-icon">⬇️</span> Download QR';
        echo '</a>';
        echo '</div>';
        echo '</div>';
    }
    
    echo '<div class="plan-action">';
    echo '<a href="contact.php" class="plan-btn btn-magnetic ripple' . ($isPopular ? ' primary' : '') . '">';
    echo '<span class="btn-text">Choose Plan</span>';
    echo '<span class="btn-arrow">→</span>';
    echo '</a>';
    echo '</div>';
    
    echo '</div>'; // pricing-card
    echo '</div>'; // col
}
echo '</div>'; // row
?>