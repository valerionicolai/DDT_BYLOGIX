<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Document extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('audit')
            ->logOnly(['title','description','document_type_id','document_category_id','client_id','project_id','status','barcode','file_path','created_date','due_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Document $document) {
            // Delete associated file when document is deleted
            $document->deleteFile();
        });

        static::creating(function ($document) {
            if (empty($document->barcode)) {
                $document->barcode = app(\App\Services\BarcodeService::class)->generateDocumentBarcode($document->id ?? 0);
            }
        });

        static::created(function ($document) {
            // Aggiorna il barcode con l'ID reale dopo la creazione
            if ($document->barcode === app(\App\Services\BarcodeService::class)->generateDocumentBarcode(0)) {
                $document->barcode = app(\App\Services\BarcodeService::class)->generateDocumentBarcode($document->id);
                $document->saveQuietly();
            }
        });
    }

    protected $fillable = [
        'title',
        'description',
        'document_type_id',
        'document_category_id',
        'client_id',
        'project_id',
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
     * Get the project that owns the document
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the materials for the document
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Generate a unique barcode for the document
     */
    public function generateBarcode(): string
    {
        return app(\App\Services\BarcodeService::class)->generateDocumentBarcode($this->id);
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
     * Get barcode SVG
     */
    public function getBarcodeSVG(): string
    {
        return app(\App\Services\BarcodeService::class)->generateBarcodeSVG($this->barcode);
    }

    /**
     * Get barcode PNG (base64)
     */
    public function getBarcodePNG(): string
    {
        return app(\App\Services\BarcodeService::class)->generateBarcodePNG($this->barcode);
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