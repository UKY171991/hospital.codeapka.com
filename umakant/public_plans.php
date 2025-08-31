<?php
// public_plans.php - outputs a simple, safe HTML fragment of available plans
require_once __DIR__ . '/inc/connection.php';

try{
    $stmt = $pdo->prepare('SELECT id,name,description,price,upi,time_type,qr_code FROM plans ORDER BY id DESC');
    $stmt->execute();
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(Throwable $e){
    $plans = [];
}

if (!$plans || count($plans) === 0){
    echo '<div class="small">No plans available at this time.</div>';
    return;
}

echo '<div class="pricing-grid">';
foreach($plans as $p){
    $name = htmlspecialchars($p['name'] ?? '');
    $desc = htmlspecialchars($p['description'] ?? '');
    $price = $p['price'] !== null ? number_format((float)$p['price'],2) : 'Contact';
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
    $isPopular = (stripos($name, 'professional') !== false || stripos($name, 'yearly') !== false);
    
    echo '<div class="pricing-card' . ($isPopular ? ' popular' : '') . '">';
    
    // Add popular badge for highlighted plans
    if ($isPopular) {
        echo '<div class="popular-badge">Most Popular</div>';
    }
    
    echo '<div class="plan-header">';
    echo '<h3>' . $name . '</h3>';
    echo '<p>' . $desc . '</p>';
    echo '<div class="price">';
    echo '<span class="currency">₹</span>';
    echo '<span class="amount">' . ($price !== 'Contact' ? $price : 'Contact') . '</span>';
    echo '<span class="period">/ ' . ($type === 'yearly' ? 'year' : 'month') . '</span>';
    echo '</div>';
    echo '</div>';
    
    echo '<div class="plan-features">';
    // Add some default features based on plan type
    if (stripos($name, 'basic') !== false) {
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Up to 10 users</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Basic patient management</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Appointment scheduling</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Basic reporting</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Email support</span></div>';
    } elseif (stripos($name, 'professional') !== false || stripos($name, 'yearly') !== false) {
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Up to 50 users</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Advanced patient management</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Inventory management</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Billing & invoicing</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Advanced analytics</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Priority support</span></div>';
    } else {
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Unlimited users</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Multi-location support</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Custom integrations</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Advanced security</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Dedicated support</span></div>';
        echo '<div class="feature-item"><span class="feature-icon">✓</span><span>Custom training</span></div>';
    }
    
    if($upi) {
        echo '<div class="feature-item"><span class="feature-icon">💳</span><span>UPI: ' . $upi . '</span></div>';
    }
    echo '</div>';
    
    if($qr){
        echo '<div class="qr-section">';
        echo '<div class="qr-wrap text-center">';
        echo '<img class="qr-thumb" src="' . htmlspecialchars($qr) . '" alt="QR code for payment" style="max-width: 120px; height: auto; border-radius: 8px;">';
        echo '</div>';
        echo '<p class="text-center mt-2"><a class="qr-download" href="' . htmlspecialchars($qr) . '" target="_blank">Download QR Code</a></p>';
        echo '</div>';
    }
    
    echo '<div class="plan-action">';
    echo '<a href="contact.php" class="plan-btn' . ($isPopular ? ' primary' : '') . '">Get Started</a>';
    echo '</div>';
    
    echo '</div>';
}
echo '</div>';
?>