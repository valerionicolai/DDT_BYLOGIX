<?php

// Test script to debug CSRF and session handling
echo "=== CSRF and Session Debug Test ===\n";

// Initialize cURL
$ch = curl_init();

// Cookie jar to maintain session
$cookieJar = tempnam(sys_get_temp_dir(), 'cookies');

// Step 1: Get CSRF cookie
echo "1. Getting CSRF cookie...\n";
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/sanctum/csrf-cookie',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "CSRF cookie response code: $httpCode\n";

// Extract headers and body
list($headers, $body) = explode("\r\n\r\n", $response, 2);
echo "CSRF response headers:\n$headers\n\n";

// Step 2: Read cookies to get CSRF token
$cookies = file_get_contents($cookieJar);
echo "Cookies after CSRF request:\n$cookies\n\n";

// Extract XSRF-TOKEN from cookies
preg_match('/XSRF-TOKEN\s+([^\s]+)/', $cookies, $matches);
$xsrfToken = isset($matches[1]) ? urldecode($matches[1]) : null;
echo "Extracted XSRF-TOKEN: " . ($xsrfToken ? $xsrfToken : 'NOT FOUND') . "\n\n";

// Step 3: Attempt login with CSRF token
echo "2. Attempting login with CSRF token...\n";
$loginData = json_encode([
    'email' => 'admin@dttbylogix.com',
    'password' => 'password123'
]);

curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/api/auth/login',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $loginData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
        'X-XSRF-TOKEN: ' . $xsrfToken,
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Login response code: $httpCode\n";

// Extract headers and body
list($headers, $body) = explode("\r\n\r\n", $response, 2);
echo "Login response headers:\n$headers\n\n";
echo "Login response body:\n$body\n\n";

// Step 4: Check cookies after login
$cookies = file_get_contents($cookieJar);
echo "Cookies after login:\n$cookies\n\n";

// Step 5: Test protected endpoint
echo "3. Testing protected endpoint...\n";
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/api/auth/user',
    CURLOPT_POST => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Protected endpoint response code: $httpCode\n";

// Extract headers and body
list($headers, $body) = explode("\r\n\r\n", $response, 2);
echo "Protected endpoint response headers:\n$headers\n\n";
echo "Protected endpoint response body:\n$body\n\n";

// Cleanup
curl_close($ch);
unlink($cookieJar);

echo "=== End CSRF and Session Debug Test ===\n";