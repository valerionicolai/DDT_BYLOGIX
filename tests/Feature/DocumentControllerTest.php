<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user and authenticate
        $this->user = User::factory()->create([
            'role' => 'admin'
        ]);
        
        Sanctum::actingAs($this->user);
    }

    /**
     * Test document listing with pagination
     */
    public function test_can_list_documents_with_pagination()
    {
        // Create test documents
        Document::factory()->count(25)->create();

        $response = $this->getJson('/api/documents?per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data',
                        'current_page',
                        'per_page',
                        'total'
                    ]
                ]);

        $data = $response->json('data');
        $this->assertCount(10, $data['data']);
        $this->assertEquals(25, $data['total']);
    }

    /**
     * Test document creation
     */
    public function test_can_create_document()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');

        $documentData = [
            'title' => 'Test Document',
            'description' => 'Test Description',
            'category' => 'Contract',
            'supplier' => 'Test Supplier',
            'status' => 'draft',
            'file' => $file
        ];

        $response = $this->postJson('/api/documents', $documentData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'title',
                        'category',
                        'barcode',
                        'file_path'
                    ]
                ]);

        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'category' => 'Contract',
            'supplier' => 'Test Supplier'
        ]);

        // Check if barcode was generated
        $document = Document::where('title', 'Test Document')->first();
        $this->assertNotNull($document->barcode);
        $this->assertStringStartsWith('C', $document->barcode); // Contract starts with C

        // Check if file was stored
        $this->assertTrue(Storage::disk('public')->exists($document->file_path));
    }

    /**
     * Test document creation validation
     */
    public function test_document_creation_validation()
    {
        $response = $this->postJson('/api/documents', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'category']);
    }

    /**
     * Test document retrieval
     */
    public function test_can_retrieve_document()
    {
        $document = Document::factory()->create([
            'title' => 'Test Document',
            'category' => 'Contract'
        ]);

        $response = $this->getJson("/api/documents/{$document->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'title',
                        'category',
                        'barcode'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $document->id,
                        'title' => 'Test Document',
                        'category' => 'Contract'
                    ]
                ]);
    }

    /**
     * Test document update
     */
    public function test_can_update_document()
    {
        Storage::fake('public');

        $document = Document::factory()->create([
            'title' => 'Original Title',
            'category' => 'Contract'
        ]);

        $newFile = UploadedFile::fake()->create('updated.pdf', 1500, 'application/pdf');

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'file' => $newFile
        ];

        $response = $this->putJson("/api/documents/{$document->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description'
        ]);

        // Check if new file was stored
        $updatedDocument = Document::find($document->id);
        $this->assertTrue(Storage::disk('public')->exists($updatedDocument->file_path));
    }

    /**
     * Test document deletion
     */
    public function test_can_delete_document()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');
        $filePath = $file->storeAs('documents', 'test.pdf', 'public');

        $document = Document::factory()->create([
            'file_path' => $filePath
        ]);

        Storage::disk('public')->put($filePath, 'test content');

        $response = $this->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Document deleted successfully'
                ]);

        $this->assertSoftDeleted('documents', [
            'id' => $document->id
        ]);

        // Check if file was deleted
        $this->assertFalse(Storage::disk('public')->exists($filePath));
    }

    /**
     * Test barcode search
     */
    public function test_can_search_by_barcode()
    {
        $document1 = Document::factory()->create([
            'title' => 'Document 1',
            'barcode' => 'C0001'
        ]);

        $document2 = Document::factory()->create([
            'title' => 'Document 2',
            'barcode' => 'I0002'
        ]);

        $response = $this->getJson('/api/documents/search?barcode=C0001');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data'
                ])
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $document1->id);
    }

    /**
     * Test barcode regeneration
     */
    public function test_can_regenerate_barcode()
    {
        $document = Document::factory()->create([
            'title' => 'Test Document',
            'category' => 'Contract',
            'barcode' => 'OLD_BARCODE'
        ]);

        $response = $this->postJson("/api/documents/{$document->id}/regenerate-barcode");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'barcode'
                    ]
                ]);

        $document->refresh();
        $this->assertNotEquals('OLD_BARCODE', $document->barcode);
        $this->assertStringStartsWith('C', $document->barcode);
    }

    /**
     * Test document filtering
     */
    public function test_can_filter_documents()
    {
        Document::factory()->create([
            'title' => 'Contract Document',
            'category' => 'Contract',
            'status' => 'active',
            'supplier' => 'ABC Company'
        ]);

        Document::factory()->create([
            'title' => 'Invoice Document',
            'category' => 'Invoice',
            'status' => 'draft',
            'supplier' => 'XYZ Corp'
        ]);

        // Filter by category
        $response = $this->getJson('/api/documents?category=Contract');
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data.data');

        // Filter by status
        $response = $this->getJson('/api/documents?status=active');
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data.data');

        // Filter by supplier
        $response = $this->getJson('/api/documents?supplier=ABC');
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data.data');

        // Search by title
        $response = $this->getJson('/api/documents?search=Contract');
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data.data');
    }

    /**
     * Test document sorting
     */
    public function test_can_sort_documents()
    {
        $document1 = Document::factory()->create([
            'title' => 'A Document',
            'created_at' => now()->subDays(2)
        ]);

        $document2 = Document::factory()->create([
            'title' => 'B Document',
            'created_at' => now()->subDay()
        ]);

        // Sort by title ascending
        $response = $this->getJson('/api/documents?sort_by=title&sort_order=asc');
        $response->assertStatus(200);
        
        $data = $response->json('data.data');
        $this->assertEquals('A Document', $data[0]['title']);
        $this->assertEquals('B Document', $data[1]['title']);

        // Sort by created_at descending (default)
        $response = $this->getJson('/api/documents?sort_by=created_at&sort_order=desc');
        $response->assertStatus(200);
        
        $data = $response->json('data.data');
        $this->assertEquals($document2->id, $data[0]['id']);
        $this->assertEquals($document1->id, $data[1]['id']);
    }

    /**
     * Test CSV export
     */
    public function test_can_export_csv()
    {
        Document::factory()->count(3)->create([
            'category' => 'Contract'
        ]);

        $response = $this->get('/api/documents/export/csv');

        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
                ->assertHeader('Content-Disposition');

        // For StreamedResponse, we need to get content differently
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        
        $this->assertStringContainsString('"ID","Title","Description"', $content);
        $this->assertStringContainsString('Contract', $content);
    }

    /**
     * Test CSV export with filters
     */
    public function test_can_export_csv_with_filters()
    {
        $contractDoc = Document::factory()->create([
            'category' => 'Contract',
            'status' => 'active',
            'title' => 'Test Contract'
        ]);

        $invoiceDoc = Document::factory()->create([
            'category' => 'Invoice',
            'status' => 'draft',
            'title' => 'Test Invoice'
        ]);

        $response = $this->get('/api/documents/export/csv?category=Contract&status=active');

        $response->assertStatus(200);

        // For StreamedResponse, we need to get content differently
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        
        $lines = explode("\n", trim($content));
        
        // Should have header + 1 data row
        $this->assertCount(2, $lines);
        $this->assertStringContainsString('Test Contract', $content);
        $this->assertStringNotContainsString('Test Invoice', $content);
    }

    /**
     * Test file download
     */
    public function test_can_download_document_file()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');
        $filePath = $file->storeAs('documents', 'test.pdf', 'public');

        $document = Document::factory()->create([
            'file_path' => $filePath,
            'file_name' => 'test.pdf'
        ]);

        $response = $this->get("/api/documents/{$document->id}/download");

        $response->assertStatus(200)
                ->assertHeader('Content-Disposition');
    }

    /**
     * Test file download with missing file
     */
    public function test_download_missing_file_returns_404()
    {
        $document = Document::factory()->create([
            'file_path' => 'nonexistent/file.pdf'
        ]);

        $response = $this->get("/api/documents/{$document->id}/download");

        $response->assertStatus(404);
    }

    /**
     * Test unauthorized access
     */
    public function test_unauthorized_access_returns_401()
    {
        // Remove authentication
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/documents');

        $response->assertStatus(401);
    }
}