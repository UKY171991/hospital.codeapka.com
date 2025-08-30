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

echo '<div class="plans-grid">';
foreach($plans as $p){
    $name = htmlspecialchars($p['name'] ?? '');
    $desc = htmlspecialchars($p['description'] ?? '');
    $price = $p['price'] !== null ? number_format((float)$p['price'],2) : 'Contact';
    $type = htmlspecialchars($p['time_type'] ?? 'monthly');
    $upi = htmlspecialchars($p['upi'] ?? '');
    $qr = $p['qr_code'] ?? null;
    // normalize qr path to public URL
    if ($qr && !preg_match('#^https?://#i', $qr)){
        if (strpos($qr, '/') === 0) $qr = (isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'https') . '://' . $_SERVER['HTTP_HOST'] . $qr;
        else $qr = (isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'https') . '://' . $_SERVER['HTTP_HOST'] . '/umakant/' . ltrim($qr,'/');
    }

    echo '<div class="card plan">';
    echo '<h3>' . $name . '</h3>';
    echo '<div class="price">' . ($price !== 'Contact' ? '₹' . $price : 'Contact') . ' <span class="small">/ ' . ($type === 'yearly' ? 'year' : 'month') . '</span></div>';
    echo '<div class="features small"><div>' . $desc . '</div>';
    if($upi) echo '<div>UPI: ' . $upi . '</div>';
    echo '</div>';
    if($qr) echo '<p style="margin-top:.75rem"><a class="button small" href="' . htmlspecialchars($qr) . '" target="_blank">Download QR</a></p>';
    echo '</div>';
}
echo '</div>';

?>
