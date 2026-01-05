<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/auth.php';

$userIds = getUsersUnderAdmin($pdo);
$counts = [];
$tables = [
  'doctors','patients','owners','notices','plans','entries','tests','users'
];

function getFilteredCount($pdo, $table, $userIds) {
    try {
        $where = "";
        $params = [];
        if ($userIds !== null) {
            $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE 'added_by'");
            if ($stmt && $stmt->fetch()) {
                if (empty($userIds)) return 0;
                $placeholders = implode(',', array_fill(0, count($userIds), '?'));
                $where = " WHERE added_by IN ($placeholders)";
                $params = $userIds;
            }
        }
        $sql = "SELECT COUNT(*) FROM `$table`" . $where;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    } catch (Throwable $e) {
        return '--';
    }
}

try {
  foreach($tables as $t){
    $counts[$t] = getFilteredCount($pdo, $t, $userIds);
  }

  // test_categories separately
  try{
    $table_cat = null;
    $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
    if($stmt->fetch()){
      $table_cat = 'categories';
    } else {
      $stmt2 = $pdo->query("SHOW TABLES LIKE 'test_categories'");
      if($stmt2->fetch()){
        $table_cat = 'test_categories';
      }
    }
    
    if($table_cat){
      $counts['test_categories'] = getFilteredCount($pdo, $table_cat, $userIds);
      $counts['test_categories_table'] = $table_cat;
    } else {
      $counts['test_categories'] = '--';
      $counts['test_categories_table'] = null;
    }
  } catch (Throwable $e){
    $counts['test_categories'] = '--';
    $counts['test_categories_table'] = null;
  }

  // uploads
  try{
    $stmt = $pdo->query("SHOW TABLES LIKE 'zip_uploads'");
    if($stmt->fetch()){
      $counts['uploads'] = getFilteredCount($pdo, 'zip_uploads', $userIds);
    } else {
      $counts['uploads'] = '--';
    }
  } catch (Throwable $e) { $counts['uploads'] = '--'; }

  echo json_encode(['success'=>true,'counts'=>$counts]);
} catch (Throwable $e){
  error_log('dashboard_counts error: ' . $e->getMessage());
  echo json_encode(['success'=>false,'message'=>'Failed to retrieve counts']);
}
