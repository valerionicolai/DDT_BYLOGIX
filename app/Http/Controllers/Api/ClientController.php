<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Client::query();

            // Filtro per status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Ricerca per nome, email o azienda
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%");
                });
            }

            // Ordinamento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginazione
            $perPage = $request->get('per_page', 15);
            $clients = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $clients,
                'message' => 'Clients retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving clients',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:clients,email',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
                'status' => 'nullable|in:active,inactive',
            ]);

            $client = Client::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully',
                'data' => $client
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
                'message' => 'Error creating client',
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
            $client = Client::with('projects')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $client,
                'message' => 'Client retrieved successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $client = Client::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:clients,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
                'status' => 'nullable|in:active,inactive',
            ]);

            $client->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully',
                'data' => $client->fresh()
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating client',
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
            $client = Client::findOrFail($id);

            // Verifica se il cliente ha progetti associati
            if ($client->projects()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete client: it has associated projects'
                ], 409);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_clients' => Client::count(),
                'active_clients' => Client::where('status', 'active')->count(),
                'inactive_clients' => Client::where('status', 'inactive')->count(),
                'prospect_clients' => Client::where('status', 'prospect')->count(),
                'clients_with_projects' => Client::has('projects')->count(),
                'recent_clients' => Client::where('created_at', '>=', now()->subDays(30))->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Client statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
