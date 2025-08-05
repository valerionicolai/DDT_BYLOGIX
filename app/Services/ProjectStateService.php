<?php

namespace App\Services;

use App\Enums\ProjectState;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectStateService
{
    /**
     * Get all available project states
     */
    public function getAllStates(): array
    {
        return ProjectState::cases();
    }

    /**
     * Get project states as options for forms
     */
    public function getStateOptions(): array
    {
        return ProjectState::options();
    }

    /**
     * Get projects by state
     */
    public function getProjectsByState(ProjectState $state): Collection
    {
        return Project::byStatus($state)->get();
    }

    /**
     * Get projects count by state
     */
    public function getProjectsCountByState(): array
    {
        $counts = [];
        foreach (ProjectState::cases() as $state) {
            $counts[$state->value] = [
                'state' => $state,
                'label' => $state->label(),
                'count' => Project::byStatus($state)->count(),
                'color' => $state->color(),
                'icon' => $state->icon(),
            ];
        }
        return $counts;
    }

    /**
     * Transition a project to a new state
     */
    public function transitionProject(Project $project, ProjectState $newState, ?string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            if (!$project->canTransitionTo($newState)) {
                Log::warning("Invalid state transition attempted", [
                    'project_id' => $project->id,
                    'from_state' => $project->status->value,
                    'to_state' => $newState->value,
                ]);
                return false;
            }

            $oldState = $project->status;
            $success = $project->transitionTo($newState);

            if ($success) {
                // Log the state transition
                Log::info("Project state transition successful", [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'from_state' => $oldState->value,
                    'to_state' => $newState->value,
                    'reason' => $reason,
                ]);

                // You could also create a state transition history record here
                // $this->createStateTransitionHistory($project, $oldState, $newState, $reason);
            }

            DB::commit();
            return $success;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Project state transition failed", [
                'project_id' => $project->id,
                'from_state' => $project->status->value,
                'to_state' => $newState->value,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Bulk transition multiple projects to a new state
     */
    public function bulkTransitionProjects(array $projectIds, ProjectState $newState, ?string $reason = null): array
    {
        $results = [
            'success' => [],
            'failed' => [],
            'invalid' => [],
        ];

        $projects = Project::whereIn('id', $projectIds)->get();

        foreach ($projects as $project) {
            if (!$project->canTransitionTo($newState)) {
                $results['invalid'][] = [
                    'project' => $project,
                    'reason' => 'Invalid state transition',
                ];
                continue;
            }

            if ($this->transitionProject($project, $newState, $reason)) {
                $results['success'][] = $project;
            } else {
                $results['failed'][] = $project;
            }
        }

        return $results;
    }

    /**
     * Get valid transitions for a project
     */
    public function getValidTransitions(Project $project): array
    {
        return $project->getValidTransitions();
    }

    /**
     * Get projects that can be transitioned to a specific state
     */
    public function getProjectsEligibleForTransition(ProjectState $targetState): Collection
    {
        $eligibleStates = [];
        foreach (ProjectState::cases() as $state) {
            if ($state->canTransitionTo($targetState)) {
                $eligibleStates[] = $state->value;
            }
        }

        return Project::whereIn('status', $eligibleStates)->get();
    }

    /**
     * Get state statistics
     */
    public function getStateStatistics(): array
    {
        $total = Project::count();
        $stats = [];

        foreach (ProjectState::cases() as $state) {
            $count = Project::byStatus($state)->count();
            $stats[] = [
                'state' => $state,
                'label' => $state->label(),
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0,
                'color' => $state->color(),
                'icon' => $state->icon(),
            ];
        }

        return [
            'total' => $total,
            'by_state' => $stats,
            'active_projects' => Project::inActiveStates()->count(),
            'final_projects' => Project::inFinalStates()->count(),
            'inactive_projects' => Project::inInactiveStates()->count(),
        ];
    }

    /**
     * Create a new project with initial state
     */
    public function createProjectWithState(array $data, ProjectState $initialState = ProjectState::DRAFT): Project
    {
        $data['status'] = $initialState;
        return Project::create($data);
    }

    /**
     * Update project state and other attributes
     */
    public function updateProject(Project $project, array $data): bool
    {
        try {
            DB::beginTransaction();

            // Check if status is being changed
            if (isset($data['status']) && $data['status'] instanceof ProjectState) {
                $newState = $data['status'];
                if (!$project->canTransitionTo($newState)) {
                    return false;
                }
            }

            $project->update($data);
            
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Project update failed", [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Archive completed or cancelled projects
     */
    public function archiveEligibleProjects(): int
    {
        $eligibleProjects = Project::whereIn('status', [
            ProjectState::COMPLETED->value,
            ProjectState::CANCELLED->value,
        ])->get();

        $archivedCount = 0;
        foreach ($eligibleProjects as $project) {
            if ($this->transitionProject($project, ProjectState::ARCHIVED, 'Auto-archived')) {
                $archivedCount++;
            }
        }

        return $archivedCount;
    }

    /**
     * Get overdue projects that should be reviewed
     */
    public function getOverdueProjects(): Collection
    {
        return Project::overdue()->get();
    }

    /**
     * Auto-transition overdue projects to review state
     */
    public function reviewOverdueProjects(): int
    {
        $overdueProjects = $this->getOverdueProjects();
        $reviewedCount = 0;

        foreach ($overdueProjects as $project) {
            if ($project->canTransitionTo(ProjectState::REVIEW)) {
                if ($this->transitionProject($project, ProjectState::REVIEW, 'Auto-review due to overdue status')) {
                    $reviewedCount++;
                }
            }
        }

        return $reviewedCount;
    }
}