<?php

namespace App\Enums;

enum ClientStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PROSPECT = 'prospect';
    case SUSPENDED = 'suspended';
    case ARCHIVED = 'archived';

    /**
     * Get all enum values as an array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get enum options for forms.
     */
    public static function options(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::INACTIVE->value => 'Inactive',
            self::PROSPECT->value => 'Prospect',
            self::SUSPENDED->value => 'Suspended',
            self::ARCHIVED->value => 'Archived',
        ];
    }

    /**
     * Get the human-readable label for the enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::PROSPECT => 'Prospect',
            self::SUSPENDED => 'Suspended',
            self::ARCHIVED => 'Archived',
        };
    }

    /**
     * Get the color for the enum value.
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'gray',
            self::PROSPECT => 'blue',
            self::SUSPENDED => 'warning',
            self::ARCHIVED => 'danger',
        };
    }

    /**
     * Get the icon for the enum value.
     */
    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-o-check-circle',
            self::INACTIVE => 'heroicon-o-minus-circle',
            self::PROSPECT => 'heroicon-o-eye',
            self::SUSPENDED => 'heroicon-o-pause-circle',
            self::ARCHIVED => 'heroicon-o-archive-box',
        };
    }

    /**
     * Check if the status is active.
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Check if the status allows business operations.
     */
    public function canDoBusinessWith(): bool
    {
        return in_array($this, [self::ACTIVE, self::PROSPECT]);
    }

    /**
     * Get statuses that can transition to this status.
     */
    public function getValidTransitionsFrom(): array
    {
        return match ($this) {
            self::ACTIVE => [self::PROSPECT, self::INACTIVE, self::SUSPENDED],
            self::INACTIVE => [self::ACTIVE, self::PROSPECT, self::SUSPENDED],
            self::PROSPECT => [self::ACTIVE, self::INACTIVE],
            self::SUSPENDED => [self::ACTIVE, self::INACTIVE, self::ARCHIVED],
            self::ARCHIVED => [], // Cannot transition from archived
        };
    }

    /**
     * Get statuses that this status can transition to.
     */
    public function getValidTransitionsTo(): array
    {
        return match ($this) {
            self::ACTIVE => [self::INACTIVE, self::SUSPENDED, self::ARCHIVED],
            self::INACTIVE => [self::ACTIVE, self::PROSPECT, self::SUSPENDED, self::ARCHIVED],
            self::PROSPECT => [self::ACTIVE, self::INACTIVE, self::ARCHIVED],
            self::SUSPENDED => [self::ACTIVE, self::INACTIVE, self::ARCHIVED],
            self::ARCHIVED => [], // Cannot transition from archived
        };
    }

    /**
     * Get the label for the client status (alias for label method).
     */
    public function getLabel(): string
    {
        return $this->label();
    }

    /**
     * Get the color for the client status (alias for color method).
     */
    public function getColor(): string
    {
        return $this->color();
    }

    /**
     * Get the icon for the client status (alias for icon method).
     */
    public function getIcon(): string
    {
        return $this->icon();
    }
}