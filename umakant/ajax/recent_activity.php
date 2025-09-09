<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../inc/connection.php';

$items = [];
try {
    // Recent patients
    try {
        $stmt = $pdo->query("SELECT id, name, mobile, created_at FROM patients ORDER BY created_at DESC LIMIT 5");
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = [
                'type' => 'patient',
                'title' => 'New patient registered',
                'details' => $r['name'] . ' (' . ($r['mobile'] ?: 'no mobile') . ')',
                'time' => $r['created_at'],
            ];
        }
    } catch (Throwable $e) { /* ignore */ }

    // Recent entries
    try {
        $stmt = $pdo->query("SELECT e.id, e.patient_id, e.entry_date, p.name AS patient_name FROM entries e LEFT JOIN patients p ON e.patient_id = p.id ORDER BY e.created_at DESC LIMIT 5");
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = [
                'type' => 'entry',
                'title' => 'Test entry created',
                'details' => trim(($r['patient_name'] ?: 'Unknown') . ' (Entry ID: ' . $r['id'] . ')'),
                'time' => $r['entry_date'] ?: $r['created_at'] ?? null,
            ];
        }
    } catch (Throwable $e) { }

    // Recent notices
    try {
        $stmt = $pdo->query("SELECT id, title, created_at FROM notices ORDER BY created_at DESC LIMIT 5");
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = [
                'type' => 'notice',
                'title' => 'New notice published',
                'details' => $r['title'],
                'time' => $r['created_at'],
            ];
        }
    } catch (Throwable $e) { }

    // Recent uploads
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'zip_uploads'");
        if ($stmt->fetch()) {
            $stmt2 = $pdo->query("SELECT id, original_name, created_at FROM zip_uploads ORDER BY created_at DESC LIMIT 5");
            while ($r = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $items[] = [
                    'type' => 'upload',
                    'title' => 'File uploaded',
                    'details' => $r['original_name'] ?: $r['id'],
                    'time' => $r['created_at'],
                ];
            }
        }
    } catch (Throwable $e) { }

    // Sort items by time descending and limit to 10
    usort($items, function($a, $b){ $ta = strtotime($a['time'] ?: '0'); $tb = strtotime($b['time'] ?: '0'); return $tb <=> $ta; });
    $items = array_slice($items, 0, 10);

    echo json_encode(['success' => true, 'items' => $items]);
} catch (Throwable $e) {
    error_log('recent_activity error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load recent activity']);
}

?>
