<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    /**
     * Display a listing of the materials.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Material::with(['entryDocument', 'materialType']);

        // Apply filters
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('material_type_id')) {
            $query->byMaterialType($request->material_type_id);
        }

        if ($request->has('location')) {
            $query->byLocation($request->location);
        }

        if ($request->has('entry_document_id')) {
            $query->where('entry_document_id', $request->entry_document_id);
        }

        if ($request->has('expired')) {
            $query->expired();
        }

        if ($request->has('expiring_soon')) {
            $query->expiringSoon();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $materials = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $materials,
        ]);
    }

    /**
     * Store a newly created material in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'entry_document_id' => 'required|exists:entry_documents,id',
            'material_type_id' => 'required|exists:material_types,id',
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.001',
            'unit_of_measure' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'lot_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today',
            'location' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:ordered,received,used,returned',
            'properties' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $material = Material::create(array_merge(
                $validator->validated(),
                ['vat_rate' => $request->vat_rate ?? 22.00]
            ));

            $material->load(['entryDocument', 'materialType']);

            return response()->json([
                'success' => true,
                'message' => 'Material created successfully',
                'data' => $material,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create material',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified material.
     */
    public function show(Material $material): JsonResponse
    {
        $material->load(['entryDocument', 'materialType']);

        return response()->json([
            'success' => true,
            'data' => $material,
        ]);
    }

    /**
     * Update the specified material in storage.
     */
    public function update(Request $request, Material $material): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'material_type_id' => 'sometimes|exists:material_types,id',
            'description' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|numeric|min:0.001',
            'unit_of_measure' => 'sometimes|string|max:50',
            'unit_price' => 'sometimes|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'lot_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today',
            'location' => 'nullable|string|max:100',
            'status' => 'sometimes|string|in:ordered,received,used,returned',
            'properties' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $material->update($validator->validated());

            $material->load(['entryDocument', 'materialType']);

            return response()->json([
                'success' => true,
                'message' => 'Material updated successfully',
                'data' => $material,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update material',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified material from storage.
     */
    public function destroy(Material $material): JsonResponse
    {
        try {
            $material->delete();

            return response()->json([
                'success' => true,
                'message' => 'Material deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete material',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get materials statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_materials' => Material::count(),
            'received_materials' => Material::byStatus('received')->count(),
            'used_materials' => Material::byStatus('used')->count(),
            'expired_materials' => Material::expired()->count(),
            'expiring_soon' => Material::expiringSoon()->count(),
            'total_value' => Material::sum('total_price'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
