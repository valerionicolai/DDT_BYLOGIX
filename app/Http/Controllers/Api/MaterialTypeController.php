<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaterialType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class MaterialTypeController extends Controller
{
    /**
     * Display a listing of material types with filtering, searching, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = MaterialType::query();

            // Filtro per status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filtro per categoria
            if ($request->filled('category')) {
                $query->byCategory($request->category);
            }

            // Filtro per range di prezzo
            if ($request->filled('min_price')) {
                $query->where('default_price', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('default_price', '<=', $request->max_price);
            }

            // Filtro per unità di misura
            if ($request->filled('unit_of_measure')) {
                $query->where('unit_of_measure', $request->unit_of_measure);
            }

            // Ricerca testuale
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            }

            // Solo materiali attivi (se richiesto)
            if ($request->boolean('active_only')) {
                $query->active();
            }

            // Ordinamento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            
            $allowedSorts = ['name', 'category', 'default_price', 'unit_of_measure', 'status', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortDirection);
            }

            // Paginazione
            $perPage = min($request->get('per_page', 15), 100);
            $materialTypes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $materialTypes->items(),
                'pagination' => [
                    'current_page' => $materialTypes->currentPage(),
                    'last_page' => $materialTypes->lastPage(),
                    'per_page' => $materialTypes->perPage(),
                    'total' => $materialTypes->total(),
                    'from' => $materialTypes->firstItem(),
                    'to' => $materialTypes->lastItem(),
                ],
                'filters_applied' => $request->only(['status', 'category', 'min_price', 'max_price', 'unit_of_measure', 'search', 'active_only']),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero dei tipi di materiale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created material type.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:material_types,name',
                'description' => 'nullable|string|max:1000',
                'unit_of_measure' => 'required|string|max:50',
                'default_price' => 'nullable|numeric|min:0|max:999999.99',
                'category' => 'nullable|string|max:100',
                'properties' => 'nullable|array',
                'properties.*' => 'string|max:255',
                'status' => ['nullable', Rule::in(['active', 'inactive'])],
            ]);

            // Imposta status di default
            $validated['status'] = $validated['status'] ?? 'active';

            $materialType = MaterialType::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tipo di materiale creato con successo',
                'data' => $materialType
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errori di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nella creazione del tipo di materiale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified material type.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $materialType = MaterialType::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $materialType
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo di materiale non trovato'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero del tipo di materiale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified material type.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $materialType = MaterialType::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:material_types,name,' . $id,
                'description' => 'nullable|string|max:1000',
                'unit_of_measure' => 'sometimes|required|string|max:50',
                'default_price' => 'nullable|numeric|min:0|max:999999.99',
                'category' => 'nullable|string|max:100',
                'properties' => 'nullable|array',
                'properties.*' => 'string|max:255',
                'status' => ['nullable', Rule::in(['active', 'inactive'])],
            ]);

            $materialType->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tipo di materiale aggiornato con successo',
                'data' => $materialType->fresh()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo di materiale non trovato'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errori di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento del tipo di materiale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified material type.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $materialType = MaterialType::findOrFail($id);

            // Qui potresti aggiungere controlli per verificare se il materiale è utilizzato in progetti
            // Per ora procediamo con l'eliminazione diretta

            $materialType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo di materiale eliminato con successo'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo di materiale non trovato'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'eliminazione del tipo di materiale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get material types statistics.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total' => MaterialType::count(),
                'active' => MaterialType::where('status', 'active')->count(),
                'inactive' => MaterialType::where('status', 'inactive')->count(),
                'by_category' => MaterialType::selectRaw('category, COUNT(*) as count')
                    ->whereNotNull('category')
                    ->groupBy('category')
                    ->pluck('count', 'category'),
                'with_price' => MaterialType::whereNotNull('default_price')->count(),
                'without_price' => MaterialType::whereNull('default_price')->count(),
                'average_price' => MaterialType::whereNotNull('default_price')->avg('default_price'),
                'price_range' => [
                    'min' => MaterialType::whereNotNull('default_price')->min('default_price'),
                    'max' => MaterialType::whereNotNull('default_price')->max('default_price'),
                ],
                'units_of_measure' => MaterialType::selectRaw('unit_of_measure, COUNT(*) as count')
                    ->groupBy('unit_of_measure')
                    ->pluck('count', 'unit_of_measure'),
                'recent' => MaterialType::where('created_at', '>=', now()->subDays(30))->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero delle statistiche',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available categories.
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = MaterialType::whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->sort()
                ->values();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero delle categorie',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available units of measure.
     */
    public function unitsOfMeasure(): JsonResponse
    {
        try {
            $units = MaterialType::distinct()
                ->pluck('unit_of_measure')
                ->sort()
                ->values();

            return response()->json([
                'success' => true,
                'data' => $units
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero delle unità di misura',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
