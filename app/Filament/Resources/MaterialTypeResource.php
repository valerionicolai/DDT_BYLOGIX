<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialTypeResource\Pages;
use App\Filament\Resources\MaterialTypeResource\RelationManagers;
use App\Models\MaterialType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialTypeResource extends Resource
{
    protected static ?string $model = MaterialType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('unit_of_measure')
                    ->required(),
                Forms\Components\TextInput::make('default_price')
                    ->numeric(),
                Forms\Components\TextInput::make('category'),
                Forms\Components\Textarea::make('properties')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Attivo',
                        'inactive' => 'Inattivo',
                    ])
                    ->default('active')
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_of_measure')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Attivo',
                        'inactive' => 'Inattivo',
                        default => $state,
                    })
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Attivo',
                        'inactive' => 'Inattivo',
                    ])
                    ->label('Stato'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterialTypes::route('/'),
            'create' => Pages\CreateMaterialType::route('/create'),
            'view' => Pages\ViewMaterialType::route('/{record}'),
            'edit' => Pages\EditMaterialType::route('/{record}/edit'),
        ];
    }
}
