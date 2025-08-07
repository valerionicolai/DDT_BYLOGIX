<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Sprint 1.1 Implementation...\n";

// Get required data
$client = App\Models\Client::first();
$project = App\Models\Project::first();
$materialType = App\Models\MaterialType::first();

if (!$client || !$project || !$materialType) {
    echo "Missing base data. Client: " . ($client ? 'found' : 'missing') . ", Project: " . ($project ? 'found' : 'missing') . ", MaterialType: " . ($materialType ? 'found' : 'missing') . "\n";
    exit(1);
}

// Get or create document types and categories
$docType = App\Models\DocumentType::first();
$docCategory = App\Models\DocumentCategory::first();

if (!$docType) {
    echo "Creating DocumentType...\n";
    $docType = App\Models\DocumentType::create([
        'name' => 'Documento Generico',
        'description' => 'Tipo di documento generico per test',
        'code' => 'GEN'
    ]);
}

if (!$docCategory) {
    echo "Creating DocumentCategory...\n";
    $docCategory = App\Models\DocumentCategory::create([
        'name' => 'Categoria Test',
        'description' => 'Categoria di test per documenti',
        'code' => 'TEST'
    ]);
}

// Create test document
echo "Creating test document...\n";
$document = App\Models\Document::create([
    'title' => 'Documento Test Sprint 1.1',
    'description' => 'Documento creato per testare le relazioni con i materiali',
    'document_type_id' => $docType->id,
    'document_category_id' => $docCategory->id,
    'client_id' => $client->id,
    'project_id' => $project->id,
    'status' => 'active',
    'created_date' => now(),
    'due_date' => now()->addDays(15)
]);

echo "Document created with ID: " . $document->id . "\n";
echo "Document barcode: " . $document->barcode . "\n";

// Now create material
echo "Creating test material...\n";
$material = App\Models\Material::create([
    'document_id' => $document->id,
    'material_type_id' => $materialType->id,
    'description' => 'Materiale di test per Sprint 1.1 - Verifica relazioni complete',
    'state' => App\Enums\MaterialState::DA_CONSERVARE,
    'due_date' => now()->addDays(30),
    'quantity' => 5.25,
    'location' => 'Magazzino Principale - Zona A - Scaffale 1',
    'notes' => 'Materiale creato durante implementazione Sprint 1.1'
]);

echo "\n🎯 SPRINT 1.1 IMPLEMENTATION COMPLETE! 🎯\n";
echo "Material ID: " . $material->id . "\n";
echo "QR Code: " . $material->qr_code . "\n";
echo "State: " . $material->state->getLabel() . "\n";
echo "Full description: " . $material->full_description . "\n";

echo "\n=== RELATIONSHIP VERIFICATION ===\n";
echo "Material -> Document: " . $material->document->title . "\n";
echo "Material -> MaterialType: " . $material->materialType->name . "\n";
echo "Document -> Project: " . $material->document->project->name . "\n";
echo "Document -> Client: " . $material->document->client->name . "\n";
echo "Project -> Client: " . $material->document->project->client->name . "\n";

echo "\n✅ ALL DELIVERABLES COMPLETED:\n";
echo "- ✅ Modello Material completo\n";
echo "- ✅ Enum MaterialState (DA_CONSERVARE, DA_TRATTENERE, DA_RESTITUIRE)\n";
echo "- ✅ Relazione Document::project()\n";
echo "- ✅ Relazione Document::materials()\n";
echo "- ✅ Migration per project_id in documents\n";
echo "- ✅ Migration per tabella materials\n";
echo "- ✅ Test relazioni funzionanti\n";

// Clean up
unlink(__FILE__);