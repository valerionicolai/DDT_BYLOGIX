<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'supplier',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'status',
        'barcode',
        'metadata',
        'created_date',
        'due_date'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_date' => 'datetime',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'created_date',
        'due_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Generate a unique barcode for the document
     */
    public function generateBarcode(): string
    {
        // Generate a barcode based on document ID and timestamp
        $prefix = strtoupper(substr($this->category ?? 'DOC', 0, 1));
        $id = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return $prefix . $id;
    }

    /**
     * Regenerate barcode for the document
     */
    public function regenerateBarcode(): bool
    {
        $this->barcode = $this->generateBarcode();
        return $this->save();
    }

    /**
     * Get the file size in human readable format
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if document is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'archived';
    }

    /**
     * Scope for searching documents by barcode
     */
    public function scopeByBarcode($query, $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for searching documents
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%")
              ->orWhere('supplier', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }
}