<?php

// Test diretto del ClientController con Tinker

use App\Http\Controllers\Api\ClientController;
use App\Models\Client;
use Illuminate\Http\Request;

echo "=== TEST DIRETTO CLIENT CONTROLLER ===\n\n";

// 1. Test del metodo index
echo "1. Test metodo index()...\n";
$controller = new ClientController();
$request = new Request();

try {
    $response = $controller->index($request);
    $data = json_decode($response->getContent(), true);
    echo "✓ Index funziona correttamente\n";
    echo "Numero clients: " . count($data['data']) . "\n";
} catch (Exception $e) {
    echo "✗ Errore nel metodo index: " . $e->getMessage() . "\n";
}
echo "\n";

// 2. Test del metodo stats
echo "2. Test metodo stats()...\n";
try {
    $response = $controller->stats();
    $data = json_decode($response->getContent(), true);
    echo "✓ Stats funziona correttamente\n";
    print_r($data['data']);
} catch (Exception $e) {
    echo "✗ Errore nel metodo stats: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Test del metodo show
echo "3. Test metodo show()...\n";
$firstClient = Client::first();
if ($firstClient) {
    try {
        $response = $controller->show($firstClient->id);
        $data = json_decode($response->getContent(), true);
        echo "✓ Show funziona correttamente\n";
        echo "Client: " . $data['data']['name'] . "\n";
    } catch (Exception $e) {
        echo "✗ Errore nel metodo show: " . $e->getMessage() . "\n";
    }
} else {
    echo "Nessun client trovato per il test\n";
}
echo "\n";

// 4. Test creazione client
echo "4. Test metodo store()...\n";
$request = new Request();
$request->merge([
    'name' => 'Test Client Tinker',
    'email' => 'test.tinker@example.com',
    'phone' => '+39 123 456789',
    'company' => 'Test Company Tinker',
    'address' => 'Via Test 123',
    'city' => 'Test City',
    'postal_code' => '12345',
    'country' => 'Italia',
    'notes' => 'Client di test creato con Tinker',
    'status' => 'active'
]);

try {
    $response = $controller->store($request);
    $data = json_decode($response->getContent(), true);
    echo "✓ Store funziona correttamente\n";
    echo "Client creato: " . $data['data']['name'] . " (ID: " . $data['data']['id'] . ")\n";
    $createdClientId = $data['data']['id'];
} catch (Exception $e) {
    echo "✗ Errore nel metodo store: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== TUTTI I TEST DIRETTI COMPLETATI ===\n";