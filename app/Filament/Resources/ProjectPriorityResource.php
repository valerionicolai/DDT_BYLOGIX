<?php

namespace App\Filament\Resources;

use App\Enums\ProjectPriority;
use App\Filament\Resources\ProjectPriorityResource\Pages;
use App\Models\Project;
use App\Services\ProjectPriorityService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProjectPriorityResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'Project Priorities';

    protected static ?string $modelLabel = 'Project Priority';

    protected static ?string $pluralModelLabel = 'Project Priorities';

    protected static ?string $navigationGroup = 'Project Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Priority & Status')
                    ->schema([
                        Forms\Components\Select::make('priority')
                            ->options(ProjectPriority::options())
                            ->required()
                            ->default(ProjectPriority::MEDIUM->value)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $priority = ProjectPriority::from($state);
                                    $set('priority_description', $priority->description());
                                    $set('sla_hours', $priority->slaHours());
                                }
                            })
                            ->helperText(fn ($state) => $state ? ProjectPriority::from($state)->description() : null),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'on_hold' => 'On Hold',
                            ])
                            ->required()
                            ->default('draft'),
                        Forms\Components\TextInput::make('sla_hours')
                            ->label('SLA Hours')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Automatically set based on priority'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Project Details')
                    ->schema([
                        Forms\Components\Select::make('project_manager_id')
                            ->label('Project Manager')
                            ->relationship('projectManager', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('start_date'),
                        Forms\Components\DatePicker::make('end_date')
                            ->after('start_date'),
                        Forms\Components\TextInput::make('progress')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Budget Information')
                    ->schema([
                        Forms\Components\TextInput::make('budget')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        Forms\Components\TextInput::make('actual_cost')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => ProjectPriority::from($state)->color())
                    ->icon(fn (string $state): string => ProjectPriority::from($state)->icon())
                    ->formatStateUsing(fn (string $state): string => ProjectPriority::from($state)->label())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        'on_hold' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('projectManager.name')
                    ->label('Project Manager')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Deadline')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_overdue')
                    ->label('Overdue')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('priority')
                    ->options(ProjectPriority::options())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'on_hold' => 'On Hold',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('high_priority')
                    ->label('High Priority & Above')
                    ->query(fn (Builder $query): Builder => $query->highPriority()),
                Tables\Filters\Filter::make('urgent_priority')
                    ->label('Urgent Priority')
                    ->query(fn (Builder $query): Builder => $query->urgentPriority()),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->overdue()),
            ])
            ->actions([
                Tables\Actions\Action::make('escalate_priority')
                    ->label('Escalate')
                    ->icon('heroicon-o-arrow-up')
                    ->color('warning')
                    ->visible(fn (Project $record): bool => $record->canEscalatePriority())
                    ->action(function (Project $record) {
                        $oldPriority = $record->priority;
                        $record->escalatePriority();
                        
                        Notification::make()
                            ->title('Priority Escalated')
                            ->body("Project priority changed from {$oldPriority->label()} to {$record->priority->label()}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('de_escalate_priority')
                    ->label('De-escalate')
                    ->icon('heroicon-o-arrow-down')
                    ->color('info')
                    ->visible(fn (Project $record): bool => $record->canDeEscalatePriority())
                    ->action(function (Project $record) {
                        $oldPriority = $record->priority;
                        $record->deEscalatePriority();
                        
                        Notification::make()
                            ->title('Priority De-escalated')
                            ->body("Project priority changed from {$oldPriority->label()} to {$record->priority->label()}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('change_priority')
                    ->label('Change Priority')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('new_priority')
                            ->label('New Priority')
                            ->options(ProjectPriority::options())
                            ->required()
                            ->default(fn (Project $record) => $record->priority->value),
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for Change')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Project $record, array $data) {
                        $oldPriority = $record->priority;
                        $newPriority = ProjectPriority::from($data['new_priority']);
                        
                        $record->setPriority($newPriority);
                        
                        Notification::make()
                            ->title('Priority Changed')
                            ->body("Project priority changed from {$oldPriority->label()} to {$newPriority->label()}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_change_priority')
                        ->label('Change Priority')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('new_priority')
                                ->label('New Priority')
                                ->options(ProjectPriority::options())
                                ->required(),
                            Forms\Components\Textarea::make('reason')
                                ->label('Reason for Change')
                                ->required()
                                ->maxLength(500),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $newPriority = ProjectPriority::from($data['new_priority']);
                            $count = 0;
                            
                            foreach ($records as $record) {
                                $record->setPriority($newPriority);
                                $count++;
                            }
                            
                            Notification::make()
                                ->title('Bulk Priority Change')
                                ->body("Changed priority for {$count} projects to {$newPriority->label()}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('bulk_escalate')
                        ->label('Escalate Priority')
                        ->icon('heroicon-o-arrow-up')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $count = 0;
                            
                            foreach ($records as $record) {
                                if ($record->canEscalatePriority()) {
                                    $record->escalatePriority();
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Bulk Priority Escalation')
                                ->body("Escalated priority for {$count} projects")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority', 'desc')
            ->poll('30s');
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
            'index' => Pages\ListProjectPriorities::route('/'),
            'create' => Pages\CreateProjectPriority::route('/create'),
            'view' => Pages\ViewProjectPriority::route('/{record}'),
            'edit' => Pages\EditProjectPriority::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['client', 'projectManager'])
            ->orderByPriority();
    }
}
