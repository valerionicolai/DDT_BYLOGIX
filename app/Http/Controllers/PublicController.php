<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Document;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PublicController extends Controller
{
    protected QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Handle QR code scanning and redirect to appropriate resource
     */
    public function scanQRCode(Request $request, string $qrCode): RedirectResponse|JsonResponse
    {
        try {
            // Decode the QR code to get type and ID
            $decoded = $this->qrCodeService->decodeQRCode($qrCode);
            
            if (!$decoded) {
                return $this->handleInvalidQRCode($request);
            }

            $type = $decoded['type'];
            $id = $decoded['id'];

            // Redirect based on type
            switch ($type) {
                case 'material':
                    return redirect()->route('public.material', ['id' => $id]);
                case 'document':
                    return redirect()->route('public.document', ['id' => $id]);
                default:
                    return $this->handleInvalidQRCode($request);
            }
        } catch (\Exception $e) {
            return $this->handleInvalidQRCode($request, $e->getMessage());
        }
    }

    /**
     * Display public material details
     */
    public function showMaterial(Request $request, int $id): View|JsonResponse
    {
        try {
            $material = Material::with(['document', 'materialType'])
                ->findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $material->id,
                        'description' => $material->description,
                        'state' => $material->state->value,
                        'state_label' => $material->state->getLabel(),
                        'quantity' => $material->quantity,
                        'location' => $material->location,
                        'due_date' => $material->due_date,
                        'material_type' => $material->materialType->name,
                        'document' => $material->document ? [
                            'id' => $material->document->id,
                            'title' => $material->document->title,
                            'description' => $material->document->description,
                        ] : null,
                        'qr_code' => $material->qr_code,
                        'created_at' => $material->created_at,
                        'updated_at' => $material->updated_at,
                    ]
                ]);
            }

            return view('public.material', compact('material'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material not found'
                ], 404);
            }

            return view('public.not-found', [
                'type' => 'Material',
                'message' => 'The requested material could not be found.'
            ]);
        }
    }

    /**
     * Display public document details
     */
    public function showDocument(Request $request, int $id): View|JsonResponse
    {
        try {
            $document = Document::with(['materials', 'documentType', 'documentCategory'])
                ->findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $document->id,
                        'title' => $document->title,
                        'description' => $document->description,
                        'document_type' => $document->documentType->name,
                        'document_category' => $document->documentCategory->name,
                        'file_path' => $document->file_path,
                        'file_size' => $document->file_size,
                        'mime_type' => $document->mime_type,
                        'materials_count' => $document->materials->count(),
                        'materials' => $document->materials->map(function ($material) {
                            return [
                                'id' => $material->id,
                                'description' => $material->description,
                                'state' => $material->state->value,
                                'state_label' => $material->state->getLabel(),
                            ];
                        }),
                        'created_at' => $document->created_at,
                        'updated_at' => $document->updated_at,
                    ]
                ]);
            }

            return view('public.document', compact('document'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            return view('public.not-found', [
                'type' => 'Document',
                'message' => 'The requested document could not be found.'
            ]);
        }
    }

    /**
     * API endpoint to get QR code image
     */
    public function getQRCodeImage(Request $request, string $type, int $id): JsonResponse
    {
        try {
            $qrCodeData = null;
            
            switch ($type) {
                case 'material':
                    $material = Material::findOrFail($id);
                    $qrCodeData = $this->qrCodeService->generateQRCodeForMaterial($material);
                    break;
                case 'document':
                    $document = Document::findOrFail($id);
                    $qrCodeData = $this->qrCodeService->generateQRCodeForDocument($document);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid type'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'qr_code_url' => $qrCodeData['url'],
                    'qr_code_path' => $qrCodeData['path'],
                    'public_url' => $qrCodeData['public_url'],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle invalid QR codes
     */
    private function handleInvalidQRCode(Request $request, string $error = null): View|RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code',
                'error' => $error
            ], 400);
        }

        return view('public.invalid-qr', [
            'message' => 'The QR code is invalid or has expired.',
            'error' => $error
        ]);
    }

    /**
     * QR Code scanner page
     */
    public function scanner(): View
    {
        return view('public.scanner');
    }

    /**
     * API endpoint for batch QR code generation
     */
    public function generateBatchQRCodes(Request $request): JsonResponse
    {
        $request->validate([
            'materials' => 'array',
            'materials.*' => 'integer|exists:materials,id',
            'documents' => 'array',
            'documents.*' => 'integer|exists:documents,id',
        ]);

        try {
            $results = [];

            // Generate QR codes for materials
            if ($request->has('materials')) {
                $materials = Material::whereIn('id', $request->materials)->get();
                $materialQRCodes = $this->qrCodeService->generateBatchQRCodes($materials, 'material');
                $results['materials'] = $materialQRCodes;
            }

            // Generate QR codes for documents
            if ($request->has('documents')) {
                $documents = Document::whereIn('id', $request->documents)->get();
                $documentQRCodes = $this->qrCodeService->generateBatchQRCodes($documents, 'document');
                $results['documents'] = $documentQRCodes;
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'QR codes generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate batch QR codes: ' . $e->getMessage()
            ], 500);
        }
    }
}
