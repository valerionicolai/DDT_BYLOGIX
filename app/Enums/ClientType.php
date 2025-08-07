<?php

namespace App\Enums;

enum ClientType: string
{
    case CLIENTE = 'cliente';
    case FORNITORE = 'fornitore';

    /**
     * Get the label for the client type.
     */
    public function label(): string
    {
        return match($this) {
            self::CLIENTE => 'Cliente',
            self::FORNITORE => 'Fornitore',
        };
    }

    /**
     * Get all client type options for forms.
     */
    public static function options(): array
    {
        return [
            self::CLIENTE->value => self::CLIENTE->label(),
            self::FORNITORE->value => self::FORNITORE->label(),
        ];
    }

    /**
     * Get the icon for the client type.
     */
    public function icon(): string
    {
        return match($this) {
            self::CLIENTE => 'heroicon-o-user',
            self::FORNITORE => 'heroicon-o-building-office',
        };
    }

    /**
     * Get the color for the client type.
     */
    public function color(): string
    {
        return match($this) {
            self::CLIENTE => 'primary',
            self::FORNITORE => 'success',
        };
    }

    /**
     * Get the label for the client type (alias for label method).
     */
    public function getLabel(): string
    {
        return $this->label();
    }

    /**
     * Get the color for the client type (alias for color method).
     */
    public function getColor(): string
    {
        return $this->color();
    }

    /**
     * Get the icon for the client type (alias for icon method).
     */
    public function getIcon(): string
    {
        return $this->icon();
    }
}
