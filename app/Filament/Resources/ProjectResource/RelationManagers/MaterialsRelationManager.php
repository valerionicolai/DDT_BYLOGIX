<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    protected static ?string $recordTitleAttribute = 'description';

    protected static ?string $title = 'Materials';

    protected static ?string $icon = 'heroicon-o-cube';

    public function form(Form $form): Form
    {
        // No form: this view shows a read-only list only
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('materialType.name')
                    ->label('Material Type')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                TextColumn::make('document.title')
                    ->label('Document')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('barcode')
                    ->label('Barcode')
                    ->fontFamily('mono')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Barcode copied!')
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('document_id')
                    ->label('Document')
                    ->options(function () {
                        return $this->ownerRecord->documents()
                            ->pluck('title', 'id')
                            ->toArray();
                    })
                    ->searchable(),
                
                Tables\Filters\SelectFilter::make('material_type_id')
                    ->label('Material Type')
                    ->relationship('materialType', 'name')
                    ->searchable(),

                // Quick filter: barcode contains/exact
                Tables\Filters\Filter::make('barcode_filter')
                    ->label('Barcode')
                    ->form([
                        Forms\Components\TextInput::make('barcode')->label('Value'),
                        Forms\Components\Select::make('match')
                            ->label('Search Type')
                            ->options([
                                'contains' => 'Contains',
                                'exact' => 'Exact',
                            ])
                            ->default('contains'),
                    ])
                    ->query(function ($query, array $data) {
                        $value = $data['barcode'] ?? null;
                        $mode = $data['match'] ?? 'contains';
                        if (!$value) {
                            return $query;
                        }
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
                
                Tables\Filters\Filter::make('created_from')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Created from'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['created_from'],
                            fn ($query, $date) => $query->whereDate('created_at', '>=', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['created_from']) {
                            return null;
                        }
                        return 'Created from: ' . \Carbon\Carbon::parse($data['created_from'])->format('M j, Y');
                    }),
                
                Tables\Filters\Filter::make('created_until')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Created until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['created_until'],
                            fn ($query, $date) => $query->whereDate('created_at', '<=', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['created_until']) {
                            return null;
                        }
                        return 'Created until: ' . \Carbon\Carbon::parse($data['created_until'])->format('M j, Y');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => \App\Filament\Resources\MaterialResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Material')
                    ->icon('heroicon-m-plus')
                    ->color('primary')
                    ->url(function () {
                        $documents = $this->ownerRecord->documents()->pluck('id')->implode(',');
                        return \App\Filament\Resources\MaterialResource::getUrl('create') . 
                               '?project_id=' . $this->ownerRecord->id . 
                               '&document_ids=' . $documents;
                    }),
            ])
            ->bulkActions([
                // No bulk actions to keep the view focused
            ])
            // Default order on existing field: created_at desc (consistent with Document view)
            ->defaultSort('created_at', 'desc');
    }
}