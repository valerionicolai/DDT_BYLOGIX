<?php

echo "=== Testing Debug Auth ===\n\n";

// Initialize cURL
$cookieJar = tempnam(sys_get_temp_dir(), 'cookies');

// 1. Login first
echo "1. Attempting login...\n";
$loginCh = curl_init();
curl_setopt($loginCh, CURLOPT_URL, 'http://localhost:8000/api/auth/login');
curl_setopt($loginCh, CURLOPT_POST, true);
curl_setopt($loginCh, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'admin@dttbylogix.com',
    'password' => 'password123'
]));
curl_setopt($loginCh, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($loginCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($loginCh, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($loginCh, CURLOPT_COOKIEFILE, $cookieJar);

$loginResponse = curl_exec($loginCh);
$loginHttpCode = curl_getinfo($loginCh, CURLINFO_HTTP_CODE);
curl_close($loginCh);

echo "Login response code: $loginHttpCode\n";
echo "Login response body: $loginResponse\n\n";

// 2. Test debug auth endpoint
echo "2. Testing debug auth endpoint...\n";
$debugCh = curl_init();
curl_setopt($debugCh, CURLOPT_URL, 'http://localhost:8000/api/debug-auth');
curl_setopt($debugCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($debugCh, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($debugCh, CURLOPT_COOKIEFILE, $cookieJar);
curl_setopt($debugCh, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

$debugResponse = curl_exec($debugCh);
$debugHttpCode = curl_getinfo($debugCh, CURLINFO_HTTP_CODE);
curl_close($debugCh);

echo "Debug auth response code: $debugHttpCode\n";
echo "Debug auth response body: " . json_encode(json_decode($debugResponse), JSON_PRETTY_PRINT) . "\n\n";

// Cleanup
unlink($cookieJar);

echo "=== End Debug Auth Test ===\n";