<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Document;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Select::make('type_id')
                    ->relationship('documentType', 'name')
                    ->required(),
                Forms\Components\FileUpload::make('file')
                    ->label('File')
                    ->directory('documents')
                    ->visibility('public'),
                Forms\Components\TextInput::make('barcode')
                    ->label('Barcode')
                    ->unique(ignoreRecord: true),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->label('Title'),
                Tables\Columns\TextColumn::make('documentType.name')->label('Type')->sortable(),
                Tables\Columns\TextColumn::make('barcode')->label('Barcode')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('barcode')
                    ->form([
                        Forms\Components\TextInput::make('barcode')
                            ->label('Barcode')
                            ->placeholder('Enter barcode')
                            ->helperText('Search for an exact match or contains (toggle below).'),
                        Forms\Components\ToggleButtons::make('match')
                            ->label('Match Mode')
                            ->options([
                                'exact' => 'Exact',
                                'contains' => 'Contains',
                            ])
                            ->inline()
                            ->default('contains')
                            ->icons([
                                'exact' => 'heroicon-m-check',
                                'contains' => 'heroicon-m-magnifying-glass',
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        $value = $data['barcode'] ?? null;
                        if (!$value) return $query;
                        $mode = $data['match'] ?? 'contains';
                        return $mode === 'exact'
                            ? $query->where('barcode', $value)
                            : $query->where('barcode', 'like', '%' . $value . '%');
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!($data['barcode'] ?? null)) {
                            return null;
                        }
                        $mode = $data['match'] ?? 'contains';
                        return 'Barcode: ' . $data['barcode'] . ($mode === 'exact' ? ' (exact)' : ' (contains)');
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Document')
                    ->modalHeading('New Document')
                    ->icon('heroicon-m-plus')
                    ->color('primary')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['project_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (Document $record): string => $record->file_url ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (Document $record): bool => !empty($record->file_path)),
                
                EditAction::make()
                    ->icon('heroicon-m-pencil-square'),
                
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-m-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}