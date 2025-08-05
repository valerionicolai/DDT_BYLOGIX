<?php

namespace App\Filament\Resources\ProjectStateResource\Pages;

use App\Filament\Resources\ProjectStateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProjectState extends ViewRecord
{
    protected static string $resource = ProjectStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}