<?php

namespace App\Filament\Resources\MaterialResource\Pages;

use App\Filament\Resources\MaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Placeholder;

class ViewMaterial extends ViewRecord
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit Material')
                ->icon('heroicon-o-pencil-square'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Header con informazioni principali
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('materialType.name')
                                        ->label('Material Type')
                                        ->size(Components\TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->icon('heroicon-o-cube'),

                                    Components\TextEntry::make('barcode')
                                        ->label('Barcode')
                                        ->fontFamily('mono')
                                        ->badge()
                                        ->color('gray')
                                        ->icon('heroicon-o-qr-code')
                                        ->copyable()
                                        ->copyMessage('Barcode copied!'),

                                    Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->columnSpanFull(),

                                    Components\TextEntry::make('quantity')
                                        ->label('Quantity')
                                        ->badge()
                                        ->color('info')
                                        ->icon('heroicon-o-calculator'),

                                    Components\TextEntry::make('location')
                                        ->label('Location')
                                        ->icon('heroicon-o-map-pin')
                                        ->placeholder('Not specified'),
                                ]),

                            Components\Grid::make(1)
                                ->schema([
                                    Components\TextEntry::make('state')
                                        ->label('Status')
                                        ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/A')
                                        ->badge()
                                        ->color(fn ($state) => $state?->getColor() ?? 'gray')
                                        ->icon(fn ($state) => $state?->getIcon() ?? 'heroicon-o-question-mark-circle'),

                                    Components\TextEntry::make('due_date')
                                        ->label('Due Date')
                                        ->date('d/m/Y')
                                        ->icon('heroicon-o-calendar-days')
                                        ->badge()
                                        ->color(fn ($record) => $record->is_overdue ? 'danger' : ($record->days_until_due <= 7 ? 'warning' : 'success')),

                                    Components\TextEntry::make('document.title')
                                        ->label('Document')
                                        ->icon('heroicon-o-document-text')
                                        ->url(fn ($record) => $record->document ? \App\Filament\Resources\DocumentResource::getUrl('view', ['record' => $record->document]) : null)
                                        ->openUrlInNewTab(),
                                ]),
                        ])->from('md'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // Gallery Immagini
                Components\Section::make('\ud83d\udcf7 Image Gallery')
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('images')
                            ->collection('images')
                            ->conversion('thumb')
                            ->label('')
                            ->columnSpanFull()
                            ->limit(50)
                            ->ring(2)
                            ->extraImgAttributes(['class' => 'cursor-pointer'])
                            ->visible(fn ($record) => $record->getMedia('images')->isNotEmpty()),

                        Components\TextEntry::make('empty_images')
                            ->label('')
                            ->state('No images uploaded for this material.')
                            ->visible(fn ($record) => $record->getMedia('images')->isEmpty())
                            ->extraAttributes(['class' => 'text-center text-gray-500 py-8']),
                    ])
                    ->collapsed(false)
                    ->collapsible(),

                // Gallery Video
                Components\Section::make('\ud83c\udfa5 Videos')
                    ->schema([
                        Components\RepeatableEntry::make('videos')
                            ->label('')
                            ->schema([
                                Components\TextEntry::make('name')
                                    ->label('File Name')
                                    ->icon('heroicon-o-film'),
                                Components\TextEntry::make('size')
                                    ->label('Size')
                                    ->formatStateUsing(fn ($state) => $state ? round($state / 1024 / 1024, 2) . ' MB' : 'N/A'),
                                Components\ViewEntry::make('preview')
                                    ->label('Preview')
                                    ->view('filament.infolists.components.video-preview')
                                    ->viewData(fn ($record, $state) => [
                                        'url' => $state?->getUrl() ?? null,
                                        'name' => $state?->name ?? 'Video',
                                    ]),
                            ])
                            ->state(fn ($record) => $record->getMedia('videos'))
                            ->visible(fn ($record) => $record->getMedia('videos')->isNotEmpty())
                            ->columnSpanFull()
                            ->columns(3),

                        Components\TextEntry::make('empty_videos')
                            ->label('')
                            ->state('No videos uploaded for this material.')
                            ->visible(fn ($record) => $record->getMedia('videos')->isEmpty())
                            ->extraAttributes(['class' => 'text-center text-gray-500 py-8']),
                    ])
                    ->collapsed(true)
                    ->collapsible(),

                // Note e metadata
                Components\Section::make('Notes')
                    ->schema([
                        Components\TextEntry::make('notes')
                            ->label('')
                            ->markdown()
                            ->prose()
                            ->columnSpanFull()
                            ->placeholder('No notes added.'),
                    ])
                    ->collapsed(true)
                    ->collapsible()
                    ->visible(fn ($record) => !empty($record->notes)),

                // Metadata e timestamp
                Components\Section::make('System Information')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('created_at')
                                    ->label('Created at')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-clock'),

                                Components\TextEntry::make('updated_at')
                                    ->label('Updated at')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-pencil-square'),
                            ]),
                    ])
                    ->collapsed(true)
                    ->collapsible(),
            ]);
    }
}