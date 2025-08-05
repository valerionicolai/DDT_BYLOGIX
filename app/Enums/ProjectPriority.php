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
     * Get all priority values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get priority options for forms (value => label)
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    /**
     * Get human-readable label for the priority
     */
    public function label(): string
    {
        return match($this) {
            self::LOW => 'Bassa',
            self::MEDIUM => 'Media',
            self::HIGH => 'Alta',
            self::URGENT => 'Urgente',
            self::CRITICAL => 'Critica',
        };
    }

    /**
     * Get color for the priority badge
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
     * Get icon for the priority
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
     * Get numeric weight for sorting (higher number = higher priority)
     */
    public function weight(): int
    {
        return match($this) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::URGENT => 4,
            self::CRITICAL => 5,
        };
    }

    /**
     * Get description for the priority level
     */
    public function description(): string
    {
        return match($this) {
            self::LOW => 'Priorità bassa - può essere completato quando possibile',
            self::MEDIUM => 'Priorità media - dovrebbe essere completato entro i tempi normali',
            self::HIGH => 'Priorità alta - richiede attenzione prioritaria',
            self::URGENT => 'Urgente - richiede attenzione immediata',
            self::CRITICAL => 'Critica - massima priorità, blocca altre attività',
        };
    }

    /**
     * Check if this priority is higher than another
     */
    public function isHigherThan(ProjectPriority $other): bool
    {
        return $this->weight() > $other->weight();
    }

    /**
     * Check if this priority is lower than another
     */
    public function isLowerThan(ProjectPriority $other): bool
    {
        return $this->weight() < $other->weight();
    }

    /**
     * Check if this priority requires immediate attention
     */
    public function requiresImmediateAttention(): bool
    {
        return in_array($this, [self::URGENT, self::CRITICAL]);
    }

    /**
     * Check if this priority is considered high level
     */
    public function isHighLevel(): bool
    {
        return in_array($this, [self::HIGH, self::URGENT, self::CRITICAL]);
    }

    /**
     * Check if this priority is considered low level
     */
    public function isLowLevel(): bool
    {
        return in_array($this, [self::LOW, self::MEDIUM]);
    }

    /**
     * Get escalation priority (next higher level)
     */
    public function escalate(): ?ProjectPriority
    {
        return match($this) {
            self::LOW => self::MEDIUM,
            self::MEDIUM => self::HIGH,
            self::HIGH => self::URGENT,
            self::URGENT => self::CRITICAL,
            self::CRITICAL => null, // Already at maximum
        };
    }

    /**
     * Get de-escalation priority (next lower level)
     */
    public function deEscalate(): ?ProjectPriority
    {
        return match($this) {
            self::CRITICAL => self::URGENT,
            self::URGENT => self::HIGH,
            self::HIGH => self::MEDIUM,
            self::MEDIUM => self::LOW,
            self::LOW => null, // Already at minimum
        };
    }

    /**
     * Get SLA (Service Level Agreement) hours for this priority
     */
    public function slaHours(): int
    {
        return match($this) {
            self::LOW => 168, // 1 week
            self::MEDIUM => 72, // 3 days
            self::HIGH => 24, // 1 day
            self::URGENT => 4, // 4 hours
            self::CRITICAL => 1, // 1 hour
        };
    }

    /**
     * Get all priorities sorted by weight (ascending)
     */
    public static function sortedByWeight(): array
    {
        $priorities = self::cases();
        usort($priorities, fn($a, $b) => $a->weight() <=> $b->weight());
        return $priorities;
    }

    /**
     * Get all priorities sorted by weight (descending)
     */
    public static function sortedByWeightDesc(): array
    {
        return array_reverse(self::sortedByWeight());
    }

    /**
     * Get priorities that require immediate attention
     */
    public static function getUrgentPriorities(): array
    {
        return array_filter(self::cases(), fn($priority) => $priority->requiresImmediateAttention());
    }

    /**
     * Get high level priorities
     */
    public static function getHighLevelPriorities(): array
    {
        return array_filter(self::cases(), fn($priority) => $priority->isHighLevel());
    }

    /**
     * Get low level priorities
     */
    public static function getLowLevelPriorities(): array
    {
        return array_filter(self::cases(), fn($priority) => $priority->isLowLevel());
    }

    /**
     * Create from string value with fallback
     */
    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::MEDIUM;
    }

    /**
     * Get options array for forms (value => label)
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $priority) {
            $options[$priority->value] = $priority->label();
        }
        return $options;
    }

    /**
     * Get options array sorted by weight
     */
    public static function getOptionsSortedByWeight(): array
    {
        $options = [];
        foreach (self::sortedByWeight() as $priority) {
            $options[$priority->value] = $priority->label();
        }
        return $options;
    }
}