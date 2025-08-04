<?php

echo "=== Testing Login API Directly ===\n";

$baseUrl = 'http://127.0.0.1:8000';

// First, get CSRF cookie
echo "1. Getting CSRF cookie...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/sanctum/csrf-cookie');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "CSRF cookie response code: $httpCode\n";

// Extract CSRF token from cookies
$csrfToken = null;
if (file_exists('/tmp/cookies.txt')) {
    $cookies = file_get_contents('/tmp/cookies.txt');
    if (preg_match('/XSRF-TOKEN\s+([^\s]+)/', $cookies, $matches)) {
        $csrfToken = urldecode($matches[1]);
        echo "CSRF token extracted: " . substr($csrfToken, 0, 20) . "...\n";
    }
}

// Now try to login
echo "2. Attempting login...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'admin@dttbylogix.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest',
    $csrfToken ? 'X-XSRF-TOKEN: ' . $csrfToken : ''
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login response code: $httpCode\n";
echo "Login response: $response\n";

// Test if we can access protected endpoint
echo "3. Testing protected endpoint...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/auth/user');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "User endpoint response code: $httpCode\n";
echo "User endpoint response: $response\n";

echo "=== End Test ===\n";