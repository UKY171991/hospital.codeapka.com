<?php
// api_config.php - optional settings for patho_api
// Do NOT commit real secrets to source control. Prefer environment variables on the server.

// Shared secret that allows direct API inserts when supplied via header X-Api-Key or param secret_key
$PATHO_API_SECRET = getenv('PATHO_API_SECRET') ?: 'hospital-api-secret-2024';

// Default user id to use for added_by when secret-based direct insert is used.
$PATHO_API_DEFAULT_USER_ID = getenv('PATHO_API_DEFAULT_USER_ID') !== false ? (int)getenv('PATHO_API_DEFAULT_USER_ID') : 1;

// Note: To configure, set environment variables on the server or create this file with the values
// (but avoid committing secrets). Example (server env):
// PATHO_API_SECRET=some-long-secret
// PATHO_API_DEFAULT_USER_ID=1

?>
