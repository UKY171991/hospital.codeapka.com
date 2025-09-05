<?php
// debug_patient_list.php - wrapper to capture patient_api.php output/errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
$_GET['action'] = 'list';
$_GET['draw'] = 1;
$_GET['start'] = 0;
$_GET['length'] = 10;

include __DIR__ . '/ajax/patient_api.php';

$content = ob_get_clean();
file_put_contents(__DIR__ . '/debug_patient_list_out.json', $content);
echo "Wrote debug_patient_list_out.json\n";
echo $content;
?>
