<?php

echo "=== Debug Login Test ===\n\n";

$baseUrl = 'http://localhost:8000';
$cookieJar = tempnam(sys_get_temp_dir(), 'cookies');

// Function to make cURL request
function makeRequest($url, $method = 'GET', $data = null, $headers = [], $cookieJar = null) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    if ($cookieJar) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    }
    
    if ($data && ($method === 'POST' || $method === 'PUT')) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    
    curl_close($ch);
    
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    return [
        'code' => $httpCode,
        'headers' => $headers,
        'body' => $body,
        'header_lines' => explode("\r\n", trim($headers))
    ];
}

echo "1. Getting CSRF token...\n";
$csrfResponse = makeRequest("$baseUrl/sanctum/csrf-cookie", 'GET', null, [], $cookieJar);
echo "CSRF response code: " . $csrfResponse['code'] . "\n";

echo "\n2. Attempting debug login...\n";
$loginData = json_encode([
    'email' => 'admin@dttbylogix.com',
    'password' => 'password123'
]);

$loginHeaders = [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
];

$loginResponse = makeRequest("$baseUrl/api/debug-login", 'POST', $loginData, $loginHeaders, $cookieJar);
echo "Debug login response code: " . $loginResponse['code'] . "\n";
echo "Debug login response body: " . json_encode(json_decode($loginResponse['body'], true), JSON_PRETTY_PRINT) . "\n";

echo "\n3. Checking session after debug login...\n";
$debugHeaders = [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
];

$debugResponse = makeRequest("$baseUrl/api/debug-session", 'GET', null, $debugHeaders, $cookieJar);
echo "Debug session response code: " . $debugResponse['code'] . "\n";
echo "Debug session response body: " . json_encode(json_decode($debugResponse['body'], true), JSON_PRETTY_PRINT) . "\n";

// Clean up
unlink($cookieJar);

echo "\n=== End Debug Login Test ===\n";