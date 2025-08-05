<?php

namespace App\Filament\Resources\ProjectStateResource\Pages;

use App\Filament\Resources\ProjectStateResource;
use App\Services\ProjectStateService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ListProjectStates extends ListRecords
{
    protected static string $resource = ProjectStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('archive_eligible')
                ->label('Archivia Progetti Completati')
                ->icon('heroicon-o-archive-box')
                ->action(function () {
                    $service = app(ProjectStateService::class);
                    $count = $service->archiveEligibleProjects();
                    
                    $this->notify('success', "Archiviati {$count} progetti");
                })
                ->requiresConfirmation(),
            
            Actions\Action::make('review_overdue')
                ->label('Rivedi Progetti in Ritardo')
                ->icon('heroicon-o-exclamation-triangle')
                ->action(function () {
                    $service = app(ProjectStateService::class);
                    $count = $service->reviewOverdueProjects();
                    
                    $this->notify('success', "Spostati {$count} progetti in revisione");
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProjectStateStatsWidget::class,
        ];
    }
}

class ProjectStateStatsWidget extends \Filament\Widgets\StatsOverviewWidget
{
    protected function getStats(): array
    {
        $service = app(ProjectStateService::class);
        $statistics = $service->getStateStatistics();

        return [
            Stat::make('Totale Progetti', $statistics['total'])
                ->description('Tutti i progetti nel sistema')
                ->descriptionIcon('heroicon-m-folder')
                ->color('primary'),

            Stat::make('Progetti Attivi', $statistics['active_projects'])
                ->description('In corso di lavorazione')
                ->descriptionIcon('heroicon-m-play')
                ->color('success'),

            Stat::make('Progetti Completati', $statistics['final_projects'])
                ->description('Completati o cancellati')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Progetti Inattivi', $statistics['inactive_projects'])
                ->description('Bozze e in pausa')
                ->descriptionIcon('heroicon-m-pause')
                ->color('warning'),
        ];
    }
}