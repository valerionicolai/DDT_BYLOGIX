<?php

namespace App\Http\Controllers;

use App\Enums\ProjectState;
use App\Models\Project;
use App\Services\ProjectStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ProjectStateController extends Controller
{
    public function __construct(
        private ProjectStateService $projectStateService
    ) {}

    /**
     * Get all available project states
     */
    public function index(): JsonResponse
    {
        $states = $this->projectStateService->getAllStates();
        
        return response()->json([
            'success' => true,
            'data' => array_map(function ($state) {
                return [
                    'value' => $state->value,
                    'label' => $state->label(),
                    'color' => $state->color(),
                    'icon' => $state->icon(),
                    'is_active' => $state->isActive(),
                    'is_final' => $state->isFinal(),
                    'is_inactive' => $state->isInactive(),
                ];
            }, $states),
        ]);
    }

    /**
     * Get state options for forms
     */
    public function options(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->projectStateService->getStateOptions(),
        ]);
    }

    /**
     * Get projects by state
     */
    public function projectsByState(string $state): JsonResponse
    {
        try {
            $projectState = ProjectState::from($state);
            $projects = $this->projectStateService->getProjectsByState($projectState);

            return response()->json([
                'success' => true,
                'data' => [
                    'state' => [
                        'value' => $projectState->value,
                        'label' => $projectState->label(),
                        'color' => $projectState->color(),
                        'icon' => $projectState->icon(),
                    ],
                    'projects' => $projects->load(['client', 'user']),
                    'count' => $projects->count(),
                ],
            ]);
        } catch (\ValueError $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid project state',
            ], 400);
        }
    }

    /**
     * Get project count by state
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->projectStateService->getStateStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Transition a project to a new state
     */
    public function transition(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'state' => ['required', new Enum(ProjectState::class)],
            'reason' => 'nullable|string|max:500',
        ]);

        $newState = ProjectState::from($request->state);
        $reason = $request->reason;

        if (!$project->canTransitionTo($newState)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid state transition',
                'current_state' => $project->status->value,
                'requested_state' => $newState->value,
                'valid_transitions' => array_map(
                    fn($state) => $state->value,
                    $project->getValidTransitions()
                ),
            ], 422);
        }

        $success = $this->projectStateService->transitionProject($project, $newState, $reason);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Project state updated successfully',
                'data' => [
                    'project' => $project->fresh()->load(['client', 'user']),
                    'previous_state' => $request->state,
                    'new_state' => $project->status->value,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update project state',
        ], 500);
    }

    /**
     * Bulk transition multiple projects
     */
    public function bulkTransition(Request $request): JsonResponse
    {
        $request->validate([
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
            'state' => ['required', new Enum(ProjectState::class)],
            'reason' => 'nullable|string|max:500',
        ]);

        $newState = ProjectState::from($request->state);
        $results = $this->projectStateService->bulkTransitionProjects(
            $request->project_ids,
            $newState,
            $request->reason
        );

        return response()->json([
            'success' => true,
            'message' => 'Bulk transition completed',
            'data' => [
                'successful_transitions' => count($results['success']),
                'failed_transitions' => count($results['failed']),
                'invalid_transitions' => count($results['invalid']),
                'results' => $results,
            ],
        ]);
    }

    /**
     * Get valid transitions for a project
     */
    public function validTransitions(Project $project): JsonResponse
    {
        $validTransitions = $this->projectStateService->getValidTransitions($project);

        return response()->json([
            'success' => true,
            'data' => [
                'current_state' => [
                    'value' => $project->status->value,
                    'label' => $project->status->label(),
                    'color' => $project->status->color(),
                    'icon' => $project->status->icon(),
                ],
                'valid_transitions' => array_map(function ($state) {
                    return [
                        'value' => $state->value,
                        'label' => $state->label(),
                        'color' => $state->color(),
                        'icon' => $state->icon(),
                    ];
                }, $validTransitions),
            ],
        ]);
    }

    /**
     * Get projects eligible for transition to a specific state
     */
    public function eligibleProjects(string $state): JsonResponse
    {
        try {
            $targetState = ProjectState::from($state);
            $projects = $this->projectStateService->getProjectsEligibleForTransition($targetState);

            return response()->json([
                'success' => true,
                'data' => [
                    'target_state' => [
                        'value' => $targetState->value,
                        'label' => $targetState->label(),
                        'color' => $targetState->color(),
                        'icon' => $targetState->icon(),
                    ],
                    'eligible_projects' => $projects->load(['client', 'user']),
                    'count' => $projects->count(),
                ],
            ]);
        } catch (\ValueError $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid project state',
            ], 400);
        }
    }

    /**
     * Archive eligible projects
     */
    public function archiveEligible(): JsonResponse
    {
        $archivedCount = $this->projectStateService->archiveEligibleProjects();

        return response()->json([
            'success' => true,
            'message' => "Successfully archived {$archivedCount} projects",
            'data' => [
                'archived_count' => $archivedCount,
            ],
        ]);
    }

    /**
     * Get overdue projects
     */
    public function overdueProjects(): JsonResponse
    {
        $overdueProjects = $this->projectStateService->getOverdueProjects();

        return response()->json([
            'success' => true,
            'data' => [
                'overdue_projects' => $overdueProjects->load(['client', 'user']),
                'count' => $overdueProjects->count(),
            ],
        ]);
    }

    /**
     * Auto-review overdue projects
     */
    public function reviewOverdue(): JsonResponse
    {
        $reviewedCount = $this->projectStateService->reviewOverdueProjects();

        return response()->json([
            'success' => true,
            'message' => "Successfully moved {$reviewedCount} overdue projects to review",
            'data' => [
                'reviewed_count' => $reviewedCount,
            ],
        ]);
    }

    /**
     * Create a new project with initial state
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'user_id' => 'required|exists:users,id',
            'initial_state' => ['nullable', new Enum(ProjectState::class)],
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'deadline' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        $initialState = $request->initial_state 
            ? ProjectState::from($request->initial_state)
            : ProjectState::DRAFT;

        $project = $this->projectStateService->createProjectWithState(
            $request->except('initial_state'),
            $initialState
        );

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => $project->load(['client', 'user']),
        ], 201);
    }

    /**
     * Update a project
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'sometimes|exists:clients,id',
            'user_id' => 'sometimes|exists:users,id',
            'status' => ['sometimes', new Enum(ProjectState::class)],
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'deadline' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        $data = $request->all();
        if (isset($data['status'])) {
            $data['status'] = ProjectState::from($data['status']);
        }

        $success = $this->projectStateService->updateProject($project, $data);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'data' => $project->fresh()->load(['client', 'user']),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update project',
        ], 500);
    }
}