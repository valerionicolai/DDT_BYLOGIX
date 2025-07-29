<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Barcode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'type',
        'format',
        'data',
        'barcodeable_type',
        'barcodeable_id',
        'is_active',
        'generated_at',
        'expires_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Supported barcode types.
     */
    const TYPES = [
        'CODE128' => 'CODE128',
        'CODE39' => 'CODE39',
        'EAN13' => 'EAN13',
        'EAN8' => 'EAN8',
        'QR' => 'QR',
        'DATAMATRIX' => 'DATAMATRIX',
    ];

    /**
     * Supported image formats.
     */
    const FORMATS = [
        'png' => 'png',
        'svg' => 'svg',
        'jpg' => 'jpg',
        'gif' => 'gif',
    ];

    /**
     * Get the parent barcodeable model (polymorphic relation).
     */
    public function barcodeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include active barcodes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include non-expired barcodes.
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope a query to filter by barcode type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if the barcode is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the barcode is valid (active and not expired).
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Generate a unique barcode code.
     */
    public static function generateUniqueCode(string $prefix = 'BC'): string
    {
        do {
            $code = $prefix . '-' . strtoupper(Str::random(8)) . '-' . now()->format('ymd');
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Create a barcode for a given model.
     */
    public static function createForModel(
        Model $model,
        string $type = 'CODE128',
        string $format = 'png',
        array $metadata = []
    ): self {
        $code = static::generateUniqueCode();
        
        return static::create([
            'code' => $code,
            'type' => $type,
            'format' => $format,
            'data' => $code, // Default data is the code itself
            'barcodeable_type' => get_class($model),
            'barcodeable_id' => $model->id,
            'is_active' => true,
            'generated_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Deactivate the barcode.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Activate the barcode.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Get the barcode image URL or path.
     */
    public function getImagePath(): string
    {
        return "barcodes/{$this->id}.{$this->format}";
    }

    /**
     * Get barcode display name.
     */
    public function getDisplayName(): string
    {
        return "{$this->type} - {$this->code}";
    }
}
