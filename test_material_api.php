<?php

/**
 * Test script for Material API endpoints
 * Run with: php test_material_api.php
 */

$baseUrl = 'http://127.0.0.1:8000/api';

// Test endpoints
$endpoints = [
    'GET /materials' => $baseUrl . '/materials',
    'GET /materials/stats' => $baseUrl . '/materials/stats',
    'GET /material-types' => $baseUrl . '/material-types',
    'GET /projects' => $baseUrl . '/projects',
];

echo "Testing Material API Endpoints...\n";
echo "================================\n\n";

foreach ($endpoints as $name => $url) {
    echo "Testing: $name\n";
    echo "URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ CURL Error: $error\n";
    } else {
        $data = json_decode($response, true);
        
        if ($httpCode === 200) {
            echo "✅ Success (HTTP $httpCode)\n";
            if (isset($data['success']) && $data['success']) {
                echo "   API Response: Success\n";
            } else {
                echo "   API Response: " . ($data['message'] ?? 'Unknown') . "\n";
            }
        } elseif ($httpCode === 401) {
            echo "🔒 Authentication Required (HTTP $httpCode)\n";
            echo "   This is expected for protected endpoints\n";
        } else {
            echo "❌ Failed (HTTP $httpCode)\n";
            echo "   Response: " . substr($response, 0, 200) . "...\n";
        }
    }
    
    echo "\n";
}

echo "Testing Public QR Scanner...\n";
echo "============================\n";

$publicUrl = 'http://127.0.0.1:8000/public/scanner';
echo "URL: $publicUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $publicUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL Error: $error\n";
} else {
    if ($httpCode === 200) {
        echo "✅ Public QR Scanner accessible (HTTP $httpCode)\n";
        if (strpos($response, 'QR Code Scanner') !== false) {
            echo "   Page content looks correct\n";
        }
    } else {
        echo "❌ Failed (HTTP $httpCode)\n";
    }
}

echo "\nTest completed!\n";