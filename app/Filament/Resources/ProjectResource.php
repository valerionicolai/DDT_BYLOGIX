<?php

namespace App\Filament\Resources;

use App\Enums\ProjectPriority;
use App\Enums\ProjectState;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\DocumentsRelationManager;
use App\Models\Client;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Project Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')->required()->maxLength(255),
                                Select::make('client_id')->label('Client')->options(Client::all()->pluck('name','id'))->searchable()->required(),
                            ]),
                        Textarea::make('description')->rows(3)->columnSpanFull(),
                    ]),
                Section::make('Status & Priority')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')->options(collect(ProjectState::cases())->mapWithKeys(fn($c)=>[$c->value=>$c->label()]))->required(),
                                Select::make('priority')->options(collect(ProjectPriority::cases())->mapWithKeys(fn($c)=>[$c->value=>$c->label()]))->required(),
                            ]),
                    ]),
                Section::make('Dates')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('start_date'),
                                DatePicker::make('end_date'),
                                DatePicker::make('deadline'),
                            ]),
                    ]),
                Section::make('Budget')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('budget')->numeric()->prefix('€'),
                                TextInput::make('estimated_cost')->numeric()->prefix('€'),
                                TextInput::make('actual_cost')->numeric()->prefix('€'),
                            ]),
                    ]),
                Section::make('Notes & Metadata')
                    ->schema([
                        Textarea::make('notes')->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.company')
                    ->label('Client/Supplier')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $state ?: $record->client?->name ?: '-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => ($enum = $state instanceof \App\Enums\ProjectState ? $state : (is_string($state) ? \App\Enums\ProjectState::tryFrom($state) : null))?->label() ?? (string) $state)
                    ->badge()
                    ->color(fn ($state) => ($enum = $state instanceof \App\Enums\ProjectState ? $state : (is_string($state) ? \App\Enums\ProjectState::tryFrom($state) : null))?->color() ?? 'gray')
                    ->icon(fn ($state) => ($enum = $state instanceof \App\Enums\ProjectState ? $state : (is_string($state) ? \App\Enums\ProjectState::tryFrom($state) : null))?->icon() ?? 'heroicon-m-question-mark-circle'),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->formatStateUsing(fn ($state) => ($enum = $state instanceof \App\Enums\ProjectPriority ? $state : (is_string($state) ? \App\Enums\ProjectPriority::tryFrom($state) : null))?->label() ?? (string) $state)
                    ->badge()
                    ->color(fn ($state) => ($enum = $state instanceof \App\Enums\ProjectPriority ? $state : (is_string($state) ? \App\Enums\ProjectPriority::tryFrom($state) : null))?->color() ?? 'gray')
                    ->icon(fn ($state) => ($enum = $state instanceof \App\Enums\ProjectPriority ? $state : (is_string($state) ? \App\Enums\ProjectPriority::tryFrom($state) : null))?->icon() ?? 'heroicon-m-flag'),
                TextColumn::make('documents_count')
                    ->label('Documents')
                    ->counts('documents')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-document')
                    ->sortable(),
                TextColumn::make('materials_count')
                    ->label('Materials')
                    ->counts('materials')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-cube')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(collect(ProjectState::cases())->mapWithKeys(fn($c)=>[$c->value=>$c->label()])),
                SelectFilter::make('priority')->label('Priority')->options(collect(ProjectPriority::cases())->mapWithKeys(fn($c)=>[$c->value=>$c->label()])),
                SelectFilter::make('client_id')->label('Client/Supplier')->options(Client::query()->orderBy('company')->pluck('company','id')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['documents', 'materials']));
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
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
