<?php

namespace App\Enums;

enum MaterialState: string
{
    case DA_CONSERVARE = 'da_conservare';
    case DA_TRATTENERE = 'da_trattenere';
    case DA_RESTITUIRE = 'da_restituire';

    /**
     * Get the label for the material state
     */
    public function getLabel(): string
    {
        return match($this) {
            self::DA_CONSERVARE => 'To Keep',
            self::DA_TRATTENERE => 'To Hold',
            self::DA_RESTITUIRE => 'To Return',
        };
    }

    /**
     * Get the color for the material state
     */
    public function getColor(): string
    {
        return match($this) {
            self::DA_CONSERVARE => 'success',
            self::DA_TRATTENERE => 'warning',
            self::DA_RESTITUIRE => 'danger',
        };
    }

    /**
     * Get the icon for the material state
     */
    public function getIcon(): string
    {
        return match($this) {
            self::DA_CONSERVARE => 'heroicon-o-archive-box',
            self::DA_TRATTENERE => 'heroicon-o-clock',
            self::DA_RESTITUIRE => 'heroicon-o-arrow-uturn-left',
        };
    }

    /**
     * Get all states as array for select options
     */
    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($state) => [$state->value => $state->getLabel()])
            ->toArray();
    }

    /**
     * Check if the state requires urgent action
     */
    public function isUrgent(): bool
    {
        return $this === self::DA_RESTITUIRE;
    }
}
