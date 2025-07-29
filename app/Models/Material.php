<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entry_document_id',
        'material_type_id',
        'description',
        'quantity',
        'unit_of_measure',
        'unit_price',
        'total_price',
        'vat_rate',
        'vat_amount',
        'lot_number',
        'expiry_date',
        'location',
        'status',
        'properties',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'expiry_date' => 'date',
        'properties' => 'array',
    ];

    /**
     * Get the entry document that owns the material.
     */
    public function entryDocument(): BelongsTo
    {
        return $this->belongsTo(EntryDocument::class);
    }

    /**
     * Get the material type that owns the material.
     */
    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }

    /**
     * Scope a query to only include materials by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by material type.
     */
    public function scopeByMaterialType($query, int $materialTypeId)
    {
        return $query->where('material_type_id', $materialTypeId);
    }

    /**
     * Scope a query to filter by location.
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope a query to include expired materials.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                    ->whereNotNull('expiry_date');
    }

    /**
     * Scope a query to include materials expiring soon.
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now())
                    ->whereNotNull('expiry_date');
    }

    /**
     * Calculate and set the total price and VAT amount.
     */
    public function calculateTotals(): void
    {
        $totalPrice = $this->quantity * $this->unit_price;
        $vatAmount = ($totalPrice * $this->vat_rate) / 100;
        
        $this->update([
            'total_price' => $totalPrice,
            'vat_amount' => $vatAmount,
        ]);
    }

    /**
     * Get formatted total price with currency.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '€ ' . number_format($this->total_price, 2, ',', '.');
    }

    /**
     * Get formatted unit price with currency.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '€ ' . number_format($this->unit_price, 2, ',', '.');
    }

    /**
     * Get the material status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'ordered' => 'yellow',
            'received' => 'green',
            'used' => 'blue',
            'returned' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if the material is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if the material is expiring soon.
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= 30;
    }

    /**
     * Get the full material description with type.
     */
    public function getFullDescriptionAttribute(): string
    {
        $parts = array_filter([
            $this->materialType?->name,
            $this->description,
            $this->lot_number ? "(Lotto: {$this->lot_number})" : null,
        ]);

        return implode(' - ', $parts);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically calculate totals when creating or updating
        static::saving(function ($material) {
            if ($material->isDirty(['quantity', 'unit_price', 'vat_rate'])) {
                $totalPrice = $material->quantity * $material->unit_price;
                $vatAmount = ($totalPrice * $material->vat_rate) / 100;
                
                $material->total_price = $totalPrice;
                $material->vat_amount = $vatAmount;
            }
        });

        // Update entry document totals when material is saved or deleted
        static::saved(function ($material) {
            $material->entryDocument->calculateTotals();
        });

        static::deleted(function ($material) {
            $material->entryDocument->calculateTotals();
        });
    }
}
