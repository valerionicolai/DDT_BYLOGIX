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
    
    protected static ?string $navigationLabel = 'Anagrafica Clienti/Fornitori';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Cliente/Fornitore';
    
    protected static ?string $pluralModelLabel = 'Clienti/Fornitori';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Azienda')
                    ->schema([
                        Forms\Components\TextInput::make('company')
                            ->label('Azienda')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                ClientType::CLIENTE->value => '👤 Cliente',
                                ClientType::FORNITORE->value => '🏢 Fornitore',
                            ])
                            ->default(ClientType::CLIENTE->value)
                            ->required()
                            ->live()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('vat_number')
                            ->label('Partita IVA / VAT')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Referenti')
                    ->schema([
                        Forms\Components\Repeater::make('contacts')
                            ->relationship('contacts')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome Referente')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('area_role')
                                    ->label('Area/Ruolo')
                                    ->placeholder('es. Commerciale, Tecnico, Amministrativo...')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Telefono')
                                    ->tel()
                                    ->columnSpan(1),
                                Forms\Components\Toggle::make('is_primary')
                                    ->label('Referente Principale')
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('Aggiungi Referente')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nuovo Referente'),
                    ]),

                Forms\Components\Section::make('Informazioni Generali (Referente Principale)')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Referente Principale (legacy)')
                            ->helperText('Questo campo è mantenuto per compatibilità. Utilizzare la sezione Referenti sopra.')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Principale (legacy)')
                            ->email()
                            ->helperText('Questo campo è mantenuto per compatibilità. Utilizzare la sezione Referenti sopra.')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefono Principale (legacy)')
                            ->tel()
                            ->helperText('Questo campo è mantenuto per compatibilità. Utilizzare la sezione Referenti sopra.')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsed(),
                
                Forms\Components\Section::make('Indirizzo')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Indirizzo')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('Città')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('CAP')
                            ->columnSpan(1),
                        Forms\Components\Select::make('country')
                            ->label('Paese')
                            ->options(CountryService::getCountriesSorted())
                            ->searchable()
                            ->default('IT')
                            ->columnSpan(2),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Altro')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Note')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('Stato')
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
                    ->label('Azienda')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon())
                    ->sortable(),
                Tables\Columns\TextColumn::make('contacts.name')
                    ->label('Referenti')
                    ->formatStateUsing(function ($record) {
                        $contacts = $record->contacts;
                        if ($contacts->isEmpty()) {
                            return $record->name ? "📧 {$record->name}" : 'Nessun referente';
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
                    ->label('Referente Legacy')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email Legacy')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vat_number')
                    ->label('P.IVA')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefono')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Città')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('Paese')
                    ->formatStateUsing(fn ($state) => CountryService::getCountryName($state))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Stato')
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Aggiornato il')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(ClientType::options()),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options(ClientStatus::options()),
                Tables\Filters\SelectFilter::make('country')
                    ->label('Paese')
                    ->options(CountryService::getCountriesSorted())
                    ->searchable(),
                Tables\Filters\Filter::make('active_clients')
                    ->label('Solo Clienti Attivi')
                    ->query(fn (Builder $query): Builder => $query->active()),
                
                Tables\Filters\Filter::make('business_clients')
                    ->label('Clienti per Business')
                    ->query(fn (Builder $query): Builder => $query->whereIn('status', [
                        ClientStatus::ACTIVE,
                        ClientStatus::PROSPECT
                    ])),
            ])
            ->actions([
                Tables\Actions\Action::make('change_status')
                    ->label('Cambia Stato')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('new_status')
                            ->label('Nuovo Stato')
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
                            ->label('Motivo (opzionale)')
                            ->rows(3),
                    ])
                    ->action(function (array $data, Client $record): void {
                        $newStatus = ClientStatus::from($data['new_status']);
                        $service = app(ClientStatusService::class);
                        
                        if ($service->transitionClient($record, $newStatus, $data['reason'] ?? null)) {
                            Notification::make()
                                ->title('Stato aggiornato con successo')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Errore nell\'aggiornamento dello stato')
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
