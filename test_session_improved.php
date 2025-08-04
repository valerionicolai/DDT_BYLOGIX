<?php

echo "=== Improved Session Authentication Debug Test ===\n\n";

$baseUrl = 'http://localhost:8000';
$cookieJar = tempnam(sys_get_temp_dir(), 'cookies');

// Function to extract cookies from headers
function extractCookies($headers) {
    $cookies = [];
    foreach ($headers as $header) {
        if (strpos($header, 'Set-Cookie:') === 0) {
            $cookieData = substr($header, 12); // Remove "Set-Cookie: "
            $parts = explode(';', $cookieData);
            $cookiePart = trim($parts[0]);
            if (strpos($cookiePart, '=') !== false) {
                list($name, $value) = explode('=', $cookiePart, 2);
                $cookies[trim($name)] = trim($value);
            }
        }
    }
    return $cookies;
}

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

// Extract XSRF token from cookies
$cookies = extractCookies($csrfResponse['header_lines']);
$xsrfToken = isset($cookies['XSRF-TOKEN']) ? urldecode($cookies['XSRF-TOKEN']) : null;

echo "Extracted XSRF-TOKEN: " . ($xsrfToken ? "Found" : "NOT FOUND") . "\n";
if ($xsrfToken) {
    echo "XSRF Token: " . substr($xsrfToken, 0, 50) . "...\n";
}

// Check cookie jar contents
if (file_exists($cookieJar)) {
    echo "\nCookie jar contents:\n";
    echo file_get_contents($cookieJar) . "\n";
}

echo "\n2. Attempting login...\n";
$loginData = json_encode([
    'email' => 'admin@dttbylogix.com',
    'password' => 'password123'
]);

$loginHeaders = [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
];

if ($xsrfToken) {
    $loginHeaders[] = 'X-XSRF-TOKEN: ' . $xsrfToken;
}

$loginResponse = makeRequest("$baseUrl/api/auth/login", 'POST', $loginData, $loginHeaders, $cookieJar);
echo "Login response code: " . $loginResponse['code'] . "\n";
echo "Login response body: " . $loginResponse['body'] . "\n";

// Check cookie jar after login
if (file_exists($cookieJar)) {
    echo "\nCookie jar after login:\n";
    echo file_get_contents($cookieJar) . "\n";
}

echo "\n3. Testing protected endpoint...\n";
$protectedHeaders = [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
];

$protectedResponse = makeRequest("$baseUrl/api/auth/user", 'GET', null, $protectedHeaders, $cookieJar);
echo "Protected endpoint response code: " . $protectedResponse['code'] . "\n";
echo "Protected endpoint response body: " . $protectedResponse['body'] . "\n";

// Clean up
unlink($cookieJar);

echo "\n=== End Improved Session Debug Test ===\n";