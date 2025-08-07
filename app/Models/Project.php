<?php

namespace App\Models;

use App\Enums\ProjectState;
use App\Enums\ProjectPriority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
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
        'client_id',
        'user_id',
        'status',
        'priority',
        'start_date',
        'end_date',
        'deadline',
        'budget',
        'estimated_cost',
        'actual_cost',
        'progress_percentage',
        'notes',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'deadline' => 'date',
        'budget' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'progress_percentage' => 'integer',
        'metadata' => 'array',
        'status' => ProjectState::class,
        'priority' => ProjectPriority::class,
    ];

    /**
     * Get the client that owns the project.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user (project manager) that owns the project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the documents for the project.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Scope a query to only include active projects.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            ProjectState::ACTIVE->value,
            ProjectState::IN_PROGRESS->value,
            ProjectState::REVIEW->value
        ]);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, ProjectState|string $status)
    {
        $statusValue = $status instanceof ProjectState ? $status->value : $status;
        return $query->where('status', $statusValue);
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopeByPriority($query, ProjectPriority|string $priority)
    {
        $priorityValue = $priority instanceof ProjectPriority ? $priority->value : $priority;
        return $query->where('priority', $priorityValue);
    }

    /**
     * Scope a query to include overdue projects.
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->whereNotIn('status', [
                        ProjectState::COMPLETED->value,
                        ProjectState::CANCELLED->value,
                        ProjectState::ARCHIVED->value
                    ]);
    }

    /**
     * Check if the project is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline && 
               $this->deadline->isPast() && 
               !$this->status->isFinal();
    }

    /**
     * Get the project's duration in days.
     */
    public function getDurationInDaysAttribute(): ?int
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
        }
        
        return null;
    }

    /**
     * Get the remaining budget.
     */
    public function getRemainingBudgetAttribute(): ?float
    {
        if ($this->budget && $this->actual_cost) {
            return $this->budget - $this->actual_cost;
        }
        
        return $this->budget;
    }

    /**
     * Get the budget utilization percentage.
     */
    public function getBudgetUtilizationAttribute(): ?float
    {
        if ($this->budget && $this->actual_cost) {
            return ($this->actual_cost / $this->budget) * 100;
        }
        
        return null;
    }

    /**
     * Get formatted budget with currency.
     */
    public function getFormattedBudgetAttribute(): string
    {
        if ($this->budget) {
            return '€ ' . number_format($this->budget, 2, ',', '.');
        }
        
        return 'Budget non definito';
    }

    /**
     * Get the project status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    /**
     * Get the project priority badge color.
     */
    public function getPriorityColorAttribute(): string
    {
        return $this->priority->color();
    }

    /**
     * Transition the project to a new state
     */
    public function transitionTo(ProjectState $newState): bool
    {
        if (!$this->status->canTransitionTo($newState)) {
            return false;
        }

        $this->status = $newState;
        return $this->save();
    }

    /**
     * Get valid transitions for the current state
     */
    public function getValidTransitions(): array
    {
        return $this->status->getValidTransitions();
    }

    /**
     * Check if the project can transition to a specific state
     */
    public function canTransitionTo(ProjectState $newState): bool
    {
        return $this->status->canTransitionTo($newState);
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Get the status icon
     */
    public function getStatusIconAttribute(): string
    {
        return $this->status->icon();
    }

    /**
     * Check if the project is in an active state
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * Check if the project is in a final state
     */
    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    /**
     * Check if the project is in an inactive state
     */
    public function isInactive(): bool
    {
        return $this->status->isInactive();
    }

    /**
     * Get the priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return $this->priority->label();
    }

    /**
     * Get the priority icon
     */
    public function getPriorityIconAttribute(): string
    {
        return $this->priority->icon();
    }



    /**
     * Scope for projects in specific state categories
     */
    public function scopeInActiveStates($query)
    {
        return $query->whereIn('status', array_map(
            fn($state) => $state->value,
            array_filter(ProjectState::cases(), fn($state) => $state->isActive())
        ));
    }

    public function scopeInFinalStates($query)
    {
        return $query->whereIn('status', array_map(
            fn($state) => $state->value,
            array_filter(ProjectState::cases(), fn($state) => $state->isFinal())
        ));
    }

    public function scopeInInactiveStates($query)
    {
        return $query->whereIn('status', array_map(
            fn($state) => $state->value,
            array_filter(ProjectState::cases(), fn($state) => $state->isInactive())
        ));
    }



    /**
     * Scope for projects with specific priority levels
     */
    public function scopeWithPriorities($query, array $priorities)
    {
        $priorityValues = array_map(function($priority) {
            return $priority instanceof ProjectPriority ? $priority->value : $priority;
        }, $priorities);
        
        return $query->whereIn('priority', $priorityValues);
    }
}
