<?php
/**
 * Simple API test page
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>API Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>API Test Results</h2>
    <div id="results"></div>

    <script>
        $(document).ready(function() {
            console.log('Testing API endpoints...');
            
            // Test the main API
            $.ajax({
                url: 'patho_api/entry.php',
                method: 'GET',
                data: { action: 'list' },
                dataType: 'json',
                success: function(response) {
                    console.log('API Response:', response);
                    $('#results').append('<h3>API Test: SUCCESS</h3>');
                    $('#results').append('<pre>' + JSON.stringify(response, null, 2) + '</pre>');
                },
                error: function(xhr, status, error) {
                    console.error('API Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    $('#results').append('<h3>API Test: FAILED</h3>');
                    $('#results').append('<p>Status: ' + xhr.status + '</p>');
                    $('#results').append('<p>Error: ' + error + '</p>');
                    $('#results').append('<pre>' + xhr.responseText + '</pre>');
                }
            });
            
            // Test alternative API
            $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: { 
                    action: 'list',
                    secret_key: 'hospital-api-secret-2024'
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Alternative API Response:', response);
                    $('#results').append('<h3>Alternative API Test: SUCCESS</h3>');
                    $('#results').append('<pre>' + JSON.stringify(response, null, 2) + '</pre>');
                },
                error: function(xhr, status, error) {
                    console.error('Alternative API Error:', error);
                    $('#results').append('<h3>Alternative API Test: FAILED</h3>');
                    $('#results').append('<p>Status: ' + xhr.status + '</p>');
                    $('#results').append('<p>Error: ' + error + '</p>');
                }
            });
        });
    </script>
</body>
</html>