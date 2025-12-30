<?php
require_once 'inc/connection.php';
$stmt = $pdo->query("DESCRIBE followup_clients");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($columns, JSON_PRETTY_PRINT);
?>
