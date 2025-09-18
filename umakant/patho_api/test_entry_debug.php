<?php
// Test Entry API with debugging
header('Content-Type: application/json');

$url = 'https://hospital.codeapka.com/umakant/patho_api/entry.php?action=list';
$headers = [
    'X-Api-Key: hospital-api-secret-2024',
    'Content-Type: application/json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo json_encode([
    'http_code' => $httpCode,
    'response' => $response ? json_decode($response, true) : null,
    'raw_response' => $response
], JSON_PRETTY_PRINT);
?>
