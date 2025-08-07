<?php

namespace App\Filament\Widgets;

use App\Services\ProjectStateService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStateStatsWidget extends StatsOverviewWidget
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