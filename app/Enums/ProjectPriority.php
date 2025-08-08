<?php

namespace App\Enums;

enum ProjectPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';
    case CRITICAL = 'critical';

    /**
     * Get all enum values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all enum cases as key-value pairs
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }

    /**
     * Get the human-readable label for the priority
     */
    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
            self::CRITICAL => 'Critical',
        };
    }

    /**
     * Get the color associated with the priority
     */
    public function color(): string
    {
        return match($this) {
            self::LOW => 'green',
            self::MEDIUM => 'blue',
            self::HIGH => 'yellow',
            self::URGENT => 'orange',
            self::CRITICAL => 'red',
        };
    }

    /**
     * Get the icon associated with the priority
     */
    public function icon(): string
    {
        return match($this) {
            self::LOW => 'heroicon-o-arrow-down',
            self::MEDIUM => 'heroicon-o-minus',
            self::HIGH => 'heroicon-o-arrow-up',
            self::URGENT => 'heroicon-o-exclamation-triangle',
            self::CRITICAL => 'heroicon-o-fire',
        };
    }

    /**
     * Get the label for the project priority (alias for label method).
     */
    public function getLabel(): string
    {
        return $this->label();
    }

    /**
     * Get the color for the project priority (alias for color method).
     */
    public function getColor(): string
    {
        return $this->color();
    }

    /**
     * Get the icon for the project priority (alias for icon method).
     */
    public function getIcon(): string
    {
        return $this->icon();
    }
}