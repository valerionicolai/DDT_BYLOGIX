<?php

function makeRequest($url, $method = 'GET', $data = null, $headers = [], $cookies = []) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    
    if (!empty($cookies)) {
        $cookieString = '';
        foreach ($cookies as $name => $value) {
            $cookieString .= "$name=$value; ";
        }
        curl_setopt($ch, CURLOPT_COOKIE, rtrim($cookieString, '; '));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    
    curl_close($ch);
    
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    // Extract cookies from response headers
    $responseCookies = [];
    if (preg_match_all('/Set-Cookie:\s*([^=]+)=([^;]+)/', $headers, $matches)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            $name = trim($matches[1][$i]);
            $value = trim($matches[2][$i]);
            $responseCookies[$name] = $value;
        }
    }
    
    return [
        'code' => $httpCode,
        'body' => $body,
        'headers' => $headers,
        'cookies' => $responseCookies
    ];
}

$baseUrl = 'http://localhost:8000';
$cookies = [];

echo "=== Testing Manual Session Management ===\n\n";

// 1. Get CSRF token
echo "1. Getting CSRF token...\n";
$csrfResponse = makeRequest("$baseUrl/sanctum/csrf-cookie", 'GET', null, [], $cookies);
echo "CSRF response code: " . $csrfResponse['code'] . "\n";

// Update cookies with CSRF token and session
$cookies = array_merge($cookies, $csrfResponse['cookies']);
echo "Cookies after CSRF: " . json_encode(array_keys($cookies)) . "\n";

// 2. Login
echo "\n2. Attempting login...\n";
$loginData = json_encode([
    'email' => 'admin@dttbylogix.com',
    'password' => 'password123'
]);

$loginHeaders = [
    'Content-Type: application/json',
    'Accept: application/json',
];

// Add XSRF token if available
if (isset($cookies['XSRF-TOKEN'])) {
    $loginHeaders[] = 'X-XSRF-TOKEN: ' . urldecode($cookies['XSRF-TOKEN']);
}

$loginResponse = makeRequest("$baseUrl/api/auth/login", 'POST', $loginData, $loginHeaders, $cookies);
echo "Login response code: " . $loginResponse['code'] . "\n";
echo "Login response body: " . $loginResponse['body'] . "\n";
echo "Login response headers:\n" . $loginResponse['headers'] . "\n";

// Update cookies with login response
$cookies = array_merge($cookies, $loginResponse['cookies']);
echo "Cookies after login: " . json_encode($cookies) . "\n";

// 3. Test protected endpoint
echo "\n3. Testing protected endpoint...\n";
$protectedHeaders = [
    'Accept: application/json',
];

// Add XSRF token if available
if (isset($cookies['XSRF-TOKEN'])) {
    $protectedHeaders[] = 'X-XSRF-TOKEN: ' . urldecode($cookies['XSRF-TOKEN']);
}

$protectedResponse = makeRequest("$baseUrl/api/debug-auth", 'GET', null, $protectedHeaders, $cookies);
echo "Protected endpoint response code: " . $protectedResponse['code'] . "\n";
echo "Protected endpoint response headers:\n" . $protectedResponse['headers'] . "\n";
echo "Protected endpoint response body: " . $protectedResponse['body'] . "\n";

echo "\n=== End Manual Session Test ===\n";