<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Project::with(['client', 'user']);

            // Filtro per status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filtro per priorità
            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            // Filtro per cliente
            if ($request->has('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            // Filtro per project manager
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filtro per progetti scaduti
            if ($request->has('overdue') && $request->overdue === 'true') {
                $query->overdue();
            }

            // Filtro per range di date
            if ($request->has('start_date_from')) {
                $query->where('start_date', '>=', $request->start_date_from);
            }
            if ($request->has('start_date_to')) {
                $query->where('start_date', '<=', $request->start_date_to);
            }

            // Filtro per budget
            if ($request->has('budget_min')) {
                $query->where('budget', '>=', $request->budget_min);
            }
            if ($request->has('budget_max')) {
                $query->where('budget', '<=', $request->budget_max);
            }

            // Ricerca per nome o descrizione
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Ordinamento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginazione
            $perPage = $request->get('per_page', 15);
            $projects = $query->paginate($perPage);

            // Aggiungi attributi calcolati
            $projects->getCollection()->transform(function ($project) {
                $project->append(['is_overdue', 'duration_in_days', 'remaining_budget', 'budget_utilization']);
                return $project;
            });

            return response()->json([
                'success' => true,
                'data' => $projects,
                'message' => 'Projects retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving projects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'client_id' => 'required|exists:clients,id',
                'user_id' => 'required|exists:users,id',
                'status' => 'nullable|in:draft,active,completed,cancelled,on_hold',
                'priority' => 'nullable|in:low,medium,high,urgent',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'deadline' => 'nullable|date',
                'budget' => 'nullable|numeric|min:0',
                'estimated_cost' => 'nullable|numeric|min:0',
                'actual_cost' => 'nullable|numeric|min:0',
                'progress_percentage' => 'nullable|integer|min:0|max:100',
                'notes' => 'nullable|string',
                'metadata' => 'nullable|array',
            ]);

            // Validazioni aggiuntive
            if (isset($validated['end_date']) && isset($validated['deadline'])) {
                if (Carbon::parse($validated['deadline'])->lt(Carbon::parse($validated['end_date']))) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The deadline cannot be earlier than the project end date',
                        'errors' => ['deadline' => ['The deadline must be on or after the end date']]
                    ], 422);
                }
            }

            $project = Project::create($validated);
            $project->load(['client', 'user']);
            $project->append(['is_overdue', 'duration_in_days', 'remaining_budget']);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'data' => $project
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $project = Project::with(['client', 'user'])->findOrFail($id);
            $project->append(['is_overdue', 'duration_in_days', 'remaining_budget', 'budget_utilization']);

            return response()->json([
                'success' => true,
                'data' => $project,
                'message' => 'Project retrieved successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $project = Project::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'client_id' => 'sometimes|required|exists:clients,id',
                'user_id' => 'sometimes|required|exists:users,id',
                'status' => 'nullable|in:draft,active,completed,cancelled,on_hold',
                'priority' => 'nullable|in:low,medium,high,urgent',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'deadline' => 'nullable|date',
                'budget' => 'nullable|numeric|min:0',
                'estimated_cost' => 'nullable|numeric|min:0',
                'actual_cost' => 'nullable|numeric|min:0',
                'progress_percentage' => 'nullable|integer|min:0|max:100',
                'notes' => 'nullable|string',
                'metadata' => 'nullable|array',
            ]);

            // Validazioni aggiuntive per le date
            $endDate = $validated['end_date'] ?? $project->end_date;
            $deadline = $validated['deadline'] ?? $project->deadline;
            
            if ($endDate && $deadline) {
                if (Carbon::parse($deadline)->lt(Carbon::parse($endDate))) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The deadline cannot be earlier than the project end date',
                        'errors' => ['deadline' => ['The deadline must be on or after the end date']]
                    ], 422);
                }
            }

            // Automatically update progress_percentage if the project is completed
            if (isset($validated['status']) && $validated['status'] === 'completed') {
                $validated['progress_percentage'] = 100;
            }

            $project->update($validated);
            $project->load(['client', 'user']);
            $project->append(['is_overdue', 'duration_in_days', 'remaining_budget', 'budget_utilization']);

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'data' => $project
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $project = Project::findOrFail($id);

            // Controllo se il progetto può essere eliminato
            if ($project->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete an active project. Change the status first.'
                ], 409);
            }

            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get project statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_projects' => Project::count(),
                'active_projects' => Project::where('status', 'active')->count(),
                'completed_projects' => Project::where('status', 'completed')->count(),
                'draft_projects' => Project::where('status', 'draft')->count(),
                'cancelled_projects' => Project::where('status', 'cancelled')->count(),
                'on_hold_projects' => Project::where('status', 'on_hold')->count(),
                'overdue_projects' => Project::overdue()->count(),
                'high_priority_projects' => Project::where('priority', 'high')->count(),
                'urgent_priority_projects' => Project::where('priority', 'urgent')->count(),
                'total_budget' => Project::sum('budget'),
                'total_actual_cost' => Project::sum('actual_cost'),
                'average_progress' => Project::avg('progress_percentage'),
                'recent_projects' => Project::where('created_at', '>=', now()->subDays(30))->count()
            ];

            // Statistiche per status
            $statusStats = Project::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Statistiche per priorità
            $priorityStats = Project::selectRaw('priority, count(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'general' => $stats,
                    'by_status' => $statusStats,
                    'by_priority' => $priorityStats
                ],
                'message' => 'Project statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update project progress
     */
    public function updateProgress(Request $request, string $id): JsonResponse
    {
        try {
            $project = Project::findOrFail($id);

            $validated = $request->validate([
                'progress_percentage' => 'required|integer|min:0|max:100',
                'notes' => 'nullable|string'
            ]);

            // Automatically update status if needed
            if ($validated['progress_percentage'] == 100 && $project->status !== 'completed') {
                $validated['status'] = 'completed';
            } elseif ($validated['progress_percentage'] > 0 && $project->status === 'draft') {
                $validated['status'] = 'active';
            }

            $project->update($validated);
            $project->load(['client', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Project progress updated successfully',
                'data' => $project
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating progress',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
