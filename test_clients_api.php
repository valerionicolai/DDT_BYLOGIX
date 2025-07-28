<?php

// Script di test per le API dei clients

$baseUrl = 'http://127.0.0.1:8000/api';

// Funzione per fare richieste HTTP
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
        'Content-Type: application/json',
        'Accept: application/json'
    ], $headers));
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

echo "=== TEST API CLIENTS ===\n\n";

// 1. Login per ottenere il token
echo "1. Login per ottenere il token...\n";
$loginResponse = makeRequest($baseUrl . '/auth/login', 'POST', [
    'email' => 'admin@dttbylogix.com',
    'password' => 'password'
]);

if ($loginResponse['status'] !== 200) {
    echo "ERRORE: Login fallito\n";
    print_r($loginResponse);
    exit;
}

$token = $loginResponse['body']['data']['token'];
echo "✓ Login riuscito, token ottenuto\n\n";

$authHeaders = ['Authorization: Bearer ' . $token];

// 2. Test GET /api/clients (lista clients)
echo "2. Test GET /api/clients (lista clients)...\n";
$response = makeRequest($baseUrl . '/clients', 'GET', null, $authHeaders);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✓ Lista clients ottenuta con successo\n";
    echo "Numero clients: " . count($response['body']['data']) . "\n";
} else {
    echo "✗ Errore nella lista clients\n";
    print_r($response['body']);
}
echo "\n";

// 3. Test GET /api/clients/stats
echo "3. Test GET /api/clients/stats...\n";
$response = makeRequest($baseUrl . '/clients/stats', 'GET', null, $authHeaders);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✓ Statistiche clients ottenute con successo\n";
    print_r($response['body']['data']);
} else {
    echo "✗ Errore nelle statistiche clients\n";
    print_r($response['body']);
}
echo "\n";

// 4. Test POST /api/clients (crea nuovo client)
echo "4. Test POST /api/clients (crea nuovo client)...\n";
$newClient = [
    'name' => 'Test Client',
    'email' => 'test@example.com',
    'phone' => '+39 123 456789',
    'company' => 'Test Company',
    'address' => 'Via Test 123',
    'city' => 'Test City',
    'postal_code' => '12345',
    'country' => 'Italia',
    'notes' => 'Client di test creato via API',
    'status' => 'active'
];

$response = makeRequest($baseUrl . '/clients', 'POST', $newClient, $authHeaders);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 201) {
    echo "✓ Client creato con successo\n";
    $createdClientId = $response['body']['data']['id'];
    echo "ID client creato: " . $createdClientId . "\n";
} else {
    echo "✗ Errore nella creazione del client\n";
    print_r($response['body']);
    exit;
}
echo "\n";

// 5. Test GET /api/clients/{id} (mostra client specifico)
echo "5. Test GET /api/clients/{$createdClientId} (mostra client specifico)...\n";
$response = makeRequest($baseUrl . '/clients/' . $createdClientId, 'GET', null, $authHeaders);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✓ Client specifico ottenuto con successo\n";
    echo "Nome: " . $response['body']['data']['name'] . "\n";
} else {
    echo "✗ Errore nel recupero del client specifico\n";
    print_r($response['body']);
}
echo "\n";

// 6. Test PUT /api/clients/{id} (aggiorna client)
echo "6. Test PUT /api/clients/{$createdClientId} (aggiorna client)...\n";
$updateData = [
    'name' => 'Test Client Updated',
    'notes' => 'Client di test aggiornato via API'
];

$response = makeRequest($baseUrl . '/clients/' . $createdClientId, 'PUT', $updateData, $authHeaders);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✓ Client aggiornato con successo\n";
    echo "Nome aggiornato: " . $response['body']['data']['name'] . "\n";
} else {
    echo "✗ Errore nell'aggiornamento del client\n";
    print_r($response['body']);
}
echo "\n";

// 7. Test filtri e ricerca
echo "7. Test filtri e ricerca...\n";
$response = makeRequest($baseUrl . '/clients?search=Test&status=active&per_page=5', 'GET', null, $authHeaders);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✓ Ricerca e filtri funzionanti\n";
    echo "Risultati trovati: " . count($response['body']['data']) . "\n";
} else {
    echo "✗ Errore nella ricerca e filtri\n";
    print_r($response['body']);
}
echo "\n";

// 8. Test DELETE /api/clients/{id} (elimina client)
echo "8. Test DELETE /api/clients/{$createdClientId} (elimina client)...\n";
$response = makeRequest($baseUrl . '/clients/' . $createdClientId, 'DELETE', null, $authHeaders);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 200) {
    echo "✓ Client eliminato con successo\n";
} else {
    echo "✗ Errore nell'eliminazione del client\n";
    print_r($response['body']);
}
echo "\n";

// 9. Verifica che il client sia stato eliminato
echo "9. Verifica eliminazione client...\n";
$response = makeRequest($baseUrl . '/clients/' . $createdClientId, 'GET', null, $authHeaders);
echo "Status: " . $response['status'] . "\n";
if ($response['status'] === 404) {
    echo "✓ Client correttamente eliminato (404 Not Found)\n";
} else {
    echo "✗ Client non eliminato correttamente\n";
    print_r($response['body']);
}
echo "\n";

echo "=== TUTTI I TEST COMPLETATI ===\n";