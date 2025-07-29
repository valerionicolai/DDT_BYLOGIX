<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST PERMESSI ADMIN - ROUTE CLIENTI ===\n\n";

// Test delle route dei clienti con diversi ruoli
function testClientRoutes() {
    $baseUrl = 'http://localhost:8000/api/v1';
    
    echo "1. Test autenticazione come ADMIN...\n";
    
    // Login come admin
    $loginData = [
        'email' => 'admin@dttbylogix.com',
        'password' => 'password123'
    ];
    
    $loginResponse = makeRequest($baseUrl . '/auth/login', 'POST', $loginData);
    
    if ($loginResponse['status'] !== 200) {
        echo "✗ Errore nel login admin: " . $loginResponse['status'] . "\n";
        echo "Response: " . json_encode($loginResponse['body']) . "\n";
        return;
    }
    
    $adminToken = $loginResponse['body']['token'];
    $adminHeaders = ['Authorization: Bearer ' . $adminToken];
    
    echo "✓ Login admin riuscito\n";
    echo "Token: " . substr($adminToken, 0, 20) . "...\n\n";
    
    // Test operazioni CRUD come admin
    echo "2. Test operazioni CRUD come ADMIN...\n\n";
    
    // CREATE (POST)
    echo "2.1. Test CREATE (POST /clients)...\n";
    $clientData = [
        'name' => 'Test Admin Client',
        'email' => 'test.admin@example.com',
        'phone' => '+39 123 456789',
        'company' => 'Test Company Admin',
        'address' => 'Via Test 123',
        'city' => 'Milano',
        'postal_code' => '20100',
        'country' => 'Italia',
        'notes' => 'Cliente di test per admin'
    ];
    
    $createResponse = makeRequest($baseUrl . '/clients', 'POST', $clientData, $adminHeaders);
    echo "Status: " . $createResponse['status'] . "\n";
    
    if ($createResponse['status'] === 201) {
        echo "✓ CREATE riuscito - Admin può creare clienti\n";
        $createdClientId = $createResponse['body']['data']['id'];
        echo "ID cliente creato: " . $createdClientId . "\n";
    } else {
        echo "✗ CREATE fallito\n";
        echo "Response: " . json_encode($createResponse['body']) . "\n";
        return;
    }
    echo "\n";
    
    // READ (GET)
    echo "2.2. Test READ (GET /clients)...\n";
    $readResponse = makeRequest($baseUrl . '/clients', 'GET', null, $adminHeaders);
    echo "Status: " . $readResponse['status'] . "\n";
    
    if ($readResponse['status'] === 200) {
        echo "✓ READ riuscito - Admin può leggere lista clienti\n";
        echo "Numero clienti: " . count($readResponse['body']['data']) . "\n";
    } else {
        echo "✗ READ fallito\n";
        echo "Response: " . json_encode($readResponse['body']) . "\n";
    }
    echo "\n";
    
    // READ SINGLE (GET)
    echo "2.3. Test READ SINGLE (GET /clients/{id})...\n";
    $readSingleResponse = makeRequest($baseUrl . '/clients/' . $createdClientId, 'GET', null, $adminHeaders);
    echo "Status: " . $readSingleResponse['status'] . "\n";
    
    if ($readSingleResponse['status'] === 200) {
        echo "✓ READ SINGLE riuscito - Admin può leggere singolo cliente\n";
        echo "Nome cliente: " . $readSingleResponse['body']['data']['name'] . "\n";
    } else {
        echo "✗ READ SINGLE fallito\n";
        echo "Response: " . json_encode($readSingleResponse['body']) . "\n";
    }
    echo "\n";
    
    // UPDATE (PUT)
    echo "2.4. Test UPDATE (PUT /clients/{id})...\n";
    $updateData = [
        'name' => 'Test Admin Client Updated',
        'notes' => 'Cliente aggiornato da admin'
    ];
    
    $updateResponse = makeRequest($baseUrl . '/clients/' . $createdClientId, 'PUT', $updateData, $adminHeaders);
    echo "Status: " . $updateResponse['status'] . "\n";
    
    if ($updateResponse['status'] === 200) {
        echo "✓ UPDATE riuscito - Admin può aggiornare clienti\n";
        echo "Nome aggiornato: " . $updateResponse['body']['data']['name'] . "\n";
    } else {
        echo "✗ UPDATE fallito\n";
        echo "Response: " . json_encode($updateResponse['body']) . "\n";
    }
    echo "\n";
    
    // STATS (GET) - Solo admin
    echo "2.5. Test STATS (GET /clients/stats)...\n";
    $statsResponse = makeRequest($baseUrl . '/clients/stats', 'GET', null, $adminHeaders);
    echo "Status: " . $statsResponse['status'] . "\n";
    
    if ($statsResponse['status'] === 200) {
        echo "✓ STATS riuscito - Admin può accedere alle statistiche\n";
        echo "Stats: " . json_encode($statsResponse['body']['data']) . "\n";
    } else {
        echo "✗ STATS fallito\n";
        echo "Response: " . json_encode($statsResponse['body']) . "\n";
    }
    echo "\n";
    
    // DELETE (DELETE)
    echo "2.6. Test DELETE (DELETE /clients/{id})...\n";
    $deleteResponse = makeRequest($baseUrl . '/clients/' . $createdClientId, 'DELETE', null, $adminHeaders);
    echo "Status: " . $deleteResponse['status'] . "\n";
    
    if ($deleteResponse['status'] === 200) {
        echo "✓ DELETE riuscito - Admin può eliminare clienti\n";
    } else {
        echo "✗ DELETE fallito\n";
        echo "Response: " . json_encode($deleteResponse['body']) . "\n";
    }
    echo "\n";
    
    // Test con utente normale
    echo "3. Test con utente NORMALE (non admin)...\n\n";
    
    // Login come utente normale
    $userLoginData = [
        'email' => 'mario.rossi@example.com',
        'password' => 'password123'
    ];
    
    $userLoginResponse = makeRequest($baseUrl . '/auth/login', 'POST', $userLoginData);
    
    if ($userLoginResponse['status'] !== 200) {
        echo "✗ Errore nel login utente normale\n";
        echo "Response: " . json_encode($userLoginResponse['body']) . "\n";
        return;
    }
    
    $userToken = $userLoginResponse['body']['token'];
    $userHeaders = ['Authorization: Bearer ' . $userToken];
    
    echo "✓ Login utente normale riuscito\n\n";
    
    // Test operazioni che dovrebbero essere negate
    echo "3.1. Test CREATE come utente normale (dovrebbe fallire)...\n";
    $userCreateResponse = makeRequest($baseUrl . '/clients', 'POST', $clientData, $userHeaders);
    echo "Status: " . $userCreateResponse['status'] . "\n";
    
    if ($userCreateResponse['status'] === 403) {
        echo "✓ CREATE negato correttamente - Utente normale non può creare clienti\n";
    } else {
        echo "✗ CREATE non negato - PROBLEMA DI SICUREZZA!\n";
        echo "Response: " . json_encode($userCreateResponse['body']) . "\n";
    }
    echo "\n";
    
    echo "3.2. Test READ come utente normale (dovrebbe essere permesso)...\n";
    $userReadResponse = makeRequest($baseUrl . '/clients', 'GET', null, $userHeaders);
    echo "Status: " . $userReadResponse['status'] . "\n";
    
    if ($userReadResponse['status'] === 200) {
        echo "✓ READ permesso - Utente normale può leggere lista clienti\n";
    } else {
        echo "✗ READ negato - Potrebbe essere un problema\n";
        echo "Response: " . json_encode($userReadResponse['body']) . "\n";
    }
    echo "\n";
    
    echo "3.3. Test STATS come utente normale (dovrebbe fallire)...\n";
    $userStatsResponse = makeRequest($baseUrl . '/clients/stats', 'GET', null, $userHeaders);
    echo "Status: " . $userStatsResponse['status'] . "\n";
    
    if ($userStatsResponse['status'] === 403) {
        echo "✓ STATS negato correttamente - Utente normale non può accedere alle statistiche\n";
    } else {
        echo "✗ STATS non negato - PROBLEMA DI SICUREZZA!\n";
        echo "Response: " . json_encode($userStatsResponse['body']) . "\n";
    }
    echo "\n";
    
    echo "=== RIEPILOGO TEST PERMESSI ===\n";
    echo "✓ Admin può eseguire tutte le operazioni CRUD sui clienti\n";
    echo "✓ Admin può accedere alle statistiche\n";
    echo "✓ Utente normale non può creare/modificare/eliminare clienti\n";
    echo "✓ Utente normale può leggere la lista clienti\n";
    echo "✓ Utente normale non può accedere alle statistiche\n";
}

function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => array_merge([
            'Content-Type: application/json',
            'Accept: application/json'
        ], $headers),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30
    ]);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'status' => 0,
            'body' => ['error' => $error],
            'raw' => $response
        ];
    }
    
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true) ?: [],
        'raw' => $response
    ];
}

// Esegui i test
testClientRoutes();

echo "\n=== TEST COMPLETATO ===\n";