<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Document::with(['project', 'client', 'user']);

            // Filtro per tipo di documento
            if ($request->has('document_type')) {
                $query->where('document_type', $request->document_type);
            }

            // Filtro per status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filtro per cliente
            if ($request->has('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            // Filtro per progetto
            if ($request->has('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            // Filtro per fornitore
            if ($request->has('supplier_name')) {
                $query->where('supplier_name', 'like', "%{$request->supplier_name}%");
            }

            // Filtro per range di date
            if ($request->has('date_from')) {
                $query->where('document_date', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->where('document_date', '<=', $request->date_to);
            }

            // Ricerca per titolo o descrizione
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%");
                });
            }

            // Ordinamento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginazione
            $perPage = $request->get('per_page', 15);
            $documents = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $documents,
                'message' => 'Documenti recuperati con successo'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero dei documenti',
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
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'document_type' => 'required|string|max:255',
                'supplier_name' => 'nullable|string|max:255',
                'file_path' => 'nullable|string',
                'file_name' => 'nullable|string',
                'file_size' => 'nullable|string',
                'mime_type' => 'nullable|string',
                'project_id' => 'nullable|exists:projects,id',
                'client_id' => 'nullable|exists:clients,id',
                'user_id' => 'required|exists:users,id',
                'status' => 'nullable|in:draft,active,archived,deleted',
                'document_date' => 'nullable|date',
                'amount' => 'nullable|numeric|min:0',
                'reference_number' => 'nullable|string|max:255',
                'metadata' => 'nullable|array',
            ]);

            $document = Document::create($validated);
            $document->load(['project', 'client', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Documento creato con successo',
                'data' => $document
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione del documento',
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
            $document = Document::with(['project', 'client', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $document,
                'message' => 'Documento recuperato con successo'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Documento non trovato'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nel recupero del documento',
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
            $document = Document::findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'document_type' => 'sometimes|required|string|max:255',
                'supplier_name' => 'nullable|string|max:255',
                'file_path' => 'nullable|string',
                'file_name' => 'nullable|string',
                'file_size' => 'nullable|string',
                'mime_type' => 'nullable|string',
                'project_id' => 'nullable|exists:projects,id',
                'client_id' => 'nullable|exists:clients,id',
                'status' => 'nullable|in:draft,active,archived,deleted',
                'document_date' => 'nullable|date',
                'amount' => 'nullable|numeric|min:0',
                'reference_number' => 'nullable|string|max:255',
                'metadata' => 'nullable|array',
            ]);

            $document->update($validated);
            $document->load(['project', 'client', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Documento aggiornato con successo',
                'data' => $document
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Documento non trovato'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore di validazione',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'aggiornamento del documento',
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
            $document = Document::findOrFail($id);
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Documento eliminato con successo'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Documento non trovato'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'eliminazione del documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export documents to CSV format
     */
    public function exportCsv(Request $request)
    {
        try {
            $query = Document::with(['project', 'client', 'user']);

            // Applica gli stessi filtri dell'index per coerenza
            if ($request->has('document_type')) {
                $query->where('document_type', $request->document_type);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            if ($request->has('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            if ($request->has('supplier_name')) {
                $query->where('supplier_name', 'like', "%{$request->supplier_name}%");
            }

            if ($request->has('date_from')) {
                $query->where('document_date', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->where('document_date', '<=', $request->date_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%");
                });
            }

            // Ordinamento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $documents = $query->get();

            // Genera il contenuto CSV
            $csvContent = $this->generateCsvContent($documents);

            // Genera il nome del file con timestamp
            $filename = 'documents_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

            return response($csvContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'esportazione CSV',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate CSV content from documents collection
     */
    private function generateCsvContent($documents): string
    {
        $csvData = [];
        
        // Header CSV
        $csvData[] = [
            'ID',
            'Titolo',
            'Descrizione',
            'Tipo Documento',
            'Nome Fornitore',
            'Progetto',
            'Cliente',
            'Utente',
            'Status',
            'Data Documento',
            'Importo',
            'Numero Riferimento',
            'Nome File',
            'Dimensione File',
            'Tipo MIME',
            'Data Creazione',
            'Data Aggiornamento'
        ];

        // Dati dei documenti
        foreach ($documents as $document) {
            $csvData[] = [
                $document->id,
                $document->title,
                $document->description,
                $document->document_type,
                $document->supplier_name,
                $document->project ? $document->project->name : '',
                $document->client ? $document->client->name : '',
                $document->user ? $document->user->name : '',
                $document->status,
                $document->document_date ? $document->document_date->format('Y-m-d') : '',
                $document->amount,
                $document->reference_number,
                $document->file_name,
                $document->file_size,
                $document->mime_type,
                $document->created_at->format('Y-m-d H:i:s'),
                $document->updated_at->format('Y-m-d H:i:s')
            ];
        }

        // Converte l'array in stringa CSV
        $output = fopen('php://temp', 'r+');
        
        foreach ($csvData as $row) {
            fputcsv($output, $row, ';'); // Usa punto e virgola come separatore per compatibilità con Excel italiano
        }
        
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        // Aggiungi BOM per supporto UTF-8 in Excel
        return "\xEF\xBB\xBF" . $csvContent;
    }
}
