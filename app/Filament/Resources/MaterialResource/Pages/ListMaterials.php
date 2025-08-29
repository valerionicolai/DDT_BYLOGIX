<?php

namespace App\Filament\Resources\MaterialResource\Pages;

use App\Filament\Resources\MaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaterials extends ListRecords
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('scan_barcode')
                ->label('Scan Barcode')
                ->icon('heroicon-o-qr-code')
                ->action(function () {
                    $this->js('window.open("/scanner", "_blank")');
                }),
        ];
    }
}