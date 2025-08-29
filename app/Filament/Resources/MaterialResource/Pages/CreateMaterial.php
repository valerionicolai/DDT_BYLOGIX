<?php

namespace App\Filament\Resources\MaterialResource\Pages;

use App\Filament\Resources\MaterialResource;
use App\Filament\Resources\DocumentResource; // aggiunto
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMaterial extends CreateRecord
{
    protected static string $resource = MaterialResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $documentId = $this->getDocumentIdFromRequest();
        if($documentId) {
            $data['document_id'] = $documentId;
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        $documentId = $this->getDocumentIdFromRequest() ?? $this->record->document_id;
        
        // Reindirizza sempre alla view del documento associato
        if ($documentId) {
            return DocumentResource::getUrl('view', ['record' => $documentId]);
        }
        
        // Fallback sicuro
        return $this->getResource()::getUrl('index');
    }
    
    protected function getDocumentIdFromRequest(): ?int
    {
        if(request()->has('document_id')) {
            return (int) request()->query('document_id');
        }
        
        if(request()->has('document.id')) {
            return (int) request()->query('document.id');
        }
        
        return null;
    }
}