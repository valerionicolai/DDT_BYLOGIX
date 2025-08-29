<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use App\Filament\Resources\MaterialResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\Placeholder::make('client')
                            ->label('Client')
                            ->content(function ($record) {
                                if (!$record->client) {
                                    return 'No supplier assigned';
                                }
                                
                                return $record->client->name . ($record->client->company ? ' (' . $record->client->company . ')' : '');
                            }),
                        Forms\Components\TextInput::make('title')
                            ->label('Doc Name')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->disabled()
                            ->columnSpanFull(),
                        
                        Forms\Components\Placeholder::make('document_type')
                            ->label('Document Type')
                            ->content(function ($record) {
                                if (!$record->documentType) {
                                    return 'No document type assigned';
                                }
                                
                                return new \Illuminate\Support\HtmlString(
                                    '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full" style="background-color: ' . ($record->documentType->color ?? '#6b7280') . '20; color: ' . ($record->documentType->color ?? '#6b7280') . ';">' . 
                                    $record->documentType->name . 
                                    '</span>'
                                );
                            }),
                        
                        Forms\Components\Placeholder::make('document_category')
                            ->label('Category')
                            ->content(function ($record) {
                                if (!$record->documentCategory) {
                                    return 'No category assigned';
                                }
                                
                                return new \Illuminate\Support\HtmlString(
                                    '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full" style="background-color: ' . ($record->documentCategory->color ?? '#6b7280') . '20; color: ' . ($record->documentCategory->color ?? '#6b7280') . ';">' . 
                                    $record->documentCategory->name . 
                                    '</span>'
                                );
                            }),
                        
                        

                    ])
                    ->columns(2),

                Forms\Components\Section::make('File Information')
                    ->schema([
                        Forms\Components\Placeholder::make('file_attachment')
                            ->label('Document Attachment')
                            ->content(function ($record) {
                                if (!$record->file_path) {
                                    return 'No file attached';
                                }
                                
                                if (!$record->file_exists) {
                                    return 'File not found';
                                }
                                
                                $fileName = basename($record->file_path);
                                $fileUrl = $record->file_url;
                                
                                // Match the icon logic from the table
                                $icon = match (true) {
                                    $record->is_pdf => '📄',
                                    $record->is_image => '🖼️',
                                    default => '📎'
                                };
                                
                                return new \Illuminate\Support\HtmlString(
                                    '<a href="' . $fileUrl . '" target="_blank" class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-500 font-medium">
                                        <span class="text-lg">' . $icon . '</span>
                                        ' . $fileName . '
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>'
                                );
                            }),
                    ]),

                Forms\Components\Section::make('Status & Dates')
                    ->schema([
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('barcode')
                            ->label('Barcode')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('created_date')
                            ->label('Created Date')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('due_date')
                            ->label('Due Date')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Placeholder::make('metadata_display')
                            ->label('Metadata')
                            ->content(function ($record) {
                                $state = $record->metadata ?? null;
                                if (!is_array($state) || empty($state)) {
                                    return '—';
                                }

                                $items = '';
                                foreach ($state as $key => $value) {
                                    $items .= '<div class="flex items-center justify-between py-0.5">'
                                        . '<span class="text-gray-500">' . e($key) . '</span>'
                                        . '<span class="font-medium">' . e(is_scalar($value) ? (string) $value : json_encode($value)) . '</span>'
                                        . '</div>';
                                }

                                return new \Illuminate\Support\HtmlString($items);
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add_material')
                ->label('Add material')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->url(fn () => MaterialResource::getUrl('create', [
                    'document_id' => $this->getRecord()->id,
                    'document.id' => $this->getRecord()->id,
                ])),

            Actions\Action::make('download')
                ->label('Download File')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn () => $this->getRecord()->file_url, shouldOpenInNewTab: true)
                ->visible(fn () => $this->getRecord()->file_exists),
            
            Actions\Action::make('view_file')
                ->label('View File')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => $this->getRecord()->file_url, shouldOpenInNewTab: true)
                ->visible(fn () => $this->getRecord()->file_exists && ($this->getRecord()->is_pdf || $this->getRecord()->is_image)),
            
            Actions\Action::make('scan_barcode')
                ->label('Scan Barcode')
                ->icon('heroicon-o-qr-code')
                ->color('success')
                ->url('/scanner', shouldOpenInNewTab: true),
            
            Actions\EditAction::make(),
            
            Actions\DeleteAction::make()
                ->before(function () {
                    // Delete associated file when deleting the record
                    $this->getRecord()->deleteFile();
                }),
        ];
    }
    
    public function getRelationManagers(): array
    {
        return [
            DocumentResource\RelationManagers\MaterialsRelationManager::class,
        ];
    }
}
