<?php
$ctx = stream_context_create([
    'http' => [
        'ignore_errors' => true,
        'timeout' => 15,
        'header' => "Accept: application/json\r\nAccept-Encoding: identity\r\n"
    ],
]);
$url = 'https://hospital.codeapka.com/umakant/ajax/owner_api.php?action=list';
$response = file_get_contents($url, false, $ctx);
var_dump($http_response_header ?? []);
echo "\n=== RESPONSE ===\n";
echo $response;

$decoded = json_decode($response, true);
echo "\n=== DECODED ===\n";
var_dump($decoded);
