<?php
/**
 * Debug authentication variables
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Include API config
require_once __DIR__ . '/../inc/api_config.php';

$debug_info = [
    'PATHO_API_SECRET' => isset($PATHO_API_SECRET) ? 'SET (length: ' . strlen($PATHO_API_SECRET) . ')' : 'NOT SET',
    'PATHO_API_DEFAULT_USER_ID' => isset($PATHO_API_DEFAULT_USER_ID) ? $PATHO_API_DEFAULT_USER_ID : 'NOT SET',
    'headers' => getallheaders(),
    'request' => $_REQUEST,
    'env_secret' => getenv('PATHO_API_SECRET') ?: 'NOT SET',
    'secret_value' => $PATHO_API_SECRET ?? 'UNDEFINED'
];

echo json_encode($debug_info, JSON_PRETTY_PRINT);
?>
