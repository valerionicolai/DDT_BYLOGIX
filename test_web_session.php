<?php

echo "=== Web Session Test ===\n\n";

// Initialize cURL with cookie jar
$cookieJar = tempnam(sys_get_temp_dir(), 'cookies');
$baseUrl = 'http://localhost:8000';

function makeRequest($url, $data = null, $cookieJar = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $headers = [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest'
    ];
    
    if ($cookieJar) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    }
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $headers[] = 'Content-Type: application/json';
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    
    curl_close($ch);
    
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    return [
        'code' => $httpCode,
        'headers' => $headers,
        'body' => $body
    ];
}

// 1. Test web session
echo "1. Testing web session...\n";
$response1 = makeRequest($baseUrl . '/test-session', null, $cookieJar);
echo "Response code: " . $response1['code'] . "\n";
echo "Response body: " . $response1['body'] . "\n\n";

echo "Cookie jar after first request:\n";
echo file_get_contents($cookieJar) . "\n\n";

// 2. Test web session again (same session)
echo "2. Testing web session again (same session)...\n";
$response2 = makeRequest($baseUrl . '/test-session', null, $cookieJar);
echo "Response code: " . $response2['code'] . "\n";
echo "Response body: " . $response2['body'] . "\n\n";

// Clean up
unlink($cookieJar);

echo "=== End Web Session Test ===\n";