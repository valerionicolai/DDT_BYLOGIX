<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $status = $request->get('status');
        $category = $request->get('category');
        $supplier = $request->get('supplier');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = Document::query();

        // Apply filters
        if ($search) {
            $query->search($search);
        }

        if ($status) {
            $query->byStatus($status);
        }

        if ($category) {
            $query->byCategory($category);
        }

        if ($supplier) {
            $query->where('supplier', 'like', "%{$supplier}%");
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        $documents = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    /**
     * Store a newly created document
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'supplier' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            'status' => 'nullable|in:draft,active,archived',
            'metadata' => 'nullable|array',
            'created_date' => 'nullable|date',
            'due_date' => 'nullable|date|after:created_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['status'] = $data['status'] ?? 'draft';
        $data['created_date'] = $data['created_date'] ?? now();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getMimeType();
        }

        $document = Document::create($data);
        
        // Generate barcode after creation
        $document->barcode = $document->generateBarcode();
        $document->save();

        return response()->json([
            'success' => true,
            'message' => 'Document created successfully',
            'data' => $document
        ], 201);
    }

    /**
     * Display the specified document
     */
    public function show(Document $document): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $document
        ]);
    }

    /**
     * Update the specified document
     */
    public function update(Request $request, Document $document): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|string|max:100',
            'supplier' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'status' => 'nullable|in:draft,active,archived',
            'metadata' => 'nullable|array',
            'created_date' => 'nullable|date',
            'due_date' => 'nullable|date|after:created_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getMimeType();
        }

        $document->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'data' => $document
        ]);
    }

    /**
     * Remove the specified document
     */
    public function destroy(Document $document): JsonResponse
    {
        // Delete associated file
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    /**
     * Search documents by barcode
     */
    public function searchByBarcode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $documents = Document::byBarcode($request->barcode)->get();

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Regenerate barcode for a document
     */
    public function regenerateBarcode(Document $document): JsonResponse
    {
        $success = $document->regenerateBarcode();

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Barcode regenerated successfully',
                'data' => $document
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to regenerate barcode'
        ], 500);
    }

    /**
     * Export documents to CSV format
     */
    public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $category = $request->get('category');
        $supplier = $request->get('supplier');

        $query = Document::query();

        // Apply same filters as index
        if ($search) {
            $query->search($search);
        }

        if ($status) {
            $query->byStatus($status);
        }

        if ($category) {
            $query->byCategory($category);
        }

        if ($supplier) {
            $query->where('supplier', 'like', "%{$supplier}%");
        }

        $documents = $query->get();

        $csvContent = $this->generateCsvContent($documents);

        $filename = 'documents_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Download document file
     */
    public function download(Document $document): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        
        return response()->download($filePath, $document->file_name);
    }

    /**
     * Generate CSV content from documents collection
     */
    private function generateCsvContent($documents): string
    {
        $csvContent = '';
        
        // CSV Headers
        $headers = [
            'ID',
            'Title',
            'Description',
            'Category',
            'Supplier',
            'Barcode',
            'Status',
            'File Name',
            'File Size',
            'File Type',
            'Created Date',
            'Due Date',
            'Created at',
            'Updated at'
        ];
        
        $csvContent .= implode(',', array_map(function($header) {
            return '"' . str_replace('"', '""', $header) . '"';
        }, $headers)) . "\n";
        
        // CSV Data
        foreach ($documents as $document) {
            $row = [
                $document->id,
                $document->title,
                $document->description,
                $document->category,
                $document->supplier,
                $document->barcode,
                $document->status,
                $document->file_name,
                $document->formatted_file_size,
                $document->file_type,
                $document->created_date ? $document->created_date->format('Y-m-d H:i:s') : '',
                $document->due_date ? $document->due_date->format('Y-m-d H:i:s') : '',
                $document->created_at->format('Y-m-d H:i:s'),
                $document->updated_at->format('Y-m-d H:i:s')
            ];
            
            $csvContent .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field ?? '') . '"';
            }, $row)) . "\n";
        }
        
        return $csvContent;
    }
}