<?php

namespace App\Models;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'vat_number',
        'type',
        'address',
        'city',
        'postal_code',
        'country',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ClientStatus::class,
        'type' => ClientType::class,
    ];

    /**
     * Get the projects for the client.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
    
    /**
     * Get the documents supplied by this client.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the contacts for the client.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    /**
     * Get the primary contact for the client.
     */
    public function primaryContact(): HasMany
    {
        return $this->hasMany(ClientContact::class)->where('is_primary', true);
    }

    /**
     * Scope a query to only include active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('status', ClientStatus::ACTIVE);
    }

    /**
     * Get the client's full address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}
