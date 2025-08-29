<?php

namespace App\Filament\Resources\DocumentResource\RelationManagers;

use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DocumentResource\Pages\ViewDocument;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    protected static ?string $recordTitleAttribute = 'description';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Il modello Material non ha 'name': usiamo 'description'
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required(),
                
                Forms\Components\Select::make('material_type_id')
                    ->label('Material Type')
                    ->relationship('materialType', 'name')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity'),
                
                Tables\Columns\TextColumn::make('materialType.name')
                    ->label('Material Type'),
                
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // Ordine di default su created_at (esistente), non su colonna inesistente
            ->defaultSort('created_at', 'desc');
    }
}