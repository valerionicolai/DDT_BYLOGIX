<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentCategoryResource\Pages;
use App\Models\DocumentCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentCategoryResource extends Resource
{
    protected static ?string $model = DocumentCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Document Categories';

    protected static ?string $modelLabel = 'Document Category';

    protected static ?string $pluralModelLabel = 'Document Categories';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

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
                                    $set('code', str()->slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(255)
                            ->unique(DocumentCategory::class, 'code', ignoreRecord: true)
                            ->helperText('Unique identifier for this category'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Appearance')
                    ->schema([
                        Forms\Components\ColorPicker::make('color')
                            ->default('#6b7280')
                            ->helperText('Color used for visual identification'),

                        Forms\Components\Select::make('icon')
                            ->options([
                                'heroicon-o-folder' => 'Folder',
                                'heroicon-o-folder-open' => 'Folder Open',
                                'heroicon-o-document' => 'Document',
                                'heroicon-o-document-text' => 'Document Text',
                                'heroicon-o-archive-box' => 'Archive Box',
                                'heroicon-o-clipboard-document' => 'Clipboard Document',
                                'heroicon-o-inbox' => 'Inbox',
                                'heroicon-o-tag' => 'Tag',
                                'heroicon-o-bookmark' => 'Bookmark',
                                'heroicon-o-star' => 'Star',
                            ])
                            ->default('heroicon-o-folder')
                            ->searchable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Whether this category is available for selection'),

                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order in which categories are displayed'),
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

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\ColorColumn::make('color')
                    ->label('Color'),

                Tables\Columns\IconColumn::make('icon')
                    ->icon(fn (string $state): string => $state),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('documents_count')
                    ->label('Documents')
                    ->counts('documents')
                    ->badge()
                    ->color('primary'),

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

                Tables\Filters\Filter::make('has_documents')
                    ->label('Has Documents')
                    ->query(fn (Builder $query): Builder => $query->has('documents'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (DocumentCategory $record) {
                        if ($record->documents()->count() > 0) {
                            throw new \Exception('Cannot delete category that has documents assigned to it.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->documents()->count() > 0) {
                                    throw new \Exception('Cannot delete categories that have documents assigned to them.');
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
            'index' => Pages\ListDocumentCategories::route('/'),
            'create' => Pages\CreateDocumentCategory::route('/create'),
            'view' => Pages\ViewDocumentCategory::route('/{record}'),
            'edit' => Pages\EditDocumentCategory::route('/{record}/edit'),
        ];
    }


}