<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EntryDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_number',
        'document_type',
        'supplier_name',
        'supplier_vat',
        'supplier_address',
        'document_date',
        'delivery_date',
        'total_amount',
        'vat_amount',
        'net_amount',
        'currency',
        'status',
        'notes',
        'metadata',
        'project_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'document_date' => 'date',
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the project that owns the entry document.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created the entry document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the materials for the entry document.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Get all barcodes for this document.
     */
    public function barcodes(): MorphMany
    {
        return $this->morphMany(Barcode::class, 'barcodeable');
    }

    /**
     * Get the active barcode for this document.
     */
    public function activeBarcode()
    {
        return $this->barcodes()->active()->first();
    }

    /**
     * Scope a query to only include documents by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by document type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope a query to filter by supplier.
     */
    public function scopeBySupplier($query, string $supplier)
    {
        return $query->where('supplier_name', 'like', "%{$supplier}%");
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('document_date', [$startDate, $endDate]);
    }

    /**
     * Calculate and update document totals based on materials.
     */
    public function calculateTotals(): void
    {
        $materialsTotal = $this->materials->sum('total_price');
        $materialsVat = $this->materials->sum('vat_amount');
        
        $this->update([
            'net_amount' => $materialsTotal,
            'vat_amount' => $materialsVat,
            'total_amount' => $materialsTotal + $materialsVat,
        ]);
    }

    /**
     * Get formatted total amount with currency.
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->total_amount, 2, ',', '.');
    }

    /**
     * Get the document status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'confirmed' => 'blue',
            'received' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if the document can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'confirmed']);
    }

    /**
     * Check if the document can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Generate a unique document number.
     */
    public static function generateDocumentNumber(): string
    {
        $year = now()->year;
        $lastDocument = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastDocument ? 
            (int) substr($lastDocument->document_number, -4) + 1 : 1;
        
        return sprintf('DOC-%d-%04d', $year, $nextNumber);
    }
}
