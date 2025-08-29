<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $projectId = request()->query('project_id');
        if ($projectId) {
            $data['project_id'] = (int) $projectId;
        }

        $clientId = request()->query('client_id');
        if ($clientId) {
            $data['client_id'] = (int) $clientId;
        }

        return $data;
    }
}
