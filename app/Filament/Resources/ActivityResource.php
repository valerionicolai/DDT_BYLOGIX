<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity as ActivityModel;

class ActivityResource extends Resource
{
    protected static ?string $model = ActivityModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?string $modelLabel = 'Activity Log';

    protected static ?string $pluralModelLabel = 'Activity Logs';

    protected static ?string $slug = 'activity-logs';

    protected static ?int $navigationSort = 99;

    protected static ?string $navigationGroup = 'System';

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && method_exists($user, 'hasRoleColumn') && $user->hasRoleColumn('super_admin');
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        return $user && method_exists($user, 'hasRoleColumn') && $user->hasRoleColumn('super_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('log_name')
                    ->label('Log')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->limit(80)
                    ->searchable(),

                TextColumn::make('causer')
                    ->label('Causer')
                    ->getStateUsing(fn ($record) => $record->causer?->name ?? ($record->causer_type ? class_basename($record->causer_type) . '#' . $record->causer_id : '-'))
                    ->searchable(),

                TextColumn::make('subject')
                    ->label('Subject')
                    ->getStateUsing(fn ($record) => $record->subject
                        ? class_basename($record->subject_type) . '#' . $record->subject_id
                        : ($record->subject_type ? class_basename($record->subject_type) . '#' . $record->subject_id : '-')
                    )
                    ->searchable(),

                TextColumn::make('properties')
                    ->label('Properties')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn ($record) => json_encode($record->properties)),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log')
                    ->options(fn () => ActivityModel::query()
                        ->select('log_name')
                        ->distinct()
                        ->pluck('log_name', 'log_name')
                        ->filter()
                        ->toArray()
                    ),
                SelectFilter::make('event')
                    ->label('Event')
                    ->options([
                        'created' => 'created',
                        'updated' => 'updated',
                        'deleted' => 'deleted',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ActivityResource\Pages\ListActivities::route('/'),
            'view' => ActivityResource\Pages\ViewActivity::route('/{record}'),
        ];
    }
}