<?php

echo "=== Testing Web Protected Route ===\n\n";

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

// 2. Test protected web route
echo "2. Testing protected web route...\n";
$protectedCh = curl_init();
curl_setopt($protectedCh, CURLOPT_URL, 'http://localhost:8000/test-protected-web');
curl_setopt($protectedCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($protectedCh, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($protectedCh, CURLOPT_COOKIEFILE, $cookieJar);
curl_setopt($protectedCh, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

$protectedResponse = curl_exec($protectedCh);
$protectedHttpCode = curl_getinfo($protectedCh, CURLINFO_HTTP_CODE);
curl_close($protectedCh);

echo "Protected web route response code: $protectedHttpCode\n";
echo "Protected web route response body: $protectedResponse\n\n";

// Cleanup
unlink($cookieJar);

echo "=== End Web Protected Route Test ===\n";