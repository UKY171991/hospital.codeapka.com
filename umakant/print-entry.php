<?php
require_once 'inc/connection.php';
require_once 'inc/simple_auth.php';

// Authenticate
$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    die("Authentication required");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    die("Entry ID required");
}

// Fetch entry with patient and doctor details
$stmt = $pdo->prepare("
    SELECT e.*, p.name as patient_name, p.uhid as patient_uhid, p.age, p.age_unit, p.sex,
           d.name as doctor_name, d.qualification as doctor_qualification,
           u.full_name as added_by_name
    FROM entries e
    LEFT JOIN patients p ON e.patient_id = p.id
    LEFT JOIN doctors d ON e.doctor_id = d.id
    LEFT JOIN users u ON e.added_by = u.id
    WHERE e.id = ?
");
$stmt->execute([$id]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entry) {
    die("Entry not found");
}

// Fetch tests for this entry
$stmt = $pdo->prepare("
    SELECT et.*, t.name as test_name, t.specimen, t.method, t.reference_range as test_ref_range,
           t.min as test_min, t.max as test_max, t.unit as test_unit
    FROM entry_tests et
    LEFT JOIN tests t ON et.test_id = t.id
    WHERE et.entry_id = ?
    ORDER BY et.id ASC
");
$stmt->execute([$id]);
$entry_tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch hospital/owner info for header
$stmt = $pdo->query("SELECT * FROM owners LIMIT 1");
$owner = $stmt->fetch(PDO::FETCH_ASSOC);
$hospital_name = $owner['name'] ?? 'Pathology Laboratory';
$hospital_address = $owner['address'] ?? '';
$hospital_phone = $owner['phone'] ?? '';
$hospital_email = $owner['email'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pathology Report - #<?php echo $id; ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #fff;
        }
        .report-header {
            text-align: center;
            border-bottom: 2px solid #444;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .hospital-name {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            color: #0056b3;
            text-transform: uppercase;
        }
        .hospital-info {
            font-size: 14px;
            margin: 5px 0;
        }
        .report-title {
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0;
            text-transform: uppercase;
        }
        .patient-info-box {
            width: 100%;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 8px;
            font-size: 14px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .test-results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .test-results-table th {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        .test-results-table td {
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 14px;
        }
        .test-name {
            font-weight: bold;
        }
        .result-value {
            font-weight: bold;
            color: #000;
        }
        .abnormal {
            color: red;
        }
        .report-footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .footer-sign {
            text-align: center;
            width: 200px;
        }
        .footer-sign .sign-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
        .print-btn-container {
            margin-bottom: 20px;
            text-align: right;
        }
        @media print {
            .print-btn-container {
                display: none;
            }
            body {
                padding: 0;
            }
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="print-btn-container">
        <button class="btn" onclick="window.print()">Print Report</button>
        <button class="btn" style="background-color: #6c757d;" onclick="window.close()">Close</button>
    </div>

    <div class="report-header">
        <div class="hospital-name"><?php echo htmlspecialchars($hospital_name); ?></div>
        <div class="hospital-info"><?php echo htmlspecialchars($hospital_address); ?></div>
        <div class="hospital-info">Phone: <?php echo htmlspecialchars($hospital_phone); ?> | Email: <?php echo htmlspecialchars($hospital_email); ?></div>
        <div class="report-title">Pathology Report</div>
    </div>

    <div class="patient-info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">Patient Name:</td>
                <td><?php echo htmlspecialchars($entry['patient_name']); ?></td>
                <td class="info-label">Report Date:</td>
                <td><?php echo date('d-M-Y', strtotime($entry['entry_date'])); ?></td>
            </tr>
            <tr>
                <td class="info-label">Age / Sex:</td>
                <td><?php echo htmlspecialchars($entry['age'] . ' ' . $entry['age_unit'] . ' / ' . $entry['sex']); ?></td>
                <td class="info-label">Entry ID:</td>
                <td>#<?php echo $id; ?></td>
            </tr>
            <tr>
                <td class="info-label">Referrer:</td>
                <td>Dr. <?php echo htmlspecialchars($entry['doctor_name'] ?? 'Self'); ?></td>
                <td class="info-label">UHID:</td>
                <td><?php echo htmlspecialchars($entry['patient_uhid'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="info-label">Specimen:</td>
                <td>
                    <?php 
                        $specimens = array_unique(array_filter(array_column($entry_tests, 'specimen')));
                        echo htmlspecialchars(implode(', ', $specimens) ?: 'N/A');
                    ?>
                </td>
                <td class="info-label">Status:</td>
                <td>
                    <span class="status-badge status-<?php echo $entry['status']; ?>">
                        <?php echo $entry['status']; ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <table class="test-results-table">
        <thead>
            <tr>
                <th width="35%">Test Description</th>
                <th width="15%">Value</th>
                <th width="15%">Unit</th>
                <th width="35%">Reference Range</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entry_tests as $test): ?>
            <tr>
                <td class="test-name"><?php echo htmlspecialchars($test['test_name']); ?></td>
                <td class="result-value"><?php echo htmlspecialchars($test['result_value'] ?: 'PENDING'); ?></td>
                <td><?php echo htmlspecialchars($test['unit'] ?: $test['test_unit'] ?: '-'); ?></td>
                <td>
                    <?php 
                        if (!empty($test['test_ref_range'])) {
                            echo htmlspecialchars($test['test_ref_range']);
                        } elseif (isset($test['test_min']) && isset($test['test_max'])) {
                            echo htmlspecialchars($test['test_min'] . ' - ' . $test['test_max']);
                        } else {
                            echo '-';
                        }
                    ?>
                </td>
            </tr>
            <?php if (!empty($test['remarks'])): ?>
            <tr>
                <td colspan="4" style="font-size: 12px; color: #666; font-style: italic; border-top: none; padding-top: 0;">
                    Note: <?php echo htmlspecialchars($test['remarks']); ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!empty($entry['notes'])): ?>
    <div style="margin-bottom: 20px;">
        <div style="font-weight: bold; margin-bottom: 5px;">General Interpretation / Notes:</div>
        <div style="font-size: 14px; border-left: 3px solid #ddd; padding-left: 10px;">
            <?php echo nl2br(htmlspecialchars($entry['notes'])); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="report-footer">
        <div class="footer-sign">
            <div class="sign-line">Lab Technician</div>
            <div style="font-size: 12px;"><?php echo htmlspecialchars($entry['added_by_name']); ?></div>
        </div>
        <div class="footer-sign">
            <div class="sign-line">Pathologist</div>
            <div style="font-size: 12px;">M.D. (Pathology)</div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 50px; font-size: 12px; color: #888;">
        *** End of Report ***
    </div>

    <script>
        // Auto print after a short delay
        window.onload = function() {
            setTimeout(function() {
                // window.print();
            }, 1000);
        };
    </script>
</body>
</html>
