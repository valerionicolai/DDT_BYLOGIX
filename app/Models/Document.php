<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Document extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Document $document) {
            // Delete associated file when document is deleted
            $document->deleteFile();
        });
    }

    protected $fillable = [
        'title',
        'description',
        'document_type_id',
        'document_category_id',
        'client_id',
        'file_path',
        'status',
        'barcode',
        'metadata',
        'created_date',
        'due_date'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_date' => 'datetime',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'created_date',
        'due_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the document type that owns the document
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the document category that owns the document
     */
    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }
    
    /**
     * Get the client that is associated with the document as supplier
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Generate a unique barcode for the document
     */
    public function generateBarcode(): string
    {
        // Generate a barcode based on document ID and timestamp
        $prefix = strtoupper(substr($this->documentCategory?->code ?? 'DOC', 0, 1));
        $id = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return $prefix . $id;
    }

    /**
     * Regenerate barcode for the document
     */
    public function regenerateBarcode(): bool
    {
        $this->barcode = $this->generateBarcode();
        return $this->save();
    }



    /**
     * Get the full file URL
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Check if file exists
     */
    public function getFileExistsAttribute(): bool
    {
        if (!$this->file_path) {
            return false;
        }

        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return pathinfo($this->file_path, PATHINFO_EXTENSION);
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute(): bool
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        return in_array(strtolower($this->file_extension ?? ''), $imageTypes);
    }

    /**
     * Check if file is a PDF
     */
    public function getIsPdfAttribute(): bool
    {
        return strtolower($this->file_extension ?? '') === 'pdf';
    }

    /**
     * Delete the associated file
     */
    public function deleteFile(): bool
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->delete($this->file_path);
        }
        
        return true;
    }

    /**
     * Update file information from uploaded file
     */
    public function updateFileInfo(string $filePath): void
    {
        $this->file_path = $filePath;
    }

    /**
     * Check if document is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'archived';
    }

    /**
     * Scope for searching documents by barcode
     */
    public function scopeByBarcode($query, $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }



    /**
     * Scope for searching documents
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhereHas('documentCategory', function ($query) use ($search) {
                  $query->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('client', function ($query) use ($search) {
                  $query->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%");
              });
        });
    }
}