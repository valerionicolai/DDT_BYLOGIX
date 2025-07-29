<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'title',
        'description',
        'document_type',
        'supplier_name',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'project_id',
        'client_id',
        'user_id',
        'status',
        'document_date',
        'amount',
        'reference_number',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'document_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Relazione con il progetto
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relazione con il cliente
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relazione con l'utente che ha creato il documento
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
