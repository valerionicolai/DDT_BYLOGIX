<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTypeResource\Pages;
use App\Models\DocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $context, $state, Forms\Set $set) {
                                if ($context === 'create') {
                                    $set('code', strtoupper(str_replace(' ', '_', $state)));
                                }
                            }),
                        
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(20)
                            ->unique(DocumentType::class, 'code', ignoreRecord: true)
                            ->rules(['regex:/^[A-Z0-9_]+$/'])
                            ->helperText('Use uppercase letters, numbers, and underscores only'),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Appearance')
                    ->schema([
                        Forms\Components\ColorPicker::make('color')
                            ->default('#3B82F6')
                            ->helperText('Color used for visual identification'),
                        
                        Forms\Components\Select::make('icon')
                            ->options([
                                'heroicon-o-document' => 'Document',
                                'heroicon-o-document-text' => 'Document Text',
                                'heroicon-o-document-duplicate' => 'Document Duplicate',
                                'heroicon-o-clipboard-document' => 'Clipboard Document',
                                'heroicon-o-clipboard-document-list' => 'Clipboard List',
                                'heroicon-o-folder' => 'Folder',
                                'heroicon-o-folder-open' => 'Folder Open',
                                'heroicon-o-archive-box' => 'Archive Box',
                                'heroicon-o-inbox' => 'Inbox',
                                'heroicon-o-paper-clip' => 'Paper Clip',
                                'heroicon-o-photo' => 'Photo',
                                'heroicon-o-video-camera' => 'Video',
                                'heroicon-o-musical-note' => 'Audio',
                                'heroicon-o-code-bracket' => 'Code',
                                'heroicon-o-table-cells' => 'Spreadsheet',
                                'heroicon-o-presentation-chart-line' => 'Presentation',
                            ])
                            ->default('heroicon-o-document')
                            ->searchable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Inactive types cannot be assigned to new documents'),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first in lists'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Data')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->keyLabel('Property')
                            ->valueLabel('Value')
                            ->addActionLabel('Add property')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),
                
                Tables\Columns\IconColumn::make('icon')
                    ->icon(fn (string $state): string => $state),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                
                Tables\Columns\TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label('Documents')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (DocumentType $record) {
                        if ($record->documents()->count() > 0) {
                            throw new \Exception('Cannot delete document type that has associated documents.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->documents()->count() > 0) {
                                    throw new \Exception("Cannot delete document type '{$record->name}' that has associated documents.");
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'view' => Pages\ViewDocumentType::route('/{record}'),
            'edit' => Pages\EditDocumentType::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}