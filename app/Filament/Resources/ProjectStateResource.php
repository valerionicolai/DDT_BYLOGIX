<?php

namespace App\Filament\Resources;

use App\Enums\ProjectState;
use App\Filament\Resources\ProjectStateResource\Pages;
use App\Models\Project;
use App\Services\ProjectStateService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectStateResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Gestione Stati Progetti';

    protected static ?string $modelLabel = 'Stato Progetto';

    protected static ?string $pluralModelLabel = 'Stati Progetti';

    protected static ?string $navigationGroup = 'Gestione Progetti';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Progetto')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Progetto')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(3),
                        
                        Forms\Components\Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable(),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Project Manager')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('Stato e Priorità')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Stato')
                            ->options(ProjectState::options())
                            ->default(ProjectState::DRAFT->value)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $get, $set) {
                                // You can add logic here to handle state changes
                            }),
                        
                        Forms\Components\Select::make('priority')
                            ->label('Priorità')
                            ->options([
                                'low' => 'Bassa',
                                'medium' => 'Media',
                                'high' => 'Alta',
                                'urgent' => 'Urgente',
                            ])
                            ->default('medium'),
                    ])->columns(2),

                Forms\Components\Section::make('Date e Scadenze')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Data Inizio'),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Data Fine'),
                        
                        Forms\Components\DatePicker::make('deadline')
                            ->label('Scadenza'),
                    ])->columns(3),

                Forms\Components\Section::make('Budget e Costi')
                    ->schema([
                        Forms\Components\TextInput::make('budget')
                            ->label('Budget')
                            ->numeric()
                            ->prefix('€'),
                        
                        Forms\Components\TextInput::make('estimated_cost')
                            ->label('Costo Stimato')
                            ->numeric()
                            ->prefix('€'),
                        
                        Forms\Components\TextInput::make('actual_cost')
                            ->label('Costo Effettivo')
                            ->numeric()
                            ->prefix('€'),
                        
                        Forms\Components\TextInput::make('progress_percentage')
                            ->label('Percentuale Completamento')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Progetto')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Stato')
                    ->formatStateUsing(fn (ProjectState $state): string => $state->label())
                    ->colors([
                        'gray' => ProjectState::DRAFT->value,
                        'blue' => ProjectState::PLANNING->value,
                        'green' => ProjectState::ACTIVE->value,
                        'indigo' => ProjectState::IN_PROGRESS->value,
                        'yellow' => ProjectState::ON_HOLD->value,
                        'purple' => ProjectState::REVIEW->value,
                        'emerald' => ProjectState::COMPLETED->value,
                        'red' => ProjectState::CANCELLED->value,
                        'slate' => ProjectState::ARCHIVED->value,
                    ])
                    ->icons([
                        'heroicon-o-document' => ProjectState::DRAFT->value,
                        'heroicon-o-clipboard-document-list' => ProjectState::PLANNING->value,
                        'heroicon-o-play' => ProjectState::ACTIVE->value,
                        'heroicon-o-arrow-path' => ProjectState::IN_PROGRESS->value,
                        'heroicon-o-pause' => ProjectState::ON_HOLD->value,
                        'heroicon-o-eye' => ProjectState::REVIEW->value,
                        'heroicon-o-check-circle' => ProjectState::COMPLETED->value,
                        'heroicon-o-x-circle' => ProjectState::CANCELLED->value,
                        'heroicon-o-archive-box' => ProjectState::ARCHIVED->value,
                    ])
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Priorità')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'low' => 'Bassa',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => ['high', 'urgent'],
                    ]),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Project Manager')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progresso')
                    ->formatStateUsing(fn (?int $state): string => $state ? $state . '%' : 'N/A')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Scadenza')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_overdue')
                    ->label('In Ritardo')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options(ProjectState::options()),
                
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priorità')
                    ->options([
                        'low' => 'Bassa',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'urgent' => 'Urgente',
                    ]),
                
                Tables\Filters\Filter::make('overdue')
                    ->label('In Ritardo')
                    ->query(fn (Builder $query): Builder => $query->overdue()),
                
                Tables\Filters\Filter::make('active_states')
                    ->label('Stati Attivi')
                    ->query(fn (Builder $query): Builder => $query->inActiveStates()),
            ])
            ->actions([
                Tables\Actions\Action::make('transition')
                    ->label('Cambia Stato')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('new_state')
                            ->label('Nuovo Stato')
                            ->options(function (Project $record) {
                                $validTransitions = $record->getValidTransitions();
                                $options = [];
                                foreach ($validTransitions as $state) {
                                    $options[$state->value] = $state->label();
                                }
                                return $options;
                            })
                            ->required(),
                        
                        Forms\Components\Textarea::make('reason')
                            ->label('Motivo (opzionale)')
                            ->rows(3),
                    ])
                    ->action(function (array $data, Project $record): void {
                        $newState = ProjectState::from($data['new_state']);
                        $service = app(ProjectStateService::class);
                        
                        if ($service->transitionProject($record, $newState, $data['reason'] ?? null)) {
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
                    ->visible(fn (Project $record): bool => count($record->getValidTransitions()) > 0),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_transition')
                    ->label('Cambia Stato Multiplo')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('new_state')
                            ->label('Nuovo Stato')
                            ->options(ProjectState::options())
                            ->required(),
                        
                        Forms\Components\Textarea::make('reason')
                            ->label('Motivo (opzionale)')
                            ->rows(3),
                    ])
                    ->action(function (array $data, $records): void {
                        $newState = ProjectState::from($data['new_state']);
                        $service = app(ProjectStateService::class);
                        $projectIds = $records->pluck('id')->toArray();
                        
                        $results = $service->bulkTransitionProjects($projectIds, $newState, $data['reason'] ?? null);
                        
                        Notification::make()
                            ->title('Transizione multipla completata')
                            ->body(sprintf(
                                'Successi: %d, Fallimenti: %d, Non validi: %d',
                                count($results['success']),
                                count($results['failed']),
                                count($results['invalid'])
                            ))
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProjectStates::route('/'),
            'create' => Pages\CreateProjectState::route('/create'),
            'edit' => Pages\EditProjectState::route('/{record}/edit'),
            'view' => Pages\ViewProjectState::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['client', 'user']);
    }
}