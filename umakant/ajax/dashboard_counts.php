<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../inc/connection.php';
$counts = [];
$tables = [
  'doctors','patients','owners','notices','plans','entries','tests','users'
];
try {
  foreach($tables as $t){
    try{
      $counts[$t] = (int) $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
    } catch (Throwable $e){
      $counts[$t] = '--';
    }
  }
  // test_categories separately
  try{
    $counts['test_categories'] = (int) $pdo->query('SELECT COUNT(*) FROM test_categories')->fetchColumn();
  } catch (Throwable $e){
    $counts['test_categories'] = '--';
  }
  // uploads
  try{
    $stmt = $pdo->query("SHOW TABLES LIKE 'zip_uploads'");
    $has = $stmt->fetch() ? true : false;
    if($has){
      $counts['uploads'] = (int) $pdo->query('SELECT COUNT(*) FROM zip_uploads')->fetchColumn();
    } else {
      $counts['uploads'] = '--';
    }
  } catch (Throwable $e) { $counts['uploads'] = '--'; }

  echo json_encode(['success'=>true,'counts'=>$counts]);
} catch (Throwable $e){
  error_log('dashboard_counts error: ' . $e->getMessage());
  echo json_encode(['success'=>false,'message'=>'Failed to retrieve counts']);
}
