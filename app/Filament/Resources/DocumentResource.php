<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentCategory;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        
                        Forms\Components\Select::make('document_type_id')
                            ->label('Document Type')
                            ->relationship('documentType', 'name')
                            ->options(DocumentType::active()->ordered()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->maxLength(20)
                                    ->unique(DocumentType::class, 'code'),
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(1000),
                                Forms\Components\ColorPicker::make('color')
                                    ->default('#3B82F6'),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ]),
                        
                        Forms\Components\Select::make('document_category_id')
                            ->label('Document Category')
                            ->relationship('documentCategory', 'name')
                            ->options(\App\Models\DocumentCategory::active()->ordered()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(\App\Models\DocumentCategory::class, 'code'),
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(1000),
                                Forms\Components\ColorPicker::make('color')
                                    ->default('#6b7280'),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ]),
                        

                    ])
                    ->columns(2),

                Forms\Components\Section::make('File Information')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Supplier (Client)')
                            ->relationship('client', 'name')
                            ->options(Client::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('company')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('address')
                                    ->maxLength(500),
                                Forms\Components\TextInput::make('city')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('postal_code')
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('country')
                                    ->maxLength(255),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('active'),
                            ]),
                        
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Document Attachment')
                            ->disk('public')
                            ->directory('documents')
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                                $hash = substr(md5(now()->format('Y-m-d H:i:s.u')), 0, 8);
                                return $originalName . '_' . $hash . '.' . $extension;
                            })
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/*',
                                'text/*',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                'application/vnd.ms-powerpoint'
                            ])
                            ->maxSize(20480) // 20MB
                            ->downloadable()
                            ->previewable()
                            ->openable()
                            ->deletable()
                            ->columnSpanFull()
                            ->helperText('Supported formats: PDF, Images, Documents (Word, Excel, PowerPoint), Text files. Maximum size: 20MB'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Dates')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'archived' => 'Archived',
                            ])
                            ->default('draft')
                            ->required(),
                        
                        Forms\Components\TextInput::make('barcode')
                            ->maxLength(255)
                            ->unique(Document::class, 'barcode', ignoreRecord: true)
                            ->helperText('Leave empty to auto-generate'),
                        
                        Forms\Components\DateTimePicker::make('created_date')
                            ->default(now()),
                        
                        Forms\Components\DateTimePicker::make('due_date'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Document Type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->documentType?->color ?? 'gray'),
                
                Tables\Columns\TextColumn::make('documentCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->documentCategory?->color ?? 'gray'),
                

                
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Supplier')
                    ->description(fn ($record) => $record->client?->company)
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('file_path')
                    ->label('Attachment')
                    ->searchable()
                    ->limit(30)
                    ->url(fn ($record) => $record->file_url, shouldOpenInNewTab: true)
                    ->icon(fn ($record) => match (true) {
                        $record->is_pdf => 'heroicon-o-document-text',
                        $record->is_image => 'heroicon-o-photo',
                        default => 'heroicon-o-document'
                    })
                    ->color(fn ($record) => $record->file_exists ? 'primary' : 'danger')
                    ->tooltip(fn ($record) => $record->file_exists ? 'Click to download' : 'File not found')
                    ->formatStateUsing(fn ($state) => $state ? basename($state) : 'No file'),
                

                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'active' => 'success',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('barcode')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_date')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record && $record->is_overdue ? 'danger' : null)
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
                Tables\Filters\SelectFilter::make('document_type_id')
                    ->label('Document Type')
                    ->relationship('documentType', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('document_category_id')
                    ->label('Document Category')
                    ->relationship('documentCategory', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Supplier (Client)')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'archived' => 'Archived',
                    ])
                    ->multiple(),
                
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Documents')
                    ->query(fn (Builder $query): Builder => $query->where('due_date', '<', now())->where('status', '!=', 'archived'))
                    ->toggle(),
                
                Tables\Filters\Filter::make('created_date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn ($record) => $record->file_url, shouldOpenInNewTab: true)
                    ->visible(fn ($record) => $record->file_exists),
                
                Tables\Actions\Action::make('view_file')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => $record->file_url, shouldOpenInNewTab: true)
                    ->visible(fn ($record) => $record->file_exists && ($record->is_pdf || $record->is_image)),
                
                Tables\Actions\Action::make('replace_file')
                    ->label('Replace File')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->form([
                        Forms\Components\FileUpload::make('new_file')
                            ->label('New Document Attachment')
                            ->disk('public')
                            ->directory('documents')
                            ->preserveFilenames()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'image/webp',
                                'text/plain',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            ])
                            ->maxSize(20480) // 20MB
                            ->required()
                            ->helperText('Supported formats: PDF, Images, Text, Word, Excel, PowerPoint (Max: 20MB)'),
                    ])
                    ->action(function ($record, array $data) {
                        if (isset($data['new_file'])) {
                            // Delete old file if it exists
                            $record->deleteFile();
                            
                            // Update with new file
                            $record->updateFileInfo($data['new_file']);
                            $record->save();
                            
                            Notification::make()
                                ->title('File replaced successfully')
                                ->success()
                                ->send();
                        }
                    }),
                
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        // Delete associated file when deleting the record
                        $record->deleteFile();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
