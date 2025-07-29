<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test document creation with required fields
     */
    public function test_document_can_be_created_with_required_fields()
    {
        $document = Document::create([
            'title' => 'Test Document',
            'category' => 'Contract',
            'status' => 'draft'
        ]);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertEquals('Test Document', $document->title);
        $this->assertEquals('Contract', $document->category);
        $this->assertEquals('draft', $document->status);
        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'category' => 'Contract'
        ]);
    }

    /**
     * Test barcode generation
     */
    public function test_barcode_generation()
    {
        $document = Document::create([
            'title' => 'Test Document',
            'category' => 'Contract',
            'status' => 'draft'
        ]);

        $barcode = $document->generateBarcode();
        
        $this->assertNotEmpty($barcode);
        $this->assertStringStartsWith('C', $barcode); // Contract starts with C
        $this->assertStringContainsString(str_pad($document->id, 4, '0', STR_PAD_LEFT), $barcode);
    }

    /**
     * Test barcode regeneration
     */
    public function test_barcode_regeneration()
    {
        $document = Document::create([
            'title' => 'Test Document',
            'category' => 'Invoice',
            'status' => 'draft'
        ]);

        $originalBarcode = $document->barcode;
        $result = $document->regenerateBarcode();
        
        $this->assertTrue($result);
        $this->assertNotEquals($originalBarcode, $document->barcode);
        $this->assertStringStartsWith('I', $document->barcode); // Invoice starts with I
    }

    /**
     * Test file size formatting
     */
    public function test_file_size_formatting()
    {
        $document = Document::create([
            'title' => 'Test Document',
            'category' => 'Contract',
            'file_size' => 1024
        ]);

        $this->assertEquals('1 KB', $document->formatted_file_size);

        $document->file_size = 1048576; // 1MB
        $this->assertEquals('1 MB', $document->formatted_file_size);

        $document->file_size = null;
        $this->assertEquals('N/A', $document->formatted_file_size);
    }

    /**
     * Test overdue document detection
     */
    public function test_overdue_document_detection()
    {
        // Document with past due date
        $overdueDocument = Document::create([
            'title' => 'Overdue Document',
            'category' => 'Contract',
            'status' => 'active',
            'due_date' => Carbon::yesterday()
        ]);

        $this->assertTrue($overdueDocument->is_overdue);

        // Document with future due date
        $futureDocument = Document::create([
            'title' => 'Future Document',
            'category' => 'Contract',
            'status' => 'active',
            'due_date' => Carbon::tomorrow()
        ]);

        $this->assertFalse($futureDocument->is_overdue);

        // Archived document should not be overdue
        $archivedDocument = Document::create([
            'title' => 'Archived Document',
            'category' => 'Contract',
            'status' => 'archived',
            'due_date' => Carbon::yesterday()
        ]);

        $this->assertFalse($archivedDocument->is_overdue);
    }

    /**
     * Test barcode search scope
     */
    public function test_barcode_search_scope()
    {
        $document1 = Document::create([
            'title' => 'Document 1',
            'category' => 'Contract',
            'barcode' => 'C0001'
        ]);

        $document2 = Document::create([
            'title' => 'Document 2',
            'category' => 'Invoice',
            'barcode' => 'I0002'
        ]);

        $results = Document::byBarcode('C0001')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($document1->id, $results->first()->id);
    }

    /**
     * Test status filter scope
     */
    public function test_status_filter_scope()
    {
        Document::create([
            'title' => 'Draft Document',
            'category' => 'Contract',
            'status' => 'draft'
        ]);

        Document::create([
            'title' => 'Active Document',
            'category' => 'Contract',
            'status' => 'active'
        ]);

        $draftDocuments = Document::byStatus('draft')->get();
        $activeDocuments = Document::byStatus('active')->get();

        $this->assertCount(1, $draftDocuments);
        $this->assertCount(1, $activeDocuments);
        $this->assertEquals('Draft Document', $draftDocuments->first()->title);
        $this->assertEquals('Active Document', $activeDocuments->first()->title);
    }

    /**
     * Test category filter scope
     */
    public function test_category_filter_scope()
    {
        Document::create([
            'title' => 'Contract Document',
            'category' => 'Contract'
        ]);

        Document::create([
            'title' => 'Invoice Document',
            'category' => 'Invoice'
        ]);

        $contractDocuments = Document::byCategory('Contract')->get();
        $invoiceDocuments = Document::byCategory('Invoice')->get();

        $this->assertCount(1, $contractDocuments);
        $this->assertCount(1, $invoiceDocuments);
        $this->assertEquals('Contract Document', $contractDocuments->first()->title);
        $this->assertEquals('Invoice Document', $invoiceDocuments->first()->title);
    }

    /**
     * Test search scope
     */
    public function test_search_scope()
    {
        Document::create([
            'title' => 'Important Contract',
            'description' => 'This is a very important document',
            'category' => 'Contract',
            'supplier' => 'ABC Company',
            'barcode' => 'C0001'
        ]);

        Document::create([
            'title' => 'Regular Invoice',
            'description' => 'Standard invoice',
            'category' => 'Invoice',
            'supplier' => 'XYZ Corp',
            'barcode' => 'I0002'
        ]);

        // Search by title
        $titleResults = Document::search('Important')->get();
        $this->assertCount(1, $titleResults);
        $this->assertEquals('Important Contract', $titleResults->first()->title);

        // Search by description
        $descResults = Document::search('very important')->get();
        $this->assertCount(1, $descResults);

        // Search by supplier
        $supplierResults = Document::search('ABC')->get();
        $this->assertCount(1, $supplierResults);

        // Search by barcode
        $barcodeResults = Document::search('C0001')->get();
        $this->assertCount(1, $barcodeResults);

        // Search with no results
        $noResults = Document::search('nonexistent')->get();
        $this->assertCount(0, $noResults);
    }

    /**
     * Test metadata casting
     */
    public function test_metadata_casting()
    {
        $metadata = [
            'version' => '1.0',
            'priority' => 'high',
            'tags' => ['urgent', 'important']
        ];

        $document = Document::create([
            'title' => 'Test Document',
            'category' => 'Contract',
            'metadata' => $metadata
        ]);

        $this->assertIsArray($document->metadata);
        $this->assertEquals($metadata, $document->metadata);
        $this->assertEquals('high', $document->metadata['priority']);
        $this->assertContains('urgent', $document->metadata['tags']);
    }

    /** @test */
    public function date_casting()
    {
        $createdDate = Carbon::parse('2024-01-15');
        $dueDate = Carbon::parse('2024-02-15');
        
        $document = Document::factory()->create([
            'created_date' => $createdDate,
            'due_date' => $dueDate
        ]);

        $this->assertInstanceOf(Carbon::class, $document->created_date);
        $this->assertInstanceOf(Carbon::class, $document->due_date);
        $this->assertTrue($createdDate->isSameDay($document->created_date));
        $this->assertTrue($dueDate->isSameDay($document->due_date));
    }

    /**
     * Test soft deletes
     */
    public function test_soft_deletes()
    {
        $document = Document::create([
            'title' => 'Test Document',
            'category' => 'Contract'
        ]);

        $documentId = $document->id;

        // Delete the document
        $document->delete();

        // Should not be found in normal queries
        $this->assertNull(Document::find($documentId));

        // Should be found with trashed
        $this->assertNotNull(Document::withTrashed()->find($documentId));

        // Should be in trashed only
        $trashedDocuments = Document::onlyTrashed()->get();
        $this->assertCount(1, $trashedDocuments);
        $this->assertEquals($documentId, $trashedDocuments->first()->id);
    }
}