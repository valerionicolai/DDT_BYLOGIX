<?php

namespace App\Filament\Resources\ProjectStateResource\Pages;

use App\Filament\Resources\ProjectStateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectState extends CreateRecord
{
    protected static string $resource = ProjectStateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}