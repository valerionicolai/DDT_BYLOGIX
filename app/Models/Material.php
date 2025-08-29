<?php

namespace App\Models;

use App\Enums\MaterialState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;

class Material extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\MaterialFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'document_id',
        'material_type_id',
        'description',
        'state',
        'due_date',
        'barcode',
        'notes',
        'quantity',
        'location',
        'metadata',
    ];

    protected $casts = [
        'state' => MaterialState::class,
        'due_date' => 'date',
        'quantity' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($material) {
            if (empty($material->barcode)) {
                $material->barcode = app(\App\Services\BarcodeService::class)->generateMaterialBarcode($material->id ?? 0);
            }
        });

        static::created(function ($material) {
            // Aggiorna il barcode con l'ID reale dopo la creazione
            if ($material->barcode === app(\App\Services\BarcodeService::class)->generateMaterialBarcode(0)) {
                $material->barcode = app(\App\Services\BarcodeService::class)->generateMaterialBarcode($material->id);
                $material->saveQuietly();
            }
        });
    }

    // ==================== MEDIA ====================
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->acceptsFile(function ($file) {
                return in_array($file->mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
            })
            ->useFallbackUrl('/images/placeholder-image.svg')
            ->useFallbackPath(public_path('images/placeholder-image.svg'));

        $this->addMediaCollection('videos')
            ->useDisk('public')
            ->acceptsFile(function ($file) {
                return str_starts_with($file->mimeType, 'video/');
            });
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the document that owns the material
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the material type
     */
    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope materials by state
     */
    public function scopeByState(Builder $query, MaterialState $state): Builder
    {
        return $query->where('state', $state);
    }

    /**
     * Scope materials by barcode
     */
    public function scopeByBarcode(Builder $query, string $barcode): Builder
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Scope overdue materials
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now()->toDateString());
    }

    /**
     * Scope materials due soon (within 7 days)
     */
    public function scopeDueSoon(Builder $query, int $days = 7): Builder
    {
        return $query->whereBetween('due_date', [
            now()->toDateString(),
            now()->addDays($days)->toDateString()
        ]);
    }

    /**
     * Scope urgent materials (da restituire)
     */
    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('state', MaterialState::DA_RESTITUIRE);
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Check if material is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now()->toDate();
    }

    /**
     * Get days until due date
     */
    public function getDaysUntilDueAttribute(): int
    {
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Get formatted quantity with unit
     */
    public function getFormattedQuantityAttribute(): string
    {
        $unit = $this->materialType->unit_of_measure ?? 'pz';
        return "{$this->quantity} {$unit}";
    }

    /**
     * Get full description including material type
     */
    public function getFullDescriptionAttribute(): string
    {
        $typeName = $this->materialType->name ?? 'N/A';
        return "{$typeName}: {$this->description}";
    }

    // ==================== METHODS ====================

    /**
     * Generate unique barcode for material
     */
    public function generateBarcode(): string
    {
        return app(\App\Services\BarcodeService::class)->generateMaterialBarcode($this->id);
    }

    /**
     * Regenerate barcode
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
     * Transition to new state
     */
    public function transitionTo(MaterialState $newState, ?string $notes = null): bool
    {
        $this->state = $newState;
        
        if ($notes) {
            $this->notes = $this->notes ? $this->notes . "\n" . $notes : $notes;
        }

        return $this->save();
    }

    /**
     * Check if material can be transitioned to given state
     */
    public function canTransitionTo(MaterialState $newState): bool
    {
        // Business logic for state transitions
        return match($this->state) {
            MaterialState::DA_CONSERVARE => in_array($newState, [
                MaterialState::DA_TRATTENERE, 
                MaterialState::DA_RESTITUIRE
            ]),
            MaterialState::DA_TRATTENERE => in_array($newState, [
                MaterialState::DA_CONSERVARE, 
                MaterialState::DA_RESTITUIRE
            ]),
            MaterialState::DA_RESTITUIRE => in_array($newState, [
                MaterialState::DA_CONSERVARE, 
                MaterialState::DA_TRATTENERE
            ]),
        };
    }

    /**
     * Get barcode URL for authenticated access
     */
    public function getBarcodeUrlAttribute(): string
    {
        return route('admin.materials.show', ['material' => $this->id]);
    }
}
