<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "Start<br>";
require_once 'inc/header.php';
echo "Header Loaded<br>";
require_once 'inc/sidebar.php';
echo "Sidebar Loaded<br>";
?>
<div>Hello World</div>
<?php require_once 'inc/footer.php'; ?>
