<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource\RelationManagers;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationLabel = 'Materials';
    
    protected static ?string $modelLabel = 'Material';
    
    protected static ?string $pluralModelLabel = 'Materials';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('document_id')
                    ->relationship('document', 'title')
                    ->default(function () {
                        return request()->input('document_id') ?? null;
                    })
                    ->required(),
                Forms\Components\Select::make('material_type_id')
                    ->relationship('materialType', 'name')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('location'),
                Forms\Components\DatePicker::make('due_date')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->downloadable()
                    ->openable()
                    ->imageEditor()
                    ->imageResizeMode('contain')
                    ->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('videos')
                    ->collection('videos')
                    ->multiple()
                    ->acceptedFileTypes(['video/*'])
                    ->reorderable()
                    ->appendFiles()
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document.title')
                    ->numeric()
                    ->sortable()
                    ->url(fn ($record) => $record->document ? MaterialResource::getUrl('view', ['record' => $record->document]) : null),
                Tables\Columns\TextColumn::make('materialType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                SpatieMediaLibraryImageColumn::make('images')
                    ->collection('images')
                    ->label('Images')
                    ->conversion('thumb')
                    ->limit(3),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->fontFamily('mono')
                    ->searchable()
                    ->url(fn ($record) => MaterialResource::getUrl('view', ['record' => $record])),
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
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'view' => Pages\ViewMaterial::route('/{record}'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}