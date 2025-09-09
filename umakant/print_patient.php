<?php
require_once __DIR__ . '/inc/connection.php';
// Lightweight printable patient view

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$patient = null;
if ($id > 0) {
    $sql = "SELECT p.*, COALESCE(u.full_name, u.username, p.added_by) AS added_by_name
            FROM patients p
            LEFT JOIN users u ON (p.added_by = u.id OR p.added_by = u.username)
            WHERE p.id = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
}

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function fmtDate($d) {
    if (!$d) return 'N/A';
    $ts = strtotime($d);
    if (!$ts) return esc($d);
    return date('d-m-Y H:i', $ts);
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Print Patient - <?= $id ?: 'N/A' ?></title>
    <!-- Use existing site styles if available -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/vendors/bootstrap.min.css">
    <style>
        body{background:#f7f9fc;color:#222;font-family:Arial,Helvetica,sans-serif}
        .print-container{max-width:900px;margin:24px auto;padding:20px}
        .card-print{background:#fff;border:1px solid #e6e9ef;padding:22px;border-radius:6px}
        .header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
        .hospital-title{font-size:20px;font-weight:700;color:#0b5ed7}
        .detail-row{display:flex;padding:10px 0;border-bottom:1px dashed #efefef}
        .detail-label{width:28%;font-weight:600;color:#333}
        .detail-value{width:72%;color:#444}
        .meta{font-size:13px;color:#666}
        .no-print{margin-bottom:12px}
        @media print{ .no-print{display:none!important} body{background:#fff} .print-container{margin:0;padding:0} }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="no-print text-right">
            <button class="btn btn-primary" onclick="window.print()">Print</button>
            <button class="btn btn-secondary" onclick="window.close()">Close</button>
        </div>

        <div class="card-print">
            <div class="header">
                <div>
                    <div class="hospital-title">Hospital Patient Record</div>
                    <div class="meta">Generated: <?= date('d-m-Y H:i') ?></div>
                </div>
                <div style="text-align:right">
                    <div><strong>Patient ID:</strong> <?= esc($patient['id'] ?? $id) ?></div>
                    <div class="meta">UHID: <?= esc($patient['uhid'] ?? '—') ?></div>
                </div>
            </div>

            <?php if (!$patient): ?>
                <div class="alert alert-warning">Patient not found (ID: <?= $id ?>)</div>
            <?php else: ?>
                <div class="detail-row">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value"><?= esc($patient['name']) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Age / Unit</div>
                    <div class="detail-value"><?= esc($patient['age']) ?> <?= esc($patient['age_unit'] ?? '') ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Gender</div>
                    <div class="detail-value"><?= esc($patient['gender'] ?? $patient['sex'] ?? '') ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Mobile</div>
                    <div class="detail-value"><?= esc($patient['mobile']) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?= esc($patient['email']) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Father / Husband</div>
                    <div class="detail-value"><?= esc($patient['father_husband']) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Address</div>
                    <div class="detail-value"><?= nl2br(esc($patient['address'])) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Registration</div>
                    <div class="detail-value"><?= fmtDate($patient['created_at'] ?? $patient['created']) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Added By</div>
                    <div class="detail-value"><?= esc($patient['added_by_name'] ?? $patient['added_by'] ?? 'System') ?></div>
                </div>

                <?php if (!empty($patient['other_notes']) || !empty($patient['notes'])): ?>
                <div class="detail-row">
                    <div class="detail-label">Notes</div>
                    <div class="detail-value"><?= nl2br(esc($patient['other_notes'] ?? $patient['notes'])) ?></div>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <div style="margin-top:18px; display:flex; justify-content:space-between;">
                <div class="meta">Printed from Hospital Admin</div>
                <div class="meta">Signature: ______________________</div>
            </div>
        </div>
    </div>
</body>
</html>
