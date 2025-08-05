<?php

namespace App\Services;

use App\Enums\ProjectPriority;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectPriorityService
{
    /**
     * Get all available priorities
     */
    public function getAllPriorities(): array
    {
        return ProjectPriority::cases();
    }

    /**
     * Get priority options for forms
     */
    public function getPriorityOptions(): array
    {
        return ProjectPriority::getOptions();
    }

    /**
     * Get priority options sorted by weight
     */
    public function getPriorityOptionsSortedByWeight(): array
    {
        return ProjectPriority::getOptionsSortedByWeight();
    }

    /**
     * Get projects by priority
     */
    public function getProjectsByPriority(ProjectPriority $priority): Collection
    {
        return Project::byPriority($priority)->get();
    }

    /**
     * Get projects count by priority
     */
    public function getProjectsCountByPriority(): array
    {
        $counts = [];
        foreach (ProjectPriority::cases() as $priority) {
            $count = Project::byPriority($priority)->count();
            $counts[] = [
                'priority' => $priority->value,
                'label' => $priority->label(),
                'count' => $count,
                'color' => $priority->color(),
                'icon' => $priority->icon(),
                'weight' => $priority->weight(),
            ];
        }
        return $counts;
    }

    /**
     * Get priority statistics
     */
    public function getPriorityStatistics(): array
    {
        $total = Project::count();
        $highPriorityCount = Project::highPriority()->count();
        $lowPriorityCount = Project::lowPriority()->count();
        $urgentCount = Project::urgentPriority()->count();

        $byPriority = $this->getProjectsCountByPriority();
        
        // Add percentage to each priority
        foreach ($byPriority as &$priorityData) {
            $priorityData['percentage'] = $total > 0 ? round(($priorityData['count'] / $total) * 100, 1) : 0;
        }

        return [
            'total' => $total,
            'high_priority_projects' => $highPriorityCount,
            'low_priority_projects' => $lowPriorityCount,
            'urgent_projects' => $urgentCount,
            'by_priority' => $byPriority,
        ];
    }

    /**
     * Change project priority
     */
    public function changePriority(Project $project, ProjectPriority $newPriority, string $reason = null): bool
    {
        try {
            $oldPriority = $project->priority;
            $project->priority = $newPriority;
            
            if ($project->save()) {
                // Log the priority change if needed
                Log::info("Project priority changed", [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'old_priority' => $oldPriority->value,
                    'new_priority' => $newPriority->value,
                    'reason' => $reason,
                ]);
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to change project priority", [
                'project_id' => $project->id,
                'new_priority' => $newPriority->value,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Bulk change priority for multiple projects
     */
    public function bulkChangePriority(array $projectIds, ProjectPriority $newPriority, string $reason = null): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($projectIds as $projectId) {
            $project = Project::find($projectId);
            
            if (!$project) {
                $results['failed'][] = [
                    'project_id' => $projectId,
                    'error' => 'Project not found',
                ];
                continue;
            }

            if ($this->changePriority($project, $newPriority, $reason)) {
                $results['success'][] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'new_priority' => $newPriority->value,
                ];
            } else {
                $results['failed'][] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'error' => 'Failed to update priority',
                ];
            }
        }

        return $results;
    }

    /**
     * Escalate project priority
     */
    public function escalatePriority(Project $project, string $reason = null): bool
    {
        $newPriority = $project->priority->escalate();
        
        if (!$newPriority) {
            return false; // Already at maximum priority
        }

        return $this->changePriority($project, $newPriority, $reason);
    }

    /**
     * De-escalate project priority
     */
    public function deEscalatePriority(Project $project, string $reason = null): bool
    {
        $newPriority = $project->priority->deEscalate();
        
        if (!$newPriority) {
            return false; // Already at minimum priority
        }

        return $this->changePriority($project, $newPriority, $reason);
    }

    /**
     * Get projects that require immediate attention
     */
    public function getUrgentProjects(): Collection
    {
        return Project::urgentPriority()->get();
    }

    /**
     * Get projects with high priority
     */
    public function getHighPriorityProjects(): Collection
    {
        return Project::highPriority()->get();
    }

    /**
     * Get projects with low priority
     */
    public function getLowPriorityProjects(): Collection
    {
        return Project::lowPriority()->get();
    }

    /**
     * Get projects ordered by priority
     */
    public function getProjectsOrderedByPriority(): Collection
    {
        return Project::orderByPriority()->get();
    }

    /**
     * Create project with specific priority
     */
    public function createProjectWithPriority(array $projectData, ProjectPriority $priority): Project
    {
        $projectData['priority'] = $priority;
        return Project::create($projectData);
    }

    /**
     * Update project with priority management
     */
    public function updateProjectWithPriority(Project $project, array $data): bool
    {
        try {
            // If priority is being changed, log it
            if (isset($data['priority']) && $data['priority'] !== $project->priority->value) {
                $oldPriority = $project->priority;
                $newPriority = ProjectPriority::fromString($data['priority']);
                
                Log::info("Project priority being updated", [
                    'project_id' => $project->id,
                    'old_priority' => $oldPriority->value,
                    'new_priority' => $newPriority->value,
                ]);
            }

            return $project->update($data);
        } catch (\Exception $e) {
            Log::error("Failed to update project with priority", [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get projects that might need priority escalation based on SLA
     */
    public function getProjectsNeedingEscalation(): Collection
    {
        $allProjects = collect();
        
        foreach (ProjectPriority::cases() as $priority) {
            $slaHours = $priority->slaHours();
            $cutoffTime = now()->subHours($slaHours);
            
            $projectsForPriority = Project::byPriority($priority)
                ->where('created_at', '<', $cutoffTime)
                ->whereNotIn('status', ['completed', 'cancelled', 'archived'])
                ->get();
                
            $allProjects = $allProjects->merge($projectsForPriority);
        }
        
        // Convert to Eloquent Collection
        return Project::whereIn('id', $allProjects->pluck('id'))->get();
    }

    /**
     * Auto-escalate projects based on SLA
     */
    public function autoEscalateProjects(): array
    {
        $projectsToEscalate = $this->getProjectsNeedingEscalation();
        $results = [
            'escalated' => [],
            'failed' => [],
            'already_max' => [],
        ];

        foreach ($projectsToEscalate as $project) {
            if ($project->priority === ProjectPriority::CRITICAL) {
                $results['already_max'][] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                ];
                continue;
            }

            if ($this->escalatePriority($project, 'Auto-escalated due to SLA breach')) {
                $results['escalated'][] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'old_priority' => $project->getOriginal('priority'),
                    'new_priority' => $project->priority->value,
                ];
            } else {
                $results['failed'][] = [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                ];
            }
        }

        return $results;
    }

    /**
     * Get priority distribution chart data
     */
    public function getPriorityDistributionData(): array
    {
        $data = [];
        $counts = $this->getProjectsCountByPriority();
        
        foreach ($counts as $priorityData) {
            $data[] = [
                'label' => $priorityData['label'],
                'value' => $priorityData['count'],
                'color' => $priorityData['color'],
                'percentage' => $priorityData['percentage'] ?? 0,
            ];
        }
        
        return $data;
    }
}