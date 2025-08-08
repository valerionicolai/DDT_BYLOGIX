<?php

namespace App\Enums;

enum ProjectState: string
{
    case DRAFT = 'draft';
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';
    case REVIEW = 'review';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case ARCHIVED = 'archived';

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
     * Get the human-readable label for the state
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PLANNING => 'Planning',
            self::ACTIVE => 'Active',
            self::IN_PROGRESS => 'In Progress',
            self::ON_HOLD => 'On Hold',
            self::REVIEW => 'Under Review',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::ARCHIVED => 'Archived',
        };
    }

    /**
     * Get the color associated with the state
     */
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::PLANNING => 'blue',
            self::ACTIVE => 'green',
            self::IN_PROGRESS => 'indigo',
            self::ON_HOLD => 'yellow',
            self::REVIEW => 'purple',
            self::COMPLETED => 'emerald',
            self::CANCELLED => 'red',
            self::ARCHIVED => 'slate',
        };
    }

    /**
     * Get the icon associated with the state
     */
    public function icon(): string
    {
        return match($this) {
            self::DRAFT => 'heroicon-o-document',
            self::PLANNING => 'heroicon-o-clipboard-document-list',
            self::ACTIVE => 'heroicon-o-play',
            self::IN_PROGRESS => 'heroicon-o-arrow-path',
            self::ON_HOLD => 'heroicon-o-pause',
            self::REVIEW => 'heroicon-o-eye',
            self::COMPLETED => 'heroicon-o-check-circle',
            self::CANCELLED => 'heroicon-o-x-circle',
            self::ARCHIVED => 'heroicon-o-archive-box',
        };
    }

    /**
     * Check if the state allows transitions to another state
     */
    public function canTransitionTo(ProjectState $newState): bool
    {
        return match($this) {
            self::DRAFT => in_array($newState, [self::PLANNING, self::CANCELLED]),
            self::PLANNING => in_array($newState, [self::ACTIVE, self::ON_HOLD, self::CANCELLED]),
            self::ACTIVE => in_array($newState, [self::IN_PROGRESS, self::ON_HOLD, self::REVIEW, self::CANCELLED]),
            self::IN_PROGRESS => in_array($newState, [self::ACTIVE, self::ON_HOLD, self::REVIEW, self::COMPLETED, self::CANCELLED]),
            self::ON_HOLD => in_array($newState, [self::ACTIVE, self::IN_PROGRESS, self::CANCELLED]),
            self::REVIEW => in_array($newState, [self::IN_PROGRESS, self::COMPLETED, self::CANCELLED]),
            self::COMPLETED => in_array($newState, [self::ARCHIVED]),
            self::CANCELLED => in_array($newState, [self::ARCHIVED]),
            self::ARCHIVED => false, // No transitions from archived
        };
    }

    /**
     * Get valid next states for transition
     */
    public function getValidTransitions(): array
    {
        return array_filter(
            self::cases(),
            fn($state) => $this->canTransitionTo($state)
        );
    }

    /**
     * Check if the state is considered active
     */
    public function isActive(): bool
    {
        return in_array($this, [self::ACTIVE, self::IN_PROGRESS, self::REVIEW]);
    }

    /**
     * Check if the state is considered final
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED, self::ARCHIVED]);
    }

    /**
     * Check if the state is considered inactive
     */
    public function isInactive(): bool
    {
        return in_array($this, [self::DRAFT, self::PLANNING, self::ON_HOLD]);
    }

    /**
     * Get the label for the project state (alias for label method).
     */
    public function getLabel(): string
    {
        return $this->label();
    }

    /**
     * Get the color for the project state (alias for color method).
     */
    public function getColor(): string
    {
        return $this->color();
    }

    /**
     * Get the icon for the project state (alias for icon method).
     */
    public function getIcon(): string
    {
        return $this->icon();
    }
}