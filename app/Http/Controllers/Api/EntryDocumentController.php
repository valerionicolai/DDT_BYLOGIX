<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EntryDocument;
use App\Models\Material;
use App\Services\BarcodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EntryDocumentController extends Controller
{
    /**
     * Display a listing of the entry documents.
     */
    public function index(Request $request): JsonResponse
    {
        $query = EntryDocument::with(['project', 'user', 'materials.materialType']);

        // Apply filters
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('document_type')) {
            $query->byType($request->document_type);
        }

        if ($request->has('supplier')) {
            $query->bySupplier($request->supplier);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'document_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $documents = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    /**
     * Store a newly created entry document with materials in storage.
     */
    public function store(Request $request, BarcodeService $barcodeService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'document_number' => 'nullable|string|unique:entry_documents',
            'document_type' => 'required|string|in:entry,delivery,invoice,receipt',
            'supplier_name' => 'required|string|max:255',
            'supplier_vat' => 'nullable|string|max:20',
            'supplier_address' => 'nullable|string|max:500',
            'document_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:document_date',
            'currency' => 'nullable|string|size:3',
            'status' => 'nullable|string|in:draft,confirmed,received,cancelled',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
            'project_id' => 'nullable|exists:projects,id',
            
            // Materials validation
            'materials' => 'required|array|min:1',
            'materials.*.material_type_id' => 'required|exists:material_types,id',
            'materials.*.description' => 'required|string|max:255',
            'materials.*.quantity' => 'required|numeric|min:0.001',
            'materials.*.unit_of_measure' => 'required|string|max:50',
            'materials.*.unit_price' => 'required|numeric|min:0',
            'materials.*.vat_rate' => 'nullable|numeric|min:0|max:100',
            'materials.*.lot_number' => 'nullable|string|max:100',
            'materials.*.expiry_date' => 'nullable|date|after:today',
            'materials.*.location' => 'nullable|string|max:100',
            'materials.*.status' => 'nullable|string|in:ordered,received,used,returned',
            'materials.*.properties' => 'nullable|array',
            'materials.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Generate document number if not provided
            $documentNumber = $request->document_number ?? EntryDocument::generateDocumentNumber();

            // Create the entry document
            $entryDocument = EntryDocument::create([
                'document_number' => $documentNumber,
                'document_type' => $request->document_type,
                'supplier_name' => $request->supplier_name,
                'supplier_vat' => $request->supplier_vat,
                'supplier_address' => $request->supplier_address,
                'document_date' => $request->document_date,
                'delivery_date' => $request->delivery_date,
                'currency' => $request->currency ?? 'EUR',
                'status' => $request->status ?? 'draft',
                'notes' => $request->notes,
                'metadata' => $request->metadata,
                'project_id' => $request->project_id,
                'user_id' => auth()->id(),
                'total_amount' => 0, // Will be calculated after materials are added
                'vat_amount' => 0,
                'net_amount' => 0,
            ]);

            // Create associated materials
            $totalNet = 0;
            $totalVat = 0;

            foreach ($request->materials as $materialData) {
                $material = new Material([
                    'entry_document_id' => $entryDocument->id,
                    'material_type_id' => $materialData['material_type_id'],
                    'description' => $materialData['description'],
                    'quantity' => $materialData['quantity'],
                    'unit_of_measure' => $materialData['unit_of_measure'],
                    'unit_price' => $materialData['unit_price'],
                    'vat_rate' => $materialData['vat_rate'] ?? 22.00,
                    'lot_number' => $materialData['lot_number'] ?? null,
                    'expiry_date' => $materialData['expiry_date'] ?? null,
                    'location' => $materialData['location'] ?? null,
                    'status' => $materialData['status'] ?? 'received',
                    'properties' => $materialData['properties'] ?? null,
                    'notes' => $materialData['notes'] ?? null,
                ]);

                // Calculate totals (this will be done automatically by the model's boot method)
                $material->save();

                $totalNet += $material->total_price;
                $totalVat += $material->vat_amount;
            }

            // Update document totals
            $entryDocument->update([
                'net_amount' => $totalNet,
                'vat_amount' => $totalVat,
                'total_amount' => $totalNet + $totalVat,
            ]);

            // Generate barcode for the document
            $barcode = $barcodeService->generateForModel(
                $entryDocument,
                'CODE128',
                'png',
                ['width' => 2, 'height' => 30]
            );

            DB::commit();

            // Load relationships for response
            $entryDocument->load(['project', 'user', 'materials.materialType']);

            return response()->json([
                'success' => true,
                'message' => 'Entry document created successfully',
                'data' => $entryDocument,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create entry document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified entry document.
     */
    public function show(EntryDocument $entryDocument): JsonResponse
    {
        $entryDocument->load(['project', 'user', 'materials.materialType', 'barcodes' => function($query) {
            $query->where('is_active', true);
        }]);

        return response()->json([
            'success' => true,
            'data' => $entryDocument,
        ]);
    }

    /**
     * Update the specified entry document in storage.
     */
    public function update(Request $request, EntryDocument $entryDocument): JsonResponse
    {
        // Check if document can be edited
        if (!$entryDocument->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'This document cannot be edited in its current status',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'document_number' => [
                'nullable',
                'string',
                Rule::unique('entry_documents')->ignore($entryDocument->id),
            ],
            'document_type' => 'sometimes|string|in:entry,delivery,invoice,receipt',
            'supplier_name' => 'sometimes|string|max:255',
            'supplier_vat' => 'nullable|string|max:20',
            'supplier_address' => 'nullable|string|max:500',
            'document_date' => 'sometimes|date',
            'delivery_date' => 'nullable|date|after_or_equal:document_date',
            'currency' => 'nullable|string|size:3',
            'status' => 'sometimes|string|in:draft,confirmed,received,cancelled',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $entryDocument->update($validator->validated());

            $entryDocument->load(['project', 'user', 'materials.materialType']);

            return response()->json([
                'success' => true,
                'message' => 'Entry document updated successfully',
                'data' => $entryDocument,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update entry document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified entry document from storage.
     */
    public function destroy(EntryDocument $entryDocument): JsonResponse
    {
        // Check if document can be deleted
        if (!$entryDocument->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'This document cannot be deleted in its current status',
            ], 403);
        }

        try {
            $entryDocument->delete();

            return response()->json([
                'success' => true,
                'message' => 'Entry document deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete entry document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get materials for a specific entry document.
     */
    public function materials(EntryDocument $entryDocument): JsonResponse
    {
        $materials = $entryDocument->materials()->with('materialType')->get();

        return response()->json([
            'success' => true,
            'data' => $materials,
        ]);
    }

    /**
     * Get document statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_documents' => EntryDocument::count(),
            'draft_documents' => EntryDocument::byStatus('draft')->count(),
            'confirmed_documents' => EntryDocument::byStatus('confirmed')->count(),
            'received_documents' => EntryDocument::byStatus('received')->count(),
            'total_value' => EntryDocument::sum('total_amount'),
            'monthly_value' => EntryDocument::whereMonth('document_date', now()->month)
                                          ->whereYear('document_date', now()->year)
                                          ->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get the active barcode for a document.
     */
    public function barcode(EntryDocument $entryDocument, BarcodeService $barcodeService): JsonResponse
    {
        $activeBarcode = $entryDocument->activeBarcode();

        if (!$activeBarcode) {
            return response()->json([
                'success' => false,
                'message' => 'No active barcode found for this document',
            ], 404);
        }

        $barcodeData = [
            'id' => $activeBarcode->id,
            'code' => $activeBarcode->code,
            'type' => $activeBarcode->type,
            'format' => $activeBarcode->format,
            'is_active' => $activeBarcode->is_active,
            'generated_at' => $activeBarcode->generated_at,
            'expires_at' => $activeBarcode->expires_at,
            'image_url' => $barcodeService->getBarcodeImageUrl($activeBarcode),
            'base64' => $barcodeService->getBarcodeAsBase64($activeBarcode),
        ];

        return response()->json([
            'success' => true,
            'data' => $barcodeData,
        ]);
    }

    /**
     * Generate a new barcode for a document (deactivates the old one).
     */
    public function generateBarcode(EntryDocument $entryDocument, Request $request, BarcodeService $barcodeService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|in:CODE128,CODE39,EAN13,EAN8',
            'format' => 'nullable|string|in:png,svg,jpg,html',
            'width' => 'nullable|integer|min:1|max:10',
            'height' => 'nullable|integer|min:10|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $type = $request->get('type', 'CODE128');
            $format = $request->get('format', 'png');
            $options = [
                'width' => $request->get('width', 2),
                'height' => $request->get('height', 30),
            ];

            $barcode = $barcodeService->replaceBarcode($entryDocument, $type, $format, $options);

            $barcodeData = [
                'id' => $barcode->id,
                'code' => $barcode->code,
                'type' => $barcode->type,
                'format' => $barcode->format,
                'is_active' => $barcode->is_active,
                'generated_at' => $barcode->generated_at,
                'expires_at' => $barcode->expires_at,
                'image_url' => $barcodeService->getBarcodeImageUrl($barcode),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Barcode generated successfully',
                'data' => $barcodeData,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate barcode',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
