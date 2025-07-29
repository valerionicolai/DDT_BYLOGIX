<?php

/**
 * Test script for S2-B03 and S2-B04 endpoints
 * Tests GET /api/v1/documents (with filters), GET /api/v1/documents/{id}, PUT, DELETE
 */

$baseUrl = 'http://127.0.0.1:8000/api';
$authToken = '';

function makeRequest($method, $url, $data = null, $token = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        $token ? "Authorization: Bearer $token" : ''
    ]);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

function testEndpoint($name, $method, $url, $data = null, $expectedStatus = 200) {
    global $authToken;
    
    echo "\n🧪 Testing: $name\n";
    echo "   Method: $method\n";
    echo "   URL: $url\n";
    
    $result = makeRequest($method, $url, $data, $authToken);
    
    if ($result['status'] === $expectedStatus) {
        echo "   ✅ Status: {$result['status']} (Expected: $expectedStatus)\n";
        if (isset($result['data']['success']) && $result['data']['success']) {
            echo "   ✅ Response: Success\n";
        } else {
            echo "   ⚠️  Response: " . json_encode($result['data']) . "\n";
        }
    } else {
        echo "   ❌ Status: {$result['status']} (Expected: $expectedStatus)\n";
        echo "   ❌ Response: " . json_encode($result['data']) . "\n";
    }
    
    return $result;
}

echo "🚀 Starting S2-B03 and S2-B04 Endpoint Tests\n";
echo "=" . str_repeat("=", 50) . "\n";

// Step 1: Login
echo "\n📝 Step 1: Authentication\n";
$loginResult = testEndpoint(
    'Login',
    'POST',
    "$baseUrl/auth/login",
    ['email' => 'admin@example.com', 'password' => 'password']
);

if ($loginResult['status'] === 200 && isset($loginResult['data']['data']['token'])) {
    $authToken = $loginResult['data']['data']['token'];
    echo "   🔑 Auth token obtained successfully\n";
} else {
    echo "   ❌ Failed to obtain auth token. Exiting.\n";
    exit(1);
}

// Step 2: Create a test document for testing
echo "\n📝 Step 2: Create Test Document\n";
$testDocument = [
    'document_type' => 'entry',
    'supplier_name' => 'Test Supplier for S2-B03/B04',
    'supplier_vat' => 'IT12345678901',
    'document_date' => date('Y-m-d'),
    'notes' => 'Test document for S2-B03 and S2-B04 endpoints',
    'materials' => [
        [
            'material_type_id' => 1,
            'description' => 'Test Material for S2-B03/B04',
            'quantity' => 5,
            'unit_of_measure' => 'pz',
            'unit_price' => 10.50,
            'vat_rate' => 22.00,
            'status' => 'received'
        ]
    ]
];

$createResult = testEndpoint(
    'Create Test Document',
    'POST',
    "$baseUrl/documents",
    $testDocument,
    201
);

$documentId = null;
if ($createResult['status'] === 201 && isset($createResult['data']['data']['id'])) {
    $documentId = $createResult['data']['data']['id'];
    echo "   📄 Test document created with ID: $documentId\n";
} else {
    echo "   ❌ Failed to create test document. Exiting.\n";
    exit(1);
}

// Step 3: S2-B03 Tests - GET /api/v1/documents (lista con paginazione e filtri)
echo "\n📝 Step 3: S2-B03 - GET Documents with Filters\n";

// Test 3.1: Get all documents
testEndpoint(
    'Get All Documents',
    'GET',
    "$baseUrl/documents"
);

// Test 3.2: Get documents with status filter
testEndpoint(
    'Get Documents by Status (draft)',
    'GET',
    "$baseUrl/documents?status=draft"
);

// Test 3.3: Get documents with type filter
testEndpoint(
    'Get Documents by Type (entry)',
    'GET',
    "$baseUrl/documents?document_type=entry"
);

// Test 3.4: Get documents with supplier filter
testEndpoint(
    'Get Documents by Supplier',
    'GET',
    "$baseUrl/documents?supplier=Test Supplier"
);

// Test 3.5: Get documents with pagination
testEndpoint(
    'Get Documents with Pagination (5 per page)',
    'GET',
    "$baseUrl/documents?per_page=5"
);

// Test 3.6: Get documents with date range
$startDate = date('Y-m-d', strtotime('-7 days'));
$endDate = date('Y-m-d');
testEndpoint(
    'Get Documents by Date Range',
    'GET',
    "$baseUrl/documents?start_date=$startDate&end_date=$endDate"
);

// Test 3.7: Get documents with multiple filters
testEndpoint(
    'Get Documents with Multiple Filters',
    'GET',
    "$baseUrl/documents?status=draft&document_type=entry&per_page=10"
);

// Step 4: S2-B03 Tests - GET /api/v1/documents/{id} (dettaglio)
echo "\n📝 Step 4: S2-B03 - GET Document Detail\n";

// Test 4.1: Get specific document detail
testEndpoint(
    'Get Document Detail',
    'GET',
    "$baseUrl/documents/$documentId"
);

// Test 4.2: Get document materials
testEndpoint(
    'Get Document Materials',
    'GET',
    "$baseUrl/documents/$documentId/materials"
);

// Test 4.3: Get non-existent document (should return 404)
testEndpoint(
    'Get Non-existent Document',
    'GET',
    "$baseUrl/documents/99999",
    null,
    404
);

// Step 5: S2-B04 Tests - PUT /api/v1/documents/{id} (modifica)
echo "\n📝 Step 5: S2-B04 - PUT Document Update\n";

// Test 5.1: Update document
$updateData = [
    'supplier_name' => 'Updated Supplier Name',
    'notes' => 'Updated notes for S2-B04 test',
    'status' => 'confirmed'
];

testEndpoint(
    'Update Document',
    'PUT',
    "$baseUrl/documents/$documentId",
    $updateData
);

// Test 5.2: Update with invalid data (should return 422)
$invalidUpdateData = [
    'status' => 'invalid_status'
];

testEndpoint(
    'Update Document with Invalid Data',
    'PUT',
    "$baseUrl/documents/$documentId",
    $invalidUpdateData,
    422
);

// Test 5.3: Update non-existent document (should return 404)
testEndpoint(
    'Update Non-existent Document',
    'PUT',
    "$baseUrl/documents/99999",
    $updateData,
    404
);

// Step 6: Additional Tests
echo "\n📝 Step 6: Additional Tests\n";

// Test 6.1: Get document statistics
testEndpoint(
    'Get Document Statistics',
    'GET',
    "$baseUrl/documents/statistics"
);

// Test 6.2: Verify updated document
testEndpoint(
    'Verify Updated Document',
    'GET',
    "$baseUrl/documents/$documentId"
);

// Step 7: S2-B04 Tests - DELETE /api/v1/documents/{id} (cancellazione)
echo "\n📝 Step 7: S2-B04 - DELETE Document\n";

// Test 7.1: Delete document
testEndpoint(
    'Delete Document',
    'DELETE',
    "$baseUrl/documents/$documentId"
);

// Test 7.2: Verify document is deleted (should return 404)
testEndpoint(
    'Verify Document Deleted',
    'GET',
    "$baseUrl/documents/$documentId",
    null,
    404
);

// Test 7.3: Delete non-existent document (should return 404)
testEndpoint(
    'Delete Non-existent Document',
    'DELETE',
    "$baseUrl/documents/99999",
    null,
    404
);

echo "\n🎉 All S2-B03 and S2-B04 endpoint tests completed!\n";
echo "=" . str_repeat("=", 50) . "\n";

?>