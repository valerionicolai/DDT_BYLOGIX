<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'unit_of_measure',
        'default_price',
        'category',
        'properties',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'default_price' => 'decimal:2',
        'properties' => 'array',
        'status' => 'string',
    ];

    /**
     * Get the materials for this material type.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Scope a query to only include active material types.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->default_price) {
            return '€ ' . number_format($this->default_price, 2, ',', '.');
        }
        
        return 'Prezzo non definito';
    }

    /**
     * Get the full material description.
     */
    public function getFullDescriptionAttribute(): string
    {
        $parts = array_filter([
            $this->name,
            $this->category ? "({$this->category})" : null,
            $this->unit_of_measure ? "- {$this->unit_of_measure}" : null,
        ]);

        return implode(' ', $parts);
    }
}
