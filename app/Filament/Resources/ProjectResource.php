<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use App\Enums\ProjectState;
use App\Enums\ProjectPriority;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = 'Progetti';
    
    protected static ?string $modelLabel = 'Progetto';
    
    protected static ?string $pluralModelLabel = 'Progetti';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sezione 1: Informazioni Progetto
                Forms\Components\Section::make('Informazioni Progetto')
                    ->description('Dettagli base del progetto')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Progetto')
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label('Project Manager')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => Auth::id())
                            ->required(),
                    ])
                    ->columns(2),

                // Sezione 2: Stato e Priorità
                Forms\Components\Section::make('Stato e Priorità')
                    ->description('Gestione stato e priorità del progetto')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Stato')
                            ->options(ProjectState::options())
                            ->default(ProjectState::DRAFT->value)
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('priority')
                            ->label('Priorità')
                            ->options(ProjectPriority::options())
                            ->default(ProjectPriority::MEDIUM->value)
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('progress_percentage')
                            ->label('Progresso (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->required(),
                    ])
                    ->columns(3),

                // Sezione 3: Date e Scadenze
                Forms\Components\Section::make('Date e Scadenze')
                    ->description('Pianificazione temporale del progetto')
                    ->collapsible()
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Data Inizio')
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Data Fine')
                            ->native(false),
                        Forms\Components\DatePicker::make('deadline')
                            ->label('Scadenza')
                            ->native(false),
                    ])
                    ->columns(3),

                // Sezione 4: Budget e Costi
                Forms\Components\Section::make('Budget e Costi')
                    ->description('Gestione economica del progetto')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('budget')
                            ->label('Budget')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        Forms\Components\TextInput::make('estimated_cost')
                            ->label('Costo Stimato')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        Forms\Components\TextInput::make('actual_cost')
                            ->label('Costo Effettivo')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                    ])
                    ->columns(3),

                // Sezione 5: Note e Metadati
                Forms\Components\Section::make('Note e Metadati')
                    ->description('Informazioni aggiuntive')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Note')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('metadata')
                            ->label('Metadati (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(
                fn (Project $record): string => Pages\ViewProject::getUrl([$record]),
            )
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['client', 'user']))
            ->searchable()
            ->searchOnBlur()
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->columns([
                // Informazioni Base
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Progetto')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Project Manager')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // Stato e Priorità con Badge
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Stato')
                    ->formatStateUsing(fn (ProjectState $state): string => $state->label())
                    ->colors([
                        'gray' => ProjectState::DRAFT->value,
                        'blue' => ProjectState::PLANNING->value,
                        'success' => ProjectState::ACTIVE->value,
                        'indigo' => ProjectState::IN_PROGRESS->value,
                        'warning' => ProjectState::ON_HOLD->value,
                        'purple' => ProjectState::REVIEW->value,
                        'emerald' => ProjectState::COMPLETED->value,
                        'danger' => ProjectState::CANCELLED->value,
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
                    ->formatStateUsing(fn (ProjectPriority $state): string => $state->label())
                    ->colors([
                        'success' => ProjectPriority::LOW->value,
                        'primary' => ProjectPriority::MEDIUM->value,
                        'warning' => ProjectPriority::HIGH->value,
                        'orange' => ProjectPriority::URGENT->value,
                        'danger' => ProjectPriority::CRITICAL->value,
                    ])
                    ->sortable(),

                // Progresso
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progresso')
                    ->formatStateUsing(fn (int $state): string => $state . '%')
                    ->sortable()
                    ->toggleable(),

                // Date
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Project $record): string => $record->is_overdue ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inizio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fine')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Budget
                Tables\Columns\TextColumn::make('budget')
                    ->label('Budget')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('estimated_cost')
                    ->label('Costo Stimato')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('actual_cost')
                    ->label('Costo Effettivo')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Timestamp
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Aggiornato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtro Stato
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options(ProjectState::options())
                    ->native(false)
                    ->multiple(),

                // Filtro Priorità
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priorità')
                    ->options(ProjectPriority::options())
                    ->native(false)
                    ->multiple(),

                // Filtro Cliente
                Tables\Filters\SelectFilter::make('client')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Filtro Project Manager
                Tables\Filters\SelectFilter::make('user')
                    ->label('Project Manager')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Filtro Progetti in Ritardo
                Tables\Filters\Filter::make('overdue')
                    ->label('In Ritardo')
                    ->query(fn (Builder $query): Builder => $query->where('deadline', '<', now()))
                    ->toggle(),

                // Filtro Progetti Attivi
                Tables\Filters\Filter::make('active_states')
                    ->label('Solo Attivi')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereIn('status', [
                            ProjectState::PLANNING->value,
                            ProjectState::ACTIVE->value,
                            ProjectState::IN_PROGRESS->value,
                            ProjectState::REVIEW->value,
                        ])
                    )
                    ->toggle(),

                // Filtro per Date
                Tables\Filters\Filter::make('deadline_range')
                    ->form([
                        Forms\Components\DatePicker::make('deadline_from')
                            ->label('Scadenza da'),
                        Forms\Components\DatePicker::make('deadline_until')
                            ->label('Scadenza fino'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['deadline_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('deadline', '>=', $date),
                            )
                            ->when(
                                $data['deadline_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('deadline', '<=', $date),
                            );
                    }),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
