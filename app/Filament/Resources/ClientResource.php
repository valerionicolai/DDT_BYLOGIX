<?php

namespace App\Filament\Resources;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use App\Models\ClientContact;
use App\Services\ClientStatusService;
use App\Services\CountryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Clients/Suppliers';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $modelLabel = 'Client/Supplier';
    
    protected static ?string $pluralModelLabel = 'Clients/Suppliers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('company')
                            ->label('Company')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                ClientType::CLIENTE->value => '👤 Client',
                                ClientType::FORNITORE->value => '🏢 Supplier',
                            ])
                            ->default(ClientType::CLIENTE->value)
                            ->required()
                            ->live()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('vat_number')
                            ->label('VAT Number')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contacts')
                    ->schema([
                        Forms\Components\Repeater::make('contacts')
                            ->relationship('contacts')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Contact Name')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('area_role')
                                    ->label('Role/Department')
                                    ->placeholder('e.g. Sales, Technical, Administration...')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->columnSpan(1),
                                Forms\Components\Toggle::make('is_primary')
                                    ->label('Primary Contact')
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('Add Contact')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Contact'),
                    ]),

                Forms\Components\Section::make('General Information (Primary Contact)')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Primary Contact (legacy)')
                            ->helperText('This field is kept for compatibility. Please use the Contacts section above.')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('email')
                            ->label('Primary Email (legacy)')
                            ->helperText('This field is kept for compatibility. Please use the Contacts section above.')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('phone')
                            ->label('Primary Phone (legacy)')
                            ->helperText('This field is kept for compatibility. Please use the Contacts section above.')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsed(),
                
                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('City')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->columnSpan(1),
                        Forms\Components\Select::make('country')
                            ->label('Country')
                            ->options(CountryService::getCountriesSorted())
                            ->searchable()
                            ->default('IT')
                            ->columnSpan(2),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Other')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(ClientStatus::options())
                            ->default(ClientStatus::PROSPECT->value)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(
                fn (Client $record): string => Pages\ViewClient::getUrl([$record]),
            )
            ->columns([
                Tables\Columns\TextColumn::make('company')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),
                Tables\Columns\TextColumn::make('contacts.name')
                    ->label('Contacts')
                    ->formatStateUsing(function ($record) {
                        $contacts = $record->contacts;
                        if ($contacts->isEmpty()) {
                            return $record->name ? "📧 {$record->name}" : 'No contacts';
                        }
                        return $contacts->map(function ($contact) {
                            $role = $contact->area_role ? " ({$contact->area_role})" : '';
                            $primary = $contact->is_primary ? ' ⭐' : '';
                            return "👤 {$contact->name}{$role}{$primary}";
                        })->join(', ');
                    })
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Legacy Contact')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->label('Legacy Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vat_number')
                    ->label('VAT Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('Country')
                    ->formatStateUsing(fn ($state) => CountryService::getCountryName($state))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(ClientType::options()),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(ClientStatus::options()),
                Tables\Filters\SelectFilter::make('country')
                    ->label('Country')
                    ->options(CountryService::getCountriesSorted())
                    ->searchable(),
                Tables\Filters\Filter::make('active_clients')
                    ->label('Active Clients Only')
                    ->query(fn (Builder $query): Builder => $query->active()),
                
                Tables\Filters\Filter::make('business_clients')
                    ->label('Business Clients')
                    ->query(fn (Builder $query): Builder => $query->whereIn('status', [
                        ClientStatus::ACTIVE,
                        ClientStatus::PROSPECT
                    ])),
            ])
            ->actions([
                Tables\Actions\Action::make('change_status')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('new_status')
                            ->label('New Status')
                            ->options(function (Client $record) {
                                $service = app(ClientStatusService::class);
                                $validTransitions = $service->getValidTransitions($record);
                                $options = [];
                                foreach ($validTransitions as $status) {
                                    $options[$status->value] = $status->label();
                                }
                                return $options;
                            })
                            ->required(),
                        
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason (optional)')
                            ->rows(3),
                    ])
                    ->action(function (array $data, Client $record): void {
                        $newStatus = ClientStatus::from($data['new_status']);
                        $service = app(ClientStatusService::class);
                        
                        if ($service->transitionClient($record, $newStatus, $data['reason'] ?? null)) {
                            Notification::make()
                                ->title('Status updated successfully')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Error updating status')
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Client $record): bool => count(app(ClientStatusService::class)->getValidTransitions($record)) > 0),
                
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
