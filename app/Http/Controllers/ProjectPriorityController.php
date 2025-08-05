<?php

namespace App\Http\Controllers;

use App\Enums\ProjectPriority;
use App\Models\Project;
use App\Services\ProjectPriorityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ProjectPriorityController extends Controller
{
    protected ProjectPriorityService $priorityService;

    public function __construct(ProjectPriorityService $priorityService)
    {
        $this->priorityService = $priorityService;
    }

    /**
     * Get all available priorities
     */
    public function index(): JsonResponse
    {
        $priorities = $this->priorityService->getAllPriorities();
        
        $data = array_map(function($priority) {
            return [
                'value' => $priority->value,
                'label' => $priority->label(),
                'color' => $priority->color(),
                'icon' => $priority->icon(),
                'weight' => $priority->weight(),
                'description' => $priority->description(),
                'sla_hours' => $priority->slaHours(),
                'is_high_level' => $priority->isHighLevel(),
                'is_low_level' => $priority->isLowLevel(),
                'requires_immediate_attention' => $priority->requiresImmediateAttention(),
            ];
        }, $priorities);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get priority options for forms
     */
    public function options(): JsonResponse
    {
        $options = $this->priorityService->getPriorityOptions();
        $sortedOptions = $this->priorityService->getPriorityOptionsSortedByWeight();

        return response()->json([
            'success' => true,
            'data' => [
                'options' => $options,
                'sorted_options' => $sortedOptions,
            ],
        ]);
    }

    /**
     * Get priority statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->priorityService->getPriorityStatistics();
        $distributionData = $this->priorityService->getPriorityDistributionData();

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'distribution' => $distributionData,
            ],
        ]);
    }

    /**
     * Get projects by priority
     */
    public function getProjectsByPriority(string $priority): JsonResponse
    {
        $priorityEnum = ProjectPriority::tryFrom($priority);
        
        if (!$priorityEnum) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid priority value',
            ], 400);
        }

        $projects = $this->priorityService->getProjectsByPriority($priorityEnum);

        return response()->json([
            'success' => true,
            'data' => $projects->load(['client', 'user']),
            'meta' => [
                'priority' => [
                    'value' => $priorityEnum->value,
                    'label' => $priorityEnum->label(),
                    'color' => $priorityEnum->color(),
                ],
                'count' => $projects->count(),
            ],
        ]);
    }

    /**
     * Get urgent projects
     */
    public function getUrgentProjects(): JsonResponse
    {
        $projects = $this->priorityService->getUrgentProjects();

        return response()->json([
            'success' => true,
            'data' => $projects->load(['client', 'user']),
            'meta' => [
                'count' => $projects->count(),
            ],
        ]);
    }

    /**
     * Get high priority projects
     */
    public function getHighPriorityProjects(): JsonResponse
    {
        $projects = $this->priorityService->getHighPriorityProjects();

        return response()->json([
            'success' => true,
            'data' => $projects->load(['client', 'user']),
            'meta' => [
                'count' => $projects->count(),
            ],
        ]);
    }

    /**
     * Get projects ordered by priority
     */
    public function getProjectsOrderedByPriority(): JsonResponse
    {
        $projects = $this->priorityService->getProjectsOrderedByPriority();

        return response()->json([
            'success' => true,
            'data' => $projects->load(['client', 'user']),
            'meta' => [
                'count' => $projects->count(),
            ],
        ]);
    }

    /**
     * Get projects needing escalation
     */
    public function getProjectsNeedingEscalation(): JsonResponse
    {
        $projects = $this->priorityService->getProjectsNeedingEscalation();

        return response()->json([
            'success' => true,
            'data' => $projects->load(['client', 'user']),
            'meta' => [
                'count' => $projects->count(),
            ],
        ]);
    }

    /**
     * Create a new project with priority
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'user_id' => 'required|exists:users,id',
            'priority' => ['required', Rule::in(ProjectPriority::values())],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'deadline' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            $priority = ProjectPriority::from($validated['priority']);
            $project = $this->priorityService->createProjectWithPriority($validated, $priority);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'data' => $project->load(['client', 'user']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a project with priority management
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'sometimes|exists:clients,id',
            'user_id' => 'sometimes|exists:users,id',
            'priority' => ['sometimes', Rule::in(ProjectPriority::values())],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'deadline' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            $success = $this->priorityService->updateProjectWithPriority($project, $validated);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project updated successfully',
                    'data' => $project->fresh()->load(['client', 'user']),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update project',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change project priority
     */
    public function changePriority(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'priority' => ['required', Rule::in(ProjectPriority::values())],
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $newPriority = ProjectPriority::from($validated['priority']);
            $success = $this->priorityService->changePriority(
                $project, 
                $newPriority, 
                $validated['reason'] ?? null
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project priority changed successfully',
                    'data' => [
                        'project' => $project->fresh()->load(['client', 'user']),
                        'old_priority' => $project->getOriginal('priority'),
                        'new_priority' => $newPriority->value,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to change project priority',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change project priority',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Escalate project priority
     */
    public function escalatePriority(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $oldPriority = $project->priority;
            $success = $this->priorityService->escalatePriority(
                $project, 
                $validated['reason'] ?? 'Priority escalated'
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project priority escalated successfully',
                    'data' => [
                        'project' => $project->fresh()->load(['client', 'user']),
                        'old_priority' => $oldPriority->value,
                        'new_priority' => $project->fresh()->priority->value,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot escalate priority further or escalation failed',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to escalate project priority',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * De-escalate project priority
     */
    public function deEscalatePriority(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $oldPriority = $project->priority;
            $success = $this->priorityService->deEscalatePriority(
                $project, 
                $validated['reason'] ?? 'Priority de-escalated'
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project priority de-escalated successfully',
                    'data' => [
                        'project' => $project->fresh()->load(['client', 'user']),
                        'old_priority' => $oldPriority->value,
                        'new_priority' => $project->fresh()->priority->value,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot de-escalate priority further or de-escalation failed',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to de-escalate project priority',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk change priority for multiple projects
     */
    public function bulkChangePriority(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
            'priority' => ['required', Rule::in(ProjectPriority::values())],
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $newPriority = ProjectPriority::from($validated['priority']);
            $results = $this->priorityService->bulkChangePriority(
                $validated['project_ids'],
                $newPriority,
                $validated['reason'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Bulk priority change completed',
                'data' => $results,
                'meta' => [
                    'total_projects' => count($validated['project_ids']),
                    'successful_changes' => count($results['success']),
                    'failed_changes' => count($results['failed']),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk priority change',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Auto-escalate projects based on SLA
     */
    public function autoEscalateProjects(): JsonResponse
    {
        try {
            $results = $this->priorityService->autoEscalateProjects();

            return response()->json([
                'success' => true,
                'message' => 'Auto-escalation completed',
                'data' => $results,
                'meta' => [
                    'escalated_count' => count($results['escalated']),
                    'failed_count' => count($results['failed']),
                    'already_max_count' => count($results['already_max']),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform auto-escalation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}