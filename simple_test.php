<?php

// Test semplice per il login
$url = 'http://127.0.0.1:8000/api/auth/login';
$data = json_encode([
    'email' => 'admin@dttbylogix.com',
    'password' => 'password'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Error: $error\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    if (isset($responseData['data']['token'])) {
        $token = $responseData['data']['token'];
        echo "\nToken ottenuto: " . substr($token, 0, 20) . "...\n";
        
        // Test GET clients
        echo "\nTest GET /api/clients...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/clients');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ]);
        
        $clientsResponse = curl_exec($ch);
        $clientsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "HTTP Code: $clientsHttpCode\n";
        echo "Response: $clientsResponse\n";
    }
}