<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialType;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    protected $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Display a listing of materials with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Material::with(['materialType', 'parent', 'children', 'documents', 'projects']);

            // Apply filters
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('qr_code', 'like', "%{$search}%");
                });
            }

            if ($request->has('material_type_id')) {
                $query->where('material_type_id', $request->get('material_type_id'));
            }

            if ($request->has('state')) {
                $query->where('state', $request->get('state'));
            }

            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->get('parent_id'));
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $materials = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $materials->items(),
                'pagination' => [
                    'current_page' => $materials->currentPage(),
                    'last_page' => $materials->lastPage(),
                    'per_page' => $materials->perPage(),
                    'total' => $materials->total(),
                    'from' => $materials->firstItem(),
                    'to' => $materials->lastItem(),
                ],
                'filters' => [
                    'search' => $request->get('search'),
                    'material_type_id' => $request->get('material_type_id'),
                    'state' => $request->get('state'),
                    'parent_id' => $request->get('parent_id'),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching materials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero dei materiali',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created material
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'material_type_id' => 'required|exists:material_types,id',
                'parent_id' => 'nullable|exists:materials,id',
                'state' => 'required|in:available,in_use,maintenance,damaged,disposed',
                'quantity' => 'nullable|numeric|min:0',
                'unit_of_measure' => 'nullable|string|max:50',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'project_id' => 'nullable|exists:projects,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dati di validazione non validi',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $material = Material::create($validator->validated());

            // Generate QR code
            $qrCodeData = $this->qrCodeService->generateQRCodeForMaterial($material);
            if ($qrCodeData && isset($qrCodeData['path'])) {
                $material->update(['qr_code_path' => $qrCodeData['path']]);
            }

            DB::commit();

            $material->load(['materialType', 'parent', 'children', 'documents', 'projects']);

            return response()->json([
                'success' => true,
                'message' => 'Materiale creato con successo',
                'data' => $material
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating material: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nella creazione del materiale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified material
     */
    public function show(string $id): JsonResponse
    {
        try {
            $material = Material::with(['materialType', 'parent', 'children', 'documents', 'projects'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $material
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Materiale non trovato',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified material
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $material = Material::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'material_type_id' => 'sometimes|required|exists:material_types,id',
                'parent_id' => 'nullable|exists:materials,id',
                'state' => 'sometimes|required|in:available,in_use,maintenance,damaged,disposed',
                'quantity' => 'nullable|numeric|min:0',
                'unit_of_measure' => 'nullable|string|max:50',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'project_id' => 'nullable|exists:projects,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dati di validazione non validi',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prevent circular parent relationships
            if ($request->has('parent_id') && $request->parent_id) {
                if ($this->wouldCreateCircularReference($material, $request->parent_id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossibile impostare questo materiale come genitore: creerebbe una relazione circolare'
                    ], 422);
                }
            }

            DB::beginTransaction();

            $material->update($validator->validated());

            // Regenerate QR code if name changed
            if ($request->has('name') && $request->name !== $material->getOriginal('name')) {
                $qrCodeData = $this->qrCodeService->generateQRCodeForMaterial($material);
                if ($qrCodeData && isset($qrCodeData['path'])) {
                    $material->update(['qr_code_path' => $qrCodeData['path']]);
                }
            }

            DB::commit();

            $material->load(['materialType', 'parent', 'children', 'documents', 'projects']);

            return response()->json([
                'success' => true,
                'message' => 'Materiale aggiornato con successo',
                'data' => $material
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating material: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'aggiornamento del materiale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified material
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $material = Material::findOrFail($id);

            // Check if material has children
            if ($material->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossibile eliminare il materiale: ha materiali figli associati'
                ], 422);
            }

            DB::beginTransaction();

            // Delete QR code file
            if ($material->qr_code) {
                $this->qrCodeService->deleteQRCode($material->qr_code, 'material');
            }

            $material->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Materiale eliminato con successo'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting material: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'eliminazione del materiale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get material statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_materials' => Material::count(),
                'by_state' => Material::select('state', DB::raw('count(*) as count'))
                    ->groupBy('state')
                    ->pluck('count', 'state'),
                'by_type' => Material::join('material_types', 'materials.material_type_id', '=', 'material_types.id')
                    ->select('material_types.name', DB::raw('count(*) as count'))
                    ->groupBy('material_types.name')
                    ->pluck('count', 'name'),
                'recent_materials' => Material::where('created_at', '>=', now()->subDays(7))->count(),
                'materials_needing_maintenance' => Material::where('state', 'maintenance')->count(),
                'damaged_materials' => Material::where('state', 'damaged')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching material stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero delle statistiche',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search materials by QR code
     */
    public function searchByQRCode(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'qr_code' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code richiesto',
                    'errors' => $validator->errors()
                ], 422);
            }

            $material = Material::with(['materialType', 'parent', 'children', 'documents', 'projects'])
                ->where('qr_code', $request->qr_code)
                ->first();

            if (!$material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Materiale non trovato con questo QR code'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $material
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching material by QR code: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nella ricerca del materiale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Regenerate QR code for a material
     */
    public function regenerateQRCode(string $id): JsonResponse
    {
        try {
            $material = Material::findOrFail($id);

            DB::beginTransaction();

            // Delete old QR code
            if ($material->qr_code) {
                $this->qrCodeService->deleteQRCode($material->qr_code, 'material');
            }

            // Generate new QR code
            $qrCodeData = $this->qrCodeService->generateQRCodeForMaterial($material);
            
            if ($qrCodeData && isset($qrCodeData['path'])) {
                $material->update(['qr_code_path' => $qrCodeData['path']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'QR code rigenerato con successo',
                'data' => [
                    'qr_code' => $material->qr_code,
                    'qr_code_path' => $material->qr_code_path,
                    'qr_code_url' => $qrCodeData['public_url'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error regenerating QR code: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nella rigenerazione del QR code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export materials to CSV
     */
    public function exportCsv(Request $request): JsonResponse
    {
        try {
            $query = Material::with(['materialType', 'parent']);

            // Apply same filters as index
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('material_type_id')) {
                $query->where('material_type_id', $request->get('material_type_id'));
            }

            if ($request->has('state')) {
                $query->where('state', $request->get('state'));
            }

            $materials = $query->get();

            $csvData = [];
            $csvData[] = ['ID', 'Nome', 'Descrizione', 'Tipo', 'Stato', 'Quantità', 'Unità di Misura', 'Posizione', 'Genitore', 'QR Code', 'Creato il'];

            foreach ($materials as $material) {
                $csvData[] = [
                    $material->id,
                    $material->name,
                    $material->description,
                    $material->materialType->name ?? '',
                    $material->state,
                    $material->quantity,
                    $material->unit_of_measure,
                    $material->location,
                    $material->parent->name ?? '',
                    $material->qr_code,
                    $material->created_at->format('Y-m-d H:i:s'),
                ];
            }

            $filename = 'materials_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
            $filePath = storage_path('app/exports/' . $filename);

            // Create exports directory if it doesn't exist
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            $file = fopen($filePath, 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);

            return response()->json([
                'success' => true,
                'message' => 'Export CSV generato con successo',
                'data' => [
                    'filename' => $filename,
                    'path' => $filePath,
                    'download_url' => route('api.materials.download-export', ['filename' => $filename])
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error exporting materials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore nell\'export dei materiali',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if setting a parent would create a circular reference
     */
    private function wouldCreateCircularReference(Material $material, int $parentId): bool
    {
        $currentParent = Material::find($parentId);
        
        while ($currentParent) {
            if ($currentParent->id === $material->id) {
                return true;
            }
            $currentParent = $currentParent->parent;
        }
        
        return false;
    }
}
