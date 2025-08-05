<?php

namespace App\Filament\Resources\ProjectStateResource\Pages;

use App\Filament\Resources\ProjectStateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectState extends EditRecord
{
    protected static string $resource = ProjectStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}