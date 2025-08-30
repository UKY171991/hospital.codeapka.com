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

    echo '<div class="card plan">';
    // Check if this is the most popular plan (you can customize this logic)
    if ($name === 'Professional') {
        echo '<div class="popular-badge">POPULAR</div>';
    }
    echo '<h3>' . $name . '</h3>';
    echo '<div class="price">' . ($price !== 'Contact' ? '₹' . $price : 'Contact') . ' <span class="small">/ ' . ($type === 'yearly' ? 'year' : 'month') . '</span></div>';
    echo '<div class="features small"><div>' . $desc . '</div>';
    if($upi) echo '<div>UPI: ' . $upi . '</div>';
    echo '</div>';
    if($qr){
        echo '<div class="qr-wrap mt-3 text-center">';
        echo '<img class="qr-thumb" src="' . htmlspecialchars($qr) . '" alt="QR code">';
        echo '</div>';
        echo '<p class="mt-3 text-center"><a class="button small" href="' . htmlspecialchars($qr) . '" target="_blank">Download QR</a></p>';
    }
    echo '</div>';
}
echo '</div>';
?>