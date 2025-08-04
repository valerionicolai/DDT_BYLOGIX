<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the projects managed by the user.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Check if the user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->hasRoleColumn('admin');
    }

    /**
     * Check if the user has user role.
     */
    public function isUser(): bool
    {
        return $this->hasRoleColumn('user');
    }

    /**
     * Check if the user has a specific role (using role column).
     */
    public function hasRoleColumn(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has any of the given roles (using role column).
     */
    public function hasAnyRoleColumn(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Scope to filter users by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to filter admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope to filter regular users.
     */
    public function scopeUsers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Get panel ID safely - handle case where panel might not have an ID set
        try {
            $panelId = $panel->getId();
        } catch (\Exception $e) {
            // If panel doesn't have an ID, treat as admin panel for backward compatibility
            $panelId = 'admin';
        }
        
        // Allow access to admin panel for users with admin role or admin permission
        if ($panelId === 'admin' || $panelId === null || empty($panelId)) {
            return $this->hasRoleColumn('admin') || $this->can('access_admin_panel');
        }

        return true;
    }

    /**
     * Check if user can access admin panel.
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->hasRoleColumn('admin') || $this->can('access_admin_panel');
    }

    /**
     * Get user's display name for Filament.
     */
    public function getFilamentName(): string
    {
        return $this->name;
    }

    /**
     * Get user's avatar URL for Filament.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return null; // Can be implemented later with avatar upload
    }
}
